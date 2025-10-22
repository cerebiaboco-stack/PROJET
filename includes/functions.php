<?php
/**
 * Fonctions utilitaires pour la plateforme BloodLink
 */

/**
 * Redirige vers une URL spécifique
 * @param string $url L'URL vers laquelle rediriger
 * @param int $statusCode Code HTTP de redirection (optionnel)
 */
function redirect($url, $statusCode = 303) {
    header('Location: ' . $url, true, $statusCode);
    exit();
}

/**
 * Formate une date au format français
 * @param string $date La date à formater
 * @return string La date formatée
 */
function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') {
        return 'N/A';
    }
    return date('d/m/Y', strtotime($date));
}

/**
 * Formate une date et heure au format français
 * @param string $datetime La date et heure à formater
 * @return string La date et heure formatées
 */
function formatDateTime($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Vérifie si un utilisateur est connecté
 * @return bool True si connecté, false sinon
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur connecté a un rôle spécifique
 * @param string $role Le rôle à vérifier
 * @return bool True si l'utilisateur a le rôle, false sinon
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] == $role;
}

/**
 * Génère un message d'alerte Bootstrap
 * @param string $message Le message à afficher
 * @param string $type Le type d'alerte (success, danger, warning, info)
 * @return string Le code HTML de l'alerte
 */
function alert($message, $type = 'info') {
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">'
            . $message
            . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
            . '</div>';
}

/**
 * Échappe les caractères spéciaux pour prévenir les injections XSS
 * @param string $data La chaîne à échapper
 * @return string La chaîne échappée
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Valide une adresse email
 * @param string $email L'email à valider
 * @return bool True si l'email est valide, false sinon
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Récupère le stock de sang par groupe sanguin
 * @param mysqli $conn La connexion à la base de données
 * @param string $bloodGroup Le groupe sanguin (optionnel)
 * @return array|int Les données de stock ou le count pour un groupe spécifique
 */
function getBloodStock($conn, $bloodGroup = null) {
    if ($bloodGroup) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM poche WHERE GroupeSanguin = ? AND DatePeremption > CURDATE()");
        $stmt->bind_param("s", $bloodGroup);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        return $data['count'];
    } else {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stockData = [];
        
        foreach ($bloodGroups as $group) {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM poche WHERE GroupeSanguin = ? AND DatePeremption > CURDATE()");
            $stmt->bind_param("s", $group);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stockData[$group] = $data['count'];
        }
        
        return $stockData;
    }
}

/**
 * Récupère le statut du stock en fonction de la quantité
 * @param int $quantity La quantité de poches
 * @return string Le statut (critical, low, moderate, sufficient)
 */
function getStockStatus($quantity) {
    if ($quantity < 10) {
        return 'critical';
    } elseif ($quantity < 30) {
        return 'low';
    } elseif ($quantity < 50) {
        return 'moderate';
    } else {
        return 'sufficient';
    }
}

/**
 * Récupère le nom de l'hôpital par son ID
 * @param mysqli $conn La connexion à la base de données
 * @param int $hospitalId L'ID de l'hôpital
 * @return string Le nom de l'hôpital
 */
function getHospitalName($conn, $hospitalId) {
    $stmt = $conn->prepare("SELECT Nom FROM hopital WHERE IdHopital = ?");
    $stmt->bind_param("i", $hospitalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $hospital = $result->fetch_assoc();
        return $hospital['Nom'];
    }
    
    return 'Inconnu';
}

/**
 * Récupère les demandes de sang
 * @param mysqli $conn La connexion à la base de données
 * @param int $limit Le nombre de résultats à retourner (optionnel)
 * @return array Les demandes de sang
 */
function getBloodRequests($conn, $limit = null) {
    $sql = "SELECT d.*, h.Nom as hopital_nom, m.Nom as medecin_nom 
            FROM demande d 
            JOIN hopital h ON d.IdHopital = h.IdHopital 
            JOIN medecin m ON d.IdMedecin = m.IdMedecin 
            ORDER BY d.DateDemande DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    
    $result = $conn->query($sql);
    $requests = [];
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    
    return $requests;
}

