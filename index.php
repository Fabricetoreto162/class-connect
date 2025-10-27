<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Class Connect - Plateforme Éducative</title>
<link rel="stylesheet" href="bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">  
<link rel="stylesheet" href="fontawesome\css\all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --accent: #f72585;
      --light: #f8f9fa;
      --dark: #212529;
      --success: #4cc9f0;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    .navbar {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px);
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--primary) !important;
    }
    
    .welcome-container {
      flex: 1;
      display: flex;  
      align-items: center;
      justify-content: center;
      padding: 100px 20px 60px;
      
      
    }
    
    .welcome-card {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      padding: 40px;
      max-width: 800px;
      width: 100%;
      text-align: center;
      border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .welcome-title {
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 20px;
      font-size: 2.5rem;
    }
    
    .welcome-subtitle {
      color: var(--dark);
      font-size: 1.2rem;
      margin-bottom: 40px;
      opacity: 0.8;
    }
    
    .role-selection {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 25px;
      margin-top: 30px;
    }
    
    .role-card {
      background: white;
      border-radius: 15px;
      padding: 30px 20px;
      width: 220px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      border: 2px solid transparent;
      text-decoration: none;
      color: inherit;
    }
    
    .role-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
      border-color: var(--primary);
      text-decoration: none;
      color: inherit;
    }
    
    .role-icon {
      font-size: 3rem;
      margin-bottom: 20px;
      color: var(--primary);
    }
    
    .role-title {
      font-weight: 600;
      font-size: 1.2rem;
      margin-bottom: 10px;
      color: var(--dark);
    }
    
    .role-description {
      font-size: 0.9rem;
      color: #6c757d;
    }
    
    .btn-primary-custom {
      background: var(--primary);
      border: none;
      border-radius: 50px;
      padding: 12px 30px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
      background: var(--secondary);
      transform: translateY(-3px);
      box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
    }
    
    footer {
      background: var(--dark);
      color: white;
      padding: 25px 0;
      margin-top: auto;
    }
    
    .animated-text {
      background: linear-gradient(90deg, var(--primary), var(--accent), var(--success));
      background-size: 300% 300%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: gradient 3s ease infinite;
    }
    
    @keyframes gradient {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    @media (max-width: 768px) {
      .welcome-card {
        padding: 30px 20px;
      }
      
      .welcome-title {
        font-size: 2rem;
      }
      
      .role-card {
        width: 100%;
        max-width: 300px;
      }
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#" >
        <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning" style="font-family: cubic;">Connect</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
     
    </div>
  </nav>

  <!-- Contenu principal -->
  <div class="welcome-container w-100  " >
    <div class="welcome-card">
      <h1 class="welcome-title animated-text" id="welcomeText">Bienvenue sur Class Connect</h1>
      <p class="welcome-subtitle">La plateforme qui connecte étudiants, enseignants et administration pour une expérience éducative optimale</p>
      
      <div class="mb-4">
        <h3 class="mb-3">Qui êtes-vous ?</h3>
        <p class="text-muted">Sélectionnez votre profil pour continuer</p>
      </div>
      
      <div class="role-selection">
        <a href="./etudiant/inscription-etudiant.php" class="role-card">
          <div class="role-icon">
            <i class="fas fa-user-graduate"></i>
          </div>
          <div class="role-title">Étudiant</div>
          <div class="role-description">Accédez à vos cours, devoirs et ressources pédagogiques</div>
        </a>
        
        <a href="./enseignant/inscription-enseignant.php" class="role-card">
          <div class="role-icon">
            <i class="fas fa-chalkboard-teacher"></i>
          </div>
          <div class="role-title">Enseignant</div>
          <div class="role-description">Gérez vos cours, évaluations et interactions avec les étudiants</div>
        </a>
        
        <a href="./admin/connexion-admin.php" class="role-card">
          <div class="role-icon">
            <i class="fas fa-user-cog"></i>
          </div>
          <div class="role-title">Administration</div>
          <div class="role-description">Supervisez et gérez la plateforme éducative</div>
        </a>
      </div>
      
      <div class="mt-5">
        <p class="text-muted mb-3">Vous avez déjà un compte ?</p>
        <a href="#" class="btn btn-outline-primary btn-success-custom">Se connecter</a>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
          <p class="mb-0">&copy; 2025 Class Connect. Tous droits réservés.</p>
        </div>
        
      </div>
    </div>
  </footer>
<script src="bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.min.js"></script>
<script src="bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
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
    });
  </script>
</body>
</html>