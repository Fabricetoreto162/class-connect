<?php
session_start();
$message='';
$msg_filiere="";
$msg_annee="";

if (!isset($_SESSION["admin-nom"])){
    header("Location:connexion-admin.php");
    exit();
}

if (isset($_POST["deconnexion"])){
    $_SESSION = array();
    session_destroy();
    header("Location:connexion-admin.php");
    exit();
}

include("../connexion-bases.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);




if (isset($_POST["Enregistrer_filiere"])) {
    if (!empty($_POST['nom_filiere'])  AND !empty($_POST['filiere_description']) AND !empty($_POST["amount"])  AND !empty($_POST['years']) ) {
       
          // Préparation et exécution de la requête
           function verification_saisie_filliere($filiere){
            $filiere  = trim($filiere);
            $filiere = htmlspecialchars($filiere);
            $filiere = stripslashes($filiere);
            $filiere = strip_tags($filiere);      
            return $filiere;

        }

        $nom_filliere=ucfirst(strtolower(verification_saisie_filliere($_POST["nom_filiere"]))) ;
        $amount=verification_saisie_filliere($_POST["amount"]);

      
        $description=ucfirst(strtolower(verification_saisie_filliere($_POST['filiere_description']))) ;

        $academic_year_id=$_POST['years'];
        $insertion_filliere=$connecter->prepare("INSERT INTO departments(department_name,description,amount,academic_year_id) VALUES(:department_name,:description,:amount,:academic_year_id)");
       
        $insertion_filliere->bindParam(":academic_year_id",$academic_year_id);  
        $insertion_filliere->bindParam(":department_name",$nom_filliere);
        $insertion_filliere->bindParam(":description",$description);
        $insertion_filliere->bindParam(":amount",$amount);
        $insertion_filliere->execute();



    }else{
        $message="* Veuillez renseigner les champs";
    }

    };


        // Requête pour recuperer les fillieres
$afficher_filliere=$connecter->prepare("SELECT d.*, a.year_label FROM departments d JOIN academic_years a ON d.academic_year_id = a.academic_year_id");
$afficher_filliere->execute();
if ($afficher_filliere->rowCount() == 0) {
    $msg_filiere = "* Aucune filière trouvée.";
}


// Requête pour compter le nombre total de fillieres
$compter_filliere=$connecter->prepare("SELECT COUNT(*) AS total FROM departments");
$compter_filliere->execute();
$resultat_compter=$compter_filliere->fetch();
$total_filliere=$resultat_compter["total"];
$message1="";

//suppression d'une filliere et modification
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];
    
if ($action == 'edit_filiere') {
        // Code pour afficher le formulaire de modification avec jointure
        $sql = "SELECT * FROM departments WHERE department_id = ?";
        
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);
        $filiere = $stmt->fetch();
        if (!$filiere) {
            // Gérer le cas où la filière n'existe pas
            $msg_filiere="Filière non trouvée.";
        }

    
    }
   
$message1="";
    if ($action == 'delete_filiere') {
        // Code pour supprimer dans la base de données
        $sql = "DELETE FROM departments WHERE department_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);

        $message1="* Suppression réussie !";
        // Redirection pour éviter la resoumission du formulaire  
        header("Location: fillieres-admin.php");
        exit();
    }
}


//suppression et modification d'une année académique 
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'edit_years') {
        // Code pour afficher le formulaire de modification avec jointure
        $sql = "SELECT * FROM academic_years WHERE academic_year_id = ?";
        
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);
        $annee = $stmt->fetch();
        if (!$annee) {
            // Gérer le cas où l'année n'existe pas
            $msg_annee="Année non trouvée.";
        }

    
    }

    if ($action == 'delete_years') {
        // Code pour supprimer dans la base de données
        $sql = "DELETE FROM academic_years WHERE academic_year_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);


        $message1="* Suppression réussie !";
        // Redirection pour éviter la resoumission du formulaire  
        header("Location: fillieres-admin.php");
        exit();
    }
}

//suppression et modification d'un niveau
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'edit_niveau') {
        // Code pour afficher le formulaire de modification avec jointure
        $sql = "SELECT * FROM levels WHERE level_id = ?";
        
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);
        $niveau = $stmt->fetch();
        if (!$niveau) {
            // Gérer le cas où le niveau n'existe pas
            $msg_niveau="Niveau non trouvée.";
        }

    
    }

    if ($action == 'delete_niveau') {
        // Code pour supprimer dans la base de données
        $sql = "DELETE FROM levels WHERE level_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);

        $message1="* Suppression réussie !";
        // Redirection pour éviter la resoumission du formulaire
        header("Location: fillieres-admin.php");
        exit();
    }
}

