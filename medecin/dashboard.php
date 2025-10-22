<?php
session_start();
include('../includes/config.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$query_user = $bdd->prepare("SELECT * FROM users WHERE IdUsers = ?");
$query_user->execute([$user_id]);
$user = $query_user->fetch();

// Déterminer la page active
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nouvelle_demande'])) {
        // Nouvelle demande de sang
        $groupe_sanguin = $_POST['groupe_sanguin'];
        $quantite = $_POST['quantite'];
        $urgence = $_POST['urgence'];
        
        $insert_demande = $bdd->prepare("INSERT INTO demande (DateDemande, GroupeSanguin, Quantite, Statut, IdMedecin) VALUES (CURDATE(), ?, ?, 'En attente', ?)");
        $insert_demande->execute([$groupe_sanguin, $quantite, $user_id]);
        $message_success = "Demande créée avec succès!";
    }
    
    if (isset($_POST['modifier_profil'])) {
        // Modification du profil
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $specialite = $_POST['specialite'];
        
        $update_profil = $bdd->prepare("UPDATE medecin SET Nom = ?, email = ?, Contact = ?, Specialite = ? WHERE IdMedecin = ?");
        $update_profil->execute([$nom, $email, $contact, $specialite, $user['IdMedecin']]);
        $message_success = "Profil mis à jour avec succès!";
    }
}

// Statistiques pour le dashboard
if ($page == 'dashboard') {
    // Nombre total de demandes
    $requete_demande = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ?');
    $requete_demande->execute([$user_id]);
    $resultat_requete_demande = $requete_demande->fetch()['total'];

    // Demandes approuvées
    $requete_demande_approuvees = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "Approuvée"');
    $requete_demande_approuvees->execute([$user_id]);
    $resultat_requete_demande_approuvees = $requete_demande_approuvees->fetch()['total'];

    // Demandes en attente
    $requete_demande_attente = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "En attente"');
    $requete_demande_attente->execute([$user_id]);
    $resultat_requete_demande_attente = $requete_demande_attente->fetch()['total'];

    // Demandes rejetées
    $requete_demande_rejetees = $bdd->prepare('SELECT COUNT(*) as total FROM demande WHERE IdMedecin = ? AND Statut = "Rejetée"');
    $requete_demande_rejetees->execute([$user_id]);
    $resultat_requete_demande_rejetees = $requete_demande_rejetees->fetch()['total'];

    // Demandes récentes
    $demandes_recentes = $bdd->prepare('SELECT * FROM demande WHERE IdMedecin = ? ORDER BY DateDemande DESC LIMIT 5');
    $demandes_recentes->execute([$user_id]);
    $liste_demandes_recentes = $demandes_recentes->fetchAll();

    // Stocks disponibles
    $stocks = $bdd->query('SELECT GroupeSanguin, COUNT(*) as quantite FROM poche WHERE DatePeremption > CURDATE() GROUP BY GroupeSanguin');
    $stocks_disponibles = $stocks->fetchAll();
}

// Page mes demandes
if ($page == 'mes-demandes') {
    $mes_demandes = $bdd->prepare('SELECT * FROM demande WHERE IdMedecin = ? ORDER BY DateDemande DESC');
    $mes_demandes->execute([$user_id]);
    $liste_mes_demandes = $mes_demandes->fetchAll();
}

// Page stocks disponibles
if ($page == 'stocks') {
    $stocks_detaille = $bdd->query('SELECT GroupeSanguin, COUNT(*) as quantite FROM poche WHERE DatePeremption > CURDATE() GROUP BY GroupeSanguin');
    $liste_stocks = $stocks_detaille->fetchAll();
}

// Page historique
if ($page == 'historique') {
    $historique = $bdd->prepare('SELECT * FROM demande WHERE IdMedecin = ? ORDER BY DateDemande DESC');
    $historique->execute([$user_id]);
    $liste_historique = $historique->fetchAll();
}

