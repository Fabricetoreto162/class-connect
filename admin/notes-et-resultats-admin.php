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

// Récupération de la liste des étudiants avec département et niveau
$students_sql = "
    SELECT 
        s.student_id,
        CONCAT(s.first_name, ' ', s.last_name) AS etudiant,
        s.matricule,
        lvl.level_name,
        d.department_name
    FROM students s
    LEFT JOIN levels lvl ON s.level_id = lvl.level_id
    LEFT JOIN departments d ON lvl.department_id = d.department_id
    ORDER BY s.last_name, s.first_name
";
$students_stmt = $connecter->prepare($students_sql);
$students_stmt->execute();
$students_list = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

// --- 2) SI student_id est présent, récupérer les notes de cet étudiant ---
$selected_student = null;      // info de l'étudiant (pour l'entête "Détails")
$notes_data = [];              // tableaux des notes par matière

if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $student_id = (int) $_GET['student_id'];

    // Récupérer info générale de l'étudiant (séparé pour éviter erreurs si pas de notes)
    $info_sql = "
        SELECT s.student_id, CONCAT(s.first_name,' ', s.last_name) AS etudiant, s.matricule,
               lvl.level_name, d.department_name
        FROM students s
        LEFT JOIN levels lvl ON s.level_id = lvl.level_id
        LEFT JOIN departments d ON lvl.department_id = d.department_id
        WHERE s.student_id = :student_id
        LIMIT 1
    ";
    $info_stmt = $connecter->prepare($info_sql);
    $info_stmt->execute([':student_id' => $student_id]);
    $selected_student = $info_stmt->fetch(PDO::FETCH_ASSOC);

    // Requête pour récupérer les notes groupées par matière pour cet étudiant
   $sql = "
    SELECT 
        a.grade_id,
        a.note_date,

        -- Etudiant
        s.student_id,
        CONCAT(s.first_name, ' ', s.last_name) AS etudiant,
        s.matricule,

        -- Matière + coefficient
        sub.subject_id,
        sub.subject_name AS matiere,
        sub.coefficient,

        -- Niveau et département
        lvl.level_id,
        lvl.level_name,
        d.department_id,
        d.department_name,

        -- Semestre + année académique
        sem.semester_id,
        sem.semester_name AS semestre,
        ay.academic_year_id,
        ay.year_label AS annee_academique,

        -- Notes consolidées
        MAX(a.assignment1) AS interrogation1,
        MAX(a.assignment2) AS interrogation2,
        MAX(a.assignment3) AS interrogation3,
        MAX(a.exam1) AS devoir1,
        MAX(a.exam2) AS devoir2,

        -- Moyenne seulement si toutes les notes > 0
        CASE 
            WHEN 
                MAX(a.assignment1) > 0 AND
                MAX(a.assignment2) > 0 AND
                MAX(a.assignment3) > 0 AND
                MAX(a.exam1) > 0 AND
                MAX(a.exam2) > 0
            THEN ROUND(
                (
                    ((MAX(a.assignment1) + MAX(a.assignment2) + MAX(a.assignment3)) / 3)
                    + MAX(a.exam1) + MAX(a.exam2)
                ) / 3
            , 2)
            ELSE NULL
        END AS moyenne

    FROM assignments a
    INNER JOIN students s ON a.student_id = s.student_id
    INNER JOIN subjects sub ON a.subject_id = sub.subject_id
    INNER JOIN levels lvl ON sub.level_id = lvl.level_id
    INNER JOIN departments d ON lvl.department_id = d.department_id
    INNER JOIN semesters sem ON sub.semester_id = sem.semester_id
    INNER JOIN academic_years ay ON a.academic_year_id = ay.academic_year_id

    WHERE a.student_id = :student_id

    GROUP BY sub.subject_id
    ORDER BY sub.subject_name ASC
";


    $stmt = $connecter->prepare($sql);
    $stmt->execute([':student_id' => $student_id]);
    $notes_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Statistiques générales
