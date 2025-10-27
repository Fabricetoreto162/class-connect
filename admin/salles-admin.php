<?php
session_start();

if (!isset($_SESSION["Nom"])){
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
//recuperer toutes les annees academiques
$anne_academique_resultat=$connecter->query("SELECT * FROM academic_years where status='Actif' ");
$annees=$anne_academique_resultat->fetchAll();



// Traitement du formulaire d'ajout de salle
if (isset($_POST['enregistrer'])) {
    

    // Validation des données
    if (!empty($_POST['class_name']) && !empty($_POST['capacity']) && !empty($_POST['status']) && !empty($_POST['years'])) {
        
      function validate_input_donnee($salles) {
          $salles = trim($salles);
          $salles = stripslashes($salles);
          $salles = htmlspecialchars($salles);
          $salles=strip_tags($salles);
          return $salles;
      }

      //recupere et valide les données du formulaire
      $class_name =ucfirst(validate_input_donnee($_POST['class_name']));
      $capacity = validate_input_donnee($_POST['capacity']);
      $status = validate_input_donnee($_POST['status']);
      $academic_year_id = validate_input_donnee($_POST['years']);

      //si la salle existe déja on n'ajoute pas
      $verif_salle=$connecter->prepare("SELECT * FROM classrooms WHERE classroom_name=:classroom_name");
      $verif_salle->bindParam(':classroom_name',$class_name);
      $verif_salle->execute();
      $salle_exist=$verif_salle->fetch();
      if($salle_exist){
        $msg="Cette salle existe déja.";
      }else{
        
      //Insertion dans la base de données
       $stmt=$connecter->prepare("INSERT INTO classrooms(classroom_name,capacity,statut,academic_year_id) VALUES (:classroom_name,:capacity,:statut,:academic_year_id)");
       $stmt->bindParam(':classroom_name',$class_name);
       $stmt->bindParam(':capacity',$capacity);
       $stmt->bindParam(':statut',$status);
       $stmt->bindParam(':academic_year_id',$academic_year_id);
      $stmt->execute();


       if ($stmt) {
           $msg="Salle ajoutée avec succès.";

           // Redirection vers la page des salles après l'ajout
           header("Location: salles-admin.php");
           exit();
       } else {
           $msg = "Erreur lors de l'ajout de la salle.";
       }
        
      }
      }
      }

      //recuperer toutes les salles avec la jointure avec les annees academiques
      $salles_resultat=$connecter->query("SELECT c.classroom_id, c.classroom_name, c.capacity, c.statut, a.year_label
      FROM classrooms c
      JOIN academic_years a ON c.academic_year_id = a.academic_year_id
      ORDER BY c.classroom_id DESC");
      $salles=$salles_resultat->fetchAll();
    
//supprimer une salle
if (isset($_GET['action']) && $_GET['action'] === 'delete_salle' && isset($_GET['id'])) {
    $salle_id = $_GET['id'];

    // Vérifier si la salle existe
    $verif_salle = $connecter->prepare("SELECT * FROM classrooms WHERE classroom_id = :classroom_id");
    $verif_salle->bindParam(':classroom_id', $salle_id);
    $verif_salle->execute();
    $salle_exist = $verif_salle->fetch();

    if ($salle_exist) {
        // Supprimer la salle
        $stmt = $connecter->prepare("DELETE FROM classrooms WHERE classroom_id = :classroom_id");
        $stmt->bindParam(':classroom_id', $salle_id);
        $stmt->execute();

        if ($stmt) {
            // Redirection vers la page des salles après la suppression
            header("Location: salles-admin.php");
            exit();
        } else {
            $msg = "Erreur lors de la suppression de la salle.";
        }
    } else {
        $msg = "La salle n'existe pas.";
    }
}
//Quand l'action est edit_salle
// Vérifie si une action a été passée dans l’URL
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action === 'edit_salle') {
        // Récupération de la salle à éditer
        $stmt = $connecter->prepare("SELECT * FROM classrooms WHERE classroom_id = :classroom_id");
        $stmt->bindParam(':classroom_id', $id);
        $stmt->execute();
        $salle = $stmt->fetch();

        if (!$salle) {
            $msg = "❌ La salle n'existe pas.";
        }
    }
}

