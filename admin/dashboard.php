<?php
    session_start();
    include('../includes/config.php');
    if (isset($_SESSION['user_id'])) {
        //nombre de banque de sang
        $requete_banque=$bdd->prepare('SELECT * FROM `banque`;');
        $requete_banque->execute();
        $resultat_requete_banque=$requete_banque->rowCount();

        // Première requête médecin
        $requete_medecin1=$bdd->prepare('SELECT * FROM `medecin` ;');
        $requete_medecin1->execute();
        $resultat_requete_medecin=$requete_medecin1->rowCount();
        $requete_medecin1->closeCursor(); // Fermer le curseur

        $requete_demande=$bdd->prepare('SELECT * FROM `demande`;');
        $requete_demande->execute();
        $resultat_requete_demande=$requete_demande->rowCount();

        $requete_poche=$bdd->prepare('SELECT * FROM `poche`;');
        $requete_poche->execute();
        $resultat_requete_poche=$requete_poche->rowCount();

        // Récupérer les hôpitaux pour le formulaire médecin
        $requete_hopitaux = $bdd->prepare('SELECT * FROM hopital');
        $requete_hopitaux->execute();
        $hopitaux = $requete_hopitaux->fetchAll(PDO::FETCH_ASSOC);
        $requete_hopitaux->closeCursor();

        // Récupérer les caves pour le formulaire stock
        $requete_caves = $bdd->prepare('SELECT * FROM cave');
        $requete_caves->execute();
        $caves = $requete_caves->fetchAll(PDO::FETCH_ASSOC);
        $requete_caves->closeCursor();

        // Traitement de l'ajout de banque
        if (isset($_POST['ajouter_banque'])) {
            $nom_banque = $_POST['nom_banque'];
            $adresse = $_POST['adresse'];
            $responsable = $_POST['responsable'];
            $stock_total = $_POST['stock_total'];

            //Vérification de la banque de sang
            $requete_verification_banque=$bdd->prepare('SELECT * FROM `banque` WHERE NomBanque=?;');
            $requete_verification_banque->execute(array($nom_banque));
            if ($requete_verification_banque->rowCount()>0) {
                $message_erreur="La banque existe déjà";
            }else {
                $sql = "INSERT INTO `banque`( `NomBanque`, `Adresse`, `Responsable`, `StockTotal`) VALUES  (?, ?, ?, ?)";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$nom_banque, $adresse, $responsable, $stock_total]);
                    
                $message_success = "Banque de sang ajoutée avec succès!";
            }
        }

        // Traitement de la modification de banque
        if (isset($_POST['modifier_banque'])) {
            $id_banque = $_POST['id_banque'];
            $nom_banque = $_POST['nom_banque'];
            $adresse = $_POST['adresse'];
            $responsable = $_POST['responsable'];
            $stock_total = $_POST['stock_total'];

            $sql = "UPDATE `banque` SET `NomBanque`=?, `Adresse`=?, `Responsable`=?, `StockTotal`=? WHERE `IdBanque`=?";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([$nom_banque, $adresse, $responsable, $stock_total, $id_banque]);
                
            $message_success = "Banque de sang modifiée avec succès!";
        }

        // Traitement de la suppression de banque
        if (isset($_GET['supprimer_banque'])) {
            $id_banque = $_GET['supprimer_banque'];
            
            // Vérifier s'il y a des caves associées
            $requete_caves_associees = $bdd->prepare('SELECT COUNT(*) FROM cave WHERE Idbanque = ?');
            $requete_caves_associees->execute([$id_banque]);
            $nb_caves = $requete_caves_associees->fetchColumn();
            
            if ($nb_caves > 0) {
                $message_erreur = "Impossible de supprimer cette banque car elle contient des caves. Supprimez d'abord les caves associées.";
            } else {
                $sql = "DELETE FROM `banque` WHERE `IdBanque`=?";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$id_banque]);
                    
                $message_success = "Banque de sang supprimée avec succès!";
            }
        }

        // recuperation des information de la table banque
        $requete_banque2=$bdd->prepare('SELECT * FROM `banque`;');
        $requete_banque2->execute();
        $resultats_requete_banque=$requete_banque2->fetchAll(PDO::FETCH_ASSOC);
        $requete_banque2->closeCursor();

        // Traitement de l'ajout de médecin
        if (isset($_POST['ajouter_medecin'])) {
            $nom = $_POST['nom_medecin'];
            $specialite = $_POST['specialite'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $id_hopital = $_POST['id_hopital'];

            try {
                // D'abord créer l'utilisateur
                $sql_user = "INSERT INTO users (Email, MotDePasse, Role, DateCreation) VALUES (?, ?, 'medecin', NOW())";
                $stmt_user = $bdd->prepare($sql_user);
                $stmt_user->execute([$email, $mot_de_passe]);
                $id_user = $bdd->lastInsertId();

                // Puis créer le médecin
                $sql_medecin = "INSERT INTO medecin (IdHopital, Nom, email, Specialite, Contact) VALUES (?, ?, ?, ?, ?)";
                $stmt_medecin = $bdd->prepare($sql_medecin);
                $stmt_medecin->execute([$id_hopital, $nom, $email, $specialite, $contact]);
                
                $message_success = "Médecin ajouté avec succès!";

                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                $message_erreur = "Erreur lors de l'ajout du médecin: " . $e->getMessage();
            }
        }

        // Traitement de la modification de médecin
        if (isset($_POST['modifier_medecin'])) {
            $id_medecin = $_POST['id_medecin'];
            $nom = $_POST['nom_medecin'];
            $specialite = $_POST['specialite'];
            $contact = $_POST['contact'];
            $email = $_POST['email'];
            $id_hopital = $_POST['id_hopital'];

            try {
                $sql_medecin = "UPDATE medecin SET IdHopital=?, Nom=?, email=?, Specialite=?, Contact=? WHERE IdMedecin=?";
                $stmt_medecin = $bdd->prepare($sql_medecin);
                $stmt_medecin->execute([$id_hopital, $nom, $email, $specialite, $contact, $id_medecin]);
                
                $message_success = "Médecin modifié avec succès!";
            } catch (PDOException $e) {
                $message_erreur = "Erreur lors de la modification du médecin: " . $e->getMessage();
            }
        }

        // Traitement de la suppression de médecin
        if (isset($_GET['supprimer_medecin'])) {
            $id_medecin = $_GET['supprimer_medecin'];
            
            try {
                // Commencer une transaction
                $bdd->beginTransaction();
                
                // Supprimer d'abord l'utilisateur associé
                $sql_user = "DELETE FROM users WHERE IdMedecin = ?";
                $stmt_user = $bdd->prepare($sql_user);
                $stmt_user->execute([$id_medecin]);
                
                // Puis supprimer le médecin
                $sql_medecin = "DELETE FROM medecin WHERE IdMedecin = ?";
                $stmt_medecin = $bdd->prepare($sql_medecin);
                $stmt_medecin->execute([$id_medecin]);
                
                $bdd->commit();
                $message_success = "Médecin supprimé avec succès!";
            } catch (PDOException $e) {
                $bdd->rollBack();
                $message_erreur = "Erreur lors de la suppression du médecin: " . $e->getMessage();
            }
        }

        // recuperation des information de la table medecin
        $requete_medecin_details=$bdd->prepare('SELECT IdMedecin,medecin.Nom as 
                                                nomMedecin,email,Specialite,medecin.Contact 
                                                contactMedecin,hopital.Nom as nomHopital, medecin.IdHopital
                                                FROM `medecin`,`hopital` WHERE 
                                                hopital.IdHopital=medecin.IdHopital;');
        $requete_medecin_details->execute();
        $resultatss_requete_medecin = $requete_medecin_details->fetchAll(PDO::FETCH_ASSOC);
        $requete_medecin_details->closeCursor();

        // Traitement de l'ajout de stock
        if (isset($_POST['ajouter_stock'])) {
            $volume = $_POST['volume'];
            $groupe_sanguin = $_POST['groupe_sanguin'];
            $date_collecte = $_POST['date_collecte'];
            $date_peremption = $_POST['date_peremption'];
            $id_cave = $_POST['id_cave'];

            try {
                $sql = "INSERT INTO poche (Volume, GroupeSanguin, DateCollecte, DatePeremption, IdCave) VALUES (?, ?, ?, ?, ?)";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$volume, $groupe_sanguin, $date_collecte, $date_peremption, $id_cave]);
                
                $message_success = "Stock ajouté avec succès!";
                header("Location: dashboard.php");
                exit();
            } catch (PDOException $e) {
                $message_erreur = "Erreur lors de l'ajout du stock: " . $e->getMessage();
            }
        }

        // Traitement de la modification de stock
        if (isset($_POST['modifier_stock'])) {
            $id_poche = $_POST['id_poche'];
            $volume = $_POST['volume'];
            $groupe_sanguin = $_POST['groupe_sanguin'];
            $date_collecte = $_POST['date_collecte'];
            $date_peremption = $_POST['date_peremption'];
            $id_cave = $_POST['id_cave'];

            try {
                $sql = "UPDATE poche SET Volume=?, GroupeSanguin=?, DateCollecte=?, DatePeremption=?, IdCave=? WHERE IdPoche=?";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$volume, $groupe_sanguin, $date_collecte, $date_peremption, $id_cave, $id_poche]);
                
                $message_success = "Stock modifié avec succès!";
            } catch (PDOException $e) {
                $message_erreur = "Erreur lors de la modification du stock: " . $e->getMessage();
            }
        }

        // Traitement de la suppression de stock
        if (isset($_GET['supprimer_stock'])) {
            $id_poche = $_GET['supprimer_stock'];

            try {
                $sql = "DELETE FROM poche WHERE IdPoche=?";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$id_poche]);
                
                $message_success = "Stock supprimé avec succès!";
            } catch (PDOException $e) {
                $message_erreur = "Erreur lors de la suppression du stock: " . $e->getMessage();
            }
        }

        // Récupération des informations de la table poche pour l'affichage
        $requete_poche_details = $bdd->prepare('SELECT p.*, c.NomCave, b.NomBanque 
                                               FROM poche p 
                                               LEFT JOIN cave c ON p.IdCave = c.IdCave 
                                               LEFT JOIN banque b ON c.Idbanque = b.IdBanque');
        $requete_poche_details->execute();
        $resultats_requete_poche = $requete_poche_details->fetchAll(PDO::FETCH_ASSOC);
        $requete_poche_details->closeCursor();

        //Nombre de medecin actifs
        $requete_medecin_actifs=$bdd->prepare('SELECT COUNT(*) as count FROM `medecin`');
        $requete_medecin_actifs->execute();
        $result_medecin_actifs = $requete_medecin_actifs->fetch(PDO::FETCH_ASSOC);
        $resultat_requete_medecin_actif = $result_medecin_actifs['count'];
        $requete_medecin_actifs->closeCursor();

        //Nombre de spécialités
        $requete_specialites=$bdd->prepare('SELECT COUNT(DISTINCT Specialite) as count FROM `medecin`');
        $requete_specialites->execute();
        $result_specialites = $requete_specialites->fetch(PDO::FETCH_ASSOC);
        $resultat_requete_Specialites = $result_specialites['count'];
        $requete_specialites->closeCursor();

        //Nombre d'hôpitaux partenaires
        $requete_hopitaux_partenaires=$bdd->prepare('SELECT COUNT(*) as count FROM `hopital`');
        $requete_hopitaux_partenaires->execute();
        $result_hopitaux = $requete_hopitaux_partenaires->fetch(PDO::FETCH_ASSOC);
        $resultat_requete_hopitaux_partenaires = $result_hopitaux['count'];
        $requete_hopitaux_partenaires->closeCursor();

        // Récupérer une banque spécifique pour modification
        if (isset($_GET['modifier_banque_id'])) {
            $id_banque = $_GET['modifier_banque_id'];
            $requete_banque_single = $bdd->prepare('SELECT * FROM banque WHERE IdBanque = ?');
            $requete_banque_single->execute([$id_banque]);
            $banque_a_modifier = $requete_banque_single->fetch(PDO::FETCH_ASSOC);
            $requete_banque_single->closeCursor();
        }

        // Récupérer un médecin spécifique pour modification
        if (isset($_GET['modifier_medecin_id'])) {
            $id_medecin = $_GET['modifier_medecin_id'];
            $requete_medecin_single = $bdd->prepare('SELECT * FROM medecin WHERE IdMedecin = ?');
            $requete_medecin_single->execute([$id_medecin]);
            $medecin_a_modifier = $requete_medecin_single->fetch(PDO::FETCH_ASSOC);
            $requete_medecin_single->closeCursor();
        }

        // Récupérer un stock spécifique pour modification
        if (isset($_GET['modifier_stock_id'])) {
            $id_poche = $_GET['modifier_stock_id'];
            $requete_stock_single = $bdd->prepare('SELECT * FROM poche WHERE IdPoche = ?');
            $requete_stock_single->execute([$id_poche]);
            $stock_a_modifier = $requete_stock_single->fetch(PDO::FETCH_ASSOC);
            $requete_stock_single->closeCursor();
        }

    } else {
        header("Location: ../login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Admin - Banque de Sang</title>
    
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
            cursor: pointer;
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
        
        /* Section Content */
        .section-content {
            display: none;
        }
        
        .section-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
        
        .stats-card.info {
            border-left-color: #17a2b8;
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
        
        .stats-card.info .stats-icon {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
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
            justify-content: space-between;
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
        
        .badge-active {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-inactive {
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
        
        /* Chart Container */
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
        }
        
        /* Form Styles */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(29, 53, 87, 0.25);
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
        
        /* Progress Bars */
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        
        /* Action Buttons */
        .action-btn {
            padding: 0.25rem 0.5rem;
            margin: 0 0.1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.05);
        }
        
        /* Alert Messages */
        .alert-message {
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
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
            z-index: 1001;
            position: relative;
            background: white;
                border-radius: 5px;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Messages d'alerte -->
    <?php if (isset($message_success)): ?>
        <div class="alert alert-success alert-message alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $message_success; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($message_erreur)): ?>
        <div class="alert alert-danger alert-message alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo $message_erreur; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

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
                    <a class="nav-link active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="bloodbanks">
                        <i class="fas fa-hospital"></i>
                        <span>Banques de sang</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="doctors">
                        <i class="fas fa-user-md"></i>
                        <span>Médecins</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="requests">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Demandes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="stocks">
                        <i class="fas fa-vial"></i>
                        <span>Stocks</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Rapports</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-section="settings">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <a class="nav-link" href="../deconnexion.php">
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
                    <h4 class="mb-0 d-none d-md-inline" id="page-title">Tableau de bord Administrateur</h4>
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
                                AM
                            </div>
                            <span>
                                <?php echo($_SESSION['email']);?>
                            </span>
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
            <!-- Dashboard Section -->
            <div class="section-content active" id="dashboard">
                <!-- Stats Overview -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card primary" data-aos="fade-up">
                            <div class="stats-icon">
                                <i class="fas fa-hospital"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_banque); ?></div>
                            <div class="stats-label">Banques de sang</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card success" data-aos="fade-up" data-aos-delay="100">
                            <div class="stats-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_medecin); ?></div>
                            <div class="stats-label">Médecins actifs</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card warning" data-aos="fade-up" data-aos-delay="200">
                            <div class="stats-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_demande); ?></div>
                            <div class="stats-label">Demandes en cours</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card danger" data-aos="fade-up" data-aos-delay="300">
                            <div class="stats-icon">
                                <i class="fas fa-vial"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_poche); ?></div>
                            <div class="stats-label">Poches de sang</div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="stats-card info" data-aos="fade-up">
                            <div class="stats-icon">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_Specialites); ?></div>
                            <div class="stats-label">Spécialités médicales</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stats-card primary" data-aos="fade-up" data-aos-delay="100">
                            <div class="stats-icon">
                                <i class="fas fa-ambulance"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_hopitaux_partenaires); ?></div>
                            <div class="stats-label">Hôpitaux partenaires</div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="stats-card success" data-aos="fade-up" data-aos-delay="200">
                            <div class="stats-icon">
                                <i class="fas fa-syringe"></i>
                            </div>
                            <div class="stats-number"><?php  echo($resultat_requete_medecin_actif); ?></div>
                            <div class="stats-label">Médecins actifs</div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Recent Activity -->
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="section-card" data-aos="fade-right">
                            <div class="section-header">
                                <h5 class="section-title">Activité récente</h5>
                            </div>
                            <div class="section-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Date</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><i class="fas fa-vial text-danger me-2"></i>Stock</td>
                                                <td>Nouvelle poche de sang ajoutée</td>
                                                <td>12 Nov 2023</td>
                                                <td><span class="badge badge-completed">Terminé</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-user-md text-primary me-2"></i>Médecin</td>
                                                <td>Nouveau médecin enregistré</td>
                                                <td>11 Nov 2023</td>
                                                <td><span class="badge badge-completed">Terminé</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-clipboard-list text-warning me-2"></i>Demande</td>
                                                <td>Nouvelle demande de sang</td>
                                                <td>10 Nov 2023</td>
                                                <td><span class="badge badge-pending">En attente</span></td>
                                            </tr>
                                            <tr>
                                                <td><i class="fas fa-hospital text-info me-2"></i>Banque</td>
                                                <td>Mise à jour des informations</td>
                                                <td>9 Nov 2023</td>
                                                <td><span class="badge badge-completed">Terminé</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 mb-4">
                        <div class="section-card" data-aos="fade-left">
                            <div class="section-header">
                                <h5 class="section-title">Distribution par groupe sanguin</h5>
                            </div>
                            <div class="section-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>O+</span>
                                        <span>38%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 38%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>A+</span>
                                        <span>34%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 34%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>B+</span>
                                        <span>9%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 9%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>AB+</span>
                                        <span>3%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 3%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>O-</span>
                                        <span>7%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 7%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>A-</span>
                                        <span>6%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 6%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>B-</span>
                                        <span>2%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: 2%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>AB-</span>
                                        <span>1%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-light text-dark" role="progressbar" style="width: 1%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blood Banks Section -->
            <div class="section-content" id="bloodbanks">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Gestion des Banques de Sang</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBloodBankModal">
                            <i class="fas fa-plus me-2"></i>Ajouter une Banque
                        </button>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Adresse</th>
                                        <th>Responsable</th>
                                        <th>Stock Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultats_requete_banque as $banque): ?>
                                    <tr>
                                        <td><?php echo $banque['IdBanque']; ?></td>
                                        <td><?php echo $banque['NomBanque']; ?></td>
                                        <td><?php echo $banque['Adresse']; ?></td>
                                        <td><?php echo $banque['Responsable']; ?></td>
                                        <td><?php echo $banque['StockTotal']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBloodBankModal"
                                                    onclick="loadBloodBankData(<?php echo $banque['IdBanque']; ?>, '<?php echo $banque['NomBanque']; ?>', '<?php echo $banque['Adresse']; ?>', '<?php echo $banque['Responsable']; ?>', <?php echo $banque['StockTotal']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?supprimer_banque=<?php echo $banque['IdBanque']; ?>" 
                                               class="btn btn-sm btn-outline-danger action-btn"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette banque de sang ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctors Section -->
            <div class="section-content" id="doctors">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Gestion des Médecins</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-plus me-2"></i>Ajouter un Médecin
                        </button>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Spécialité</th>
                                        <th>Contact</th>
                                        <th>Hôpital</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultatss_requete_medecin as $medecin): ?>
                                    <tr>
                                        <td><?php echo $medecin['IdMedecin']; ?></td>
                                        <td><?php echo $medecin['nomMedecin']; ?></td>
                                        <td><?php echo $medecin['email']; ?></td>
                                        <td><?php echo $medecin['Specialite']; ?></td>
                                        <td><?php echo $medecin['contactMedecin']; ?></td>
                                        <td><?php echo $medecin['nomHopital']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editDoctorModal"
                                                    onclick="loadDoctorData(<?php echo $medecin['IdMedecin']; ?>, '<?php echo $medecin['nomMedecin']; ?>', '<?php echo $medecin['email']; ?>', '<?php echo $medecin['Specialite']; ?>', '<?php echo $medecin['contactMedecin']; ?>', <?php echo $medecin['IdHopital']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?supprimer_medecin=<?php echo $medecin['IdMedecin']; ?>" 
                                               class="btn btn-sm btn-outline-danger action-btn"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Requests Section -->
            <div class="section-content" id="requests">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Gestion des Demandes</h5>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Médecin</th>
                                        <th>Hôpital</th>
                                        <th>Groupe Sanguin</th>
                                        <th>Quantité</th>
                                        <th>Date Demande</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les données des demandes seront affichées ici -->
                                    <tr>
                                        <td colspan="8" class="text-center">Aucune demande pour le moment</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stocks Section -->
            <div class="section-content" id="stocks">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Gestion des Stocks</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                            <i class="fas fa-plus me-2"></i>Ajouter un Stock
                        </button>
                    </div>
                    <div class="section-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Volume (ml)</th>
                                        <th>Groupe Sanguin</th>
                                        <th>Date Collecte</th>
                                        <th>Date Péremption</th>
                                        <th>Cave</th>
                                        <th>Banque</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultats_requete_poche as $stock): ?>
                                    <tr>
                                        <td><?php echo $stock['IdPoche']; ?></td>
                                        <td><?php echo $stock['Volume']; ?></td>
                                        <td>
                                            <span class="blood-group bg-danger"><?php echo $stock['GroupeSanguin']; ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($stock['DateCollecte'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($stock['DatePeremption'])); ?></td>
                                        <td><?php echo $stock['NomCave']; ?></td>
                                        <td><?php echo $stock['NomBanque']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary action-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editStockModal"
                                                    onclick="loadStockData(<?php echo $stock['IdPoche']; ?>, <?php echo $stock['Volume']; ?>, '<?php echo $stock['GroupeSanguin']; ?>', '<?php echo $stock['DateCollecte']; ?>', '<?php echo $stock['DatePeremption']; ?>', <?php echo $stock['IdCave']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="?supprimer_stock=<?php echo $stock['IdPoche']; ?>" 
                                               class="btn btn-sm btn-outline-danger action-btn"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-info action-btn">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="section-content" id="reports">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Rapports et Statistiques</h5>
                    </div>
                    <div class="section-body">
                        <p>Section des rapports en cours de développement...</p>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div class="section-content" id="settings">
                <div class="section-card" data-aos="fade-up">
                    <div class="section-header">
                        <h5 class="section-title">Paramètres du Système</h5>
                    </div>
                    <div class="section-body">
                        <p>Section des paramètres en cours de développement...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Blood Bank Modal -->
    <div class="modal fade" id="addBloodBankModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une Banque de Sang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_banque" class="form-label">Nom de la Banque</label>
                            <input type="text" class="form-control" id="nom_banque" name="nom_banque" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" name="adresse" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="responsable" class="form-label">Responsable</label>
                            <input type="text" class="form-control" id="responsable" name="responsable" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock_total" class="form-label">Stock Total</label>
                            <input type="number" class="form-control" id="stock_total" name="stock_total" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="ajouter_banque" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Blood Bank Modal -->
    <div class="modal fade" id="editBloodBankModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la Banque de Sang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_banque" name="id_banque">
                        <div class="mb-3">
                            <label for="edit_nom_banque" class="form-label">Nom de la Banque</label>
                            <input type="text" class="form-control" id="edit_nom_banque" name="nom_banque" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="edit_adresse" name="adresse" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_responsable" class="form-label">Responsable</label>
                            <input type="text" class="form-control" id="edit_responsable" name="responsable" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_stock_total" class="form-label">Stock Total</label>
                            <input type="number" class="form-control" id="edit_stock_total" name="stock_total" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier_banque" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un Médecin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom_medecin" class="form-label">Nom du Médecin</label>
                            <input type="text" class="form-control" id="nom_medecin" name="nom_medecin" required>
                        </div>
                        <div class="mb-3">
                            <label for="specialite" class="form-label">Spécialité</label>
                            <input type="text" class="form-control" id="specialite" name="specialite" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_hopital" class="form-label">Hôpital</label>
                            <select class="form-select" id="id_hopital" name="id_hopital" required>
                                <option value="">Sélectionner un hôpital</option>
                                <?php foreach ($hopitaux as $hopital): ?>
                                    <option value="<?php echo $hopital['IdHopital']; ?>"><?php echo $hopital['Nom']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="ajouter_medecin" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le Médecin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_medecin" name="id_medecin">
                        <div class="mb-3">
                            <label for="edit_nom_medecin" class="form-label">Nom du Médecin</label>
                            <input type="text" class="form-control" id="edit_nom_medecin" name="nom_medecin" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_specialite" class="form-label">Spécialité</label>
                            <input type="text" class="form-control" id="edit_specialite" name="specialite" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_contact" class="form-label">Contact</label>
                            <input type="text" class="form-control" id="edit_contact" name="contact" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_hopital" class="form-label">Hôpital</label>
                            <select class="form-select" id="edit_id_hopital" name="id_hopital" required>
                                <option value="">Sélectionner un hôpital</option>
                                <?php foreach ($hopitaux as $hopital): ?>
                                    <option value="<?php echo $hopital['IdHopital']; ?>"><?php echo $hopital['Nom']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier_medecin" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div class="modal fade" id="addStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="volume" class="form-label">Volume (ml)</label>
                            <input type="number" class="form-control" id="volume" name="volume" required>
                        </div>
                        <div class="mb-3">
                            <label for="groupe_sanguin" class="form-label">Groupe Sanguin</label>
                            <select class="form-select" id="groupe_sanguin" name="groupe_sanguin" required>
                                <option value="">Sélectionner un groupe</option>
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
                        <div class="mb-3">
                            <label for="date_collecte" class="form-label">Date de Collecte</label>
                            <input type="date" class="form-control" id="date_collecte" name="date_collecte" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_peremption" class="form-label">Date de Péremption</label>
                            <input type="date" class="form-control" id="date_peremption" name="date_peremption" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_cave" class="form-label">Cave</label>
                            <select class="form-select" id="id_cave" name="id_cave" required>
                                <option value="">Sélectionner une cave</option>
                                <?php foreach ($caves as $cave): ?>
                                    <option value="<?php echo $cave['IdCave']; ?>"><?php echo $cave['NomCave']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="ajouter_stock" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Stock Modal -->
    <div class="modal fade" id="editStockModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le Stock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_poche" name="id_poche">
                        <div class="mb-3">
                            <label for="edit_volume" class="form-label">Volume (ml)</label>
                            <input type="number" class="form-control" id="edit_volume" name="volume" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_groupe_sanguin" class="form-label">Groupe Sanguin</label>
                            <select class="form-select" id="edit_groupe_sanguin" name="groupe_sanguin" required>
                                <option value="">Sélectionner un groupe</option>
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
                        <div class="mb-3">
                            <label for="edit_date_collecte" class="form-label">Date de Collecte</label>
                            <input type="date" class="form-control" id="edit_date_collecte" name="date_collecte" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date_peremption" class="form-label">Date de Péremption</label>
                            <input type="date" class="form-control" id="edit_date_peremption" name="date_peremption" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_id_cave" class="form-label">Cave</label>
                            <select class="form-select" id="edit_id_cave" name="id_cave" required>
                                <option value="">Sélectionner une cave</option>
                                <?php foreach ($caves as $cave): ?>
                                    <option value="<?php echo $cave['IdCave']; ?>"><?php echo $cave['NomCave']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier_stock" class="btn btn-primary">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Section Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link[data-section]');
            const sections = document.querySelectorAll('.section-content');
            const pageTitle = document.getElementById('page-title');
            
            // Mobile menu toggle
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const sidebar = document.querySelector('.sidebar');
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('mobile-open');
                });
            }
            
            // Section switching
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const targetSection = this.getAttribute('data-section');
                    
                    // Update active nav link
                    navLinks.forEach(nav => nav.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Show target section
                    sections.forEach(section => {
                        section.classList.remove('active');
                        if (section.id === targetSection) {
                            section.classList.add('active');
                            
                            // Update page title
                            const sectionName = this.querySelector('span').textContent;
                            pageTitle.textContent = sectionName;
                        }
                    });
                    
                    // Close mobile menu if open
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-open');
                    }
                });
            });
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            });
        });

        // Load Blood Bank Data for Edit
        function loadBloodBankData(id, nom, adresse, responsable, stockTotal) {
            document.getElementById('edit_id_banque').value = id;
            document.getElementById('edit_nom_banque').value = nom;
            document.getElementById('edit_adresse').value = adresse;
            document.getElementById('edit_responsable').value = responsable;
            document.getElementById('edit_stock_total').value = stockTotal;
        }

        // Load Doctor Data for Edit
        function loadDoctorData(id, nom, email, specialite, contact, idHopital) {
            document.getElementById('edit_id_medecin').value = id;
            document.getElementById('edit_nom_medecin').value = nom;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_specialite').value = specialite;
            document.getElementById('edit_contact').value = contact;
            document.getElementById('edit_id_hopital').value = idHopital;
        }

        // Load Stock Data for Edit
        function loadStockData(id, volume, groupeSanguin, dateCollecte, datePeremption, idCave) {
            document.getElementById('edit_id_poche').value = id;
            document.getElementById('edit_volume').value = volume;
            document.getElementById('edit_groupe_sanguin').value = groupeSanguin;
            document.getElementById('edit_date_collecte').value = dateCollecte;
            document.getElementById('edit_date_peremption').value = datePeremption;
            document.getElementById('edit_id_cave').value = idCave;
        }

        // Set minimum date for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateCollecteInput = document.getElementById('date_collecte');
            const datePeremptionInput = document.getElementById('date_peremption');
            
            if (dateCollecteInput) {
                dateCollecteInput.min = today;
            }
            if (datePeremptionInput) {
                datePeremptionInput.min = today;
            }
        });
    </script>
</body>
</html>