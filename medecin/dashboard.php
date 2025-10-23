<?php
session_start();
include('../includes/config.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Récupérer les informations de l'utilisateur avec jointure vers la table medecin
$user_id = $_SESSION['user_id'];
$query_user = $bdd->prepare("SELECT u.*, m.IdMedecin, m.Nom as NomMedecin, m.IdHopital, m.email as EmailMedecin, m.Specialite, m.Contact as ContactMedecin, h.Nom as NomHopital 
                            FROM users u 
                            LEFT JOIN medecin m ON u.IdMedecin = m.IdMedecin 
                            LEFT JOIN hopital h ON m.IdHopital = h.IdHopital 
                            WHERE u.IdUsers = ?");
$query_user->execute([$user_id]);
$user = $query_user->fetch();

// Vérifier si l'utilisateur est bien un médecin
if (!$user || $user['Role'] != 'medecin') {
    header("Location: ../login.php");
    exit();
}

// Déterminer la page active
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nouvelle_demande'])) {
        // Nouvelle demande de sang
        $groupe_sanguin = $_POST['groupe_sanguin'];
        $quantite = $_POST['quantite'];
        $urgence = $_POST['urgence'];
        $notes = $_POST['notes'] ?? '';
        
        // Vérifier le stock disponible
        $check_stock = $bdd->prepare("SELECT COUNT(*) as stock FROM poche WHERE GroupeSanguin = ? AND DatePeremption > CURDATE()");
        $check_stock->execute([$groupe_sanguin]);
        $stock_disponible = $check_stock->fetch()['stock'];
        
        if ($quantite > $stock_disponible) {
            $message_error = "Stock insuffisant! Seulement $stock_disponible poche(s) disponible(s) pour le groupe $groupe_sanguin.";
        } else {
            // Vérifier que l'utilisateur a bien un IdMedecin
            if ($user['IdMedecin']) {
                $insert_demande = $bdd->prepare("INSERT INTO demande (DateDemande, GroupeSanguin, Quantite, Statut, IdMedecin) VALUES (CURDATE(), ?, ?, 'En attente', ?)");
                $insert_demande->execute([$groupe_sanguin, $quantite, $user['IdMedecin']]);
                $message_success = "Demande créée avec succès!";
            } else {
                $message_error = "Erreur : Aucun médecin associé à votre compte.";
            }
        }
    }
    
    if (isset($_POST['modifier_profil'])) {
        // Modification du profil
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $specialite = $_POST['specialite'];
        
        if ($user['IdMedecin']) {
            $update_profil = $bdd->prepare("UPDATE medecin SET Nom = ?, email = ?, Contact = ?, Specialite = ? WHERE IdMedecin = ?");
            $update_profil->execute([$nom, $email, $contact, $specialite, $user['IdMedecin']]);
            $message_success = "Profil mis à jour avec succès!";
            
            // Mettre à jour les données locales
            $user['NomMedecin'] = $nom;
            $user['EmailMedecin'] = $email;
            $user['ContactMedecin'] = $contact;
            $user['Specialite'] = $specialite;
        } else {
            $message_error = "Erreur : Aucun médecin associé à votre compte.";
        }
    }
    
    if (isset($_POST['annuler_demande'])) {
        $id_demande = $_POST['id_demande'];
        $update_demande = $bdd->prepare("UPDATE demande SET Statut = 'Annulée' WHERE IdDemande = ? AND IdMedecin = ? AND Statut = 'En attente'");
        $update_demande->execute([$id_demande, $user['IdMedecin']]);
        $message_success = "Demande annulée avec succès!";
    }
}

