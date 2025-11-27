<?php
session_start();

$msg1 = "";
$success_msg = "";

include("../connexion-bases.php");
if(isset($_POST["Inscription"])){
   
    
   

        if(!empty($_POST["nom"]) && 
           !empty($_POST["prenom"]) &&
           !empty($_POST["email"]) &&
           !empty($_POST["password"]) &&
           !empty($_POST["date_naissance"]) &&
           !empty($_POST["sexe"]) &&
           !empty($_POST["contact"])
        ) {
            
            // Fonction pour la sécurisation des données
            function verification_donnees_etudiant($etudiant){
                $etudiant = trim($etudiant);
                $etudiant = htmlspecialchars($etudiant);
                $etudiant = stripcslashes($etudiant);
                $etudiant = strip_tags($etudiant);      
                return $etudiant;
            }

            $nom_etudiant = strtoupper(verification_donnees_etudiant($_POST["nom"]));
            $prenom_etudiant = ucfirst(strtolower(verification_donnees_etudiant($_POST["prenom"])));
            $email_etudiant = verification_donnees_etudiant($_POST["email"]);
            $password_etudiant = password_hash(verification_donnees_etudiant($_POST["password"]), PASSWORD_DEFAULT);
            $date_naissance = verification_donnees_etudiant($_POST["date_naissance"]);
            $sexe = verification_donnees_etudiant($_POST["sexe"]);
            $role = "etudiant";
            $code = "CON";
            $annee = date("Y");

            // Vérifier si l'email existe déjà
            $check_email = $connecter->prepare("SELECT email FROM students WHERE email = :email");
            $check_email->bindParam(":email", $email_etudiant);
            $check_email->execute();
            
            if($check_email->rowCount() > 0) {
                $msg1 = "Cette adresse email est déjà utilisée.";
            } else {
                // Générer le matricule automatiquement
                $stmt = $connecter->prepare("SELECT matricule FROM students WHERE matricule LIKE :prefix ORDER BY matricule DESC LIMIT 1"); 
                $prefix = "$code/$annee/%";
                $stmt->execute(['prefix' => $prefix]);
                $last = $stmt->fetchColumn();

                if ($last) {
                    $parts = explode('/', $last);
                    $num = intval($parts[2]) + 1;
                } else {
                    $num = 1;
                }

                $numeroOrdre = str_pad($num, 3, "0", STR_PAD_LEFT);
                $matricule = "$code/$annee/$numeroOrdre";
                $contact = verification_donnees_etudiant($_POST["contact"]);

                // Insertion dans la table students
                $insertion_etudiant = $connecter->prepare("INSERT INTO students(matricule, first_name, last_name, birth_date, contact, gender, email) VALUES(:matricule, :first_name, :last_name, :birth_date, :contact, :gender, :email)"); 
                $insertion_etudiant->bindParam(":first_name", $nom_etudiant);
                $insertion_etudiant->bindParam(":last_name", $prenom_etudiant);
                $insertion_etudiant->bindParam(":matricule", $matricule);
                $insertion_etudiant->bindParam(":birth_date", $date_naissance);
                $insertion_etudiant->bindParam(":gender", $sexe);
                $insertion_etudiant->bindParam(":contact", $contact);
                $insertion_etudiant->bindParam(":email", $email_etudiant);
                $insertion_etudiant->execute();

                // Sélectionner l'id de l'étudiant
                $selection_etudiant = $connecter->prepare("SELECT * FROM students WHERE email = :email");
                $selection_etudiant->bindParam(":email", $email_etudiant);
                $selection_etudiant->execute();
                $resultat_selection_etudiant = $selection_etudiant->fetch();
                $student_id = $resultat_selection_etudiant["student_id"];

                // Insertion dans la table users
                $requete_etudiant = $connecter->prepare("INSERT INTO users(student_id, password, email, role) VALUES(:student_id, :password, :email, :role)"); 
                $requete_etudiant->bindParam(":student_id", $student_id);
                $requete_etudiant->bindParam(":password", $password_etudiant);
                $requete_etudiant->bindParam(":email", $email_etudiant);
                $requete_etudiant->bindParam(":role", $role);
                $requete_etudiant->execute();

                $_SESSION['success_message'] = "Inscription réussie ! Votre matricule est : $matricule";
                header("Location: reussite-etudiant.php");
                exit();
            }
        } else {
            $msg1 = "* Merci de remplir tous les champs obligatoires.";
        }
   
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Étudiant - Class Connect</title>
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
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary) !important;
        }
        
        .main-content {
            padding-top: 80px;
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
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
            height: 100%;
            
        }
        
        
        .image-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../img/etudiant.jpg') no-repeat center;
            background-size: cover;
            opacity: 0.8;
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
            box-shadow: 0 0 0 0.2rem rgba(47, 88, 235, 0.25);
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
        
        @media (max-width: 992px) {
            .image-section {
                display: none;
            }
            
            .form-section {
                padding: 30px 20px;
            }
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
            <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning"style="font-family: cubic;">Connect</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="inscription-etudiant.php">
                        <i class="fas fa-user-plus me-1"></i> Inscription
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="connexion-etudiant.php">
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
                                <div class="text-center position-relative img z-2" >
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section formulaire -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="text-center mb-4">
                                    <h2 class="form-title">Inscription Étudiant</h2>
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
                                
                                
                                
                                <form role="form" method="POST" action="" >
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
                                        
                                        <div class="mb-3">
                                            <div class="floating-label">
                                                <input type="date" name="date_naissance" class="form-control" placeholder=" " required>
                                                <label for="date_naissance"><i class="fas fa-calendar-alt me-2"></i>Date de naissance *</label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="floating-label">
                                                <select name="sexe" class="form-control" required>
                                                    <option value=""></option>
                                                    <option value="M">Homme</option>
                                                    <option value="F">Femme</option>
                                                </select>
                                                <label for="sexe"><i class="fas fa-venus-mars me-2"></i>Sexe *</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-navigation">
                                            <button type="button" class="btn btn-outline-primary-custom" id="next1">Suivant</button>
                                        </div>
                                    </div>
                                    
                                    <!-- Étape 2: Informations de contact -->
                                    <div class="form-step" id="step2">
                                        <h5 class="mb-4">Informations de contact</h5>
                                        
                                        <div class="mb-3">
                                            <div class="floating-label">
                                                <input type="text" name="contact" class="form-control" placeholder=" " required>
                                                <label for="contact"><i class="fas fa-phone me-2"></i>Contact *</label>
                                            </div>
                                        </div>
                                        
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
                                    
                                    <!-- Étape 3: Récapitulatif -->
                                    <div class="form-step" id="step3">
                                        <h5 class="mb-4">Récapitulatif</h5>
                                        
                                        <div class="card mb-4">
                                            <div class="card-body">
                                                <p class="mb-2"><strong>Nom:</strong> <span id="summaryNom"></span></p>
                                                <p class="mb-2"><strong>Prénom:</strong> <span id="summaryPrenom"></span></p>
                                                <p class="mb-2"><strong>Email:</strong> <span id="summaryEmail"></span></p>
                                                <p class="mb-2"><strong>Date de naissance:</strong> <span id="summaryDateNaissance"></span></p>
                                                <p class="mb-2"><strong>Sexe:</strong> <span id="summarySexe"></span></p>
                                                <p class="mb-0"><strong>Contact:</strong> <span id="summaryContact"></span></p>
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
                                        <a href="connexion-etudiant.php" class="text-primary fw-bold">Connectez-vous ici</a>
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
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

<script src="./script_etudiant_inscription.js"></script>
</body>
</html>