// Update d'une filière
if (isset($_POST["update_filiere"])) {
    if (
        !empty($_POST['id']) &&
        !empty($_POST['department_name']) &&
        !empty($_POST['description']) && 
        !empty($_POST['amount']) &&
        !empty($_POST["academic_year_id"])
    ) {
        $id = $_POST['id'];
        $department_name = $_POST['department_name'];
        $description = $_POST['description'];
        $amount = $_POST['amount'];
       
        $academic_year_id = $_POST['academic_year_id'];

     
        $sql = "UPDATE departments 
                SET department_name = ?,description = ?,academic_year_id = ?, amount = ? 
                WHERE department_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$department_name,$description,$academic_year_id,$amount,$id]);

        // Redirection après mise à jour
        header("Location: fillieres-admin.php");
        exit();
    } else {
        $msg_filiere = "Veuillez remplir tous les champs.";
    }
}

// Update d'une année académique
if (isset($_POST["update_years"])) {
    if (
        !empty($_POST['id']) &&
        !empty($_POST['year_label']) &&
        !empty($_POST['start_date']) &&
        !empty($_POST['end_date']) &&
        !empty($_POST['status'])
    ) {
        $id = $_POST['id'];
        $year_label = $_POST['year_label'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $status = $_POST['status'];

        
        $sql = "UPDATE academic_years 
                SET year_label = ?, start_date = ?, end_date = ?, status = ? 
                WHERE academic_year_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$year_label, $start_date, $end_date, $status, $id]);

        // Redirection après mise à jour
        header("Location: fillieres-admin.php");
        exit();
    } else {
        $msg_annee = "Veuillez remplir tous les champs.";
    }
}

// Update d'un niveau
if (isset($_POST["update_niveau"])) {
    if (
        !empty($_POST['id']) &&
        !empty($_POST['level_name'])
    ) {
        $id = $_POST['id'];
        $level_name = $_POST['level_name'];

        
        $sql = "UPDATE levels 
                SET level_name = ? 
                WHERE level_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$level_name, $id]);

        // Redirection après mise à jour
        header("Location: fillieres-admin.php");
        exit();
    } else {
        $msg_niveau = "Veuillez remplir tous les champs.";
    }
}





// Enregistrement d'un semestre
if (isset($_POST["Enregistrer_semester"])) {
  if (!empty($_POST["semester_name"]) && !empty($_POST["date_start"]) && !empty($_POST["date_end"]) && !empty($_POST["academic_year_id"])) {
    function verification_saisie_semestre($semestre){
      $semestre  = trim($semestre);
      $semestre = htmlspecialchars($semestre);
      $semestre = stripslashes($semestre);
      $semestre = strip_tags($semestre);      
      return $semestre;
    }

    $semester_name = ucfirst(strtolower(verification_saisie_semestre($_POST["semester_name"])));
    $start_date = $_POST["date_start"];
    $end_date = $_POST["date_end"];
    $academic_year_id = $_POST["academic_year_id"];

      $insertion_semestre = $connecter->prepare("INSERT INTO semesters(semester_name, start_date,end_date,academic_year_id) VALUES(:semester_name, :start_date, :end_date, :academic_year_id)");
      $insertion_semestre->bindParam(":semester_name", $semester_name);
      $insertion_semestre->bindParam(":start_date", $start_date);
      $insertion_semestre->bindParam(":end_date", $end_date);
      $insertion_semestre->bindParam(":academic_year_id", $academic_year_id);
      $insertion_semestre->execute();

      $msg_semester = "Semestre ajouté avec succès.";
    
  } else {
    $message = "* Veuillez renseigner tous les champs.";
  }
}

// Recupération des semestres pour l'affichage
$recuperation_semestre = $connecter->prepare("SELECT semesters.*, academic_years.year_label FROM semesters  JOIN academic_years  ON semesters.academic_year_id = academic_years.academic_year_id"); 
$recuperation_semestre->execute();
$semestres = $recuperation_semestre->fetchAll();
$msg_semester="";
if (!$semestres) {
    $msg_semester = "* Aucun semestre trouvé.";
}

//suppression et modification d'un semestre
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == 'edit_semester') {
        // Code pour afficher le formulaire de modification avec jointure
        $sql = "SELECT * FROM semesters WHERE semester_id = ?";
        
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);
        $semestre = $stmt->fetch();
        $msg_semester="";
        if (!$semestre) {
            // Gérer le cas où le semestre n'existe pas
            $msg_semester="Semestre non trouvée.";
        }

    
    }

    if ($action == 'delete_semester') {
        // Code pour supprimer dans la base de données
        $sql = "DELETE FROM semesters WHERE semester_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);

        $message1="* Suppression réussie !";
        // Redirection pour éviter la resoumission du formulaire  
        header("Location: fillieres-admin.php");
        exit();
    }
}

