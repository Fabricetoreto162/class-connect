
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

if (isset($_POST["enregistrer"])) {
    if (!empty($_POST['subject_name'])  AND !empty($_POST['level']) AND !empty($_POST['semester']) AND !empty($_POST['coefficient'])  AND !empty($_POST['status']) ) {
       
          // Préparation et exécution de la requête
           function verification_saisie_filliere($matiere){
            $matiere  = trim($matiere);
            $matiere = htmlspecialchars($matiere);
            $matiere = stripcslashes($matiere);
            
            $matiere = strip_tags($matiere);      
            return $matiere;

        }

        $nom_matiere=ucfirst(strtolower(verification_saisie_filliere($_POST["subject_name"]))) ;
        $level=verification_saisie_filliere($_POST['level']);
        $semestres=ucfirst(strtolower(verification_saisie_filliere($_POST['semester']))) ;
        $coefficient=(verification_saisie_filliere($_POST['coefficient'])) ;
        $status=(verification_saisie_filliere($_POST['status'])) ;

        $insertion_matiere=$connecter->prepare("INSERT INTO subjects(subject_name,level_id,coefficient,status,semester_id) VALUES(:subject_name,:level_id,:coefficient,:status,:semester_id) ");
        $insertion_matiere->bindParam(":level_id",$level);
        $insertion_matiere->bindParam(":subject_name",$nom_matiere);        
        $insertion_matiere->bindParam(":semester_id",$semestres);
        $insertion_matiere->bindParam(":coefficient",$coefficient);
        $insertion_matiere->bindParam(":status",$status);
        $insertion_matiere->execute();



    }else{
        $message="* Veuillez renseigner les champs";
    }
 }





 // Recupération des semestres pour l'affichage
$recuperation_semestre =$connecter->prepare("SELECT semester_id,semester_name FROM semesters"); 
$recuperation_semestre->execute();
$resultat_semestre=$recuperation_semestre->fetchAll();

// Recupération des niveaux avec les filieres respectives pour l'affichage
$recuperation_niveau =$connecter->prepare("SELECT l.level_id, l.level_name, d.department_name
FROM levels l
JOIN departments d ON l.department_id = d.department_id");
$recuperation_niveau->execute();
$resultat_niveau=$recuperation_niveau->fetchAll();

//recuperation de la matieres avec les jointures levels et semesters pour afficher dans le tableau le nom de la matieres,le semestres le niveaux la filieres la status et le coefficient et enfin l'année academique
$recuperation_matiere =$connecter->prepare("SELECT 
    s.subject_id,
    s.subject_name,
    s.coefficient,
    s.status,
    l.level_name,
    d.department_name,
    se.semester_name,
    ay.year_label AS academic_years
FROM subjects s
JOIN levels l ON s.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id
JOIN semesters se ON s.semester_id = se.semester_id
JOIN academic_years ay ON se.academic_year_id = ay.academic_year_id
ORDER BY s.subject_id DESC;
");
$recuperation_matiere->execute();
$resultat_matiere=$recuperation_matiere->fetchAll();
//si une matiers n'est pas dans la bases de données
$message="";
if (!$resultat_matiere) {
    $message = "* Aucune matiere trouvée.";
}

//suppression d'une matiere
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $subject_id = $_GET['id'];

    // Préparer et exécuter la requête de suppression
    $delete_query = $connecter->prepare("DELETE FROM subjects WHERE subject_id = :subject_id");
    $delete_query->bindParam(':subject_id', $subject_id);
    
    if ($delete_query->execute()) {
        // Redirection après la suppression
        header("Location: cours-admin.php");
        exit();
    } else {
        $message = "Erreur lors de la suppression de la matiere.";
    }
}


// --- Édition d'une matière ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action === 'edit') {
        // Récupération de la matière à éditer
        $sql = "SELECT * FROM subjects WHERE subject_id = ?";
        $stmt = $connecter->prepare($sql);
        $stmt->execute([$id]);
        $matiere_to_edit = $stmt->fetch();

        if (!$matiere_to_edit) {
            $msg_subject = "Matière non trouvée.";
        }
    }
}