/**
 * Récupère les informations de l'utilisateur connecté
 * @param mysqli $conn La connexion à la base de données
 * @return array Les informations de l'utilisateur
 */
function getUserInfo($conn) {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE IdUsers = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Vérifie si une demande peut être traitée en fonction du stock disponible
 * @param mysqli $conn La connexion à la base de données
 * @param string $bloodGroup Le groupe sanguin demandé
 * @param int $quantity La quantité demandée
 * @return bool True si le stock est suffisant, false sinon
 */
function canFulfillRequest($conn, $bloodGroup, $quantity) {
    $currentStock = getBloodStock($conn, $bloodGroup);
    return $currentStock >= $quantity;
}

/**
 * Log une action dans le système
 * @param mysqli $conn La connexion à la base de données
 * @param int $userId L'ID de l'utilisateur
 * @param string $action L'action effectuée
 * @param string $details Les détails de l'action
 * @return bool True si le log a été enregistré, false sinon
 */
function logAction($conn, $userId, $action, $details = '') {
    $stmt = $conn->prepare("INSERT INTO logs (IdUsers, Action, Details, DateCreation) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss", $userId, $action, $details);
    return $stmt->execute();
}

/**
 * Génère un mot de passe aléatoire
 * @param int $length La longueur du mot de passe
 * @return string Le mot de passe généré
 */
function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
}

/**
 * Envoie un email de notification
 * @param string $to L'adresse email du destinataire
 * @param string $subject Le sujet de l'email
 * @param string $body Le corps de l'email
 * @return bool True si l'email a été envoyé, false sinon
 */
function sendEmail($to, $subject, $body) {
    // En-têtes de l'email
    $headers = "From: no-reply@bloodlink.fr\r\n";
    $headers .= "Reply-To: no-reply@bloodlink.fr\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    // Envoyer l'email
    return mail($to, $subject, $body, $headers);
}

/**
 * Convertit un statut en badge Bootstrap
 * @param string $status Le statut à convertir
 * @return string Le code HTML du badge
 */
function statusBadge($status) {
    $statusClass = '';
    
    switch (strtolower($status)) {
        case 'en attente':
        case 'pending':
            $statusClass = 'bg-warning';
            break;
        case 'approuvé':
        case 'approved':
        case 'traité':
        case 'processed':
            $statusClass = 'bg-success';
            break;
        case 'rejeté':
        case 'rejected':
        case 'annulé':
        case 'cancelled':
            $statusClass = 'bg-danger';
            break;
        case 'urgent':
            $statusClass = 'bg-danger';
            break;
        default:
            $statusClass = 'bg-info';
    }
    
    return '<span class="badge ' . $statusClass . '">' . $status . '</span>';
}

/**
 * Calcule l'âge à partir d'une date de naissance
 * @param string $birthdate La date de naissance
 * @return int L'âge
 */
function calculateAge($birthdate) {
    if (empty($birthdate)) {
        return 0;
    }
    
    $birthDate = new DateTime($birthdate);
    $today = new DateTime();
    $age = $today->diff($birthDate);
    return $age->y;
}

/**
 * Formate un nombre avec séparateur de milliers
 * @param int $number Le nombre à formater
 * @return string Le nombre formaté
 */
function formatNumber($number) {
    return number_format($number, 0, ',', ' ');
}

/**
 * Valide un numéro de téléphone français
 * @param string $phone Le numéro de téléphone à valider
 * @return bool True si le numéro est valide, false sinon
 */
function isValidFrenchPhone($phone) {
    // Supprimer tous les caractères non numériques
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Vérifier la longueur et le format
    if (strlen($phone) === 10 && preg_match('/^0[1-9][0-9]{8}$/', $phone)) {
        return true;
    }
    
    return false;
}
?>