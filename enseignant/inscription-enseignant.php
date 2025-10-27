<?php
session_start();
$msg1 = "";
$success_msg = "";

if(isset($_POST["Inscription"])){
    // Début connexion à la base de données  
    $serveur = "localhost";
    $name = "root";
    $password = "";
    
    try {
        $connexion_enseignant = new PDO("mysql:host=$serveur;dbname=gestion_des_etudiants", $name, $password);
        $connexion_enseignant->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(!empty($_POST["nom"]) && 
           !empty($_POST["prenom"]) &&
           !empty($_POST["email"]) &&
           !empty($_POST["password"])
        ) {
            
            // Fonction pour la sécurisation des données
            function verification_donnees_enseignant($enseignant){
                $enseignant = trim($enseignant);
                $enseignant = htmlspecialchars($enseignant);
                $enseignant = stripcslashes($enseignant);
                $enseignant = strip_tags($enseignant);      
                return $enseignant;
            }

            $nom_enseignant = strtoupper(verification_donnees_enseignant($_POST["nom"]));
            $prenom_enseignant = ucfirst(strtolower(verification_donnees_enseignant($_POST["prenom"])));
            $email_enseignant = verification_donnees_enseignant($_POST["email"]);
            $password_enseignant = password_hash(verification_donnees_enseignant($_POST["password"]), PASSWORD_DEFAULT);
            $phone_enseignant = verification_donnees_enseignant($_POST["indicatif"]) . " " . verification_donnees_enseignant($_POST["tel"]);
            $role = "enseignant";

            // Vérifier si l'email existe déjà
            $check_email = $connexion_enseignant->prepare("SELECT email FROM teachers WHERE email = :email");
            $check_email->bindParam(":email", $email_enseignant);
            $check_email->execute();
            
            if($check_email->rowCount() > 0) {
                $msg1 = "Cette adresse email est déjà utilisée.";
            } else {
                $insertion_enseignant = $connexion_enseignant->prepare("INSERT INTO teachers(first_name, last_name, email, phone) VALUES(:first_name, :last_name, :email, :phone) ");
                $insertion_enseignant->bindParam(":first_name", $nom_enseignant);
                $insertion_enseignant->bindParam(":last_name", $prenom_enseignant);
                $insertion_enseignant->bindParam(":email", $email_enseignant);
                $insertion_enseignant->bindParam(":phone", $phone_enseignant);
                $insertion_enseignant->execute();

                // Récupérer l'ID de l'enseignant inséré
                $selection_enseignant = $connexion_enseignant->prepare("SELECT * FROM teachers WHERE email = :email");
                $selection_enseignant->bindParam(":email", $email_enseignant);
                $selection_enseignant->execute();
                $resultat_enseignant = $selection_enseignant->fetch();
                $teacher_id = $resultat_enseignant["teacher_id"];

                $insertion_user_enseignant = $connexion_enseignant->prepare("INSERT INTO users(email, password, role, teacher_id) VALUES(:email, :password, :role, :teacher_id) ");
                $insertion_user_enseignant->bindParam(":email", $email_enseignant);
                $insertion_user_enseignant->bindParam(":password", $password_enseignant);
                $insertion_user_enseignant->bindParam(":role", $role);
                $insertion_user_enseignant->bindParam(":teacher_id", $teacher_id);
                $insertion_user_enseignant->execute();

                $_SESSION['success_message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header("Location: reussite-enseignant.php");
                exit();
            }
        } else {
            $msg1 = "* Merci de remplir tous les champs obligatoires.";
        }
    } catch(PDOException $e) {
        $msg1 = "Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Enseignant - Class Connect</title>
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
            padding: 100px 0 60px;
        }
        
        .registration-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .image-section {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../img/enseignant.jpg') no-repeat center;            
            background-size: cover;
        }
        
        .form-section {
            padding: 40px;
        }
        
        .form-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .form-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .input-group {
            position: relative;
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
            padding-left: 45px;
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
        
        .btn-primary-custom {
            background: var(--primary);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary-custom:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(67, 97, 238, 0.3);
        }
        
        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 12px 15px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            position: relative;
            z-index: 2;
        }
        
        .step.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .form-step {
            display: none;
        }
        
        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn-outline-primary-custom {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary-custom:hover {
            background: var(--primary);
            color: white;
        }
        
        .phone-input-group {
            display: flex;
            gap: 10px;
        }
        
        .country-select {
            flex: 0 0 180px;
        }
        
        .phone-input {
            flex: 1;
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 20px;
        }
        
        .floating-label .form-control {
            height: 55px;
        }
        
        .floating-label label {
            position: absolute;
            top: 18px;
            left: 15px;
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
        }
        
        .floating-label select.form-control + label {
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
        
        @media (max-width: 992px) {
            .image-section {
                display: none;
            }
            
            .form-section {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 768px) {
            .phone-input-group {
                flex-direction: column;
            }
            
            .country-select {
                flex: 1;
            }
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
                    <a class="nav-link active" href="inscription-enseignant.php">
                        <i class="fas fa-user-plus me-1"></i> Inscription
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion-enseignant.php">
                        <i class="fas fa-sign-in-alt me-1"></i> Connexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenu principal -->
<div class="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="registration-container">
                    <div class="row g-0">
                        <!-- Section image -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="image-section h-100">
                                <div class="text-center position-relative z-2">
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section formulaire -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="text-center mb-4">
                                    <h2 class="form-title">Inscription Enseignant</h2>
                                    <p class="form-subtitle">Créez votre compte en quelques étapes</p>
                                </div>
                                
                                <!-- Indicateur d'étapes -->
                                <div class="step-indicator">
                                    <div class="step active">1</div>
                                    <div class="step">2</div>
                                    <div class="step">3</div>
                                </div>
                                
                                <!-- Messages d'alerte -->
                                <?php if($msg1): ?>
                                    <div class="alert alert-danger alert-custom mb-4" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i> <?= $msg1 ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form role="form" method="POST" action="" id="registrationForm">
                                    <!-- Étape 1: Informations personnelles -->
                                    <div class="form-step active" id="step1">
                                        <h5 class="mb-4">Informations personnelles</h5>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="floating-label">
                                                    <input type="text" name="nom" class="form-control" placeholder=" " required>
                                                    <label for="nom"><i class="fas fa-user me-2"></i>Nom *</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="floating-label">
                                                    <input type="text" name="prenom" class="form-control" placeholder=" " required>
                                                    <label for="prenom"><i class="fas fa-user me-2"></i>Prénom *</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="floating-label">
                                                <input type="email" name="email" class="form-control" placeholder=" " required>
                                                <label for="email"><i class="fas fa-envelope me-2"></i>Email *</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-navigation">
                                            <button type="button" class="btn btn-outline-primary-custom" id="next1">Suivant</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Étape 2: Informations de compte -->
                                    <div class="form-step" id="step2">
                                        <h5 class="mb-4">Informations de compte</h5>
                                        
                                        <div class="mb-3">
                                            <div class="floating-label position-relative">
                                                <input type="password" name="password" id="password" class="form-control" placeholder=" " required>
                                                <label for="password"><i class="fas fa-lock me-2"></i>Mot de passe *</label>
                                                <button type="button" class="password-toggle" id="togglePassword">
                                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <div class="floating-label position-relative">
                                                <input type="password" id="confirmPassword" class="form-control" placeholder=" " required>
                                                <label for="confirmPassword"><i class="fas fa-lock me-2"></i>Confirmer le mot de passe *</label>
                                                <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                                    <i class="fas fa-eye" id="confirmEyeIcon"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" id="passwordError">Les mots de passe ne correspondent pas</div>
                                        </div>
                                        
                                        <div class="form-navigation">
                                            <button type="button" class="btn btn-outline-primary-custom" id="prev2">Précédent</button>
                                            <button type="button" class="btn btn-outline-primary-custom" id="next2">Suivant</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Étape 3: Informations de contact -->
                                    <div class="form-step" id="step3">
                                        <h5 class="mb-4">Informations de contact</h5>
                                        
                                        <div class="mb-3">
                                            <div class="phone-input-group">
                                                <div class="floating-label country-select">
                                                    <select name="indicatif" class="form-control" required>
                                                        <option value=""></option>
                                                        <option value="+229">🇧🇯 Bénin (+229)</option>
                                                        <option value="+225">🇨🇮 Côte d'Ivoire (+225)</option>
                                                        <option value="+221">🇸🇳 Sénégal (+221)</option>
                                                        <option value="+33">🇫🇷 France (+33)</option>
                                                        <option value="+1">🇺🇸 USA (+1)</option>
                                                        <option value="+237">🇨🇲 Cameroun (+237)</option>
                                                        <option value="+223">🇲🇱 Mali (+223)</option>
                                                        <option value="+234">🇳🇬 Nigéria (+234)</option>
                                                    </select>
                                                    <label for="indicatif"><i class="fas fa-flag me-2"></i>Pays</label>
                                                </div>
                                                <div class="floating-label phone-input">
                                                    <input type="tel" name="tel" class="form-control" placeholder=" ">
                                                    <label for="tel"><i class="fas fa-phone me-2"></i>Téléphone (optionnel)</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                            <label class="form-check-label" for="termsCheck">
                                                J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a> et la <a href="#" class="text-primary">politique de confidentialité</a>
                                            </label>
                                        </div>
                                        
                                        <div class="form-navigation">
                                            <button type="button" class="btn btn-outline-primary-custom" id="prev3">Précédent</button>
                                            <button type="submit" name="Inscription" class="btn btn-primary-custom" id="submitBtn">Finaliser l'inscription</button>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="text-center mt-4">
                                    <p class="mb-0">
                                        Vous avez déjà un compte? 
                                        <a href="connexion-enseignant.php" class="text-primary fw-bold">Connectez-vous ici</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        // Variables pour la navigation par étapes
        const steps = document.querySelectorAll('.form-step');
        const stepIndicators = document.querySelectorAll('.step');
        let currentStep = 0;
        
        // Éléments de navigation
        const next1 = document.getElementById('next1');
        const next2 = document.getElementById('next2');
        const prev2 = document.getElementById('prev2');
        const prev3 = document.getElementById('prev3');
        
        // Fonction pour afficher une étape
        function showStep(stepIndex) {
            steps.forEach((step, index) => {
                step.classList.toggle('active', index === stepIndex);
            });
            
            stepIndicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index <= stepIndex);
            });
            
            currentStep = stepIndex;
        }
        
        // Navigation entre les étapes
        next1.addEventListener('click', function() {
            // Validation de l'étape 1
            const nom = document.querySelector('input[name="nom"]').value;
            const prenom = document.querySelector('input[name="prenom"]').value;
            const email = document.querySelector('input[name="email"]').value;
            
            if(nom && prenom && email) {
                // Validation basique de l'email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if(emailRegex.test(email)) {
                    showStep(1);
                } else {
                    alert('Veuillez entrer une adresse email valide.');
                }
            } else {
                alert('Veuillez remplir tous les champs obligatoires de cette étape.');
            }
        });
        
        next2.addEventListener('click', function() {
            // Validation de l'étape 2
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('#confirmPassword').value;
            
            if(password && confirmPassword) {
                if(password === confirmPassword) {
                    if(password.length >= 8) {
                        showStep(2);
                    } else {
                        alert('Le mot de passe doit contenir au moins 8 caractères.');
                    }
                } else {
                    document.getElementById('passwordError').style.display = 'block';
                    document.getElementById('confirmPassword').classList.add('is-invalid');
                }
            } else {
                alert('Veuillez remplir tous les champs obligatoires de cette étape.');
            }
        });
        
        prev2.addEventListener('click', function() {
            showStep(0);
        });
        
        prev3.addEventListener('click', function() {
            showStep(1);
        });
        
        // Fonctionnalité d'affichage/masquage du mot de passe
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const confirmEyeIcon = document.getElementById('confirmEyeIcon');
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            confirmEyeIcon.classList.toggle('fa-eye');
            confirmEyeIcon.classList.toggle('fa-eye-slash');
        });
        
        // Validation en temps réel de la correspondance des mots de passe
        confirmPasswordInput.addEventListener('input', function() {
            if(passwordInput.value !== confirmPasswordInput.value) {
                document.getElementById('passwordError').style.display = 'block';
                confirmPasswordInput.classList.add('is-invalid');
            } else {
                document.getElementById('passwordError').style.display = 'none';
                confirmPasswordInput.classList.remove('is-invalid');
            }
        });
        
        // Validation du formulaire final
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            const termsCheck = document.getElementById('termsCheck');
            if(!termsCheck.checked) {
                e.preventDefault();
                alert('Veuillez accepter les conditions d\'utilisation pour finaliser votre inscription.');
            }
        });
    });
</script>
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>