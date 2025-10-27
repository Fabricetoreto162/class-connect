<?php
session_start();
$msg1 = "";
$msg2 = "";

if (isset($_POST["connexion"])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        // Début connexion à la base de données
        $serveur = "localhost";
        $name = "root";
        $password = "";

        try {
            $connecter = new PDO("mysql:host=$serveur;dbname=gestion_des_etudiants;charset=utf8", $name, $password);
            $connecter->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }

        // Récupération et sécurisation des données POST
        $email = htmlspecialchars(trim($_POST["email"]));
        $password_input = $_POST["password"];

        // Préparation et exécution de la requête
        $connecter_enseignant = $connecter->prepare("SELECT * FROM `users`, `teachers` WHERE teachers.teacher_id = users.teacher_id AND users.email = :email");
        $connecter_enseignant->bindParam(":email", $email);
        $connecter_enseignant->execute();
        $resultat_enseignant = $connecter_enseignant->fetch();

        if ($resultat_enseignant) {
            // Vérification du mot de passe et du rôle
            if (password_verify($password_input, $resultat_enseignant["password"]) && $resultat_enseignant["role"] == "enseignant") {
                // Initialisation de la session
                $_SESSION["user_id"] = $resultat_enseignant["teacher_id"];
                $_SESSION["email"] = $resultat_enseignant["email"];
                $_SESSION["role"] = $resultat_enseignant["role"];
                $_SESSION["first_name"] = $resultat_enseignant["first_name"];
                $_SESSION["last_name"] = $resultat_enseignant["last_name"];
                $_SESSION["Nom"] = $resultat_enseignant["first_name"] . " " . $resultat_enseignant["last_name"];
                $_SESSION["login_time"] = time();

                header("Location: enseignant.php");
                exit();  
            } else {
                $msg2 = "Email ou mot de passe incorrect.";
            }
        } else {
            $msg2 = "Email ou mot de passe incorrect.";
        }
    } else {
        $msg1 = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Enseignant - Class Connect</title>
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome\css\all.min.css">
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
        
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 20px 60px;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.8s ease-out;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .login-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 1rem;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }
        
        .form-control.with-icon {
            padding-left: 50px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
        }
        
        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 15px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 10px;
        }
        
        .btn-login:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 12px 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger-custom {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning-custom {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .login-footer a {
            color: var(--primary);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .additional-options {
            margin-top: 20px;
            text-align: center;
        }
        
        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
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
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 25px;
        }
        
        .floating-label .form-control {
            height: 55px;
        }
        
        .floating-label label {
            position: absolute;
            top: 18px;
            left: 50px;
            color: #6c757d;
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 5px;
        }
        
        .floating-label .form-control:focus + label,
        .floating-label .form-control:not(:placeholder-shown) + label {
            top: -8px;
            font-size: 12px;
            color: var(--primary);
            font-weight: 500;
            left: 15px;
        }
        
        /* Footer Styles */
        footer {
            background: var(--dark);
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 20px;
        }
        
        .footer-section h5 {
            font-weight: 600;
            margin-bottom: 20px;
            color: white;
        }
        
        .footer-section p, .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-section a:hover {
            color: white;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .footer-links i {
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 30px 25px;
            }
            
            .main-content {
                padding: 100px 15px 40px;
            }
        }
        
        @media (max-width: 576px) {
            .login-card {
                padding: 25px 20px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-icon {
                font-size: 2.5rem;
            }
        }
        
        /* Animation de saisie */
        .form-control:focus {
            animation: pulse 0.5s ease;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(67, 97, 238, 0); }
            100% { box-shadow: 0 0 0 0 rgba(67, 97, 238, 0); }
        }
           footer {
      background: var(--dark);
      color: white;
      padding: 25px 0;
      margin-top: auto;
    }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning" style="font-family: cubic;">Connect</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="inscription-enseignant.php">
                        <i class="fas fa-user-plus me-1"></i> Inscription
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="connexion-enseignant.php">
                        <i class="fas fa-sign-in-alt me-1"></i> Connexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="main-content">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h1 class="login-title">Connexion Enseignant</h1>
                <p class="login-subtitle">Accédez à votre espace personnel</p>
            </div>
            
            <!-- Messages d'alerte -->
            <?php if($msg1): ?>
                <div class="alert alert-warning-custom alert-custom" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <?= $msg1 ?>
                </div>
            <?php endif; ?>
            
            <?php if($msg2): ?>
                <div class="alert alert-danger-custom alert-custom" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= $msg2 ?>
                </div>
            <?php endif; ?>
            
            <form role="form" method="post" action="" id="loginForm">
                <div class="floating-label">
                    <div class="position-relative">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control with-icon" placeholder=" " required 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                        <label for="email">Adresse email</label>
                    </div>
                </div>
                
                <div class="floating-label">
                    <div class="position-relative">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password" class="form-control with-icon" placeholder=" " required>
                        <label for="password">Mot de passe</label>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Se souvenir de moi
                        </label>
                    </div>
                    
                </div>
                
                <button type="submit" name="connexion" class="btn btn-login" id="loginButton">
                    <span class="loading-spinner" id="loadingSpinner"></span>
                    <span id="loginText">Se connecter</span>
                </button>
            </form>
            
            <div class="login-footer">
                <p class="mb-0">
                    Vous n'avez pas de compte ? 
                    <a href="inscription-enseignant.php">Créez-en un ici</a>
                </p>
            </div>
           
        </div>
    </div>
</div>
<footer>
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
          <p class="mb-0">&copy; 2025 Class Connect. Tous droits réservés.</p>
        </div>
        
      </div>
    </div>
  </footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const loginButton = document.getElementById('loginButton');
        const loginText = document.getElementById('loginText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        
        // Fonctionnalité d'affichage/masquage du mot de passe
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        
        // Vérification des champs en temps réel
        const emailInput = document.querySelector('input[name="email"]');
        const passwordField = document.getElementById('password');
        
        function validateForm() {
            const emailValid = emailInput.value.length > 0;
            const passwordValid = passwordField.value.length > 0;
            
            if (emailValid && passwordValid) {
                loginButton.disabled = false;
            } else {
                loginButton.disabled = true;
            }
        }
        
        emailInput.addEventListener('input', validateForm);
        passwordField.addEventListener('input', validateForm);
        
        // Validation initiale
        validateForm();
        
        // Effet de focus amélioré
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Vérifier l'état initial
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
        
        // Récupération des identifiants depuis le stockage local si "Se souvenir de moi" était coché
        const rememberMe = document.getElementById('rememberMe');
        const savedEmail = localStorage.getItem('savedEmail');
        
        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberMe.checked = true;
            validateForm();
        }
        
        // Sauvegarde de l'email si "Se souvenir de moi" est coché
        rememberMe.addEventListener('change', function() {
            if (this.checked && emailInput.value) {
                localStorage.setItem('savedEmail', emailInput.value);
            } else {
                localStorage.removeItem('savedEmail');
            }
        });
    });
</script>
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>