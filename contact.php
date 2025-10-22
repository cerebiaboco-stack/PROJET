<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Banque de Sang</title>
    
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
        }
        
        /* Navigation - Même style que index.php */
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

        /* Hero Section Contact */
        .contact-hero-section {
            background: var(--gradient-dark);
            color: white;
            padding: 150px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .contact-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: var(--gradient-light);
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            opacity: 0.3;
        }

        .hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        /* Contact Section */
        .contact-section {
            padding: 6rem 0;
            background: #fff;
        }

        .section-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 3rem;
            background: var(--gradient-dark);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .contact-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .contact-info {
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 2rem;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        /* FAQ Section */
        .faq-section {
            padding: 6rem 0;
            background: #f8f9fa;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(72, 202, 228, 0.1);
            color: var(--primary-blue);
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.2rem rgba(72, 202, 228, 0.25);
            border-color: var(--primary-blue);
        }

        /* Map Section */
        .map-section {
            padding: 6rem 0;
            background: #fff;
        }

        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 400px;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: var(--gradient-dark);
            color: white;
            text-align: center;
        }

        /* Footer - Même style que index.php */
        footer {
            background: #1a1a2e;
            color: white;
            padding: 4rem 0 2rem;
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
            .hero-title {
                font-size: 2.5rem;
            }
            
            .contact-hero-section::before {
                width: 100%;
                clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 90%);
            }
            
            .contact-item {
                flex-direction: column;
                text-align: center;
            }
            
            .contact-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
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
                        <a class="nav-link active" href="contact.php">Contact</a>
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

    <!-- Hero Section -->
    <section class="contact-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="hero-title">Contactez notre équipe</h1>
                    <p class="hero-subtitle">Une question, un besoin spécifique ou une demande de démonstration ? Notre équipe est à votre écoute.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#contact-form" class="btn btn-primary btn-lg">Envoyer un message</a>
                        <a href="tel:+2290143456789" class="btn btn-outline-light btn-lg">Nous appeler</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Restons en contact</h2>
            <div class="row">
                <div class="col-lg-8 mb-5" data-aos="fade-right">
                    <div class="contact-form">
                        <form id="contact-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nom complet *</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Sujet *</label>
                                <select class="form-control" id="subject" required>
                                    <option value="">Sélectionnez un sujet</option>
                                    <option value="demo">Demande de démonstration</option>
                                    <option value="support">Support technique</option>
                                    <option value="partnership">Partenariat</option>
                                    <option value="other">Autre</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">Envoyer le message</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-left" data-aos-delay="200">
                    <div class="contact-info">
                        <h3 class="mb-4">Nos coordonnées</h3>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5>Adresse</h5>
                                <p>123 Rue de la Santé<br>Cotonou, Bénin</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5>Téléphone</h5>
                                <p>+229 01 43 45 67 89</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5>Email</h5>
                                <p>contact@banquedesang.fr</p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5>Horaires</h5>
                                <p>Lun - Ven: 8h00 - 18h00<br>Sam: 9h00 - 13h00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Questions fréquentes</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Comment puis-je m'inscrire sur la plateforme ?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    L'inscription est simple et gratuite. Rendez-vous sur la page <a href="register.php">Inscription</a>, choisissez votre profil et remplissez le formulaire. Votre compte sera activé après validation.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    La plateforme est-elle sécurisée ?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Absolument. Nous utilisons un chiffrement de bout en bout pour protéger toutes les données sensibles. Notre plateforme est conforme aux normes de sécurité des données de santé.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Quel est le temps de réponse pour une demande de sang ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Pour les urgences critiques, notre système priorise automatiquement les demandes. La plupart des demandes urgentes sont traitées en moins de 15 minutes.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Notre localisation</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10" data-aos="fade-up" data-aos-delay="200">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.2622453021614!2d2.1001334157604124!3d6.356326095405112!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023557d5b7c0c1b%3A0x5f5c5c5e5c5c5c5c!2sCotonou%2C%20B%C3%A9nin!5e0!3m2!1sfr!2sfr!4v1634567890123!5m2!1sfr!2sfr" 
                                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">
                    <h2 class="mb-4">Prêt à rejoindre notre réseau ?</h2>
                    <p class="mb-4">Rejoignez les établissements qui sauvent des vies grâce à notre plateforme intelligente.</p>
                    <a href="register.php" class="btn btn-light btn-lg me-3">Créer un compte</a>
                    <a href="tel:+2290143456789" class="btn btn-outline-light btn-lg">Nous appeler</a>
                </div>
            </div>
        </div>
    </section>

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
        
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contact-form');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
                form.reset();
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