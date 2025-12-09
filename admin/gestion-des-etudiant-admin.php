<?php
session_start();

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
$resultat = $connecter->query("SELECT student_id, matricule, first_name, last_name, birth_date, contact, gender, email,statut FROM students");

/// recuperation des niveaux avec les filieres respectives pour l'affichage
$recuperation_niveau =$connecter->prepare("SELECT l.level_id, l.level_name, d.department_name
FROM levels l
JOIN departments d ON l.department_id = d.department_id");
$recuperation_niveau->execute();
$resultat_niveau=$recuperation_niveau->fetchAll();

// Suppression d'un étudiant
if (isset($_GET['action']) && $_GET['action'] === 'delete_etudiant' && isset($_GET['id'])) {
    $student_id = intval($_GET['id']);
    $deleteStudent = $connecter->prepare("DELETE FROM students WHERE student_id = :id");
    $deleteStudent->bindParam(':id', $student_id, PDO::PARAM_INT);
    $deleteStudent->execute();
    header("Location: gestion-des-etudiant-admin.php");
    exit();
}

// Mise à jour d'un étudiant
if (isset($_POST['update_student'])) {
    $student_id = intval($_POST['student_id']);
    $level_id = intval($_POST['level']);
    $statut = $_POST['statut'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $matricule = $_POST['matricule'];
    $birth_date = $_POST['birth_date'];
    $contact = $_POST['contact'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    
    $updateStudent = $connecter->prepare("UPDATE students 
        SET level_id = :level_id, statut = :statut, first_name = :first_name, last_name = :last_name,
            matricule = :matricule, birth_date = :birth_date, contact = :contact, gender = :gender, email = :email
        WHERE student_id = :student_id");
    $updateStudent->bindParam(':level_id', $level_id);
    $updateStudent->bindParam(':statut', $statut );
    $updateStudent->bindParam(':first_name', $first_name);
    $updateStudent->bindParam(':last_name', $last_name );
    $updateStudent->bindParam(':matricule', $matricule );
    $updateStudent->bindParam(':birth_date', $birth_date );
    $updateStudent->bindParam(':contact', $contact);
    $updateStudent->bindParam(':gender', $gender);
    $updateStudent->bindParam(':email', $email);
    $updateStudent->bindParam(':student_id', $student_id);
    $updateStudent->execute();   
    header("Location: gestion-des-etudiant-admin.php");
    exit();
}

/// recuperation des etudiants avec la jointure des niveaux et filieres
$resultat_students = $connecter->prepare( "
SELECT 
    s.student_id, s.matricule, s.first_name, s.last_name, 
    s.birth_date, s.contact, s.gender, s.email,s.statut,
    l.level_id, l.level_name, d.department_name
FROM students s
LEFT JOIN levels l ON s.level_id = l.level_id
LEFT JOIN departments d ON l.department_id = d.department_id
ORDER BY s.last_name ASC
");
$resultat_students->execute();
$resultat_etudiants = $resultat_students->fetchAll();

//compter le nombre d'etudiants
$total_etudiants = count($resultat_etudiants);

//nombre d'etudiants filles
$total_filles = $connecter->query("SELECT COUNT(*) FROM students WHERE gender = 'F'");
$total_filles = $total_filles->fetchColumn();

//nombre d'etudiants garcons
$total_garcons = $connecter->query("SELECT COUNT(*) FROM students WHERE gender = 'M'");
$total_garcons = $total_garcons->fetchColumn();

//tous les etudiants actifs
$total_actifs = $connecter->query("SELECT COUNT(*) FROM students WHERE statut = 'Actif'");
$total_actifs = $total_actifs->fetchColumn();


?>

<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Gestion des Étudiants - Class Connect</title>
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
        .stat-females { background: linear-gradient(135deg, #ff9a9e, #fecfef); }
        .stat-males { background: linear-gradient(135deg, #a1c4fd, #c2e9fb); }
        
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
                    <a class="nav-link active" href="gestion-des-etudiant-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Étudiants</h1>
                    <p class="text-muted mb-0">Gestion complète du parcours étudiant</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                        <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$_SESSION["admin-nom"];?>
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
                <!-- Total Étudiants Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Étudiants</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_etudiants?></h2>
                                    <small class="text-success">Inscrits</small>
                                </div>
                                <div class="stat-icon stat-students">
                                    <i class="fas fa-user-graduate text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filles Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiantes</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_filles?></h2>
                                </div>
                                <div class="stat-icon stat-females">
                                    <i class="fas fa-female text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Garçons Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiants</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_garcons?></h2>
                                </div>
                                <div class="stat-icon stat-males">
                                    <i class="fas fa-male text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Actifs Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiants Actifs</h6>
                                    <h2 class="fw-bold text-dark"><?=$total_actifs?></h2>
                                </div>
                                <div class="stat-icon stat-users">
                                    <i class="fas fa-user-check text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table Section -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i> Liste des Étudiants par Filière
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
                                        <li><a class="dropdown-item" href="#">Actifs</a></li>
                                        <li><a class="dropdown-item" href="#">Inactifs</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                    <i class="fas fa-plus me-1"></i> Modifier
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nom et Prénom</th>
                                            <th>Matricule</th>
                                            <th>E-mail</th>
                                            <th>Filière</th>
                                            <th>Sexe</th>
                                            <th>Date de naissance</th>
                                            <th>Niveau</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($resultat_etudiants as $etudiant) :?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?=$etudiant["first_name"]?> <?=$etudiant["last_name"]?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?=$etudiant["matricule"]?></td>
                                            <td><?=$etudiant["email"]?></td>
                                            <td><?=$etudiant['department_name']?></td>
                                            <td><?=$etudiant["gender"]?></td>
                                            <td><?=$etudiant["birth_date"]?></td>
                                            <td><?=$etudiant['level_name']?></td>
                                            <td>
                                                <small class="d-block"><i class="fas fa-phone text-muted me-1"></i><?=$etudiant["contact"]?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?=$etudiant['statut'] == 'Actif' ? 'success' : 'secondary'?>">
                                                    <?=$etudiant['statut']?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a class="btn btn-sm btn-outline-danger" 
                                                       href="gestion-des-etudiant-admin.php?action=delete_etudiant&id=<?=$etudiant['student_id']?>">
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

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Modifier un(e) étudiant(e)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="row">
                        <label for="students" class="form-label">Sélectionner un étudiant</label>
                        <select class="form-control" name="students" onchange="chargerInfos(this.value)" required>
                            <option value="">-- Sélectionner --</option>
                            <?php 
                            $resultat = $connecter->query("SELECT student_id, matricule, first_name, last_name, birth_date, contact, gender, email,statut FROM students");
                            while($retour = $resultat->fetch()): ?>
                                <option value="<?=$retour['student_id']?>">
                                    <?=$retour['first_name'] . " " . $retour['last_name']?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <label for="student_id" class="form-label">ID Étudiant</label>
                            <input type="text" name="student_id" class="form-control" id="student_id" value="" required readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="matricule" class="form-label">Matricule</label>
                            <input type="text" name="matricule" class="form-control" id="matricule" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" name="first_name" class="form-control" id="first_name" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" name="last_name" class="form-control" id="last_name" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Sexe</label>
                            <input type="text" name="gender" class="form-control" id="gender" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contact" class="form-label">Téléphone</label>
                            <input type="tel" name="contact" class="form-control" id="contact" value="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Date de naissance</label>
                            <input type="date" name="birth_date" class="form-control" id="birth_date" value="" required>
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
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" name="statut" id="statut" required>
                                <option value="">Sélectionner</option>
                                <option value="Actif">Actif</option>
                                <option value="Inactif">Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="update_student" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
function chargerInfos(student_id) {
    if (student_id === "") return;

    fetch("get_student.php?id=" + student_id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById("student_id").value = data.student_id;
                document.getElementById("matricule").value = data.matricule;
                document.getElementById("first_name").value = data.first_name;
                document.getElementById("last_name").value = data.last_name;
                document.getElementById("birth_date").value = data.birth_date;
                document.getElementById("contact").value = data.contact;
                document.getElementById("gender").value = data.gender;
                document.getElementById("email").value = data.email;
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