// Statistiques pour le dashboard
if ($page == 'dashboard') {
    // Nombre total de demandes
    $requete_demande = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ?');
    $requete_demande->execute([$user['IdMedecin']]);
    $resultat_requete_demande = $requete_demande->fetch()['total'];

    // Demandes approuvées
    $requete_demande_approuvees = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "Approuvée"');
    $requete_demande_approuvees->execute([$user['IdMedecin']]);
    $resultat_requete_demande_approuvees = $requete_demande_approuvees->fetch()['total'];

    // Demandes en attente
    $requete_demande_attente = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "En attente"');
    $requete_demande_attente->execute([$user['IdMedecin']]);
    $resultat_requete_demande_attente = $requete_demande_attente->fetch()['total'];

    // Demandes rejetées
    $requete_demande_rejetees = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "Rejetée"');
    $requete_demande_rejetees->execute([$user['IdMedecin']]);
    $resultat_requete_demande_rejetees = $requete_demande_rejetees->fetch()['total'];

    // Demandes annulées
    $requete_demande_annulees = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "Annulée"');
    $requete_demande_annulees->execute([$user['IdMedecin']]);
    $resultat_requete_demande_annulees = $requete_demande_annulees->fetch()['total'];

    // Demandes récentes
    $demandes_recentes = $bdd->prepare('SELECT * FROM demande WHERE IdMedecin = ? ORDER BY DateDemande DESC LIMIT 5');
    $demandes_recentes->execute([$user['IdMedecin']]);
    $liste_demandes_recentes = $demandes_recentes->fetchAll();

    // Stocks disponibles
    $stocks = $bdd->query('SELECT GroupeSanguin, COUNT(*) as quantite FROM poche WHERE DatePeremption > CURDATE() GROUP BY GroupeSanguin');
    $stocks_disponibles = $stocks->fetchAll();
}

// Page mes demandes
if ($page == 'mes-demandes') {
    $mes_demandes = $bdd->prepare('SELECT d.*, b.NomBanque FROM demande d LEFT JOIN banque b ON d.IdBanque = b.IdBanque WHERE d.IdMedecin = ? ORDER BY d.DateDemande DESC');
    $mes_demandes->execute([$user['IdMedecin']]);
    $liste_mes_demandes = $mes_demandes->fetchAll();
}

