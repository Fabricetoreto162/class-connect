<?php
session_start();

// Initialisation des variables
$errors = [];
$success_msg = "";
$field_values = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'date_naissance' => '',
    'sexe' => '',
    'contact' => ''
];

if (isset($_POST["Inscription"])) {
    
    // Récupération et nettoyage des données
    function verification_donnees($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    // Récupérer et stocker les valeurs pour réaffichage
    $field_values['nom'] = isset($_POST["nom"]) ? verification_donnees($_POST["nom"]) : '';
    $field_values['prenom'] = isset($_POST["prenom"]) ? verification_donnees($_POST["prenom"]) : '';
    $field_values['email'] = isset($_POST["email"]) ? filter_var($_POST["email"], FILTER_SANITIZE_EMAIL) : '';
    $field_values['date_naissance'] = isset($_POST["date_naissance"]) ? verification_donnees($_POST["date_naissance"]) : '';
    $field_values['sexe'] = isset($_POST["sexe"]) ? verification_donnees($_POST["sexe"]) : '';
    $field_values['contact'] = isset($_POST["contact"]) ? verification_donnees($_POST["contact"]) : '';
    
    // Validation du nom
    if (empty($_POST["nom"])) {
        $errors['nom'] = "Le nom est obligatoire.";
    } else {
        $nom = strtoupper($field_values['nom']);
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $nom)) {
            $errors['nom'] = "Le nom ne doit contenir que des lettres, espaces et tirets.";
        }
        if (strlen($nom) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
        }
    }
    
    // Validation du prénom
    if (empty($_POST["prenom"])) {
        $errors['prenom'] = "Le prénom est obligatoire.";
    } else {
        $prenom = ucfirst(strtolower($field_values['prenom']));
        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $prenom)) {
            $errors['prenom'] = "Le prénom ne doit contenir que des lettres, espaces et tirets.";
        }
        if (strlen($prenom) < 2) {
            $errors['prenom'] = "Le prénom doit contenir au moins 2 caractères.";
        }
    }
    
    // Validation de l'email
    if (empty($_POST["email"])) {
        $errors['email'] = "L'email est obligatoire.";
    } else {
        $email = $field_values['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format d'email invalide.";
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $errors['email'] = "Format d'email invalide. Exemple: exemple@domaine.com";
        }
    }
    
    // Validation de la date de naissance
    if (empty($_POST["date_naissance"])) {
        $errors['date_naissance'] = "La date de naissance est obligatoire.";
    } else {
        $date_naissance = $field_values['date_naissance'];
        $today = date("Y-m-d");
        $min_date = date("Y-m-d", strtotime("-100 years"));
        $max_date = date("Y-m-d", strtotime("-10 years")); // Au moins 10 ans
        
        if ($date_naissance > $today) {
            $errors['date_naissance'] = "La date de naissance ne peut pas être dans le futur.";
        } elseif ($date_naissance < $min_date) {
            $errors['date_naissance'] = "Veuillez entrer une date de naissance valide.";
        } elseif ($date_naissance > $max_date) {
            $errors['date_naissance'] = "Vous devez avoir au moins 10 ans pour vous inscrire.";
        }
    }
    
    // Validation du sexe
    if (empty($_POST["sexe"])) {
        $errors['sexe'] = "Le sexe est obligatoire.";
    } elseif (!in_array($_POST["sexe"], ['M', 'F'])) {
        $errors['sexe'] = "Veuillez sélectionner un sexe valide.";
    }
    
    // Validation du contact
    if (empty($_POST["contact"])) {
        $errors['contact'] = "Le contact est obligatoire.";
    } else {
        $contact = $field_values['contact'];
        // Validation simple du numéro de téléphone
        if (!preg_match('/^[0-9\s\-\+\(\)]{8,20}$/', $contact)) {
            $errors['contact'] = "Format de contact invalide. Utilisez uniquement des chiffres, espaces, tirets, plus et parenthèses.";
        }
    }
    
    // Validation du mot de passe
    if (empty($_POST["password"])) {
        $errors['password'] = "Le mot de passe est obligatoire.";
    } else {
        $password = verification_donnees($_POST["password"]);
        if (strlen($password) < 8) {
            $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
    }
    
    // Validation de la confirmation du mot de passe
    if (empty($_POST["confirm_password"])) {
        $errors['confirm_password'] = "Veuillez confirmer votre mot de passe.";
    } elseif (isset($_POST["password"]) && $_POST["password"] !== $_POST["confirm_password"]) {
        $errors['confirm_password'] = "Les mots de passe ne correspondent pas.";
    }
    
    // Validation des conditions
    if (!isset($_POST["terms"])) {
        $errors['terms'] = "Vous devez accepter les conditions d'utilisation.";
    }
    
    // Si aucune erreur, procéder à l'inscription
    if (empty($errors)) {
        include("../connexion-bases.php");
        
        // Formater les données finales
        $nom_etudiant = strtoupper(verification_donnees($_POST["nom"]));
        $prenom_etudiant = ucfirst(strtolower(verification_donnees($_POST["prenom"])));
        $email_etudiant = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        $password_etudiant = password_hash(verification_donnees($_POST["password"]), PASSWORD_DEFAULT);
        $date_naissance = verification_donnees($_POST["date_naissance"]);
        $sexe = verification_donnees($_POST["sexe"]);
        $role = "etudiant";
        $code = "CON";
        $annee = date("Y");
        $contact = verification_donnees($_POST["contact"]);
        
        try {
            // Vérifier si l'email existe déjà
            $check_email = $connecter->prepare("SELECT email FROM students WHERE email = :email");
            $check_email->bindParam(":email", $email_etudiant);
            $check_email->execute();
            
            if($check_email->rowCount() > 0) {
                $errors['email'] = "Cette adresse email est déjà utilisée.";
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
            
        } catch (PDOException $e) {
            $errors['general'] = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
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
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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
        
        .form-control.is-valid {
            border-color: #28a745;
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
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
        
        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
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
                                
                            </div>
                        </div>
                        
                        <!-- Section formulaire -->
                        <div class="col-lg-6">
                            <div class="form-section">
                                <div class="text-center mb-4">
                                    <h2 class="form-title">Inscription Étudiant</h2>
                                    <p class="form-subtitle">Créez votre compte étudiant</p>
                                </div>
                                
                                <!-- Message d'erreur général -->
                                <?php if(isset($errors['general'])): ?>
                                    <div class="alert alert-danger alert-custom mb-4" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i> <?= $errors['general'] ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form role="form" method="POST" action="">
                                    <!-- Informations personnelles -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="floating-label">
                                                <input type="text" name="nom" 
                                                       class="form-control <?php echo isset($errors['nom']) ? 'is-invalid' : (isset($_POST['nom']) && empty($errors['nom']) ? 'is-valid' : ''); ?>" 
                                                       placeholder=" " 
                                                       value="<?php echo htmlspecialchars($field_values['nom']); ?>" 
                                                       required>
                                                <label for="nom"><i class="fas fa-user me-2"></i>Nom *</label>
                                                <?php if(isset($errors['nom'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['nom']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="floating-label">
                                                <input type="text" name="prenom" 
                                                       class="form-control <?php echo isset($errors['prenom']) ? 'is-invalid' : (isset($_POST['prenom']) && empty($errors['prenom']) ? 'is-valid' : ''); ?>" 
                                                       placeholder=" " 
                                                       value="<?php echo htmlspecialchars($field_values['prenom']); ?>" 
                                                       required>
                                                <label for="prenom"><i class="fas fa-user me-2"></i>Prénom *</label>
                                                <?php if(isset($errors['prenom'])): ?>
                                                    <div class="invalid-feedback"><?php echo $errors['prenom']; ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="mb-3">
                                        <div class="floating-label">
                                            <input type="email" name="email" 
                                                   class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : (isset($_POST['email']) && empty($errors['email']) ? 'is-valid' : ''); ?>" 
                                                   placeholder=" " 
                                                   value="<?php echo htmlspecialchars($field_values['email']); ?>" 
                                                   required>
                                            <label for="email"><i class="fas fa-envelope me-2"></i>Email *</label>
                                            <?php if(isset($errors['email'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Date de naissance -->
                                    <div class="mb-3">
                                        <div class="floating-label">
                                            <input type="date" name="date_naissance" 
                                                   class="form-control <?php echo isset($errors['date_naissance']) ? 'is-invalid' : (isset($_POST['date_naissance']) && empty($errors['date_naissance']) ? 'is-valid' : ''); ?>" 
                                                   placeholder=" " 
                                                   value="<?php echo htmlspecialchars($field_values['date_naissance']); ?>" 
                                                   required>
                                            <label for="date_naissance"><i class="fas fa-calendar-alt me-2"></i>Date de naissance *</label>
                                            <?php if(isset($errors['date_naissance'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['date_naissance']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Sexe -->
                                    <div class="mb-3">
                                        <div class="floating-label">
                                            <select name="sexe" 
                                                    class="form-control <?php echo isset($errors['sexe']) ? 'is-invalid' : (isset($_POST['sexe']) && empty($errors['sexe']) ? 'is-valid' : ''); ?>" 
                                                    required>
                                                <option value="">Sélectionnez votre sexe</option>
                                                <option value="M" <?php echo $field_values['sexe'] == 'M' ? 'selected' : ''; ?>>Homme</option>
                                                <option value="F" <?php echo $field_values['sexe'] == 'F' ? 'selected' : ''; ?>>Femme</option>
                                            </select>
                                            <label for="sexe"><i class="fas fa-venus-mars me-2"></i>Sexe *</label>
                                            <?php if(isset($errors['sexe'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['sexe']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Contact -->
                                    <div class="mb-3">
                                        <div class="floating-label">
                                            <input type="text" name="contact" 
                                                   class="form-control <?php echo isset($errors['contact']) ? 'is-invalid' : (isset($_POST['contact']) && empty($errors['contact']) ? 'is-valid' : ''); ?>" 
                                                   placeholder=" " 
                                                   value="<?php echo htmlspecialchars($field_values['contact']); ?>" 
                                                   required>
                                            <label for="contact"><i class="fas fa-phone me-2"></i>Contact *</label>
                                            <?php if(isset($errors['contact'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['contact']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Mot de passe -->
                                    <div class="mb-3">
                                        <div class="floating-label position-relative">
                                            <input type="password" name="password" id="password" 
                                                   class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : (isset($_POST['password']) && empty($errors['password']) ? 'is-valid' : ''); ?>" 
                                                   placeholder=" " 
                                                   required>
                                            <label for="password"><i class="fas fa-lock me-2"></i>Mot de passe *</label>
                                            <button type="button" class="password-toggle" id="togglePassword">
                                                <i class="fas fa-eye" id="eyeIcon"></i>
                                            </button>
                                            <?php if(isset($errors['password'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Confirmation mot de passe -->
                                    <div class="mb-4">
                                        <div class="floating-label position-relative">
                                            <input type="password" id="confirmPassword" name="confirm_password" 
                                                   class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : (isset($_POST['confirm_password']) && empty($errors['confirm_password']) ? 'is-valid' : ''); ?>" 
                                                   placeholder=" " 
                                                   required>
                                            <label for="confirmPassword"><i class="fas fa-lock me-2"></i>Confirmer le mot de passe *</label>
                                            <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                                <i class="fas fa-eye" id="confirmEyeIcon"></i>
                                            </button>
                                            <?php if(isset($errors['confirm_password'])): ?>
                                                <div class="invalid-feedback"><?php echo $errors['confirm_password']; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Conditions -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>" 
                                               type="checkbox" 
                                               id="termsCheck" 
                                               name="terms" 
                                               <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="termsCheck">
                                            J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a> et la <a href="#" class="text-primary">politique de confidentialité</a>
                                        </label>
                                        <?php if(isset($errors['terms'])): ?>
                                            <div class="invalid-feedback" style="display: block;"><?php echo $errors['terms']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Bouton d'inscription -->
                                    <div class="mb-4">
                                        <button type="submit" name="Inscription" class="btn btn-primary-custom">
                                            <i class="fas fa-user-plus me-2"></i>Finaliser l'inscription
                                        </button>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <p class="mb-0">
                                            Vous avez déjà un compte? 
                                            <a href="connexion-etudiant.php" class="text-primary fw-bold">Connectez-vous ici</a>
                                        </p>
                                    </div>
                                </form>
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

<!-- JavaScript minimal pour le toggle mot de passe -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle mot de passe principal
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Toggle confirmation mot de passe
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmEyeIcon = document.getElementById('confirmEyeIcon');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    
    if (toggleConfirmPassword) {
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
            confirmPasswordInput.type = type;
            confirmEyeIcon.classList.toggle('fa-eye');
            confirmEyeIcon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Formatage automatique du nom en majuscules
    const nomInput = document.querySelector('input[name="nom"]');
    if (nomInput) {
        nomInput.addEventListener('blur', function() {
            this.value = this.value.toUpperCase().trim();
        });
    }
    
    // Formatage automatique du prénom (première lettre majuscule)
    const prenomInput = document.querySelector('input[name="prenom"]');
    if (prenomInput) {
        prenomInput.addEventListener('blur', function() {
            const value = this.value.trim().toLowerCase();
            this.value = value.charAt(0).toUpperCase() + value.slice(1);
        });
    }
    
    // Validation de la date de naissance côté client
    const dateInput = document.querySelector('input[name="date_naissance"]');
    if (dateInput) {
        dateInput.addEventListener('blur', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            const minDate = new Date();
            minDate.setFullYear(today.getFullYear() - 100);
            const maxDate = new Date();
            maxDate.setFullYear(today.getFullYear() - 10);
            
            if (selectedDate > today) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (selectedDate < minDate) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (selectedDate > maxDate) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    }
});
</script>

<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>