// Update d'un semestre
// Mise à jour d’un semestre
if (isset($_POST["update_semester"])) {
    if (!empty($_POST['semester_id']) && !empty($_POST['semester_name']) &&
        !empty($_POST['start_date']) && !empty($_POST['end_date']) && !empty($_POST['academic_year_id'])) {
        
        $id = $_POST['semester_id'];
        $semester_name = $_POST['semester_name'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $academic_year_id = $_POST['academic_year_id'];

        $sql = "UPDATE semesters 
                SET semester_name = ?, start_date = ?, end_date = ?, academic_year_id = ? 
                WHERE semester_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$semester_name, $start_date, $end_date, $academic_year_id, $id]);

        header("Location: fillieres-admin.php");
        exit();
    } else {
        $msg_semester = "Veuillez remplir tous les champs.";
    }
}







// Enregistrement d'un niveau  
if (isset($_POST["Enregistrer_niveau"])) {
  if (!empty($_POST["niveau"]) && !empty($_POST["department"])) {
    function verification_saisie_niveau($niveau){
      $niveau  = trim($niveau);
      $niveau = htmlspecialchars($niveau);
      $niveau = stripslashes($niveau);
      $niveau = strip_tags($niveau);      
      return $niveau;
    }

    $level_name =ucfirst(strtolower(verification_saisie_niveau($_POST["niveau"])));
    $department =$_POST["department"];
        // Vérifier si le niveau existe déjà pour la même filière
        $verifier_niveau = $connecter->prepare("SELECT * FROM levels WHERE level_name = ? AND department_id = ?");
        $verifier_niveau->execute([$level_name, $department]);
        if ($verifier_niveau->rowCount() > 0) {
            $message = "* Ce niveau existe déjà pour cette filière.";
            // Arrêter l'exécution si le niveau existe déjà
            return;
        }      
      $insertion_niveau = $connecter->prepare("INSERT INTO levels(level_name,department_id) VALUES(:level_name,:department_id)");
      $insertion_niveau->bindParam(":level_name",$level_name);
      $insertion_niveau->bindParam(":department_id",$department);
      $insertion_niveau->execute();
      $msg_niveau = "Niveau ajouté avec succès.";
   
  } else {
    $message = "* Veuillez renseigner le champ.";
  }
}



// Enregistrement d'une année académique
if (isset($_POST["Enregistrer_year"])) {
  if (!empty($_POST["year"]) && !empty($_POST["start_date"]) && !empty($_POST["end_date"]) && !empty($_POST["status"])) {
    function verification_saisie_annee($annee){
      $annee  = trim($annee);
      $annee = htmlspecialchars($annee);
      $annee = stripslashes($annee);
      $annee = strip_tags($annee);      
      return $annee;
    }

    $year = verification_saisie_annee($_POST["year"]);
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $status = $_POST["status"];

    // Vérifier si l'année académique existe déjà
    $verifier_annee = $connecter->prepare("SELECT * FROM academic_years WHERE year_label = ?");
    $verifier_annee->execute([$year]);

    if ($verifier_annee->rowCount() == 0) {
      // L'année n'existe pas, procéder à l'insertion
      $insertion_annee = $connecter->prepare("INSERT INTO academic_years(year_label, start_date, end_date, status) VALUES(?, ?, ?, ?)");
      $insertion_annee->execute([$year, $start_date, $end_date, $status]);
      $msg_annee = "Année académique ajoutée avec succès.";
    } else {
      $message = "* Cette année académique existe déjà.";
    }
  } else {
    $message = "* Veuillez renseigner tous les champs.";
  }
}

