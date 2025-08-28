<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription-reussi-admin</title>
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="inscription-admin.css">

     <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            animation: fadeInUp 0.8s ease-out;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }
        
        .welcome-title {
            color: #333;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .welcome-text {
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .countdown {
            font-size: 14px;
            color: #888;
            margin-top: 15px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes bounce {
            from {
                transform: translateY(0px);
            }
            to {
                transform: translateY(-10px);
            }
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .success-card {
                padding: 30px 20px;
                margin: 20px;
            }
            
            .success-icon {
                font-size: 60px;
            }
        }
    </style>
    
</head>
<body>
   <!--debut nav-->
<nav class="navbar nav navbar-expand-lg  fixed-top" style="background-color: var(--bs-primary-bg-subtle);">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Class <span class="text-warning">Connect</span></a>
   
    <div class="offcanvas offcanvas-end" style="width: 250px !important; height: 100vh;" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Class <span class="connect text-warning">Connect</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body " >
        <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link   active" aria-current="page" href="inscription-etudiant.php">Inscription</a>
          </li> 
          <li class="nav-item">
            <a class="nav-link" href="connexion-etudiant.php">Connexion</a>
          </li>
         
          
        </ul>
         
      </div>
    </div>
    <button class="btn btn-outline-warning " type="submit">Notification</button>
     <button class="navbar-toggler " type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
      </span>
    </button>
  </div>
</nav>

<!--fin nav-->


<main class="main-content  ">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="welcome-title">Félicitations !</h1>
        <p class="welcome-text">
            Votre inscription sur <strong>Class Connect</strong> a été effectuée avec succès. 
            Bienvenue dans notre communauté éducative !
        </p>
        
        
        
        <p class="welcome-text">
            Vous pouvez maintenant accéder à votre espace personnel et découvrir 
            toutes les fonctionnalités de Class Connect.
        </p>
        
        <a href="connexion-etudiant.php" class="btn-login">
            <i class="fas fa-sign-in-alt me-2 text-dark"></i>Se connecter
        </a>
        
       
    </div>

   
  </main>








    <script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.min.js"></script>
 <script src="inscription-admin.js"></script>   
</body>
</html>