// Traitement du formulaire de modification de salle
if (isset($_POST['update_salle'])) {
    // Validation des données
    if (!empty($_POST['classroom_id']) && !empty($_POST['classroom_name']) && !empty($_POST['capacity']) && !empty($_POST['status']) && !empty($_POST['academic_year_id'])) {
        
      function validate_input_donnee($data) {
          $data = trim($data);
          $data = stripslashes($data);
          $data = htmlspecialchars($data);
          $data=strip_tags($data);
          return $data;
      }

      //recupere et valide les données du formulaire
      $classroom_id = validate_input_donnee($_POST['classroom_id']);
      $classroom_name =ucfirst(validate_input_donnee($_POST['classroom_name']));
      $capacity = validate_input_donnee($_POST['capacity']);
      $status = validate_input_donnee($_POST['status']);
      $academic_year_id = validate_input_donnee($_POST['academic_year_id']);

      //si la salle existe déja on n'ajoute pas
      $verif_salle=$connecter->prepare("SELECT * FROM classrooms WHERE classroom_name=:classroom_name AND classroom_id != :classroom_id");
      $verif_salle->bindParam(':classroom_name',$classroom_name);
      $verif_salle->bindParam(':classroom_id',$classroom_id);
      $verif_salle->execute();
      $salle_exist=$verif_salle->fetch();
      if($salle_exist){
        $msg="Cette salle existe déja.";
      }else{
        
      //Mise à jour dans la base de données
       $stmt=$connecter->prepare("UPDATE classrooms SET classroom_name=:classroom_name, capacity=:capacity, statut=:statut, academic_year_id=:academic_year_id WHERE classroom_id=:classroom_id");
       $stmt->bindParam(':classroom_id',$classroom_id);
       $stmt->bindParam(':classroom_name',$classroom_name);
       $stmt->bindParam(':capacity',$capacity);
       $stmt->bindParam(':statut',$status);
       $stmt->bindParam(':academic_year_id',$academic_year_id);
      $stmt->execute();
        if ($stmt) {
            $msg="Salle modifiée avec succès.";
  
            // Redirection vers la page des salles après la modification
            header("Location: salles-admin.php");
            exit();
        } else {
            $msg = "Erreur lors de la modification de la salle.";
        }
        
        }
        }
        }

        //compter le nombre total de salles
        $total_salles_resultat=$connecter->query("SELECT COUNT(*) AS total FROM classrooms");
        $total_salles=$total_salles_resultat->fetchColumn();
        //compter le nombre de salles occupées
        $salles_occupees_resultat=$connecter->query("SELECT COUNT(*) AS total FROM classrooms WHERE statut='Occupée'");
        $salles_occupees=$salles_occupees_resultat->fetchColumn();
        //compter le nombre de salles disponibles
        $salles_disponibles_resultat=$connecter->query("SELECT COUNT(*) AS total FROM classrooms WHERE statut='Disponible'");
        $salles_disponibles=$salles_disponibles_resultat->fetchColumn();
        //compter le nombre de salles en maintenance
        $salles_maintenance_resultat=$connecter->query("SELECT COUNT(*) AS total FROM classrooms WHERE statut='En maintenance'");
        $salles_maintenance=$salles_maintenance_resultat->fetchColumn();