$stats_sql = "
    SELECT 
        COUNT(DISTINCT s.student_id) as total_etudiants,
        COUNT(DISTINCT sub.subject_id) as total_matieres,
        COUNT(DISTINCT d.department_id) as total_departements,
        COUNT(DISTINCT a.grade_id) as total_notes
    FROM students s
    LEFT JOIN assignments a ON s.student_id = a.student_id
    LEFT JOIN subjects sub ON a.subject_id = sub.subject_id
    LEFT JOIN levels lvl ON s.level_id = lvl.level_id
    LEFT JOIN departments d ON lvl.department_id = d.department_id
";
$stats_stmt = $connecter->prepare($stats_sql);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Calcul de la moyenne générale si un étudiant est sélectionné
$moyenne_generale = 0;
$mention = "";
if ($selected_student && count($notes_data) > 0) {
    $total_coefficient = 0;
    $total_notes_ponderees = 0;
    
    foreach ($notes_data as $note) {
        if ($note['moyenne'] > 0) {
            $coefficient = $note['coefficient'] ?: 1;
            $total_notes_ponderees += $note['moyenne'] * $coefficient;
            $total_coefficient += $coefficient;
        }
    }
    
    if ($total_coefficient > 0) {
        $moyenne_generale = round($total_notes_ponderees / $total_coefficient, 2);
        
        // Déterminer la mention
        if ($moyenne_generale >= 16) {
            $mention = "Très Bien";
            $mention_class = "mention-excellent";
        } elseif ($moyenne_generale >= 14) {
            $mention = "Bien";
            $mention_class = "mention-bien";
        } elseif ($moyenne_generale >= 12) {
            $mention = "Assez Bien";
            $mention_class = "mention-passable";
        } elseif ($moyenne_generale >= 10) {
            $mention = "Passable";
            $mention_class = "mention-passable";
        } else {
            $mention = "Insuffisant";
            $mention_class = "mention-insuffisant";
        }
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Notes et Résultats - Class Connect">
    <meta name="author" content="Class Connect">
    <title>Notes et Résultats - Class Connect</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --danger: #e63946;
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
        
        .navbar-brand i {
            color: var(--warning);
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
        
        .notes-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .notes-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .notes-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 20px;
            font-weight: 600;
            border-radius: 15px 15px 0 0 !important;
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
        .stat-subjects { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-departments { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-notes { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .stat-average { background: linear-gradient(135deg, #ffd60a, #ffc300); }
        
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

        /* Notes Specific Styles */
        .data-table {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .data-table .table-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px;
            border: none;
        }
        
        .data-table table {
            margin-bottom: 0;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: var(--dark);
            padding: 15px;
        }
        
        .data-table td {
            padding: 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .data-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .data-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        /* Bulletin Styles */
        .bulletin-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .bulletin-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .student-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 15px;
        }
        
        .mention {
            padding: 15px;
            border-radius: 10px;
            margin-top: 1rem;
            text-align: center;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .mention-excellent { background: linear-gradient(135deg, #43e97b, #38f9d7); color: white; }
        .mention-bien { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        .mention-passable { background: linear-gradient(135deg, #ffd60a, #ffc300); color: #212529; }
        .mention-insuffisant { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        
        .note-cell {
            text-align: center;
            font-weight: 500;
        }
        
        .note-excellente { color: #28a745; }
        .note-bonne { color: #17a2b8; }
        .note-moyenne { color: #ffc107; }
        .note-faible { color: #dc3545; }
        
        .average-badge {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 1.1rem;
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
            
            .stat-card, .notes-card, .data-table {
                margin-bottom: 1rem;
            }
            
            .main-container {
                flex-direction: column;
            }
            
            .table-responsive {
                font-size: 0.875rem;
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
        
        .stat-card, .notes-card, .data-table, .bulletin-card {
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

        /* Style spécifique pour le PDF */
        @media print {
            .card, table {
                page-break-inside: avoid !important;
            }

            table {
                width: 100% !important;
                table-layout: fixed !important;
            }

            td, th {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .no-print {
                display: none !important;
            }
        }

        .small-text {
            font-size: 0.85rem;
        }
        
        .bulletin-pdf table {
            table-layout: fixed;
            width: 100%;
            word-wrap: break-word;
        }

        .bulletin-pdf th, .bulletin-pdf td {
            padding: 6px 8px;
        }
    </style>
</head>
<body>
    
<header class="navbar navbar-light sticky-top main-header flex-md-nowrap p-3">
    <div class="container-fluid">
        <a class="navbar-brand col-md-3 col-lg-2 me-0" href="#">
            <i class="fas fa-graduation-cap me-2"></i>Class <span class="text-warning">Connect</span>
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
                    <a class="nav-link active" href="notes-et-resultats-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Notes et Résultats</h1>
                    <p class="text-muted mb-0">Gestion des notes et résultats académiques</p>
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

            <!-- Statistics Cards -->
             <div class="row g-12 mb-4">
                <div class="col-xl-3 col-md-6 col-sm-8">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="card-title fw-bold text-primary"><?= $stats['total_etudiants'] ?></h3>
                                    <p class="card-text text-muted mb-0">Étudiants</p>
                                </div>
                                <div class="stat-icon stat-students text-white">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-8">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="card-title fw-bold text-info"><?= $stats['total_matieres'] ?></h3>
                                    <p class="card-text text-muted mb-0">Matières</p>
                                </div>
                                <div class="stat-icon stat-subjects text-white">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-8">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="card-title fw-bold text-success"><?= $stats['total_departements'] ?></h3>
                                    <p class="card-text text-muted mb-0">Départements</p>
                                </div>
                                <div class="stat-icon stat-departments text-white">
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row g-12 mb-4">
                <div class="col-xl-3 col-md-6 col-sm-8">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="card-title fw-bold text-warning"><?= $stats['total_notes'] ?></h3>
                                    <p class="card-text text-muted mb-0">Notes enregistrées</p>
                                </div>
                                <div class="stat-icon stat-notes text-white">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($selected_student && $moyenne_generale > 0): ?>
                <div class="col-xl-3 col-md-6 col-sm-8">
                    <div class="stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h3 class="card-title fw-bold text-primary"><?= $moyenne_generale ?>/20</h3>
                                    <p class="card-text text-muted mb-0">Moyenne Générale</p>
                                    <span class="badge <?= $mention_class ?>"><?= $mention ?></span>
                                </div>
                                <div class="stat-icon stat-average text-white">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Main Content Area -->
            <div class="container-fluid px-3">
                <!-- Header Card -->
                <div class="card notes-card mb-4">
                    <div class="card-header text-center">
                        <h3 class="card-title mb-0 text-white"><i class="fas fa-graduation-cap me-2"></i>Gestion des Notes et Résultats</h3>
                    </div>
                </div>

                <!-- Liste des étudiants -->
                <div class="data-table mb-4">
                    <div class="table-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="fas fa-list me-2"></i>Liste des Étudiants
                            </h5>
                            <span class="badge bg-light text-primary fs-6"><?= count($students_list) ?> étudiant(s)</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Matricule</th>
                                    <th>Département</th>
                                    <th>Niveau</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($students_list as $stu): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                                <span class="text-white fw-bold"><?= strtoupper(substr($stu['etudiant'], 0, 1)) ?></span>
                                            </div>
                                            <div class="fw-semibold"><?= htmlspecialchars($stu['etudiant']) ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?= htmlspecialchars($stu['matricule']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($stu['department_name']) ?></td>
                                    <td><?= htmlspecialchars($stu['level_name']) ?></td>
                                    <td>
                                        <a href="notes-et-resultats-admin.php?student_id=<?= $stu['student_id'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i> Voir notes
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bulletin de l'étudiant sélectionné -->
                <?php if ($selected_student): ?>
                <div class="bulletin-card">
                    <div class="bulletin-header">
                        <div class="student-avatar">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3 class="text-white mb-2">Bulletin de Notes</h3>
                        <h4 class="text-warning mb-3"><?= htmlspecialchars($selected_student['etudiant']) ?></h4>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Matricule:</strong></p>
                                <p class="mb-0"><?= htmlspecialchars($selected_student['matricule']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Département:</strong></p>
                                <p class="mb-0"><?= htmlspecialchars($selected_student['department_name']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Niveau:</strong></p>
                                <p class="mb-0"><?= htmlspecialchars($selected_student['level_name']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-1"><strong>Moyenne Générale:</strong></p>
                                <p class="mb-0 average-badge"><?= $moyenne_generale ?>/20</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body bulletin-pdf" id="bulletin-pdf">
                        <div class="table-responsive">
                            <table class="table table-bordered small-text">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Matière</th>
                                        <th>Interro 1</th>
                                        <th>Interro 2</th>
                                        <th>Interro 3</th>
                                        <th>Devoir 1</th>
                                        <th>Devoir 2</th>
                                        <th>Moyenne</th>
                                        <th>Coefficient</th>
                                        <th>Semestre</th>
                                        <th>Année</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($notes_data as $n): 
                                        $note_class = '';
                                        if ($n['moyenne'] >= 16) $note_class = 'note-excellente';
                                        elseif ($n['moyenne'] >= 14) $note_class = 'note-bonne';
                                        elseif ($n['moyenne'] >= 10) $note_class = 'note-moyenne';
                                        else $note_class = 'note-faible';
                                    ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($n['matiere']) ?></strong></td>
                                            <td class="note-cell"><?= htmlspecialchars($n['interrogation1']) ?></td>
                                            <td class="note-cell"><?= htmlspecialchars($n['interrogation2']) ?></td>
                                            <td class="note-cell"><?= htmlspecialchars($n['interrogation3']) ?></td>
                                            <td class="note-cell"><?= htmlspecialchars($n['devoir1']) ?></td>
                                            <td class="note-cell"><?= htmlspecialchars($n['devoir2']) ?></td>
                                            <td class="note-cell <?= $note_class ?>"><strong><?= $n['moyenne'] ?></strong></td>
                                            <td class="note-cell"><?= $n['coefficient'] ?></td>
                                            <td><?= $n['semestre'] ?></td>
                                            <td><?= $n['annee_academique'] ?></td>
                                            <td><?= date("d-m-Y", strtotime($n['note_date'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if ($moyenne_generale > 0): ?>
                        <div class="mt-4 text-center">
                            <div class="mention <?= $mention_class ?>">
                                <i class="fas fa-award me-2"></i>Mention: <?= $mention ?> (Moyenne: <?= $moyenne_generale ?>/20)
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer text-center no-print">
                        <button id="download-pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf me-2"></i>Télécharger le bulletin en PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-primary ms-2">
                            <i class="fas fa-print me-2"></i>Imprimer le bulletin
                        </button>
                    </div>
                </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("download-pdf").addEventListener("click", function () {
    const element = document.getElementById("bulletin-pdf");

    const options = {
        margin: 10,
        filename: 'bulletin_<?= $selected_student ? htmlspecialchars($selected_student['etudiant']) : 'etudiant' ?>.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { scale: 1, useCORS: true, letterRendering: true, scrollY: 0 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().set(options).from(element).save();
});



// Animation pour les cartes au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card, .notes-card, .data-table, .bulletin-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>

<script>
function afficherDateHeure() {
    const maintenant = new Date();

    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit"
    };

    const dateHeure = maintenant.toLocaleDateString("fr-FR", options);
    document.getElementById("dateHeure").textContent = dateHeure;
}

// Mise à jour toutes les 1 seconde
setInterval(afficherDateHeure, 1000);

// Exécution immédiate
afficherDateHeure();
</script>

</body>
</html>