// Traitement du formulaire de mise à jour<?php
if (isset($_POST['update_subject']) && isset($_GET['id'])) {
    $subject_id = $_GET['id'];
    $subject_name = $_POST['subject_name'];
    $coefficient = $_POST['coefficient'];
    $status = $_POST['status'];
    $semester_id = $_POST['semester_id'];
    $level_id = $_POST['level_id'];
    // Validation des données (ajoutez vos propres validations si nécessaire)
    if (!empty($subject_name) && !empty($coefficient) && !empty($status) && !empty($semester_id) && !empty($level_id)) {
        // Préparation et exécution de la requête de mise à jour
        $update_query = $connecter->prepare("UPDATE subjects SET subject_name = :subject_name, coefficient = :coefficient, status = :status, semester_id = :semester_id, level_id = :level_id WHERE subject_id = :subject_id");
        $update_query->bindParam(':subject_name', $subject_name);
        $update_query->bindParam(':coefficient', $coefficient);
        $update_query->bindParam(':status', $status);
        $update_query->bindParam(':semester_id', $semester_id);
        $update_query->bindParam(':level_id', $level_id);
        $update_query->bindParam(':subject_id', $subject_id);

        if ($update_query->execute()) {
            // Redirection après la mise à jour
            header("Location: cours-admin.php");
            exit();
        } else {
            $msg_subject = "Erreur lors de la mise à jour de la matière.";
        }
    } else {
        $msg_subject = "Veuillez remplir tous les champs.";
    }
}

