<?php
session_start();
include("includes/config.php");
$message="";

if(isset($_POST['btn_admin'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confirmpassword=$_POST['confirmPassword'];

    if ($email=="" || $email==" " || $password==""|| $password==" " || $confirmpassword=="" || $confirmpassword==" ") {
        $message="<span style='color:red; font-weight:bold;'>Tous les champs sont obligatoires</span>";
    } else {
        $requete=$bdd->prepare('SELECT * FROM `users` WHERE Email=?;');
        $requete->execute(array($email));

        if($requete->rowCount() > 0){
            $message="<span style='color:red; font-weight:bold;'>Cette adresse email est déjà associée à un compte!</span>";
        } else {
            if ($password==$confirmpassword) {
                $type="administrateur";
                $password_secur=password_hash($password,PASSWORD_DEFAULT);

                $requete=$bdd->prepare('INSERT INTO `users`(`IdUsers`, `MotDePasse`, `Role`, `Email`, `DateCreation`) VALUES (?,?,?,?,NOW());');
                $requete->execute(array(0,$password_secur,$type,$email));
                if ($requete) {
                    header("Location: login.php");
                    exit;
                } else {
                    $message="<span style='color:red; font-weight:bold;'>Erreur lors de l'enregistrement veuillez réessayer!</span>";
                }
            } else {
                $message="<span style='color:red; font-weight:bold;'>Les mots de passe ne correspondent pas!</span>";
            }
        }
    }
}

if(isset($_POST['btn_medecin'])){
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $specialite = $_POST['specialite'];
    $contact_medecin = $_POST['contact_medecin'];
    $hopital_id = $_POST['hopital_id'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmPassword'];

    // Validation des champs
    if (empty($prenom) || empty($nom) || empty($email) || empty($specialite) || empty($contact_medecin) || empty($hopital_id) || empty($password) || empty($confirmpassword)) {
        $message = "<span style='color:red; font-weight:bold;'>Tous les champs sont obligatoires</span>";
    } else {
        // Vérifier si l'email existe déjà dans users
        $requete = $bdd->prepare('SELECT * FROM `users` WHERE Email=?;');
        $requete->execute(array($email));

        if($requete->rowCount() > 0) {
            $message = "<span style='color:red; font-weight:bold;'>Cette adresse email est déjà associée à un compte!</span>";
        } else {
            if ($password == $confirmpassword) {
                $type = "medecin";
                $password_secur = password_hash($password, PASSWORD_DEFAULT);

                try {
                    // Commencer une transaction
                    $bdd->beginTransaction();

                    // 1. Insérer dans la table medecin
                    $requete_medecin = $bdd->prepare('INSERT INTO `medecin`(`IdMedecin`, `IdHopital`, `Nom`, `email`, `Specialite`, `Contact`) VALUES (?,?,?,?,?,?);');
                    $requete_medecin->execute(array(0, $hopital_id, $nom, $email, $specialite, $contact_medecin));
                    
                    // Récupérer l'ID du médecin créé
                    $id_medecin = $bdd->lastInsertId();

                    // 2. Insérer dans la table users
                    $requete_user = $bdd->prepare('INSERT INTO `users`(`IdUsers`, `MotDePasse`, `Role`, `Email`, `DateCreation`, `IdMedecin`) VALUES (?,?,?,?,NOW(),?);');
                    $requete_user->execute(array(0, $password_secur, $type, $email, $id_medecin));

                    // Valider la transaction
                    $bdd->commit();

                    header("Location: login.php");
                    exit;

                } catch (Exception $e) {
                    // Annuler la transaction en cas d'erreur
                    $bdd->rollBack();
                    $message = "<span style='color:red; font-weight:bold;'>Erreur lors de l'enregistrement: " . $e->getMessage() . "</span>";
                }
                   
            } else {
                $message = "<span style='color:red; font-weight:bold;'>Les mots de passe ne correspondent pas!</span>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Banque de Sang</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-red: #e63946;
            --light-red: #ff6b7a;
            --primary-blue: #48cae4;
            --light-blue: #90e0ef;
            --dark-blue: #023e8a;
            --gradient-primary: linear-gradient(135deg, var(--primary-blue), var(--primary-red));
            --gradient-light: linear-gradient(135deg, var(--light-blue), var(--light-red));
            --gradient-dark: linear-gradient(135deg, var(--dark-blue), #6a0f1a);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            color: #333;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            margin: 0 0.5rem;
            position: relative;
            color: #555 !important;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-red) !important;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(230, 57, 70, 0.4);
        }
        
        /* Auth Container */
        .auth-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .auth-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: var(--gradient-light);
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
            opacity: 0.7;
            pointer-events: none;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
            z-index: 10;
            position: relative;
            transition: all 0.4s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        .auth-header {
            background: var(--gradient-dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .auth-logo {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .auth-title {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            opacity: 0.8;
            font-size: 0.9rem;
        }
        
        .auth-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(72, 202, 228, 0.25);
        }
        
        .input-icon {
            position: absolute;
            right: 15px;
            top: 42px;
            color: #6c757d;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }
        
        .auth-footer {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }
        
        .auth-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .auth-link:hover {
            color: var(--primary-red);
        }
        
        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        /* Role Selection */
        .role-selection {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .role-option {
            flex: 1;
            text-align: center;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .role-option:hover {
            border-color: var(--primary-blue);
        }
        
        .role-option.active {
            border-color: var(--primary-red);
            background: rgba(230, 57, 70, 0.05);
        }
        
        .role-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--primary-blue);
        }
        
        .role-option.active .role-icon {
            color: var(--primary-red);
        }
        
        /* Formulaires spécifiques aux rôles */
        .role-form {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .role-form.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Animation for form elements */
        .form-group {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease forwards;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Footer */
        footer {
            background: #1a1a2e;
            color: white;
            padding: 2rem 0 1rem;
            margin-top: auto;
        }
        
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: var(--primary-red);
            transform: translateY(-3px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .auth-bg {
                width: 100%;
                clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 90%);
            }
            
            .auth-card {
                margin: 0 1rem;
            }
            
            .role-selection {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-tint me-2"></i>Banque de Sang
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Connexion</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="register.php">S'inscrire</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Register Section -->
    <div class="auth-container">
        <div class="auth-bg"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-8" data-aos="fade-up">
                    <div class="auth-card">
                        <div class="auth-header">
                            <div class="auth-logo">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h1 class="auth-title">Inscription</h1>
                            <p class="auth-subtitle">Rejoignez la communauté Banque de Sang</p>
                        </div>
                        <div class="auth-body">
                            <div class="form-group">
                                <label class="form-label">Type de compte</label>
                                <div class="role-selection">
                                    <div class="role-option active" data-role="admin">
                                        <div class="role-icon">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div class="role-name">Administrateur</div>
                                    </div>
                                    <div class="role-option" data-role="medecin">
                                        <div class="role-icon">
                                            <i class="fas fa-user-md"></i>
                                        </div>
                                        <div class="role-name">Médecin</div>
                                    </div>
                                </div>
                                <?php
                                    if(!empty($message)) {
                                        echo '<div class="alert alert-danger mt-3">' . $message . '</div>';
                                    }
                                ?>
                            </div>
                            
                            <!-- Formulaire Administrateur -->
                            <form id="registerFormAdmin" class="role-form active" method="POST">
                                <input type="hidden" name="role" value="admin">
                                
                                <div class="form-group">
                                    <label for="admin_email" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="admin_email" name="email" placeholder="votre@email.com" required>
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="admin_password" class="form-label">Mot de passe</label>
                                            <input type="password" class="form-control" id="admin_password" name="password" placeholder="Votre mot de passe" required>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="admin_confirmPassword" class="form-label">Confirmer le mot de passe</label>
                                            <input type="password" class="form-control" id="admin_confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe" required>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="admin_terms" name="terms" required>
                                        <label class="form-check-label" for="admin_terms">
                                            J'accepte les <a href="#" class="auth-link">conditions d'utilisation</a> et la <a href="#" class="auth-link">politique de confidentialité</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-auth" name="btn_admin">Créer un compte administrateur</button>
                                </div>
                            </form>
                            
                            <!-- Formulaire Médecin -->
                            <form id="registerFormMedecin" class="role-form" method="POST">
                                <input type="hidden" name="role" value="medecin">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medecin_firstName" class="form-label">Prénom</label>
                                            <input type="text" class="form-control" id="medecin_firstName" name="prenom" placeholder="Votre prénom" required>
                                            <i class="fas fa-user input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medecin_lastName" class="form-label">Nom</label>
                                            <input type="text" class="form-control" id="medecin_lastName" name="nom" placeholder="Votre nom" required>
                                            <i class="fas fa-user input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="medecin_email" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="medecin_email" name="email" placeholder="votre@email.com" required>
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                
                                <div class="form-group">
                                    <label for="specialite" class="form-label">Spécialité</label>
                                    <input type="text" class="form-control" id="specialite" name="specialite" placeholder="Votre spécialité médicale" required>
                                    <i class="fas fa-stethoscope input-icon"></i>
                                </div>
                                
                                <div class="form-group">
                                    <label for="contact_medecin" class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="contact_medecin" name="contact_medecin" placeholder="Votre numéro de téléphone" required>
                                    <i class="fas fa-phone input-icon"></i>
                                </div>
                                
                                <div class="form-group">
                                    <label for="hopital_id" class="form-label">Hôpital d'affiliation</label>
                                    <select class="form-control" id="hopital_id" name="hopital_id" required>
                                        <option value="">Sélectionnez votre hôpital</option>
                                        <option value="1">Hôpital National</option>
                                        <option value="2">Centre Hospitalier Universitaire</option>
                                        <option value="3">Clinique Saint-Jean</option>
                                        <option value="4">Hôpital Régional</option>
                                    </select>
                                    <i class="fas fa-hospital input-icon"></i>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medecin_password" class="form-label">Mot de passe</label>
                                            <input type="password" class="form-control" id="medecin_password" name="password" placeholder="Votre mot de passe" required>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medecin_confirmPassword" class="form-label">Confirmer le mot de passe</label>
                                            <input type="password" class="form-control" id="medecin_confirmPassword" name="confirmPassword" placeholder="Confirmer le mot de passe" required>
                                            <i class="fas fa-lock input-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="medecin_terms" name="terms" required>
                                        <label class="form-check-label" for="medecin_terms">
                                            J'accepte les <a href="#" class="auth-link">conditions d'utilisation</a> et la <a href="#" class="auth-link">politique de confidentialité</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-auth" name="btn_medecin">Créer un compte médecin</button>
                                </div>
                            </form>
                            
                            <div class="auth-footer">
                                <p>Vous avez déjà un compte ? <a href="login.php" class="auth-link">Se connecter</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3"><i class="fas fa-tint me-2"></i>Banque de Sang</h5>
                    <p class="mb-4">Plateforme intelligente de gestion et localisation de poches de sang pour les établissements de santé.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Liens rapides</h5>
                    <div class="footer-links d-flex flex-column">
                        <a href="index.php" class="mb-2">Accueil</a>
                        <a href="about.php" class="mb-2">À propos</a>
                        <a href="services.php" class="mb-2">Services</a>
                        <a href="contact.php" class="mb-2">Contact</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3">Ressources</h5>
                    <div class="footer-links d-flex flex-column">
                        <a href="#" class="mb-2">FAQ</a>
                        <a href="#" class="mb-2">Documentation</a>
                        <a href="#" class="mb-2">Support</a>
                        <a href="#" class="mb-2">Politique de confidentialité</a>
                    </div>
                </div>
                <div class="col-lg-3 mb-4">
                    <h5 class="mb-3">Contact</h5>
                    <div class="footer-links d-flex flex-column">
                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Rue de la Santé, Cotonou</p>
                        <p class="mb-2"><i class="fas fa-phone me-2"></i> +229 01 43 45 67 89</p>
                        <p class="mb-2"><i class="fas fa-envelope me-2"></i> contact@banquedesang.fr</p>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2023 Banque de Sang. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Role selection and form adaptation
        document.addEventListener('DOMContentLoaded', function() {
            const roleOptions = document.querySelectorAll('.role-option');
            const roleForms = document.querySelectorAll('.role-form');
            
            // Fonction pour mettre à jour le formulaire selon le rôle
            function updateFormForRole(role) {
                roleForms.forEach(form => {
                    if (form.id === `registerForm${role.charAt(0).toUpperCase() + role.slice(1)}`) {
                        form.classList.add('active');
                    } else {
                        form.classList.remove('active');
                    }
                });
            }
            
            // Gestionnaire de clic pour les options de rôle
            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    roleOptions.forEach(opt => opt.classList.remove('active'));
                    
                    // Add active class to clicked option
                    this.classList.add('active');
                    
                    // Update form for selected role
                    const role = this.getAttribute('data-role');
                    updateFormForRole(role);
                });
            });
            
            // Validation du formulaire médecin
            const medecinForm = document.getElementById('registerFormMedecin');
            medecinForm.addEventListener('submit', function(e) {
                const firstName = document.getElementById('medecin_firstName').value;
                const lastName = document.getElementById('medecin_lastName').value;
                const email = document.getElementById('medecin_email').value;
                const specialite = document.getElementById('specialite').value;
                const contact = document.getElementById('contact_medecin').value;
                const hopital = document.getElementById('hopital_id').value;
                const password = document.getElementById('medecin_password').value;
                const confirmPassword = document.getElementById('medecin_confirmPassword').value;
                const terms = document.getElementById('medecin_terms').checked;
                
                // Validation
                if (!firstName || !lastName || !email || !specialite || !contact || !hopital || !password || !confirmPassword) {
                    e.preventDefault();
                    alert('Veuillez remplir tous les champs obligatoires');
                    return;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Les mots de passe ne correspondent pas');
                    return;
                }
                
                if (!terms) {
                    e.preventDefault();
                    alert('Veuillez accepter les conditions d\'utilisation');
                    return;
                }
            });
            
            // Navbar background on scroll
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    document.querySelector('.navbar').style.background = 'rgba(255, 255, 255, 0.98)';
                    document.querySelector('.navbar').style.boxShadow = '0 5px 20px rgba(0, 0, 0, 0.1)';
                } else {
                    document.querySelector('.navbar').style.background = 'rgba(255, 255, 255, 0.95)';
                    document.querySelector('.navbar').style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
                }
            });
        });
    </script>
</body>
</html>