<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - Banque de Sang</title>
    
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
            background-color: #f9f9f9;
        }
        
        /* Navigation - Style minimaliste */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.05);
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
            border-radius: 5px;
            padding: 0.5rem 1rem !important;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: rgba(230, 57, 70, 0.1);
            color: var(--primary-red) !important;
        }
        
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(230, 57, 70, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(230, 57, 70, 0.3);
        }

        /* Hero Section About - Style minimaliste */
        .about-hero-section {
            background: var(--gradient-dark);
            color: white;
            padding: 180px 0 120px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .about-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.05"><polygon fill="white" points="0,1000 1000,0 1000,1000"/></svg>');
            background-size: cover;
        }

        .hero-title {
            font-weight: 700;
            font-size: 3rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Mission Section - Style minimaliste */
        .mission-section {
            padding: 6rem 0;
            background: #fff;
        }

        .section-title {
            text-align: center;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--dark-blue);
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::after {
            content: '';
            position: absolute;
            width: 60px;
            height: 4px;
            background: var(--gradient-primary);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .mission-card {
            background: white;
            border-radius: 10px;
            padding: 2.5rem 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            text-align: center;
            border: 1px solid #f0f0f0;
            position: relative;
            overflow: hidden;
        }

        .mission-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--gradient-primary);
        }

        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .mission-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            background: white;
            color: var(--primary-red);
            font-size: 1.8rem;
            border: 2px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        .mission-card:hover .mission-icon {
            background: var(--gradient-primary);
            color: white;
            border-color: transparent;
        }

        /* Values Section - Style minimaliste */
        .values-section {
            padding: 6rem 0;
            background: #f8f9fa;
        }

        .value-item {
            text-align: center;
            padding: 2rem 1rem;
        }

        .value-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            display: inline-block;
            width: 70px;
            height: 70px;
            line-height: 70px;
            border-radius: 50%;
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* Team Section - Style minimaliste */
        .team-section {
            padding: 6rem 0;
            background: #fff;
        }

        .team-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #f0f0f0;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .team-image {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .team-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 70%, rgba(0,0,0,0.1));
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.05);
        }

        .team-info {
            padding: 1.5rem;
            text-align: center;
        }

        .team-info h5 {
            color: var(--dark-blue);
            margin-bottom: 0.5rem;
        }

        .team-info .text-primary {
            color: var(--primary-red) !important;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* CTA Section - Style minimaliste */
        .cta-section {
            padding: 6rem 0;
            background: var(--gradient-dark);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000" opacity="0.05"><polygon fill="white" points="0,0 1000,1000 0,1000"/></svg>');
            background-size: cover;
        }

        .btn-light {
            background: white;
            color: var(--dark-blue);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-light:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-light {
            border: 2px solid white;
            color: white;
            border-radius: 8px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white;
            color: var(--dark-blue);
            transform: translateY(-2px);
        }

        /* Footer - Style minimaliste */
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
            border-radius: 8px;
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
                font-size: 2.2rem;
            }
            
            .about-hero-section {
                padding: 150px 0 80px;
            }
            
            .mission-card, .team-card {
                margin-bottom: 2rem;
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
                        <a class="nav-link active" href="about.php">À propos</a>
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
    <section class="about-hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9" data-aos="fade-up">
                    <h1 class="hero-title">Révolutionner la gestion du sang pour sauver des vies</h1>
                    <p class="hero-subtitle">Notre plateforme connecte les banques de sang, hôpitaux et médecins pour optimiser la distribution des ressources vitales.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="register.php" class="btn btn-primary btn-lg">Rejoindre notre réseau</a>
                        <a href="#mission" class="btn btn-outline-light btn-lg">Notre mission</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section id="mission" class="mission-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Notre Mission</h2>
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h4>Sauver des vies</h4>
                        <p>Optimiser la gestion des réserves sanguines pour réduire les délais d'intervention et sauver plus de vies.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h4>Connecter les acteurs</h4>
                        <p>Créer un réseau intelligent entre banques de sang, hôpitaux et professionnels de santé.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="mission-card">
                        <div class="mission-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Innovation continue</h4>
                        <p>Utiliser la technologie pour anticiper les besoins et optimiser les ressources disponibles.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Nos Valeurs</h2>
            <div class="row">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="value-item">
                        <div class="value-number">01</div>
                        <h4>Innovation</h4>
                        <p>Nous repoussons les limites de la technologie pour améliorer l'efficacité des soins de santé.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="value-item">
                        <div class="value-number">02</div>
                        <h4>Collaboration</h4>
                        <p>Nous croyons en la puissance du travail d'équipe et des partenariats stratégiques.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="value-item">
                        <div class="value-number">03</div>
                        <h4>Impact</h4>
                        <p>Chaque décision est guidée par notre mission de sauver des vies et d'améliorer les soins.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Notre Équipe</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-card">
                        <div class="team-image">
                            <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Dr. Jean Martin">
                        </div>
                        <div class="team-info">
                            <h5>Dr. Jean HOUTONDJI</h5>
                            <p class="text-primary">Directeur Médical</p>
                            <p>Médecin hématologue avec 15 ans d'expérience dans la gestion des banques de sang.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-card">
                        <div class="team-image">
                            <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Marie Dubois">
                        </div>
                        <div class="team-info">
                            <h5>Marie Dubois</h5>
                            <p class="text-primary">Responsable Technique</p>
                            <p>Ingénieure en informatique spécialisée dans les solutions de santé digitales.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="team-card">
                        <div class="team-image">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Paul Leroy">
                        </div>
                        <div class="team-info">
                            <h5>Paul Leroy</h5>
                            <p class="text-primary">Coordinateur Partenariats</p>
                            <p>Expert en gestion de projets et relations avec les établissements de santé.</p>
                        </div>
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
        
        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').style.background = 'rgba(255, 255, 255, 0.98)';
                document.querySelector('.navbar').style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.05)';
            } else {
                document.querySelector('.navbar').style.background = 'rgba(255, 255, 255, 0.98)';
                document.querySelector('.navbar').style.boxShadow = '0 1px 10px rgba(0, 0, 0, 0.05)';
            }
        });
    </script>
</body>
</html>