//comptage des matieres actifs
$compte_matiere_actif=$connecter->prepare("SELECT COUNT(*) AS total
FROM subjects
WHERE status = 'Actif'");
$compte_matiere_actif->execute();
$matiere_actif=$compte_matiere_actif->fetch();

//recuperation du nombres de fillieres
$compte_filliere=$connecter->prepare("SELECT COUNT(*) AS total FROM departments");
$compte_filliere->execute();
$filliere=$compte_filliere->fetch();



?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Matières - Class Connect</title>
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
        
        .stat-active { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-filieres { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-total { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        
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
        .badge-course {
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
                    <a class="nav-link active" href="cours-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Matières</h1>
                    <p class="text-muted mb-0">Aperçu global et gestion du catalogue des matières</p>
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
                <!-- Matières Actives Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Matières Actives</h6>
                                    <h2 class="fw-bold text-dark"><?=$matiere_actif['total']?></h2>
                                    <small class="text-success">En cours d'enseignement</small>
                                </div>
                                <div class="stat-icon stat-active">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filières Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Filières</h6>
                                    <h2 class="fw-bold text-dark"><?=$filliere['total']?></h2>
                                    <small class="text-info">Disponibles</small>
                                </div>
                                <div class="stat-icon stat-filieres">
                                    <i class="fas fa-folder text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Matières Card -->
                <div class="col-xl-4 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Matières</h6>
                                    <h2 class="fw-bold text-dark"><?=count($resultat_matiere)?></h2>
                                    <small class="text-primary">Catalogue complet</small>
                                </div>
                                <div class="stat-icon stat-total">
                                    <i class="fas fa-book text-white"></i>
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
                                            <input class="form-control border-0 bg-light" type="search" placeholder="Rechercher une matière par nom ou identifiant..." aria-label="Search">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMatiereModal">
                                        <i class="fas fa-plus me-2"></i>Nouvelle Matière
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Matières Table -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header bg-primary text-white py-3">
                            <h4 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Matières</h4>
                        </div>
                        <div class="card-body">
                            <?php if ($message): ?>
                                <div class="alert alert-warning mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?=$message?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table class="table table-hover table-card">
                                    <thead>
                                        <tr>
                                            <th>Nom de la matière</th>
                                            <th>Niveau</th>
                                            <th>Semestre</th>
                                            <th>Année Académique</th>
                                            <th>Filière</th>
                                            <th>Coefficient</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultat_matiere as $matieres): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                        <i class="fas fa-book text-primary"></i>
                                                    </div>
                                                    <strong><?=$matieres['subject_name'];?></strong>
                                                </div>
                                            </td>
                                            <td><?=$matieres['level_name'];?></td>
                                            <td>
                                                <span class="badge bg-info text-dark"><?=$matieres['semester_name'];?></span>
                                            </td>
                                            <td><?=$matieres['academic_years'];?></td>
                                            <td><?=$matieres['department_name'];?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?=$matieres['coefficient'];?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-course bg-<?=$matieres['status'] == 'Actif' ? 'success' : 'secondary';?>">
                                                    <?=$matieres['status'];?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a class="btn btn-sm btn-outline-primary me-1" href="cours-admin.php?action=edit&id=<?=$matieres['subject_id']?>">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-outline-danger" href="cours-admin.php?action=delete&id=<?=$matieres['subject_id']?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')">
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

<!-- Add Matiere Modal -->
<div class="modal fade" id="addMatiereModal" tabindex="-1" aria-labelledby="addMatiereModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMatiereModalLabel">
                    <i class="fas fa-book-medical me-2"></i> Ajouter une Nouvelle Matière
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="Matiere" class="form-label">Nom de la Matière</label>
                            <input type="text" name="subject_name" class="form-control" id="Matiere" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="Coefficient" class="form-label">Coefficient</label>
                            <input type="number" name="coefficient" class="form-control" id="Coefficient" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Status" class="form-label">Statut</label>
                            <select class="form-select" name="status" id="Status" required>
                                <option value="">Sélectionner</option>
                                <option value="Actif">Actif</option>
                                <option value="Inactif">Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semestre</label>
                            <select class="form-select" name="semester" id="semester" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($resultat_semestre as $semesters): ?>
                                    <option value="<?=$semesters['semester_id']?>">
                                        <?=htmlspecialchars($semesters['semester_name'])?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" name="level" id="niveau" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($resultat_niveau as $niveaux): ?>
                                    <option value="<?=$niveaux['level_id']?>">
                                        <?=htmlspecialchars($niveaux['level_name']." ".'('.( $niveaux['department_name']).')')?>
                                    </option>
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

<!-- Edit Matiere Modal -->
<?php if (isset($matiere_to_edit)) : ?>
<div class="modal fade show" id="editMatiereModal" tabindex="-1" aria-labelledby="editMatiereModalLabel" aria-modal="true" style="display:block; background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMatiereModalLabel">
                    <i class="fas fa-edit me-2"></i> Modifier la Matière
                </h5>
                <a href="cours-admin.php" class="btn-close"></a>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="Matiere" class="form-label">Nom de la matière</label>
                            <input type="text" name="subject_name" class="form-control" id="Matiere"
                                   value="<?= htmlspecialchars($matiere_to_edit['subject_name']) ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="Coefficient" class="form-label">Coefficient</label>
                            <input type="number" name="coefficient" class="form-control" id="Coefficient"
                                   value="<?= htmlspecialchars($matiere_to_edit['coefficient']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="Status" class="form-label">Statut</label>
                            <select class="form-select" name="status" id="Status" required>
                                <option value="">Sélectionner</option>
                                <option value="Actif" <?= ($matiere_to_edit['status'] === 'Actif') ? 'selected' : '' ?>>Actif</option>
                                <option value="Inactif" <?= ($matiere_to_edit['status'] === 'Inactif') ? 'selected' : '' ?>>Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semestre</label>
                            <select class="form-select" name="semester_id" id="semester" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($resultat_semestre as $semesters): ?>
                                    <option value="<?= $semesters['semester_id'] ?>"
                                        <?= ($matiere_to_edit['semester_id'] == $semesters['semester_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($semesters['semester_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <select class="form-select" name="level_id" id="niveau" required>
                                <option value="">Sélectionner</option>
                                <?php foreach ($resultat_niveau as $niveaux): ?>
                                    <option value="<?= $niveaux['level_id'] ?>"
                                        <?= ($matiere_to_edit['level_id'] == $niveaux['level_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($niveaux['level_name'] . ' (' . $niveaux['department_name'] . ')') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="cours-admin.php" class="btn btn-secondary">Annuler</a>
                        <button type="submit" name="update_subject" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
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
        const deleteButtons = document.querySelectorAll('a[href*="action=delete"]');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
</body>
</html>