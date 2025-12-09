<?php
session_start();

// Vérifier si l'utilisateur est un admin connecté
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
// Fin connexion à la base de données

// Récupérer tous les enseignants disponibles
$enseignants = [];
try {
    $sql = $connecter->prepare("
        SELECT DISTINCT 
            t.teacher_id,
            t.first_name,
            t.last_name,
            t.email,
            t.phone,
            COUNT(tn.note_id) as total_cahiers,
            MAX(tn.created_at) as dernier_cahier
        FROM teachers t
        LEFT JOIN teacher_notebooks tn ON t.teacher_id = tn.teacher_id
        GROUP BY t.teacher_id
        ORDER BY t.last_name, t.first_name
    ");
    $sql->execute();
    $enseignants = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des enseignants : " . $e->getMessage();
}

// Récupérer les cahiers d'un enseignant spécifique
if (isset($_GET['enseignant_id'])) {
    $enseignant_id = $_GET['enseignant_id'];
    try {
        // Récupérer les informations de l'enseignant
        $sql = $connecter->prepare("
            SELECT 
                t.teacher_id,
                t.first_name,
                t.last_name,
                t.email,
                t.phone,
                t.created_at as date_inscription
            FROM teachers t
            WHERE t.teacher_id = ?
        ");
        $sql->execute([$enseignant_id]);
        $enseignant_info = $sql->fetch(PDO::FETCH_ASSOC);

        // Récupérer les cahiers de texte de l'enseignant
        $sql = $connecter->prepare("
            SELECT 
                tn.note_id,
                tn.schedule_id,
                sub.subject_name,
                d.department_name,
                c.classroom_name,
                tn.contenu_du_cours,
                tn.objectifs,
                tn.objectifAtteint,
                tn.travail,
                tn.avis_remarques,
                tn.created_at,
                s.day,
                s.start_time,
                s.end_time
            FROM teacher_notebooks tn
            INNER JOIN schedules s ON tn.schedule_id = s.schedule_id
            INNER JOIN subjects sub ON s.subject_id = sub.subject_id
            INNER JOIN levels l ON sub.level_id = l.level_id
            INNER JOIN departments d ON l.department_id = d.department_id
            INNER JOIN classrooms c ON s.classroom_id = c.classroom_id
            WHERE tn.teacher_id = ?
            ORDER BY tn.created_at DESC
        ");
        $sql->execute([$enseignant_id]);
        $cahiers_enseignant = $sql->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Erreur lors de la récupération des cahiers : " . $e->getMessage();
    }
}

// Récupérer les statistiques générales
try {
    // Total des cahiers
    $sql = $connecter->prepare("SELECT COUNT(*) as total FROM teacher_notebooks");
    $sql->execute();
    $total_cahiers = $sql->fetch(PDO::FETCH_ASSOC)['total'];

    // Total des enseignants
    $sql = $connecter->prepare("SELECT COUNT(*) as total FROM teachers");
    $sql->execute();
    $total_enseignants = $sql->fetch(PDO::FETCH_ASSOC)['total'];

    // Cahiers ce mois
    $sql = $connecter->prepare("
        SELECT COUNT(*) as total 
        FROM teacher_notebooks 
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
    ");
    $sql->execute();
    $cahiers_mois = $sql->fetch(PDO::FETCH_ASSOC)['total'];
} catch (Exception $e) {
    $error = "Erreur lors de la récupération des statistiques : " . $e->getMessage();
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
    <title>Gestion des Cahiers de Texte - Class Connect</title>

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
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        /* Enseignant Card Styles */
        .enseignant-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid var(--primary);
            height: 100%;
        }
        
        .enseignant-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .enseignant-card .card-body {
            padding: 20px;
        }
        
        .enseignant-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 15px;
        }
        
        .enseignant-name {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .enseignant-email {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .enseignant-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Badge Styles */
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-en-cours {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }
        
        .badge-termine {
            background: linear-gradient(135deg, #5eff5e, #00d200);
            color: white;
        }
        
        .badge-urgent {
            background: linear-gradient(135deg, #ff6b6b, #ff0000);
            color: white;
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px 30px;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .table th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 20px;
        }
        
        .table td {
            padding: 15px 20px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 3px;
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: rgba(76, 201, 240, 0.1);
            color: var(--info);
        }
        
        .btn-delete {
            background: rgba(247, 37, 133, 0.1);
            color: var(--accent);
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Search bar */
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding-left: 45px;
            border-radius: 25px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        /* Filter buttons */
        .filter-btn {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
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
        
        /* Date Display Styles */
        .date-display {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        /* User Dropdown Styles */
        .user-dropdown {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .user-dropdown:hover,
        .user-dropdown:focus,
        .user-dropdown.active,
        .user-dropdown.show {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-color: var(--bs-btn-active-border-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        /* Bootstrap override for active button states */
        .btn-check:checked + .user-dropdown,
        .user-dropdown.active,
        .user-dropdown.show,
        .user-dropdown:first-child:active,
        :not(.btn-check) + .user-dropdown:active {
            color: white;
            background-color: var(--bs-btn-active-bg);
            border-color: var(--bs-btn-active-border-color);
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
        
        /* Statistics cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .stats-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        /* Cahier details card */
        .cahier-detail-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid var(--primary);
        }
        
        .cahier-detail-card.termine {
            border-left-color: var(--success);
        }
        
        .cahier-detail-card.non-atteint {
            border-left-color: var(--accent);
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
                    <a class="nav-link active" href="cahier-de-texte.php">
                        <i class="fas fa-file-lines"></i>
                        Gestion des cahiers de texte
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Cahiers de Texte</h1>
                    <p class="text-muted mb-0">Suivi des cahiers de texte par enseignant</p>
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

            <!-- Alert Messages -->
            <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Statistics Section -->
            <div class="row mx-4 mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-user-tie fa-2x text-primary mb-3"></i>
                        <h3 class="stats-number"><?php echo $total_enseignants ?? 0; ?></h3>
                        <p class="stats-label">Enseignants</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-file-alt fa-2x text-warning mb-3"></i>
                        <h3 class="stats-number"><?php echo $total_cahiers ?? 0; ?></h3>
                        <p class="stats-label">Total Cahiers</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-calendar-check fa-2x text-success mb-3"></i>
                        <h3 class="stats-number"><?php echo $cahiers_mois ?? 0; ?></h3>
                        <p class="stats-label">Cahiers ce mois</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stats-card">
                        <i class="fas fa-chart-line fa-2x text-info mb-3"></i>
                        <h3 class="stats-number">
                            <?php 
                                if($total_enseignants > 0) {
                                    echo round($total_cahiers / $total_enseignants, 1);
                                } else {
                                    echo 0;
                                }
                            ?>
                        </h3>
                        <p class="stats-label">Moyenne par enseignant</p>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="row mx-4 mb-4">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn filter-btn active" data-filter="all">Tous</button>
                        <button class="btn filter-btn" data-filter="avec-cahiers">Avec cahiers</button>
                        <button class="btn filter-btn" data-filter="sans-cahiers">Sans cahiers</button>
                        <button class="btn filter-btn" data-filter="actifs">Actifs</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un enseignant...">
                    </div>
                </div>
            </div>

            <!-- Enseignants Cards -->
            <div class="row mx-4" id="enseignantsContainer">
                <?php if(empty($enseignants)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun enseignant n'a été trouvé.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach($enseignants as $enseignant): 
                        $initials = strtoupper(substr($enseignant['first_name'], 0, 1) . substr($enseignant['last_name'], 0, 1));
                        $totalCahiers = $enseignant['total_cahiers'] ?? 0;
                        $hasCahiers = $totalCahiers > 0;
                        $lastCahier = $enseignant['dernier_cahier'] ? date('d/m/Y', strtotime($enseignant['dernier_cahier'])) : 'Aucun';
                    ?>
                    <div class="col-md-4 col-lg-3 mb-4 enseignant-item" 
                         data-nom="<?php echo htmlspecialchars($enseignant['last_name'] . ' ' . $enseignant['first_name']); ?>"
                         data-cahiers="<?php echo $hasCahiers ? '1' : '0'; ?>"
                         data-actif="<?php echo $totalCahiers > 2 ? '1' : '0'; ?>">
                        <div class="enseignant-card">
                            <div class="card-body text-center">
                                <div class="enseignant-avatar">
                                    <?php echo $initials; ?>
                                </div>
                                
                                <div class="enseignant-name">
                                    <?php echo htmlspecialchars($enseignant['first_name'] . ' ' . $enseignant['last_name']); ?>
                                </div>
                                
                                <div class="enseignant-email">
                                    <?php echo htmlspecialchars($enseignant['email']); ?>
                                </div>
                                
                                <div class="enseignant-stats">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $totalCahiers; ?></div>
                                        <div class="stat-label">Cahiers</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number">
                                            <?php 
                                                if($totalCahiers > 0) {
                                                    echo '✓';
                                                } else {
                                                    echo '✗';
                                                }
                                            ?>
                                        </div>
                                        <div class="stat-label">Activité</div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" 
                                            class="btn btn-primary btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#enseignantModal<?php echo $enseignant['teacher_id']; ?>">
                                        <i class="fas fa-eye me-1"></i>Voir les cahiers
                                    </button>
                                </div>
                                
                                <div class="mt-3 text-muted small">
                                    Dernier cahier : <?php echo $lastCahier; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal pour cet enseignant -->
                    <div class="modal fade" id="enseignantModal<?php echo $enseignant['teacher_id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-chalkboard-teacher me-2"></i>
                                        Cahiers de texte de <?php echo htmlspecialchars($enseignant['first_name'] . ' ' . $enseignant['last_name']); ?>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Informations de l'enseignant -->
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Informations</h6>
                                                    <p class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i> <?php echo htmlspecialchars($enseignant['email']); ?></p>
                                                    <p class="mb-1"><i class="fas fa-phone me-2 text-primary"></i> <?php echo htmlspecialchars($enseignant['phone'] ?? 'Non renseigné'); ?></p>
                                                    <p class="mb-0"><i class="fas fa-file-alt me-2 text-primary"></i> <?php echo $totalCahiers; ?> cahier(s) créé(s)</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <?php 
                                                // Récupérer les cahiers pour cet enseignant
                                                try {
                                                    $sql = $connecter->prepare("
                                                        SELECT 
                                                            tn.note_id,
                                                            tn.schedule_id,
                                                            sub.subject_name,
                                                            d.department_name,
                                                            c.classroom_name,
                                                            tn.contenu_du_cours,
                                                            tn.objectifs,
                                                            tn.objectifAtteint,
                                                            tn.travail,
                                                            tn.avis_remarques,
                                                            tn.created_at,
                                                            s.day,
                                                            s.start_time,
                                                            s.end_time
                                                        FROM teacher_notebooks tn
                                                        INNER JOIN schedules s ON tn.schedule_id = s.schedule_id
                                                        INNER JOIN subjects sub ON s.subject_id = sub.subject_id
                                                        INNER JOIN levels l ON sub.level_id = l.level_id
                                                        INNER JOIN departments d ON l.department_id = d.department_id
                                                        INNER JOIN classrooms c ON s.classroom_id = c.classroom_id
                                                        WHERE tn.teacher_id = ?
                                                        ORDER BY tn.created_at DESC
                                                        LIMIT 10
                                                    ");
                                                    $sql->execute([$enseignant['teacher_id']]);
                                                    $cahiers_enseignant = $sql->fetchAll(PDO::FETCH_ASSOC);
                                                } catch (Exception $e) {
                                                    $cahiers_enseignant = [];
                                                }
                                            ?>
                                            
                                            <?php if(empty($cahiers_enseignant)): ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>Cet enseignant n'a pas encore créé de cahiers de texte.
                                                </div>
                                            <?php else: ?>
                                                <h6 class="mb-3">Derniers cahiers (<?php echo count($cahiers_enseignant); ?>)</h6>
                                                <?php foreach($cahiers_enseignant as $cahier): 
                                                    $objectifClasse = strtolower($cahier['objectifAtteint']) === 'oui' ? 'termine' : 'non-atteint';
                                                ?>
                                                <div class="cahier-detail-card <?php echo $objectifClasse; ?> mb-3">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($cahier['subject_name']); ?></h6>
                                                            <p class="mb-1 text-muted">
                                                                <i class="fas fa-calendar me-1"></i><?php echo date("d/m/Y", strtotime($cahier['created_at'])); ?> - 
                                                                <i class="fas fa-clock me-1 ms-2"></i><?php echo htmlspecialchars($cahier['start_time']); ?> - <?php echo htmlspecialchars($cahier['end_time']); ?> - 
                                                                <i class="fas fa-door-open me-1 ms-2"></i>Salle <?php echo htmlspecialchars($cahier['classroom_name']); ?>
                                                            </p>
                                                            <p class="mb-1"><strong>Filière :</strong> <?php echo htmlspecialchars($cahier['department_name']); ?></p>
                                                            <p class="mb-1"><strong>Contenu :</strong> <?php echo nl2br(htmlspecialchars(substr($cahier['contenu_du_cours'], 0, 100))); ?>...</p>
                                                            <p class="mb-0">
                                                                <strong>Objectif :</strong> 
                                                                <?php if(strtolower($cahier['objectifAtteint']) === "oui"): ?>
                                                                    <span class="badge bg-success">
                                                                        <i class="fas fa-check me-1"></i>Atteint
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">
                                                                        <i class="fas fa-times me-1"></i>Non atteint
                                                                    </span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#cahierDetails<?php echo $cahier['note_id']; ?>">
                                                            <i class="fas fa-chevron-down"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Détails supplémentaires -->
                                                    <div class="collapse mt-3" id="cahierDetails<?php echo $cahier['note_id']; ?>">
                                                        <div class="border-top pt-3">
                                                            <?php if(!empty($cahier['objectifs'])): ?>
                                                            <p><strong>Objectifs pédagogiques :</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($cahier['objectifs'])); ?></p>
                                                            <?php endif; ?>
                                                            
                                                            <?php if(!empty($cahier['travail'])): ?>
                                                            <p><strong>Travail à faire :</strong><br>
                                                            <?php echo htmlspecialchars($cahier['travail']); ?></p>
                                                            <?php endif; ?>
                                                            
                                                            <?php if(!empty($cahier['avis_remarques'])): ?>
                                                            <p><strong>Remarques :</strong><br>
                                                            <?php echo nl2br(htmlspecialchars($cahier['avis_remarques'])); ?></p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Statistiques de l'enseignant -->
                                    <?php if(!empty($cahiers_enseignant)): ?>
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h6 class="mb-3">Statistiques</h6>
                                            <div class="row">
                                                <?php 
                                                    // Calcul des statistiques
                                                    $objectifsAtteints = 0;
                                                    $objectifsNonAtteints = 0;
                                                    $dernierMois = 0;
                                                    
                                                    foreach($cahiers_enseignant as $c) {
                                                        if(strtolower($c['objectifAtteint']) === 'oui') {
                                                            $objectifsAtteints++;
                                                        } else {
                                                            $objectifsNonAtteints++;
                                                        }
                                                        
                                                        if(date('Y-m', strtotime($c['created_at'])) == date('Y-m')) {
                                                            $dernierMois++;
                                                        }
                                                    }
                                                ?>
                                                <div class="col-md-3 mb-3">
                                                    <div class="stats-card">
                                                        <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                                        <h3 class="stats-number"><?php echo $objectifsAtteints; ?></h3>
                                                        <p class="stats-label">Objectifs atteints</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="stats-card">
                                                        <i class="fas fa-times-circle fa-2x text-danger mb-3"></i>
                                                        <h3 class="stats-number"><?php echo $objectifsNonAtteints; ?></h3>
                                                        <p class="stats-label">Objectifs non atteints</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="stats-card">
                                                        <i class="fas fa-calendar-alt fa-2x text-warning mb-3"></i>
                                                        <h3 class="stats-number"><?php echo $dernierMois; ?></h3>
                                                        <p class="stats-label">Cahiers ce mois</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <div class="stats-card">
                                                        <i class="fas fa-chart-bar fa-2x text-info mb-3"></i>
                                                        <h3 class="stats-number">
                                                            <?php 
                                                                $total = count($cahiers_enseignant);
                                                                echo $total > 0 ? round(($objectifsAtteints / $total) * 100, 0) . '%' : '0%';
                                                            ?>
                                                        </h3>
                                                        <p class="stats-label">Taux de réussite</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <a href="?enseignant_id=<?php echo $enseignant['teacher_id']; ?>&export=pdf" class="btn btn-primary">
                                        <i class="fas fa-download me-2"></i>Exporter le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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

        const texte = `${jourSemaine} ${jour} ${moisActuel} ${annee} - ${heures}:${minutes}`;
        document.getElementById("dateHeure").innerText = texte;
    }

    setInterval(afficherDateHeure, 60000);
    afficherDateHeure();

    // Filter functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            filterEnseignants(filter);
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        filterEnseignants('search', searchTerm);
    });

    function filterEnseignants(filterType, searchTerm = '') {
        const items = document.querySelectorAll('.enseignant-item');
        
        items.forEach(item => {
            let show = true;
            
            if (searchTerm) {
                const nom = item.dataset.nom.toLowerCase();
                show = nom.includes(searchTerm);
            } else {
                switch(filterType) {
                    case 'all':
                        show = true;
                        break;
                    case 'avec-cahiers':
                        show = item.dataset.cahiers === '1';
                        break;
                    case 'sans-cahiers':
                        show = item.dataset.cahiers === '0';
                        break;
                    case 'actifs':
                        show = item.dataset.actif === '1';
                        break;
                }
            }
            
            item.style.display = show ? 'block' : 'none';
        });
    }

    // Animation for cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.enseignant-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
</body>
</html>