// Page stocks disponibles
if ($page == 'stocks') {
    $stocks_detaille = $bdd->query('SELECT GroupeSanguin, COUNT(*) as quantite FROM poche WHERE DatePeremption > CURDATE() GROUP BY GroupeSanguin');
    $liste_stocks = $stocks_detaille->fetchAll();
    
    // Stocks par banque
    $stocks_banque = $bdd->query('SELECT b.NomBanque, p.GroupeSanguin, COUNT(*) as quantite 
                                 FROM poche p 
                                 LEFT JOIN cave c ON p.IdCave = c.IdCave 
                                 LEFT JOIN banque b ON c.Idbanque = b.IdBanque 
                                 WHERE p.DatePeremption > CURDATE() 
                                 GROUP BY b.NomBanque, p.GroupeSanguin 
                                 ORDER BY b.NomBanque, p.GroupeSanguin');
    $liste_stocks_banque = $stocks_banque->fetchAll();
}

// Page historique
if ($page == 'historique') {
    $historique = $bdd->prepare('SELECT d.*, b.NomBanque FROM demande d LEFT JOIN banque b ON d.IdBanque = b.IdBanque WHERE d.IdMedecin = ? ORDER BY d.DateDemande DESC');
    $historique->execute([$user['IdMedecin']]);
    $liste_historique = $historique->fetchAll();
}

// Page profil
if ($page == 'profil') {
    // Les données sont déjà dans $user grâce à la requête initiale
}

// Récupérer les détails d'une demande spécifique pour le modal
if (isset($_GET['demande_id'])) {
    $demande_id = $_GET['demande_id'];
    $demande_details = $bdd->prepare('SELECT d.*, b.NomBanque, m.Nom as NomMedecin, h.Nom as NomHopital 
                                     FROM demande d 
                                     LEFT JOIN banque b ON d.IdBanque = b.IdBanque 
                                     LEFT JOIN medecin m ON d.IdMedecin = m.IdMedecin 
                                     LEFT JOIN hopital h ON m.IdHopital = h.IdHopital 
                                     WHERE d.IdDemande = ? AND d.IdMedecin = ?');
    $demande_details->execute([$demande_id, $user['IdMedecin']]);
    $details_demande = $demande_details->fetch();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Médecin - Banque de sang</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #e63946;
            --primary-blue: #1d3557;
            --secondary-blue: #457b9d;
            --light-blue: #a8dadc;
            --light-bg: #f1faee;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #1d3557 0%, #457b9d 100%);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1.5rem;
            margin: 0.2rem 0;
            border-radius: 0;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--primary-red);
        }
        
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
        }
        
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .content {
            padding: 1.5rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .section-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-approved { background: #d1ecf1; color: #0c5460; }
        .badge-completed { background: #d4edda; color: #155724; }
        .badge-rejected { background: #f8d7da; color: #721c24; }
        .badge-cancelled { background: #e9ecef; color: #495057; }
        .badge-urgent { background: #f8d7da; color: #721c24; }
        
        .blood-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            font-weight: bold;
        }
        
        .bg-A { background: #e63946 !important; }
        .bg-B { background: #457b9d !important; }
        .bg-O { background: #2a9d8f !important; }
        .bg-AB { background: #e9c46a !important; }
        
        .mobile-menu-btn {
            display: none;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.mobile-open { width: 280px; }
            .main-content { margin-left: 0; }
            .mobile-menu-btn {
                display: inline-block;
            }
        }
        
        .stock-info {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
        
        .stock-warning {
            color: #dc3545;
            font-weight: bold;
        }
        
        .stock-ok {
            color: #198754;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-tint me-2"></i><span>Banque de Sang</span>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'dashboard' ? 'active' : '' ?>" href="?page=dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'nouvelle-demande' ? 'active' : '' ?>" href="?page=nouvelle-demande">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nouvelle demande</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'mes-demandes' ? 'active' : '' ?>" href="?page=mes-demandes">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Mes demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'stocks' ? 'active' : '' ?>" href="?page=stocks">
                        <i class="fas fa-vial"></i>
                        <span>Stocks disponibles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'historique' ? 'active' : '' ?>" href="?page=historique">
                        <i class="fas fa-history"></i>
                        <span>Historique</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $page == 'profil' ? 'active' : '' ?>" href="?page=profil">
                        <i class="fas fa-user"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-primary mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0 d-none d-md-inline">
                        <?php 
                        $titles = [
                            'dashboard' => 'Tableau de bord',
                            'nouvelle-demande' => 'Nouvelle demande',
                            'mes-demandes' => 'Mes demandes',
                            'stocks' => 'Stocks disponibles',
                            'historique' => 'Historique',
                            'profil' => 'Mon profil'
                        ];
                        echo $titles[$page] ?? 'Tableau de bord';
                        ?>
                    </h4>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="user-info">
                        <div class="user-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                            <?= substr($user['NomMedecin'] ?? $user['Email'], 0, 1) ?>
                        </div>
                        <span>Dr. <?= $user['NomMedecin'] ?? 'Utilisateur' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <?php if(isset($message_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $message_success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($message_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $message_error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Dashboard -->
            <?php if($page == 'dashboard'): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-card bg-primary text-white">
                        <div class="section-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3>Bonjour, Dr. <?= $user['NomMedecin'] ?? 'Docteur' ?></h3>
                                    <p class="mb-0">Bienvenue sur votre tableau de bord. Vous pouvez gérer vos demandes de sang et consulter les stocks disponibles.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="?page=nouvelle-demande" class="btn btn-light btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>Nouvelle demande
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card" style="border-left-color: #1d3557;">
                        <div class="stats-icon" style="background: rgba(29, 53, 87, 0.1); color: #1d3557;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stats-number"><?= $resultat_requete_demande ?></div>
                        <div class="stats-label">Demandes totales</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card" style="border-left-color: #28a745;">
                        <div class="stats-icon" style="background: rgba(40, 167, 69, 0.1); color: #28a745;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-number"><?= $resultat_requete_demande_approuvees ?></div>
                        <div class="stats-label">Demandes approuvées</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card" style="border-left-color: #ffc107;">
                        <div class="stats-icon" style="background: rgba(255, 193, 7, 0.1); color: #ffc107;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-number"><?= $resultat_requete_demande_attente ?></div>
                        <div class="stats-label">En attente</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card" style="border-left-color: #e63946;">
                        <div class="stats-icon" style="background: rgba(230, 57, 70, 0.1); color: #e63946;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stats-number"><?= $resultat_requete_demande_rejetees ?></div>
                        <div class="stats-label">Rejetées</div>
                    </div>
                </div>
                <!-- Nouvelle statistique pour les demandes annulées -->
                <div class="col-md-3 mb-3">
                    <div class="stats-card" style="border-left-color: #6c757d;">
                        <div class="stats-icon" style="background: rgba(108, 117, 125, 0.1); color: #6c757d;">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="stats-number"><?= $resultat_requete_demande_annulees ?></div>
                        <div class="stats-label">Annulées</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Mes demandes récentes</h5>
                            <a href="?page=mes-demandes" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div class="section-body">
                            <?php if(count($liste_demandes_recentes) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>Groupe</th>
                                            <th>Quantité</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($liste_demandes_recentes as $demande): ?>
                                        <tr>
                                            <td>#<?= $demande['IdDemande'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($demande['DateDemande'])) ?></td>
                                            <td>
                                                <span class="blood-group bg-<?= substr($demande['GroupeSanguin'], 0, 1) ?>">
                                                    <?= $demande['GroupeSanguin'] ?>
                                                </span>
                                            </td>
                                            <td><?= $demande['Quantite'] ?></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $demande['Statut'] == 'En attente' ? 'pending' : 
                                                    ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                                    ($demande['Statut'] == 'Rejetée' ? 'rejected' : 
                                                    ($demande['Statut'] == 'Annulée' ? 'cancelled' : 'completed'))) 
                                                ?>"><?= $demande['Statut'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Aucune demande pour le moment</p>
                                <a href="?page=nouvelle-demande" class="btn btn-primary">Créer une demande</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Stocks disponibles</h5>
                        </div>
                        <div class="section-body">
                            <?php if(count($stocks_disponibles) > 0): ?>
                                <?php foreach($stocks_disponibles as $stock): ?>
                                <div class="d-flex align-items-center p-3 border rounded mb-2">
                                    <div class="blood-group bg-<?= substr($stock['GroupeSanguin'], 0, 1) ?> me-3" style="width: 50px; height: 50px;">
                                        <?= $stock['GroupeSanguin'] ?>
                                    </div>
                                    <div>
                                        <h5 class="mb-0"><?= $stock['quantite'] ?></h5>
                                        <small class="text-muted">Poches disponibles</small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-vial fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Aucun stock disponible</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Nouvelle Demande -->
            <?php if($page == 'nouvelle-demande'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Nouvelle demande de sang</h5>
                        </div>
                        <div class="section-body">
                            <form method="POST" id="demandeForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Groupe sanguin requis *</label>
                                        <select class="form-select" name="groupe_sanguin" id="groupe_sanguin" required onchange="updateStockInfo()">
                                            <option value="">Sélectionner</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                        <div id="stockInfo" class="stock-info"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantité (poches) *</label>
                                        <input type="number" class="form-control" name="quantite" id="quantite" min="1" max="10" required onchange="validateQuantity()">
                                        <div id="quantityError" class="text-danger mt-1" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Niveau d'urgence</label>
                                    <select class="form-select" name="urgence">
                                        <option value="Normale">Normale</option>
                                        <option value="Urgente">Urgente</option>
                                        <option value="Très urgente">Très urgente</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notes supplémentaires</label>
                                    <textarea class="form-control" name="notes" rows="3" placeholder="Informations complémentaires..."></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="nouvelle_demande" class="btn btn-primary btn-lg" id="submitBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Soumettre la demande
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Mes Demandes -->
            <?php if($page == 'mes-demandes'): ?>
            <div class="section-card">
                <div class="section-header">
                    <h5 class="section-title">Mes demandes de sang</h5>
                </div>
                <div class="section-body">
                    <?php if(count($liste_mes_demandes) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Groupe</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Banque</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_mes_demandes as $demande): ?>
                                <tr>
                                    <td>#<?= $demande['IdDemande'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($demande['DateDemande'])) ?></td>
                                    <td>
                                        <span class="blood-group bg-<?= substr($demande['GroupeSanguin'], 0, 1) ?>">
                                            <?= $demande['GroupeSanguin'] ?>
                                        </span>
                                    </td>
                                    <td><?= $demande['Quantite'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $demande['Statut'] == 'En attente' ? 'pending' : 
                                            ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                            ($demande['Statut'] == 'Rejetée' ? 'rejected' : 
                                            ($demande['Statut'] == 'Annulée' ? 'cancelled' : 'completed'))) 
                                        ?>"><?= $demande['Statut'] ?></span>
                                    </td>
                                    <td><?= $demande['NomBanque'] ?? 'Non assignée' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailsModal" onclick="loadDemandeDetails(<?= $demande['IdDemande'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if($demande['Statut'] == 'En attente'): ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                                            <input type="hidden" name="id_demande" value="<?= $demande['IdDemande'] ?>">
                                            <button type="submit" name="annuler_demande" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Annuler">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune demande</h5>
                        <p class="text-muted">Vous n'avez pas encore créé de demande de sang.</p>
                        <a href="?page=nouvelle-demande" class="btn btn-primary">Créer une demande</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Stocks -->
            <?php if($page == 'stocks'): ?>
            <div class="row">
                <?php if(count($liste_stocks) > 0): ?>
                    <?php foreach($liste_stocks as $stock): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="section-card text-center">
                            <div class="section-body">
                                <div class="blood-group bg-<?= substr($stock['GroupeSanguin'], 0, 1) ?> mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                    <?= $stock['GroupeSanguin'] ?>
                                </div>
                                <h3><?= $stock['quantite'] ?></h3>
                                <p class="text-muted">Poches disponibles</p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-body text-center py-5">
                                <i class="fas fa-vial fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun stock disponible</h5>
                                <p class="text-muted">Les stocks seront bientôt mis à jour.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if(count($liste_stocks_banque) > 0): ?>
            <div class="section-card">
                <div class="section-header">
                    <h5 class="section-title">Détails des stocks par banque</h5>
                </div>
                <div class="section-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Banque</th>
                                    <th>Groupe sanguin</th>
                                    <th>Quantité disponible</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_stocks_banque as $stock): ?>
                                <tr>
                                    <td><?= $stock['NomBanque'] ?? 'Non spécifiée' ?></td>
                                    <td>
                                        <span class="blood-group bg-<?= substr($stock['GroupeSanguin'], 0, 1) ?>">
                                            <?= $stock['GroupeSanguin'] ?>
                                        </span>
                                    </td>
                                    <td><?= $stock['quantite'] ?> poches</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Page Historique -->
            <?php if($page == 'historique'): ?>
            <div class="section-card">
                <div class="section-header">
                    <h5 class="section-title">Historique des demandes</h5>
                </div>
                <div class="section-body">
                    <?php if(count($liste_historique) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Groupe</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Banque</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_historique as $demande): ?>
                                <tr>
                                    <td>#<?= $demande['IdDemande'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($demande['DateDemande'])) ?></td>
                                    <td>
                                        <span class="blood-group bg-<?= substr($demande['GroupeSanguin'], 0, 1) ?>">
                                            <?= $demande['GroupeSanguin'] ?>
                                        </span>
                                    </td>
                                    <td><?= $demande['Quantite'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $demande['Statut'] == 'En attente' ? 'pending' : 
                                            ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                            ($demande['Statut'] == 'Rejetée' ? 'rejected' : 
                                            ($demande['Statut'] == 'Annulée' ? 'cancelled' : 'completed'))) 
                                        ?>"><?= $demande['Statut'] ?></span>
                                    </td>
                                    <td><?= $demande['NomBanque'] ?? 'Non assignée' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucun historique</h5>
                        <p class="text-muted">Aucune demande dans votre historique.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Profil -->
            <?php if($page == 'profil'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Mon profil</h5>
                        </div>
                        <div class="section-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nom complet *</label>
                                        <input type="text" class="form-control" name="nom" value="<?= $user['NomMedecin'] ?? '' ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" value="<?= $user['EmailMedecin'] ?? $user['Email'] ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact</label>
                                        <input type="text" class="form-control" name="contact" value="<?= $user['ContactMedecin'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Spécialité</label>
                                        <input type="text" class="form-control" name="specialite" value="<?= $user['Specialite'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hôpital</label>
                                    <input type="text" class="form-control" value="<?= $user['NomHopital'] ?? 'Non assigné' ?>" readonly>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="modifier_profil" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour les détails de la demande -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Détails de la demande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Les détails seront chargés ici via JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Fonction pour mettre à jour les informations de stock
        function updateStockInfo() {
            var groupeSanguin = document.getElementById('groupe_sanguin').value;
            var stockInfo = document.getElementById('stockInfo');
            
            if (groupeSanguin) {
                // Faire une requête AJAX pour récupérer le stock disponible
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_stock.php?groupe=' + groupeSanguin, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var stock = JSON.parse(xhr.responseText);
                        if (stock.quantite > 0) {
                            stockInfo.innerHTML = '<span class="stock-ok">Stock disponible: ' + stock.quantite + ' poche(s)</span>';
                        } else {
                            stockInfo.innerHTML = '<span class="stock-warning">Stock épuisé pour ce groupe sanguin</span>';
                        }
                        validateQuantity(); // Valider la quantité après avoir récupéré le stock
                    }
                };
                xhr.send();
            } else {
                stockInfo.innerHTML = '';
            }
        }

        // Fonction pour valider la quantité par rapport au stock
        function validateQuantity() {
            var quantite = document.getElementById('quantite').value;
            var groupeSanguin = document.getElementById('groupe_sanguin').value;
            var quantityError = document.getElementById('quantityError');
            var submitBtn = document.getElementById('submitBtn');
            
            if (groupeSanguin && quantite) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_stock.php?groupe=' + groupeSanguin, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var stock = JSON.parse(xhr.responseText);
                        if (parseInt(quantite) > stock.quantite) {
                            quantityError.textContent = 'Quantité demandée (' + quantite + ') supérieure au stock disponible (' + stock.quantite + ')';
                            quantityError.style.display = 'block';
                            submitBtn.disabled = true;
                        } else {
                            quantityError.style.display = 'none';
                            submitBtn.disabled = false;
                        }
                    }
                };
                xhr.send();
            }
        }

        // Fonction pour charger les détails d'une demande
        function loadDemandeDetails(demandeId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '?page=mes-demandes&demande_id=' + demandeId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Recharger la page pour afficher les détails dans le modal
                    location.href = '?page=mes-demandes&demande_id=' + demandeId;
                }
            };
            xhr.send();
        }

        // Afficher les détails de la demande si un ID est passé en paramètre
        <?php if (isset($_GET['demande_id']) && $details_demande): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var detailsContent = document.getElementById('detailsContent');
            if (detailsContent) {
                detailsContent.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID Demande:</strong> #<?= $details_demande['IdDemande'] ?></p>
                            <p><strong>Date:</strong> <?= date('d/m/Y', strtotime($details_demande['DateDemande'])) ?></p>
                            <p><strong>Groupe sanguin:</strong> 
                                <span class="blood-group bg-<?= substr($details_demande['GroupeSanguin'], 0, 1) ?>">
                                    <?= $details_demande['GroupeSanguin'] ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Quantité:</strong> <?= $details_demande['Quantite'] ?> poche(s)</p>
                            <p><strong>Statut:</strong> 
                                <span class="badge badge-<?= 
                                    $details_demande['Statut'] == 'En attente' ? 'pending' : 
                                    ($details_demande['Statut'] == 'Approuvée' ? 'approved' : 
                                    ($details_demande['Statut'] == 'Rejetée' ? 'rejected' : 
                                    ($details_demande['Statut'] == 'Annulée' ? 'cancelled' : 'completed'))) 
                                ?>"><?= $details_demande['Statut'] ?></span>
                            </p>
                            <p><strong>Banque assignée:</strong> <?= $details_demande['NomBanque'] ?? 'Non assignée' ?></p>
                        </div>
                    </div>
                    <?php if ($details_demande['NomHopital']): ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Hôpital:</strong> <?= $details_demande['NomHopital'] ?></p>
                            <p><strong>Médecin demandeur:</strong> Dr. <?= $details_demande['NomMedecin'] ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                `;
                
                var detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
                detailsModal.show();
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>