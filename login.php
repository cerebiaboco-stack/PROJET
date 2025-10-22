<?php
    session_start();
    $message="";
    include('includes/config.php');
    
    // Vérifier si l'utilisateur est déjà connecté
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['role'] == "administrateur") {
            header("Location: /Projet/admin/dashboard.php");
        } else {
            header("Location: /Projet/medecin/dashboard.php");
        }
        exit();
    }

    // Traitement du formulaire de connexion
    if (isset($_POST['btn_valider'])) {
        $email=$_POST['email'];
        $password=$_POST['password'];
        
        if ($email=="" || $password=="") {
            $message="<span style='color:red; font-weight:bold;'>Tous les champs sont obligatoires!</span>";
        } else {
            $requete=$bdd->prepare('SELECT * FROM `users` WHERE Email=?;');
            $requete->execute(array($email));
            $requete_resultat=$requete->fetch(PDO::FETCH_ASSOC);

            if ($requete_resultat){
                            if (password_verify($password,$requete_resultat['MotDePasse'])) {
                echo "Mot de passe valide<br>";
                echo "Rôle: " . $requete_resultat['Role'] . "<br>";
                echo "Email: " . $requete_resultat['Email'] . "<br>";
                
                $_SESSION['email']=$requete_resultat['Email'];
                $_SESSION['role']=$requete_resultat['Role'];  
                
                if ($_SESSION['role']=="administrateur") {
                    echo "Redirection vers admin dashboard<br>";
                    $_SESSION['user_id']=$requete_resultat['IdUsers'];
                    // header("Location: /Projet/admin/dashboard.php");
                    // exit;
                } else {
                    echo "Tentative de connexion médecin<br>";
                    // C'est un médecin
                    $req = $bdd->prepare('SELECT * FROM users u INNER JOIN medecin m ON u.IdUsers = m.IdUsers WHERE u.Email = ?');
                    $req->execute(array($_SESSION['email']));
                    $req_resultat = $req->fetch(PDO::FETCH_ASSOC);
                    
                    if ($req_resultat) {
                        echo "Profil médecin trouvé:<br>";
                        echo "Nom: " . $req_resultat['Nom'] . "<br>";
                        echo "Spécialité: " . $req_resultat['Specialite'] . "<br>";
                        echo "ID Médecin: " . $req_resultat['IdMedecin'] . "<br>";
                        
                        $_SESSION['user_id'] = $req_resultat['IdUsers'];
                        $_SESSION['role'] = $req_resultat['Role'];
                        $_SESSION['email'] = $req_resultat['Email'];
                        $_SESSION['nom'] = $req_resultat['Nom'];
                        $_SESSION['specialite'] = $req_resultat['Specialite'];
                        $_SESSION['contact'] = $req_resultat['Contact'];
                        $_SESSION['id_medecin'] = $req_resultat['IdMedecin'];
                        
                        echo "Redirection vers dashboard médecin<br>";
                        // header("Location: /Projet/medecin/dashboard.php");
                        // exit;
                    } else {2
                        echo "Profil médecin NON trouvé<br>";
                        // Afficher les erreurs SQL
                        echo "Erreur SQL: ";
                        print_r($req->errorInfo());
                        echo "<br>";
                        
                        session_destroy();
                        $message = "<span style='color:red; font-weight:bold;'>Profil médecin non trouvé!</span>";
                    }
                }
                exit; // Temporaire pour voir les messages
}
            
                } else {
                    $message="<span style='color:red; font-weight:bold;'>Mot de passe non valide!</span>";
                }
            } else {
                $message="<span style='color:red; font-weight:bold;'>Adresse email non valide !</span>";
            }
        }
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Banque de Sang</title>
    
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
            pointer-events: none; /* Correction: permet les clics à travers */
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            z-index: 10; /* Correction: z-index plus élevé */
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
                        <a class="nav-link active" href="login.php">Connexion</a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="register.php">S'inscrire</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <div class="auth-container">
        <div class="auth-bg"></div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5" data-aos="fade-up">
                    <div class="auth-card">
                        <div class="auth-header">
                            <div class="auth-logo">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h1 class="auth-title">Connexion</h1>
                            <p class="auth-subtitle">Accédez à votre compte Banque de Sang</p>
                        </div>
                            <?php
                                echo($message);
                            ?>
                        <div class="auth-body">
                            <form id="loginForm" method="POST">
                                <div class="form-group">
                                    <label for="email" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="votre@email.com" required>
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                                    <i class="fas fa-lock input-icon"></i>
                                </div>
                                <div class="form-group d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    <a href="#" class="auth-link">Mot de passe oublié ?</a>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-auth" name="btn_valider">Se connecter</button>
                                </div>
                            </form>
                            <div class="auth-footer">
                                <p>Vous n'avez pas de compte ? <a href="register.php" class="auth-link">Créer un compte</a></p>
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
        
        // Form validation and submission
        
    </script>
</body>
</html>