?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Salles - Class Connect</title>
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
        
        .stat-total { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-occupied { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-available { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-maintenance { background: linear-gradient(135deg, #ffd166, #ff9e00); }
        
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
        
        /* Table Styles */
        .table-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table-card thead {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .table-card th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table-card td {
            padding: 15px;
            vertical-align: middle;
        }
        
        /* Badge Styles */
        .badge-salle {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
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
        
        .stat-card {
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
                    <a class="nav-link" href="fillieres-admin.php">
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
                    <a class="nav-link active" href="salles-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Salles</h1>
                    <p class="text-muted mb-0">Aperçu global et gestion des salles de classe</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                        <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$_SESSION["Nom"]?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4 px-3">
                <!-- Total Salles Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Salles</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_salles;?></h2>
                                    <small class="text-primary">Toutes salles confondues</small>
                                </div>
                                <div class="stat-icon stat-total">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salles Occupées Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Salles Occupées</h6>
                                    <h2 class="fw-bold text-dark"><?=$salles_occupees;?></h2>
                                    <small class="text-danger">En cours d'utilisation</small>
                                </div>
                                <div class="stat-icon stat-occupied">
                                    <i class="fas fa-door-closed text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salles Disponibles Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Salles Disponibles</h6>
                                    <h2 class="fw-bold text-dark"><?=$salles_disponibles;?></h2>
                                    <small class="text-success">Libres pour réservation</small>
                                </div>
                                <div class="stat-icon stat-available">
                                    <i class="fas fa-door-open text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salles Maintenance Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">En Maintenance</h6>
                                    <h2 class="fw-bold text-dark"><?=$salles_maintenance;?></h2>
                                    <small class="text-warning">Hors service temporaire</small>
                                </div>
                                <div class="stat-icon stat-maintenance">
                                    <i class="fas fa-tools text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Actions -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <form class="d-flex" role="search">
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0">
                                                <i class="fas fa-search text-muted"></i>
                                            </span>
                                            <input class="form-control border-0 bg-light" type="search" placeholder="Rechercher une salle par nom ou capacité..." aria-label="Search">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSalleModal">
                                        <i class="fas fa-plus me-2"></i>Nouvelle Salle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salles Table -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Salles</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($msg)): ?>
                                <div class="alert alert-<?= strpos($msg, 'succès') !== false ? 'success' : 'danger' ?> alert-dismissible fade show mb-4">
                                    <i class="fas fa-<?= strpos($msg, 'succès') !== false ? 'check' : 'exclamation-triangle' ?> me-2"></i>
                                    <?= $msg; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-card">
                                    <thead>
                                        <tr>
                                            <th>Salle</th>
                                            <th>Capacité</th>
                                            <th>Statut</th>
                                            <th>Année Académique</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($salles as $salle_item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                        <i class="fas fa-door-open text-primary"></i>
                                                    </div>
                                                    <strong><?=$salle_item['classroom_name'];?></strong>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?=$salle_item['capacity'];?> places</span>
                                            </td>
                                            <td>
                                                <?php if($salle_item['statut']=='Disponible'): ?>
                                                    <span class="badge badge-salle bg-success"><?=$salle_item['statut'];?></span>
                                                <?php elseif($salle_item['statut']=='Occupée'): ?>
                                                    <span class="badge badge-salle bg-danger"><?=$salle_item['statut'];?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-salle bg-warning text-dark"><?=$salle_item['statut'];?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?=$salle_item['year_label'];?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="salles-admin.php?action=edit_salle&id=<?=$salle_item['classroom_id']?>" class="btn btn-sm btn-outline-primary me-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-outline-danger" href="salles-admin.php?action=delete_salle&id=<?=$salle_item['classroom_id']?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette salle ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire Ajout Salle -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ajouter une Nouvelle Salle</h4>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="class_name" class="form-label">Nom de la Salle</label>
                                        <input type="text" name="class_name" class="form-control" placeholder="Ex: Salle A12" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="capacity" class="form-label">Capacité</label>
                                        <input type="number" name="capacity" class="form-control" placeholder="Nombre de places" min="1" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select" name="status" required>
                                            <option value="Disponible">Disponible</option>
                                            <option value="Occupée">Occupée</option>
                                            <option value="En maintenance">En maintenance</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label for="years" class="form-label">Année Académique</label>
                                        <select name="years" class="form-select" required>
                                            <option value="">Sélectionner</option>
                                            <?php foreach($annees as $annee): ?>
                                                <option value="<?=$annee['academic_year_id'];?>"><?=$annee['year_label'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary" name="enregistrer">
                                    <i class="fas fa-save me-1"></i> Enregistrer la Salle
                                </button>
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

<!-- Add Salle Modal -->
<div class="modal fade" id="addSalleModal" tabindex="-1" aria-labelledby="addSalleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalleModalLabel">
                    <i class="fas fa-door-open me-2"></i> Ajouter une Nouvelle Salle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="class_name" class="form-label">Nom de la Salle</label>
                            <input type="text" name="class_name" class="form-control" placeholder="Ex: Salle A12" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacité</label>
                            <input type="number" name="capacity" class="form-control" placeholder="Nombre de places" min="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" name="status" required>
                                <option value="Disponible">Disponible</option>
                                <option value="Occupée">Occupée</option>
                                <option value="En maintenance">En maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="years" class="form-label">Année Académique</label>
                            <select name="years" class="form-select" required>
                                <option value="">Sélectionner</option>
                                <?php foreach($annees as $annee): ?>
                                    <option value="<?=$annee['academic_year_id'];?>"><?=$annee['year_label'];?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="enregistrer" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CORRECTION : Modal d'édition avec Bootstrap correct -->
<?php if (isset($salle)): ?>
<div class="modal fade" id="editSalleModal" tabindex="-1" aria-labelledby="editSalleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSalleModalLabel">
                    <i class="fas fa-edit me-2"></i> Modifier la Salle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="salles-admin.php">
                <div class="modal-body">
                    <input type="hidden" name="classroom_id" value="<?= htmlspecialchars($salle['classroom_id']) ?>">

                    <div class="mb-3">
                        <label for="classroom_name" class="form-label">Nom de la salle</label>
                        <input type="text" name="classroom_name" class="form-control" 
                               value="<?= htmlspecialchars($salle['classroom_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacité</label>
                        <input type="number" name="capacity" class="form-control" 
                               value="<?= htmlspecialchars($salle['capacity']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" class="form-select" required>
                            <option value="Disponible" <?= ($salle['statut'] == 'Disponible') ? 'selected' : '' ?>>Disponible</option>
                            <option value="Occupée" <?= ($salle['statut'] == 'Occupée') ? 'selected' : '' ?>>Occupée</option>
                            <option value="En maintenance" <?= ($salle['statut'] == 'En maintenance') ? 'selected' : '' ?>>En maintenance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Année Académique</label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">Sélectionner une année</option>
                            <?php if (!empty($annees)): ?>
                                <?php foreach ($annees as $annee): ?>
                                    <option 
                                        value="<?= htmlspecialchars($annee['academic_year_id']) ?>"
                                        <?= (isset($salle['academic_year_id']) && $salle['academic_year_id'] == $annee['academic_year_id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($annee['year_label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Aucune année disponible</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" name="update_salle">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour ouvrir automatiquement la modal d'édition -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = new bootstrap.Modal(document.getElementById('editSalleModal'));
        editModal.show();
        
        // Fermer la modal et rediriger vers la page sans paramètres
        document.getElementById('editSalleModal').addEventListener('hidden.bs.modal', function () {
            window.location.href = 'salles-admin.php';
        });
    });
</script>
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
        const cards = document.querySelectorAll('.stat-card');
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

        // Confirmation de suppression
        const deleteButtons = document.querySelectorAll('a[href*="action=delete_salle"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette salle ?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
</body>
</html>