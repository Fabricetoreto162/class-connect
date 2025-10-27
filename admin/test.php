<?php
session_start();
include("../connexion-bases.php");

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

$resultat = $connecter->query("SELECT teacher_id, first_name, last_name FROM teachers");

$sql ="SELECT * FROM subjects";
$subjects = $connecter->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST["enregistre_teacher"])) {
    if (!empty($_POST['teacher_id']) && !empty($_POST['subject_id']) && !empty($_POST['statut'])) {

        function validation_input($donnes) {
            return htmlspecialchars(strip_tags(trim($donnes)));
        }

        $teacher_id = validation_input($_POST['teacher_id']);
        $subject_id = validation_input($_POST['subject_id']);
        $statut     = validation_input($_POST['statut']);

        $insert = $connecter->prepare("INSERT INTO teachers_affectation (teacher_id, subject_id, statut)
                                       VALUES (:teacher_id, :subject_id, :statut)");
        $insert->bindParam(':teacher_id', $teacher_id);
        $insert->bindParam(':subject_id', $subject_id);
        $insert->bindParam(':statut', $statut);
        $insert->execute();
    }
}

//recuperer les information d'un enseignant depuis teachers affcetation table avec les jointures 
$sql="SELECT 
    teachers.teacher_id,
    teachers_affectation.statut,
    teachers_affectation.id_affectation AS id_affectation,
    teachers.email,
    teachers.phone,
    CONCAT(teachers.first_name, ' ', teachers.last_name) AS professeur,
    GROUP_CONCAT(subjects.subject_name ORDER BY subjects.subject_name SEPARATOR ',<br><br>') AS matieres_enseignees,
    GROUP_CONCAT(DISTINCT levels.level_name ORDER BY levels.level_name SEPARATOR ',<br><br>') AS niveaux,
    GROUP_CONCAT(DISTINCT departments.department_name ORDER BY departments.department_name SEPARATOR ',<br><br>') AS filieres
FROM teachers_affectation
JOIN teachers ON teachers_affectation.teacher_id = teachers.teacher_id
JOIN subjects ON teachers_affectation.subject_id = subjects.subject_id
JOIN levels ON subjects.level_id = levels.level_id
JOIN departments ON levels.department_id = departments.department_id
GROUP BY teachers.teacher_id;
";
$enseignants = $connecter->prepare($sql);
$enseignants->execute();
$resultat_enseignants = $enseignants->fetchAll(PDO::FETCH_ASSOC);

//supprimer un enseignant
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $teacher_id = intval($_GET['id']);
    $deleteTeacher_affectation = $connecter->prepare("DELETE FROM teachers_affectation WHERE id_affectation = :id");
    $deleteTeacher_affectation->bindParam(':id', $teacher_id, PDO::PARAM_INT);
    $deleteTeacher_affectation->execute();
    header("Location: gestion-des-enseignants-admin.php");
    exit();
}