// Page profil
if ($page == 'profil') {
    $info_medecin = $bdd->prepare('SELECT m.*, h.Nom as NomHopital FROM medecin m LEFT JOIN hopital h ON m.IdHopital = h.IdHopital WHERE m.IdMedecin = ?');
    $info_medecin->execute([$user['IdMedecin']]);
    $medecin = $info_medecin->fetch();
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
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            .sidebar.mobile-open { width: 280px; }
            .main-content { margin-left: 0; }
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
                            <?= substr($user['Email'] ?? 'M', 0, 1) ?>
                        </div>
                        <span><?= $medecin['Nom'] ?? 'Dr. Utilisateur' ?></span>
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

            <!-- Page Dashboard -->
            <?php if($page == 'dashboard'): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-card bg-primary text-white">
                        <div class="section-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3>Bonjour, <?= $medecin['Nom'] ?? 'Docteur' ?></h3>
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
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Mes demandes récentes</h5>
                            <a href="?page=mes-demandes" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div class="section-body">
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
                                            <td><span class="blood-group bg-danger"><?= $demande['GroupeSanguin'] ?></span></td>
                                            <td><?= $demande['Quantite'] ?></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $demande['Statut'] == 'En attente' ? 'pending' : 
                                                    ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                                    ($demande['Statut'] == 'Rejetée' ? 'rejected' : 'completed')) 
                                                ?>"><?= $demande['Statut'] ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <h5 class="section-title">Stocks disponibles</h5>
                        </div>
                        <div class="section-body">
                            <?php foreach($stocks_disponibles as $stock): ?>
                            <div class="d-flex align-items-center p-3 border rounded mb-2">
                                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <span class="text-white fw-bold"><?= $stock['GroupeSanguin'] ?></span>
                                </div>
                                <div>
                                    <h5 class="mb-0"><?= $stock['quantite'] ?></h5>
                                    <small class="text-muted">Poches disponibles</small>
                                </div>
                            </div>
                            <?php endforeach; ?>
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
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Groupe sanguin requis *</label>
                                        <select class="form-select" name="groupe_sanguin" required>
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
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantité (poches) *</label>
                                        <input type="number" class="form-control" name="quantite" min="1" max="10" required>
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
                                    <button type="submit" name="nouvelle_demande" class="btn btn-primary btn-lg">
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Groupe</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($liste_mes_demandes as $demande): ?>
                                <tr>
                                    <td>#<?= $demande['IdDemande'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($demande['DateDemande'])) ?></td>
                                    <td><span class="blood-group bg-danger"><?= $demande['GroupeSanguin'] ?></span></td>
                                    <td><?= $demande['Quantite'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $demande['Statut'] == 'En attente' ? 'pending' : 
                                            ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                            ($demande['Statut'] == 'Rejetée' ? 'rejected' : 'completed')) 
                                        ?>"><?= $demande['Statut'] ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Détails
                                        </button>
                                        <?php if($demande['Statut'] == 'En attente'): ?>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Stocks -->
            <?php if($page == 'stocks'): ?>
            <div class="row">
                <?php foreach($liste_stocks as $stock): ?>
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="section-card text-center">
                        <div class="section-body">
                            <div class="blood-group bg-danger mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                <?= $stock['GroupeSanguin'] ?>
                            </div>
                            <h3><?= $stock['quantite'] ?></h3>
                            <p class="text-muted">Poches disponibles</p>
                            <button class="btn btn-outline-primary btn-sm">Voir détails</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h5 class="section-title">Détails des stocks par banque</h5>
                </div>
                <div class="section-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Les stocks sont mis à jour en temps réel. Contactez la banque pour plus d'informations.
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Page Historique -->
            <?php if($page == 'historique'): ?>
            <div class="section-card">
                <div class="section-header">
                    <h5 class="section-title">Historique des demandes</h5>
                </div>
                <div class="section-body">
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
                                    <td><span class="blood-group bg-danger"><?= $demande['GroupeSanguin'] ?></span></td>
                                    <td><?= $demande['Quantite'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $demande['Statut'] == 'En attente' ? 'pending' : 
                                            ($demande['Statut'] == 'Approuvée' ? 'approved' : 
                                            ($demande['Statut'] == 'Rejetée' ? 'rejected' : 'completed')) 
                                        ?>"><?= $demande['Statut'] ?></span>
                                    </td>
                                    <td><?= $demande['IdBanque'] ? 'Banque #'.$demande['IdBanque'] : 'Non assignée' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
                                        <input type="text" class="form-control" name="nom" value="<?= $medecin['Nom'] ?? '' ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="email" value="<?= $medecin['email'] ?? '' ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Contact</label>
                                        <input type="text" class="form-control" name="contact" value="<?= $medecin['Contact'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Spécialité</label>
                                        <input type="text" class="form-control" name="specialite" value="<?= $medecin['Specialite'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Hôpital</label>
                                    <input type="text" class="form-control" value="<?= $medecin['NomHopital'] ?? 'Non assigné' ?>" readonly>
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
    </script>
</body>
</html>