// Recupération des niveaux avec jointures de departments et années académiques pour les sélecteurs
$recuperarion_niveau = $connecter->prepare("SELECT l.*, d.department_name FROM levels l
JOIN departments d ON l.department_id = d.department_id");
$recuperarion_niveau->execute();
$niveaux = $recuperarion_niveau->fetchAll();
$msg_niveaux="";
if (!$niveaux) {
    $msg_niveaux = "* Aucun niveau trouvé.";
}


$recuperation_annee_academique = $connecter->prepare("SELECT * FROM academic_years where status='Actif'");
$recuperation_annee_academique->execute();
$annees = $recuperation_annee_academique->fetchAll();
$msg_annee="";
if (!$annees) {
    $msg_annee = "* Aucune année académique trouvée.";
}


//recuperation fillieres pour le select
$recuperation_filliere = $connecter->prepare("SELECT * FROM departments");
$recuperation_filliere->execute();
$fillieres = $recuperation_filliere->fetchAll();



?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Filières - Class Connect</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
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
            --warning: #ffd60a;
            --info: #4cc9f0;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        
        /* Header Styles */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary) !important;
        }
        
        .main-header {
            background-color: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }
        
        /* Main Container */
        .main-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: calc(100vh - 73px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            flex-shrink: 0;
            padding-inline: 15px;
            padding-bottom: 20px;
        }
        
        .sidebar .nav-link {
            color: rgb(255, 193, 7) !important;
            padding: 10px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--light);
            color: rgb(255, 193, 7) !important;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Main Content Wrapper */
        .content-wrapper {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: calc(100vh - 73px);
            overflow-y: auto;
        }
        
        /* Main Content Styles */
        .main-content {
            background: #f8f9fa;
            flex: 1;
            padding-bottom: 2rem;
        }
        
        /* Card Styles */
        .stat-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card .card-body {
            padding: 25px;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-filieres { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-annees { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-niveaux { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-semestres { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        
        /* Header Section */
        .page-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .user-dropdown {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            color: white;
            font-weight: 500;
        }
        
        .user-dropdown:hover {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            transform: translateY(-2px);
        }
        
        .date-display {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        /* Quick Action Buttons */
        .quick-action-btn {
            background: white;
            border: 2px solid transparent;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            transition: all 0.3s ease;
            color: var(--dark);
            text-decoration: none;
            display: block;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        .quick-action-btn:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            color: var(--dark);
            text-decoration: none;
        }
        
        .quick-action-btn i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        /* Section Headers */
        .section-header {
            border-left: 4px solid var(--primary);
            padding-left: 15px;
            margin: 2rem 0 1rem 0;
        }
        
        /* Filiere Cards */
        .filiere-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .filiere-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 20px;
        }
        
        .filiere-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        /* Footer Styles */
        .main-footer {
            background: white;
            border-top: 1px solid #e9ecef;
            margin-top: auto;
            flex-shrink: 0;
            width: 100%;
        }
        
        .settings-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .settings-btn:hover {
            transform: rotate(45deg);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.4);
        }
        
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                min-height: 100vh;
                top: 0;
                padding-top: 73px;
                width: 75vw !important;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0 !important;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .main-container {
                flex-direction: column;
            }
            
            .quick-action-btn {
                padding: 15px 10px;
            }
            
            .quick-action-btn i {
                font-size: 1.5rem;
            }
        }
        
        /* Animation for cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .stat-card, .filiere-card {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #2c3e50;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
        
        /* Custom scrollbar for main content */
        .content-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        
        .content-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .content-wrapper::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        .content-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--warning);
        }
        
        /* Ensure proper height calculations */
        @media (min-width: 769px) {
            .sidebar {
                height: calc(100vh - 73px);
                position: sticky;
                top: 73px;
                width: 25vw !important;
            }
        }
    </style>
</head>
<body>
    
<header class="navbar navbar-light sticky-top main-header flex-md-nowrap p-3">
    <div class="container-fluid">
        <a class="navbar-brand col-md-3 col-lg-2 me-0" href="#">
            <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning" style="font-family: cubic;">Connect</span>
        </a>
        
        <div class="d-flex align-items-center">
            <small class="text-danger me-3"><?=$message1?></small>
            <form action="" method="post" class="">
                <button type="submit" name="deconnexion" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                </button>
            </form>
        </div>
        <button style="margin-inline: 8px !important;" class="navbar-toggler bg-dark d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</header>

<div class="main-container">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar w-25 collapse">
        <div class="pt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="dashbord-admin.php">
                        <i class="fas fa-chart-line"></i>
                        Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="utilisateur-admin.php">
                        <i class="fas fa-users"></i>
                        Utilisateurs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="fillieres-admin.php">
                        <i class="fas fa-folder"></i>
                        Filières
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cours-admin.php">
                        <i class="fas fa-book"></i>
                        Matières
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="salles-admin.php">
                        <i class="fas fa-school"></i>
                        Salles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion-des-etudiant-admin.php">
                        <i class="fas fa-user-graduate"></i>
                        Gestion des étudiants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion-des-enseignants-admin.php">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Gestion des enseignants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="gestion-des-emploies-du-temps-admin.php">
                        <i class="fas fa-calendar-days"></i>
                        Gestion des emplois du temps
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="suivis-des-emargements-admin.php">
                        <i class="fas fa-file-signature"></i>
                        Suivi des émargements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="notes-et-resultats-admin.php">
                        <i class="fas fa-book-open"></i>
                        Notes et résultats
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="paiements-et-finance-admin.php">
                        <i class="fas fa-sack-dollar"></i>
                        Paiements et finances
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="cahier-de-texte.php">
                        <i class="fas fa-file-lines"></i>
                         Gestions des cahier de texte
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Content Wrapper -->
    <div class="content-wrapper col-md-9 ms-sm-auto col-lg-10 px-0">
        <!-- Main Content -->
        <main class="main-content px-md-4">
            <!-- Header Section -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-4 page-header px-4 mt-4">
                <div>
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Filières</h1>
                    <p class="text-muted mb-0">Gérez les filières, niveaux, années académiques et semestres</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                        <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$_SESSION["admin-nom"]?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 px-3">
                <!-- Total Filières -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Filières</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_filliere?></h2>
                                    <small class="text-success">Actives</small>
                                </div>
                                <div class="stat-icon stat-filieres">
                                    <i class="fas fa-folder text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actions Rapides -->
                <div class="col-xl-9">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <h6 class="text-muted mb-3">Actions Rapides</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <button type="button" class="quick-action-btn text-primary" data-bs-toggle="modal" data-bs-target="#addanneeModal">
                                        <i class="fas fa-calendar-plus"></i>
                                        <small class="fw-bold">Ajouter Année</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="quick-action-btn text-success" data-bs-toggle="modal" data-bs-target="#addNiveauModal">
                                        <i class="fas fa-layer-group"></i>
                                        <small class="fw-bold">Ajouter Niveau</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="quick-action-btn text-info" data-bs-toggle="modal" data-bs-target="#addsemesterModal">
                                        <i class="fas fa-calendar-alt"></i>
                                        <small class="fw-bold">Ajouter Semestre</small>
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <a href="#" class="quick-action-btn text-warning">
                                        <i class="fas fa-download"></i>
                                        <small class="fw-bold">Exporter</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Années Académiques -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <h3 class="section-header fw-bold text-dark">Années Académiques</h3>
                    <p class="text-danger mb-3"><?=$msg_annee?></p>
                    <div class="row g-3">
                        <?php foreach($annees as $resultat_annee): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card filiere-card">
                                <div class="card-header">
                                    <h6 class="mb-0"><?=$resultat_annee["year_label"]?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-<?=$resultat_annee["status"] == 'Actif' ? 'success' : 'secondary'?>">
                                                <?=$resultat_annee["status"]?>
                                            </span>
                                            <small class="text-muted d-block mt-1">
                                                <?=$resultat_annee["start_date"]?> - <?=$resultat_annee["end_date"]?>
                                            </small>
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-outline-primary me-1" href="fillieres-admin.php?action=edit_years&id=<?=$resultat_annee['academic_year_id']?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="fillieres-admin.php?action=delete_years&id=<?=$resultat_annee['academic_year_id']?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Semestres -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <h3 class="section-header fw-bold text-dark">Semestres</h3>
                    <p class="text-danger mb-3"><?=$msg_semester?></p>
                    <div class="row g-3">
                        <?php if (!empty($semestres)): ?>
                            <?php foreach ($semestres as $resultat_semestre): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card filiere-card h-100">
                                    <div class="card-header">
                                        <h6 class="mb-0"><?= htmlspecialchars($resultat_semestre["semester_name"]) ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <small class="text-muted">Année académique</small>
                                            <p class="mb-2 fw-bold"><?= htmlspecialchars($resultat_semestre["year_label"]) ?></p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Période</small>
                                            <p class="mb-0"><?= htmlspecialchars($resultat_semestre["start_date"]) ?> - <?= htmlspecialchars($resultat_semestre["end_date"]) ?></p>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <a class="btn btn-sm btn-outline-primary me-1" href="fillieres-admin.php?action=edit_semester&id=<?= $resultat_semestre['semester_id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="fillieres-admin.php?action=delete_semester&id=<?= $resultat_semestre['semester_id'] ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Niveaux -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <h3 class="section-header fw-bold text-dark">Niveaux</h3>
                    <p class="text-danger mb-3"><?=$msg_niveaux?></p>
                    <div class="row g-3">
                        <?php foreach($niveaux as $resultat_niveau): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card filiere-card">
                                <div class="card-header">
                                    <h6 class="mb-0"><?=$resultat_niveau["level_name"]?></h6>
                                </div>
                                 <div class="">
                                    <h6 class="mb-0 mx-2"><?=$resultat_niveau["department_name"]?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Niveau d'étude</small>
                                        </div>
                                        <div>
                                            <a class="btn btn-sm btn-outline-primary me-1" href="fillieres-admin.php?action=edit_niveau&id=<?=$resultat_niveau['level_id']?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="fillieres-admin.php?action=delete_niveau&id=<?=$resultat_niveau['level_id']?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Filières -->
            <div class="row px-3  mt-4" >
                <div class="col-12">
                    <h3 class="section-header fw-bold text-dark">Filières</h3>
                    <p class="text-danger mb-3"><?=$msg_filiere?></p>
                    <div class="row g-3 " >
                        <?php if (isset($afficher_filliere) && $afficher_filliere instanceof PDOStatement): ?>
                            <?php while($resultat_afficher=$afficher_filliere->fetch()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card filiere-card h-100" style="background-color: #c5cdd4ff;">
                                    <div class="card-header">
                                        <h6 class="mb-0"><?=$resultat_afficher["department_name"]?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <small class="text-muted">Description</small>
                                            <p class="mb-2"><?=$resultat_afficher["description"]?></p>
                                        </div>
                                       
                                        <div class="mb-3">
                                            <small class="text-muted">Année Académique</small>
                                            <p class="mb-2"><?=$resultat_afficher["year_label"]?></p>
                                        </div>
                                        <div class="mb-3">
                                            <small class="text-muted">Frais de scolarité</small>
                                            <p class="mb-0 fw-bold text-success"><?=$resultat_afficher["amount"]?> fcfa</p>
                                        </div>
                                        <div class="d-flex justify-content-end mt-3">
                                            <a class="btn btn-sm btn-outline-primary me-1" href="fillieres-admin.php?action=edit_filiere&id=<?=$resultat_afficher['department_id']?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-outline-danger" href="fillieres-admin.php?action=delete_filiere&id=<?=$resultat_afficher['department_id']?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Formulaire Ajout Filière -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter une Nouvelle Filière</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="filiereName" class="form-label">Nom de la Filière</label>
                                        <input type="text" name="nom_filiere" class="form-control" id="filiereName" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="filiere_description" class="form-label">Description</label>
                                        <textarea class="form-control" name="filiere_description" id="filiere_description" style="resize: none;" required></textarea>
                                    </div>
                                  
                                    <div class="col-md-6 mb-3">
                                        <label for="years" class="form-label">Année Académique</label>
                                        <select id="years" class="form-control" name="years" required>
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach($annees as $resultat_annee): ?>
                                                <option value="<?=$resultat_annee['academic_year_id'];?>"><?=$resultat_annee['year_label'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="amount" class="form-label">Frais de scolarité (FCFA)</label>
                                        <input type="text" id="amount" class="form-control" name="amount" required>
                                    </div>
                                </div>
                                <button type="submit" name="Enregistrer_filiere" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer la Filière
                                </button>
                                <div class="text-danger mt-3"><?=$message;?></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="main-footer bg-dark py-4 mt-auto">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-11">
                        <div class="copyright text-center text-lg-start">
                            <p class="mb-0 text-light">&copy; 2025 Class Connect. Tous droits réservés.</p>
                        </div>
                    </div>
                    <div class="col-lg-1 text-lg-end text-center mt-3 mt-lg-0">
                        <button class="btn settings-btn">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>

<!--les modal de modifications--->

      <!-- Modal pour ajouter un niveau -----> 

     <div class="modal fade" id="addNiveauModal" tabindex="-1" aria-labelledby="addNiveauModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNiveauModalLabel">
                        <i class="fa-solid fa-plus me-2"></i> Ajouter un nouvel Niveau
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="niveau" class="form-label"> Nom de Niveau</label>
                                <input type="text" name="niveau" class="form-control" id="niveau" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="departments" class="form-label"> Filières Associées</label>
                                <select name="department" id="department" class="form-control" required> 
                                  <option value="">-- Sélectionner --</option>
                                  <?php foreach($fillieres as $resultat_filliere): ?>
                                      <option value="<?=$resultat_filliere['department_id'];?>">
                                          <?=$resultat_filliere['department_name'];?>
                                      </option>
                                  <?php endforeach; ?>
                                </select>
                                
                            </div>
                            
                        </div>
                      
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="Enregistrer_niveau" class="btn btn-primary">Enregistrer</button>
                        </div>
                       
                    </form>
                </div>
               
            </div>
        </div>
    </div>





    <!-- Modal pour ajouter un semestre -----> 

     <div class="modal fade" id="addsemesterModal" tabindex="-1" aria-labelledby="addsemesterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addsemesterModalLabel">
                        <i class="fa-solid fa-plus me-2"></i> Ajouter une nouvelle Semestre 
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="semester_name" class="form-label">Semestre</label>
                                <input type="text" name="semester_name" class="form-control" id="semester_name" required>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="semester_year" class="form-label">Année Academique</label>
                                <select name="academic_year_id" id="" class="form-control" required> 
                                  <option value="">-- Sélectionner --</option>
                                  <?php foreach($annees as $resultat_annee): ?>
                                      <option value="<?=$resultat_annee['academic_year_id'];?>">
                                          <?=$resultat_annee['year_label'];?>
                                      </option>
                                  <?php endforeach; ?>
                                </select>
                            </div>
                            
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_start" class="form-label">Date de début</label>
                                <input type="date" name="date_start" class="form-control" id="date_start" required>
                            </div>
                             <div class="col-md-6 mb-3">
                                <label for="date_end" class="form-label">Date de fin</label>
                                <input type="date" name="date_end" class="form-control" id="date_end" required>
                            </div>
                            
                        </div>
                      
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="Enregistrer_semester" class="btn btn-primary">Enregistrer</button>
                        </div>
                       
                    </form>
                </div>
               
            </div>
        </div>
    </div>
      


     <!-- Modal pour ajouter une Annee actif ----->
        
     <div class="modal fade" id="addanneeModal" tabindex="-1" aria-labelledby="addanneeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addanneeModalLabel">
                        <i class="fa-solid fa-plus me-2"></i>Ajouter l'Année actif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  
                    <form method="post">

                        <div class="row  mt " >
                            <div class="col-md-6 mb-3">
                                <label for="year" class="form-label">Label year</label>
                                 <select name="year" class="form-control" id="year" >
                                 </select>
                               
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" class="form-control" id="status" >
                                  <option value="Actif">Actif</option>
                                  <option value="Inactif">Inactif</option>
                                </select>
                            </div>
                            
                        </div> 

                        <div class="row mt-3 mb-3">
                           <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date début</label>
                                <input type="date" name="start_date" value="" class="form-control" id="start_date" required>
                            </div>

                             <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date fin</label>
                                <input type="date" name="end_date" value="" class="form-control" id="end_date" required>
                            </div>
                        </div>

                       
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="Enregistrer_year" class="btn btn-primary">Enregistrer</button>
                        </div>
                       
                    </form>
                </div>
               
            </div>
        </div>
    </div>


  <!-- Modal Bootstrap pour modification filliere -->
    <?php if (isset($filiere)) : 
          $filieres = $filiere; // Renommer pour plus de clarté >
    ?>
    <div class="modal show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier la filière</h5>
            <a href="fillieres-admin.php" class="btn-close"></a>
          </div>
          <form action="" method="post">
            <div class="modal-body">
                <input type="hidden" name="id" value="<?= $filieres['department_id'] ?>">
                <div class="mb-3">
                    <label for="department_name" class="form-label">Nom de la filière</label>
                    <input type="text" class="form-control" name="department_name" id="department_name" value="<?= htmlspecialchars($filieres['department_name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" style="resize: none;" required><?= htmlspecialchars($filieres['description']) ?></textarea>

               
                
                <div class="mb-3">
                    <label for="academic_year_id" class="form-label ">Année Académique</label>
                    <select class="form-control" name="academic_year_id" id="academic_year_id" required>
                        <?php foreach($annees as $resultat_annee): ?>
                            <option value="<?=$resultat_annee['academic_year_id'];?>" <?= $resultat_annee['academic_year_id'] == $filieres['academic_year_id'] ? 'selected' : '';?>>
                                <?=$resultat_annee['year_label'];?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                  <label for="amount" class="form-label ">Montant de la Filière</label>
                  <input type="text" id="amount" class="form-control" name="amount" value="<?= htmlspecialchars($filieres['amount']) ?>" required>
                </div>

            </div>

            
            
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="window.location='fillieres-admin.php'">Annuler</button>
              <button type="submit" class="btn btn-primary" name="update_filiere">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>

</div>

<!-- Modal bootstrap pour modifier academic_years---------------->
    <?php if (isset($annee)) : 
          $year_info = $annee; // Renommer pour plus de clarté >
    ?>
    <div class="modal show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier l'année académique</h5>
            <a href="fillieres-admin.php" class="btn-close"></a>
          </div>
          <form action="" method="post">
            <div class="modal-body
">
                <input type="hidden" name="id" value="<?= $year_info['academic_year_id'] ?>">
                <div class="mb-3">
                    <label for="year_label" class="form-label">Label de l'année</label>
                    <input type="text" class="form-control" name="year_label" id="year_label" value="<?= htmlspecialchars($year_info['year_label']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Date début</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="<?= htmlspecialchars($year_info['start_date']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="end_date" class="form-label">Date fin</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="<?= htmlspecialchars($year_info['end_date']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" name="status" id="status" required>
                        <option value="Actif" <?= $year_info['status'] == 'Actif' ? 'selected' : '';?>>Actif</option>
                        <option value="Inactif" <?= $year_info['status'] == 'Inactif' ? 'selected' : '';?>>Inactif</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="window.location='fillieres-admin.php'">Annuler</button>
              <button type="submit" class="btn btn-primary" name="update_years">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>



    <!-- Modal bootstrap pour modifier niveau---------------->
    <?php if (isset($niveau)) : 
          $niveau_info = $niveau; // Renommer pour plus de clarté >
    ?>
    <div class="modal show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Modifier le niveau</h5>
            <a href="fillieres-admin.php" class="btn-close"></a>
          </div>
          <form action="" method="post">
            <div class="modal-body
">
                <input type="hidden" name="id" value="<?= $niveau_info['level_id'] ?>">
                <div class="mb-3">
                    <label for="level_name" class="form-label">Nom du niveau</label>
                    <input type="text" class="form-control" name="level_name" id="level_name" value="<?= htmlspecialchars($niveau_info['level_name']) ?>" required>
                </div>
                 <div class="mb-3">
                    <label for="level_name" class="form-label">Modifier Filières</label>
                    <select name="department" id="department" class="form-control" required> 
                      <option value="">-- Sélectionner --</option>
                      <?php foreach($fillieres as $resultat_filliere): ?>
                          <option value="<?=$resultat_filliere['department_id'];?>" <?= $resultat_filliere['department_id'] == $niveau_info['department_id'] ? 'selected' : '';?>>
                              <?=$resultat_filliere['department_name'];?>
                          </option>
                      <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="window.location='fillieres-admin.php'">Annuler</button>
              <button type="submit"  class="btn btn-primary" name="update_niveau">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Modal bootstrap pour modifier semestre---------------->
     <?php if (isset($semestre)) :
          $semestre = $semestre; // Renommer pour plus de clarté >  ?>   
    <div class="modal show d-block" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          
          <div class="modal-header">
            <h5 class="modal-title">Modifier le semestre</h5>
            <a href="fillieres-admin.php" class="btn-close"></a>
          </div>

          <form action="" method="post">
            <div class="modal-body">
              <input type="hidden" name="semester_id" value="<?= $semestre['semester_id'] ?>">

              <div class="mb-3">
                <label for="semester_name" class="form-label">Nom du semestre</label>
                <input type="text" class="form-control" name="semester_name"
                       id="semester_name" value="<?= htmlspecialchars($semestre['semester_name']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="start_date" class="form-label">Date de début</label>
                <input type="date" class="form-control" name="start_date"
                       id="start_date" value="<?= htmlspecialchars($semestre['start_date']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="end_date" class="form-label">Date de fin</label>
                <input type="date" class="form-control" name="end_date"
                       id="end_date" value="<?= htmlspecialchars($semestre['end_date']) ?>" required>
              </div>

              <div class="mb-3">
                <label for="academic_year_id" class="form-label">Année académique</label>
                <select class="form-control" name="academic_year_id" id="academic_year_id" required>
                  <?php foreach ($annees as $resultat_annee): ?>
                    <option value="<?= $resultat_annee['academic_year_id'] ?>"
                      <?= $resultat_annee['academic_year_id'] == $semestre['academic_year_id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($resultat_annee['year_label']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="window.location='fillieres-admin.php'">Annuler</button>
              <button type="submit" class="btn btn-primary" name="update_semester">Enregistrer</button>
            </div>
          </form>

        </div>
      </div>
    </div>
    <?php endif; ?>



<script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function afficherDateHeure() {
        const maintenant = new Date();
        const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
        const mois = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

        const jourSemaine = jours[maintenant.getDay()];
        const jour = maintenant.getDate();
        const moisActuel = mois[maintenant.getMonth()];
        const annee = maintenant.getFullYear();
        const heures = maintenant.getHours().toString().padStart(2, '0');
        const minutes = maintenant.getMinutes().toString().padStart(2, '0');
        const secondes = maintenant.getSeconds().toString().padStart(2, '0');

        const texte = `${jourSemaine} ${jour} ${moisActuel} ${annee} - ${heures}:${minutes}:${secondes}`;
        document.getElementById("dateHeure").innerText = texte;
    }

    setInterval(afficherDateHeure, 1000);
    afficherDateHeure();

    // Animation pour les cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .filiere-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Gestion de la hauteur du sidebar sur mobile
        function handleSidebarHeight() {
            const sidebar = document.getElementById('sidebarMenu');
            if (window.innerWidth >= 768) {
                sidebar.style.height = 'calc(100vh - 73px)';
            } else {
                sidebar.style.height = '100vh';
            }
        }
        
        window.addEventListener('resize', handleSidebarHeight);
        handleSidebarHeight();
    });

    // Script pour générer les années académiques
    (function populateSchoolYears() {
        const select = document.getElementById('year');
        const startYear = new Date().getFullYear();
        const endYear = 2099;

        select.innerHTML = '';

        for (let y = startYear; y <= endYear; y++) {
            const option = document.createElement('option');
            const label = (y)+"-"+(y + 1);
            option.value = label;
            option.textContent = label;
            select.appendChild(option);
        }

        select.selectedIndex = 0;
    })();
</script>
</body>
</html>