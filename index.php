<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Class Connect - Plateforme Éducative Innovante</title>
  <meta name="description" content="Class Connect, la plateforme qui connecte étudiants, enseignants et administration pour une expérience éducative optimale">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #6a7eee;
      --secondary: #3f37c9;
      --accent: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #4cc9f0;
      --gradient: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      --card-gradient: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: var(--gradient);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      line-height: 1.6;
    }
    
    .navbar {
      background: rgba(255, 255, 255, 0.98) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      transition: all 0.3s ease;
    }
    
    .navbar.scrolled {
      padding: 10px 0;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--primary) !important;
      display: flex;
      align-items: center;
    }
    
    .navbar-brand i {
      margin-right: 10px;
    }
    
    .welcome-container {
      flex: 1;
      display: flex;  
      align-items: center;
      justify-content: center;
      padding: 100px 20px 60px;
      min-height: calc(100vh - 80px);
    }
    
    .welcome-card {
      background: var(--card-gradient);
      backdrop-filter: blur(15px);
      border-radius: 24px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5);
      padding: 50px 40px;
      max-width: 900px;
      width: 100%;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .welcome-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--accent), var(--success));
      z-index: 1;
    }
    
    .welcome-title {
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 20px;
      font-size: 2.8rem;
      line-height: 1.2;
    }
    
    .welcome-subtitle {
      color: var(--dark);
      font-size: 1.25rem;
      margin-bottom: 40px;
      opacity: 0.8;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .role-selection {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
    }
    
    .role-card {
      background: white;
      border-radius: 18px;
      padding: 35px 25px;
      width: 240px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      transition: all 0.4s ease;
      border: 2px solid transparent;
      text-decoration: none;
      color: inherit;
      position: relative;
      overflow: hidden;
    }
    
    .role-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: var(--primary);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }
    
    .role-card:hover {
      transform: translateY(-12px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      border-color: var(--primary-light);
      text-decoration: none;
      color: inherit;
    }
    
    .role-card:hover::before {
      transform: scaleX(1);
    }
    
    .role-icon {
      font-size: 3.5rem;
      margin-bottom: 25px;
      color: var(--primary);
      transition: transform 0.3s ease;
    }
    
    .role-card:hover .role-icon {
      transform: scale(1.1);
    }
    
    .role-title {
      font-weight: 600;
      font-size: 1.3rem;
      margin-bottom: 12px;
      color: var(--dark);
    }
    
    .role-description {
      font-size: 0.95rem;
      color: #6c757d;
      line-height: 1.5;
    }
    
    .btn-primary-custom {
      background: var(--primary);
      border: none;
      border-radius: 50px;
      padding: 12px 35px;
      font-weight: 500;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
    }
    
    .btn-primary-custom:hover {
      background: var(--secondary);
      transform: translateY(-3px);
      box-shadow: 0 7px 15px rgba(67, 97, 238, 0.4);
    }
    
    .btn-outline-primary-custom {
      border: 2px solid var(--primary);
      color: var(--primary);
      border-radius: 50px;
      padding: 10px 30px;
      font-weight: 500;
      transition: all 0.3s ease;
      background: transparent;
    }
    
    .btn-outline-primary-custom:hover {
      background: var(--primary);
      color: white;
      transform: translateY(-3px);
      box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
    }
    
    footer {
      background: var(--dark);
      color: white;
      padding: 30px 0;
      margin-top: auto;
    }
    
    .animated-text {
      background: linear-gradient(90deg, var(--primary), var(--accent), var(--success));
      background-size: 300% 300%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: gradient 3s ease infinite;
    }
    
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    .floating-shapes {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: -1;
      pointer-events: none;
    }
    
    .shape {
      position: absolute;
      opacity: 0.1;
      border-radius: 50%;
    }
    
    .shape-1 {
      width: 150px;
      height: 150px;
      background: var(--primary);
      top: -50px;
      right: -50px;
    }
    
    .shape-2 {
      width: 100px;
      height: 100px;
      background: var(--accent);
      bottom: 50px;
      left: -30px;
    }
    
    .shape-3 {
      width: 80px;
      height: 80px;
      background: var(--success);
      top: 50%;
      right: 10%;
    }
    
    /* Responsive */
    @media (max-width: 992px) {
      .welcome-card {
        padding: 40px 30px;
      }
      
      .welcome-title {
        font-size: 2.4rem;
      }
      
      .role-card {
        width: 220px;
      }
    }
    
    @media (max-width: 768px) {
      .welcome-card {
        padding: 35px 25px;
      }
      
      .welcome-title {
        font-size: 2rem;
      }
      
      .welcome-subtitle {
        font-size: 1.1rem;
      }
      
      .role-card {
        width: 100%;
        max-width: 280px;
        padding: 30px 20px;
      }
      
      .role-selection {
        gap: 20px;
      }
    }
    
    @media (max-width: 576px) {
      .welcome-container {
        padding: 80px 15px 40px;
      }
      
      .welcome-card {
        padding: 30px 20px;
        border-radius: 18px;
      }
      
      .welcome-title {
        font-size: 1.8rem;
      }
      
      .welcome-subtitle {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i class="fas fa-graduation-cap"></i>Class <span class="text-warning" style="font-family: cubic;">Connect</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#about">À propos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#features">Fonctionnalités</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#contact">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Contenu principal -->
  <div class="welcome-container">
    <div class="welcome-card">
      <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
      </div>
      
      <h1 class="welcome-title animated-text" id="welcomeText">Bienvenue sur Class Connect</h1>
      <p class="welcome-subtitle">La plateforme innovante qui connecte étudiants, enseignants et administration pour une expérience éducative optimale et collaborative</p>
      
      <div class="mb-4">
        <h3 class="mb-3">Qui êtes-vous ?</h3>
        <p class="text-muted">Sélectionnez votre profil pour continuer</p>
      </div>
      
      <div class="role-selection">
        <a href="./etudiant/inscription-etudiant.php" class="role-card" aria-label="Accéder en tant qu'étudiant">
          <div class="role-icon">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="role-title">Étudiant</div>
          <div class="role-description">Accédez à vos cours, devoirs, ressources pédagogiques et interagissez avec vos enseignants</div>
        </a>
        
        <a href="./enseignant/inscription-enseignant.php" class="role-card" aria-label="Accéder en tant qu'enseignant">
          <div class="role-icon">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <div class="role-title">Enseignant</div>
          <div class="role-description">Gérez vos cours, évaluations, ressources et interactions avec les étudiants</div>
        </a>
        
        <a href="./admin/connexion-admin.php" class="role-card" aria-label="Accéder en tant qu'administrateur">
          <div class="role-icon">
            <i class="fas fa-user-cog"></i>
          </div>
          <div class="role-title">Administration</div>
          <div class="role-description">Supervisez et gérez la plateforme éducative, les utilisateurs et les contenus</div>
        </a>
      </div>
      
      <div class="mt-5">
        <p class="text-muted mb-3">Vous avez déjà un compte ?</p>
        <a href="#" class="btn btn-outline-primary-custom">Se connecter</a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
          <p class="mb-0">&copy; 2025 Class Connect. Tous droits réservés.</p>
        </div>
        <div class="col-md-6 text-md-end text-center">
          <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Animation pour le texte de bienvenue
    document.addEventListener('DOMContentLoaded', function() {
      const welcomeText = document.getElementById('welcomeText');
      if (welcomeText) {
        welcomeText.style.opacity = '0';
        welcomeText.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          welcomeText.style.transition = 'opacity 1s ease, transform 1s ease';
          welcomeText.style.opacity = '1';
          welcomeText.style.transform = 'translateY(0)';
        }, 300);
      }
      
      // Animation des cartes de rôle
      const roleCards = document.querySelectorAll('.role-card');
      roleCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 500 + (index * 200));
      });
      
      // Effet de navbar au scroll
      window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
          navbar.classList.add('scrolled');
        } else {
          navbar.classList.remove('scrolled');
        }
      });
    });
  </script>
</body>
</html>