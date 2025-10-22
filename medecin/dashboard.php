<?php
session_start();
include('../includes/config.php');
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
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
    <!-- Le reste de votre code HTML... -->

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
            --gradient-dark: linear-gradient(135deg, #1d3557 0%, #457b9d 100%);
            --gradient-light: linear-gradient(135deg, #457b9d 0%, #a8dadc 100%);
            --gradient-red: linear-gradient(135deg, #e63946 0%, #f28482 100%);
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            background: var(--gradient-dark);
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
        
        .sidebar-brand i {
            color: var(--primary-red);
        }
        
        .sidebar-menu {
            padding: 1rem 0;
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
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
        }
        
        /* Topbar */
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .topbar-search {
            max-width: 400px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gradient-red);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Content Area */
        .content {
            padding: 1.5rem;
        }
        
        /* Stats Cards */
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
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stats-card.primary {
            border-left-color: var(--primary-blue);
        }
        
        .stats-card.success {
            border-left-color: #28a745;
        }
        
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        
        .stats-card.danger {
            border-left-color: var(--primary-red);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stats-card.primary .stats-icon {
            background: rgba(29, 53, 87, 0.1);
            color: var(--primary-blue);
        }
        
        .stats-card.success .stats-icon {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .stats-card.warning .stats-icon {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .stats-card.danger .stats-icon {
            background: rgba(230, 57, 70, 0.1);
            color: var(--primary-red);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Section Cards */
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
            justify-content: between;
            align-items: center;
        }
        
        .section-title {
            font-weight: 600;
            color: var(--primary-blue);
            margin: 0;
        }
        
        .section-body {
            padding: 1.5rem;
        }
        
        /* Tables */
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--primary-blue);
        }
        
        .table-responsive {
            border-radius: 10px;
        }
        
        /* Badges */
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-approved {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary-red);
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #c1121f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Quick Actions */
        .quick-action {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .quick-action:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .quick-action-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1rem;
            color: white;
        }
        
        /* Blood Group Indicators */
        .blood-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            font-weight: bold;
            margin-right: 10px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                text-align: center;
            }
            
            .sidebar-brand span, .nav-link span {
                display: none;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 80px;
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.mobile-open {
                width: 280px;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }
        
        /* Toggle button for mobile */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-blue);
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
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
                    <a class="nav-link active" href="#">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nouvelle demande</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Mes demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-vial"></i>
                        <span>Stocks disponibles</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-history"></i>
                        <span>Historique</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-user"></i>
                        <span>Mon profil</span>
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="logout.php">
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
                    <button class="mobile-menu-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0 d-none d-md-inline">Tableau de bord Médecin</h4>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="input-group topbar-search me-3">
                        <input type="text" class="form-control" placeholder="Rechercher...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <div class="user-avatar">
                                JM
                            </div>
                            <span>Dr. Jean Martin</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Welcome Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="section-card bg-primary text-white" data-aos="fade-up">
                        <div class="section-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3>Bonjour, Dr. Jean Martin</h3>
                                    <p class="mb-0">Bienvenue sur votre tableau de bord. Vous pouvez gérer vos demandes de sang et consulter les stocks disponibles.</p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-light btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i>Nouvelle demande
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card primary" data-aos="fade-up">
                        <div class="stats-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stats-number">12</div>
                        <div class="stats-label">Demandes ce mois</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card success" data-aos="fade-up" data-aos-delay="100">
                        <div class="stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-number">9</div>
                        <div class="stats-label">Demandes approuvées</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card warning" data-aos="fade-up" data-aos-delay="200">
                        <div class="stats-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-number">2</div>
                        <div class="stats-label">En attente</div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card danger" data-aos="fade-up" data-aos-delay="300">
                        <div class="stats-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stats-number">1</div>
                        <div class="stats-label">Rejetées</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions and Recent Activity -->
            <div class="row">
                <!-- Quick Actions -->
                <div class="col-lg-4 mb-4">
                    <div class="section-card" data-aos="fade-up">
                        <div class="section-header">
                            <h5 class="section-title">Actions rapides</h5>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="quick-action">
                                        <div class="quick-action-icon bg-primary">
                                            <i class="fas fa-plus"></i>
                                        </div>
                                        <h6>Nouvelle demande</h6>
                                        <p class="small text-muted">Créer une nouvelle demande de sang</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="quick-action">
                                        <div class="quick-action-icon bg-success">
                                            <i class="fas fa-search"></i>
                                        </div>
                                        <h6>Recherche</h6>
                                        <p class="small text-muted">Rechercher des poches disponibles</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="quick-action">
                                        <div class="quick-action-icon bg-warning">
                                            <i class="fas fa-history"></i>
                                        </div>
                                        <h6>Historique</h6>
                                        <p class="small text-muted">Voir l'historique des demandes</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="quick-action">
                                        <div class="quick-action-icon bg-info">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <h6>Statistiques</h6>
                                        <p class="small text-muted">Consulter vos statistiques</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="col-lg-8 mb-4">
                    <div class="section-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="section-header">
                            <h5 class="section-title">Mes demandes récentes</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Voir tout</a>
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
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#2456</td>
                                            <td>01/10/2023</td>
                                            <td><span class="blood-group bg-danger">A+</span></td>
                                            <td>2</td>
                                            <td><span class="badge badge-pending">En attente</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Détails</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#2452</td>
                                            <td>28/09/2023</td>
                                            <td><span class="blood-group bg-primary">B+</span></td>
                                            <td>1</td>
                                            <td><span class="badge badge-approved">Approuvée</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Détails</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#2448</td>
                                            <td>25/09/2023</td>
                                            <td><span class="blood-group bg-success">O+</span></td>
                                            <td>3</td>
                                            <td><span class="badge badge-completed">Complétée</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Détails</button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>#2445</td>
                                            <td>22/09/2023</td>
                                            <td><span class="blood-group bg-warning">AB+</span></td>
                                            <td>2</td>
                                            <td><span class="badge badge-rejected">Rejetée</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Détails</button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Availability -->
            <div class="row">
                <div class="col-12">
                    <div class="section-card" data-aos="fade-up">
                        <div class="section-header">
                            <h5 class="section-title">Stocks disponibles près de vous</h5>
                            <a href="#" class="btn btn-sm btn-outline-primary">Voir tout</a>
                        </div>
                        <div class="section-body">
                            <div class="row">
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="d-flex align-items-center p-3 border rounded">
                                        <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <span class="text-white fw-bold">A+</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">42</h5>
                                            <small class="text-muted">Poches disponibles</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="d-flex align-items-center p-3 border rounded">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <span class="text-white fw-bold">B+</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">28</h5>
                                            <small class="text-muted">Poches disponibles</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="d-flex align-items-center p-3 border rounded">
                                        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <span class="text-white fw-bold">O+</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">67</h5>
                                            <small class="text-muted">Poches disponibles</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3 mb-3">
                                    <div class="d-flex align-items-center p-3 border rounded">
                                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <span class="text-white fw-bold">AB+</span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">15</h5>
                                            <small class="text-muted">Poches disponibles</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Ces stocks sont disponibles dans les banques de sang à moins de 20km de votre position.
                                </div>
                            </div>
                        </div>
                    </div>
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
    </script>
</body>
</html>