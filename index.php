<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banque de Sang - Plateforme intelligente de gestion</title>
    
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
        
        /* Hero Section */
        .hero-section {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: var(--gradient-light);
            clip-path: polygon(25% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: var(--gradient-dark);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .hero-subtitle {
            font-size: 1.25rem;
            color: #555;
            margin-bottom: 2rem;
            max-width: 90%;
        }
        
        .hero-image {
            position: relative;
            z-index: 2;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        /* Features Section */
        .features-section {
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
        
        .feature-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            background: var(--gradient-primary);
            color: white;
        }
        
        /* Statistics Section */
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
            background: #fff;
        }
        
        .cta-card {
            background: var(--gradient-dark);
            border-radius: 20px;
            padding: 4rem 2rem;
            color: white;
            text-align: center;
            box-shadow: 0 15px 40px rgba(2, 62, 138, 0.2);
        }
        
        .cta-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .btn-light {
            background: white;
            color: var(--dark-blue);
            border: none;
            border-radius: 50px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-light:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.3);
        }
        
        /* Footer */
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
        @media (max-width: 992px) {
            .hero-bg {
                width: 80%;
                clip-path: polygon(15% 0%, 100% 0%, 100% 100%, 0% 100%);
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-bg {
                width: 100%;
                clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 90%);
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-image {
                margin-top: 3rem;
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
                        <a class="nav-link active" href="index.php">Accueil</a>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content" data-aos="fade-right">
                    <h1 class="hero-title">Plateforme intelligente de gestion de sang</h1>
                    <p class="hero-subtitle">Optimisez la gestion des réserves sanguines, sauvez plus de vies grâce à notre système de localisation et de gestion en temps réel.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="register.php" class="btn btn-primary">Créer un compte</a>
                        <a href="about.php" class="btn btn-outline-primary">En savoir plus</a>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="hero-image text-center">
                        <img src="https://cdn.pixabay.com/photo/2017/03/14/03/20/woman-2141808_1280.png" alt="Don de sang" class="img-fluid" style="max-height: 600px;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Fonctionnalités principales</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-search-location"></i>
                            </div>
                            <h5 class="card-title">Localisation intelligente</h5>
                            <p class="card-text">Trouvez rapidement les poches de sang disponibles près d'un établissement de santé avec notre système de géolocalisation avancé.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-hand-holding-medical"></i>
                            </div>
                            <h5 class="card-title">Gestion des demandes</h5>
                            <p class="card-text">Suivez et gérez les demandes de sang en temps réel avec un système de priorisation intelligent pour les cas urgents.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card card h-100">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title">Tableaux de bord</h5>
                            <p class="card-text">Visualisez les statistiques et tendances pour une meilleure planification des ressources et besoins futurs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in">
                    <div class="stat-item">
                        <div class="stat-number" data-count="1542">0</div>
                        <div class="stat-label">Poches disponibles</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number" data-count="42">0</div>
                        <div class="stat-label">Demandes aujourd'hui</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number" data-count="28">0</div>
                        <div class="stat-label">Hôpitaux partenaires</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number" data-count="324">0</div>
                        <div class="stat-label">Vies sauvées ce mois</div>
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
                    <div class="cta-card">
                        <h3 class="cta-title">Rejoignez notre réseau de santé</h3>
                        <p class="mb-4">Inscrivez-vous dès maintenant pour accéder à notre plateforme et optimiser la gestion des réserves sanguines de votre établissement.</p>
                        <a href="register.php" class="btn btn-light">Créer un compte gratuit</a>
                    </div>
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