//mettre a jour l'affectation d'un enseignant
if (isset($_POST['update_affectation'])) {
    $id_affectation = intval($_POST['id_affectation']);
    $teacher_id = intval($_POST['teacher_id']);
    $subject_id = intval($_POST['subject_id']);
    $statut = $_POST['statut'];

    $update = $connecter->prepare("UPDATE teachers_affectation SET teacher_id = :teacher_id, subject_id = :subject_id, statut = :statut WHERE id_affectation = :id_affectation");
    $update->bindParam(':teacher_id', $teacher_id);
    $update->bindParam(':subject_id', $subject_id);
    $update->bindParam(':statut', $statut);
    $update->bindParam(':id_affectation', $id_affectation);
    $update->execute();
}
?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Gestion des Enseignants - Class Connect</title>
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
        
        .stat-students { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-teachers { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-users { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-classrooms { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        
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
        
        /* Table Styles */
        .table-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .specialty-badge {
            font-size: 0.75rem;
            padding: 4px 8px;
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
                    <a class="nav-link active" href="gestion-des-enseignants-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Enseignants</h1>
                    <p class="text-muted mb-0">Affectation et gestion du personnel enseignant</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                        <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$_SESSION["Nom"];?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="" method="post" >
                                    <button type="submit" name="deconnexion" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4 px-3">
                <!-- Total Enseignants Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Enseignants</h6>
                                    <h2 class="fw-bold text-dark">10</h2>
                                    <small class="text-success">Actifs</small>
                                </div>
                                <div class="stat-icon stat-teachers">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Titulaires Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Titulaires</h6>
                                    <h2 class="fw-bold text-dark">05</h2>
                                    <small class="text-success">En activité</small>
                                </div>
                                <div class="stat-icon stat-users">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vacataires Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Vacataires</h6>
                                    <h2 class="fw-bold text-dark">04</h2>
                                    <small class="text-success">En mission</small>
                                </div>
                                <div class="stat-icon stat-classrooms">
                                    <i class="fas fa-user-clock text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- En congé Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">En congé</h6>
                                    <h2 class="fw-bold text-dark">01</h2>
                                    <small class="text-info">Absents</small>
                                </div>
                                <div class="stat-icon stat-students">
                                    <i class="fas fa-umbrella-beach text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teachers Table Section -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i> Liste des Enseignants par Niveau et Filière
                            </h5>
                            <div class="d-flex">
                                <div class="input-group input-group-sm me-2" style="width: 200px;">
                                    <input type="text" class="form-control" placeholder="Rechercher...">
                                    <button class="btn btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="dropdown me-2">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> Filtres
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="#">Tous</a></li>
                                        <li><a class="dropdown-item" href="#">Titulaires</a></li>
                                        <li><a class="dropdown-item" href="#">Vacataires</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">Par Filière</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom et Prénom</th>
                                            <th>Niveau</th>
                                            <th>Filière</th>
                                            <th>Matières enseignées</th>
                                            <th>Statut</th>
                                            <th>Téléphone</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($resultat_enseignants as $enseignant): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?=$enseignant["professeur"] ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?=$enseignant["niveaux"] ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?=$enseignant["filieres"] ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary specialty-badge"><?=$enseignant["matieres_enseignees"] ?></span>
                                            </td>
                                            <td><span class="badge bg-success"><?=$enseignant["statut"] ?></span></td>
                                            <td>
                                                <small class="d-block"><i class="fas fa-phone text-muted me-1"></i><?=$enseignant["phone"] ?></small>
                                            </td>
                                            <td>
                                                <small class="d-block"><i class="fas fa-envelope text-muted me-1"></i><?=$enseignant["email"] ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="gestion-des-enseignants-admin.php?action=edit&id=<?= $enseignant['id_affectation'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-outline-danger" href="gestion-des-enseignants-admin.php?action=delete&id=<?=$enseignant['id_affectation']?>">
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

<!-- Add Teacher Modal -->
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">   
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTeacherModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Ajouter un nouvel enseignant
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body">
                <form method="post" action="">
                    <!-- Sélection de l'enseignant -->
                    <div class="row">
                        <label for="teachers" class="form-label">Sélectionner un enseignant</label>
                        <select class="form-control" name="teacher_id" id="teachers" onchange="chargerInfos(this.value)" required>
                            <option value="">-- Sélectionner --</option>
                            <?php while($retour = $resultat->fetch()): ?>
                                <option value="<?= $retour['teacher_id'] ?>">
                                    <?= htmlspecialchars($retour['first_name'] . " " . $retour['last_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- Champs auto-remplis -->
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="teacherPhone" class="form-label">Téléphone</label>
                            <input type="number" name="phone" id="teacherPhone" class="form-control" readonly>
                        </div>
                    </div>

                    <!-- ID caché de l'enseignant -->
                    <input type="hidden" name="teacher_id_hidden" id="teacher_id">

                    <!-- Sélection matière et statut -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacherSubject" class="form-label">Matière enseignée</label>
                            <select class="form-select" id="teacherSubject" name="subject_id" required>
                                <option value="">-- Sélectionner une matière --</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= htmlspecialchars($subject['subject_id']) ?>">
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="teacherStatus" class="form-label">Statut</label>
                            <select class="form-select" id="teacherStatus" name="statut" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="titulaire">Titulaire</option>
                                <option value="vacataire">Vacataire</option>
                                <option value="contractuel">Contractuel</option>
                            </select>
                        </div>
                    </div>

                    <!-- Boutons -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" name="enregistre_teacher">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Teacher Modal -->
<div class="modal fade" id="editTeacherModal" tabindex="-1" aria-labelledby="editTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTeacherModalLabel">
                    <i class="fas fa-user-edit me-2"></i> Modifier l'affectation de l'enseignant
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="">
                    <input type="hidden" name="id_affectation" id="edit_id_affectation">

                    <div class="row">
                        <label for="edit_teachers" class="form-label">Sélectionner un enseignant</label>
                        <select class="form-control" name="teacher_id" id="edit_teachers" required>
                            <option value="">-- Sélectionner --</option>
                            <?php 
                            $allTeachers = $connecter->query("SELECT teacher_id, first_name, last_name FROM teachers");
                            while($retour = $allTeachers->fetch()): ?>
                                <option value="<?= $retour['teacher_id'] ?>">
                                    <?= htmlspecialchars($retour['first_name'] . " " . $retour['last_name']) ?>
                                </option>
                            <?php endwhile; ?>  
                        </select>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label for="edit_teacherSubject" class="form-label">Matière enseignée</label>
                            <select class="form-select" id="edit_teacherSubject" name="subject_id" required>
                                <option value="">-- Sélectionner une matière --</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= htmlspecialchars($subject['subject_id']) ?>">
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_teacherStatus" class="form-label">Statut</label>
                            <select class="form-select" id="edit_teacherStatus" name="statut" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="titulaire">Titulaire</option>
                                <option value="vacataire">Vacataire</option>
                                <option value="contractuel">Contractuel</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" name="update_affectation">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>










<script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
function chargerInfos(teacher_id) {
    if (teacher_id === "") return;

    fetch("get_enseignant.php?id=" + teacher_id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById("teacher_id").value = data.teacher_id;
                document.getElementById("nom").value = data.first_name;
                document.getElementById("prenom").value = data.last_name;
                document.getElementById("email").value = data.email;
                document.getElementById("teacherPhone").value = data.phone;
            }
        })
        .catch(error => console.error("Erreur :", error));
}

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
});
</script>
</body>
</html>