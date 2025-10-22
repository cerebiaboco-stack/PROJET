<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Banque de Sang</title>
    
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

        /* Hero Section Services */
        .services-hero-section {
            background: var(--gradient-dark);
            color: white;
            padding: 150px 0 100px;
            position: relative;
            overflow: hidden;
        }

        .services-hero-section::before {
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

        /* Services Grid */
        .services-section {
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

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: var(--gradient-primary);
            color: white;
            font-size: 2rem;
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: #f8f9fa;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background: var(--gradient-primary);
            color: white;
            font-size: 1.5rem;
        }

        /* Stats Section */
        .stats-section {
            padding: 5rem 0;
            background: var(--gradient-light);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,50 Q250,0 500,50 T1000,50 L1000,100 L0,100 Z" fill="rgba(255,255,255,0.1)"></path></svg>');
            background-size: cover;
            background-position: center;
        }

        .stat-item {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .stat-number {
            font-weight: 800;
            font-size: 3rem;
            margin-bottom: 0.5rem;
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
            
            .services-hero-section::before {
                width: 100%;
                clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 90%);
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
                        <a class="nav-link active" href="services.php">Services</a>
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

    <!-- Hero Section -->
    <section class="services-hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8" data-aos="fade-right">
                    <h1 class="hero-title">Solutions intelligentes pour sauver des vies</h1>
                    <p class="hero-subtitle">Découvrez notre gamme complète de services conçus pour optimiser chaque étape de la gestion du sang.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-primary btn-lg">Commencer maintenant</a>
                        <a href="#services" class="btn btn-outline-light btn-lg">Découvrir les services</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Nos Services</h2>
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <h4>Localisation intelligente</h4>
                        <p>Trouvez rapidement les poches de sang disponibles près d'un établissement de santé avec notre système de géolocalisation avancé.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-hand-holding-medical"></i>
                        </div>
                        <h4>Gestion des demandes</h4>
                        <p>Suivez et gérez les demandes de sang en temps réel avec un système de priorisation intelligent pour les cas urgents.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Tableaux de bord</h4>
                        <p>Visualisez les statistiques et tendances pour une meilleure planification des ressources et besoins futurs.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Système d'alertes</h4>
                        <p>Recevez des notifications automatiques pour les stocks critiques et les demandes urgentes.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h4>Gestion des stocks</h4>
                        <p>Optimisez la gestion de vos réserves avec un suivi en temps réel des entrées et sorties.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Sécurité des données</h4>
                        <p>Protégez les informations sensibles avec notre système de sécurité avancé et conformité aux normes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Fonctionnalités avancées</h2>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h5>Intelligence Artificielle</h5>
                        <p>Notre IA prédit les besoins en sang et optimise la distribution automatiquement.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h5>Application Mobile</h5>
                        <p>Accédez à toutes les fonctionnalités depuis votre smartphone, où que vous soyez.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <h5>Intégration API</h5>
                        <p>Connectez facilement vos systèmes existants à notre plateforme.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5>Analytics avancés</h5>
                        <p>Analyses prédictives et rapports détaillés pour une meilleure prise de décision.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in">
                    <div class="stat-item">
                        <div class="stat-number" data-count="1500">0</div>
                        <div class="stat-label">Poches disponibles</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number" data-count="42">0</div>
                        <div class="stat-label">Hôpitaux connectés</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number" data-count="324">0</div>
                        <div class="stat-label">Vies sauvées</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number" data-count="15">0</div>
                        <div class="stat-label">Minutes de réponse</div>
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
                    <h2 class="mb-4">Prêt à optimiser votre gestion de sang ?</h2>
                    <p class="mb-4">Rejoignez notre réseau et bénéficiez de tous nos services pour sauver plus de vies.</p>
                    <a href="register.php" class="btn btn-light btn-lg me-3">Créer un compte</a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg">Nous contacter</a>
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
        
        // Counter animation for statistics
        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.stat-number');
            const speed = 200;
            
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-count');
                    const count = +counter.innerText;
                    
                    const inc = target / speed;
                    
                    if (count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };
                
                // Start counter when element is in viewport
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCount();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(counter);
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