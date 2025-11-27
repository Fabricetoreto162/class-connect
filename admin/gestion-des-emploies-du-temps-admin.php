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

// Récupérer les subjects avec leurs jointures
$sql = "
SELECT 
    t.teacher_id, 
    t.first_name, 
    t.last_name, 
    ta.statut,
    ta.id_affectation AS id_affectation,
    t.email,
    t.phone,
    s.subject_id,
    s.subject_name,
    l.level_id,
    l.level_name,
    d.department_name,
    sem.semester_id,
    sem.semester_name,
    CONCAT(t.first_name, ' ', t.last_name) AS professeur,
    
    GROUP_CONCAT(DISTINCT s.subject_name ORDER BY s.subject_name SEPARATOR ', ') AS matieres_enseignees,
    GROUP_CONCAT(DISTINCT l.level_name ORDER BY l.level_name SEPARATOR ', ') AS niveaux,
    GROUP_CONCAT(DISTINCT sem.semester_name ORDER BY sem.semester_name SEPARATOR ', ') AS semestres,
    GROUP_CONCAT(DISTINCT d.department_name ORDER BY d.department_name SEPARATOR ', ') AS filieres

FROM teachers_affectation ta
JOIN teachers t ON ta.teacher_id = t.teacher_id
JOIN subjects s ON ta.subject_id = s.subject_id
JOIN levels l ON s.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id
JOIN semesters sem ON s.semester_id = sem.semester_id

GROUP BY t.teacher_id;
";

$recuperation_matiere = $connecter->prepare($sql);
$recuperation_matiere->execute();
$resultat_matiere = $recuperation_matiere->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les salles 
$sql="SELECT * from classrooms ";
$recupererRoom=$connecter->prepare($sql);
$recupererRoom->execute();
$resultatRoom=$recupererRoom->fetchAll();

// Récupérer l'année académique
$sql="SELECT * from academic_years";
$recuperation_year=$connecter->prepare($sql);
$recuperation_year->execute();
$resultat_annee=$recuperation_year->fetchAll();

// Traitements du formulaire d'emplois du temps
if(isset($_POST['emplois_du_temps'])) {
    // Récupération des valeurs du formulaire
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];
    $classroom_id = $_POST['classroom_id'];
    $academic_year_id = $_POST['academic_year_id'];
    $semester_id = $_POST['semester_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $day = $_POST['date_input'];
    $date_start = $_POST['date_start'];
    $date_end = $_POST['date_end'];

    $sql = "INSERT INTO schedules 
            (subject_id, teacher_id, classroom_id, academic_year_id, semester_id, start_time, end_time, day, date_start, date_end)
            VALUES
            (:subject_id, :teacher_id, :classroom_id, :academic_year_id, :semester_id, :start_time, :end_time, :day, :date_start, :date_end)";

    $insertEmplois = $connecter->prepare($sql);
    $insertEmplois->bindParam(':subject_id', $subject_id);
    $insertEmplois->bindParam(':teacher_id', $teacher_id);
    $insertEmplois->bindParam(':classroom_id', $classroom_id);
    $insertEmplois->bindParam(':academic_year_id', $academic_year_id);
    $insertEmplois->bindParam(':semester_id', $semester_id);
    $insertEmplois->bindParam(':start_time', $start_time);
    $insertEmplois->bindParam(':end_time', $end_time);
    $insertEmplois->bindParam(':day', $day);
    $insertEmplois->bindParam(':date_start', $date_start);
    $insertEmplois->bindParam(':date_end', $date_end);
    $insertEmplois->execute();
}

// Récupérer les informations avec la jointure
$sql = "
SELECT 
    s.schedule_id,
    s.day,
    s.start_time,
    s.end_time,
    s.date_start,
    s.date_end,

    -- Matière
    sub.subject_id,
    sub.subject_name,
    l.level_name,
    d.department_name,

    -- Professeur
    t.teacher_id,
    CONCAT(t.first_name, ' ', t.last_name) AS teacher_fullname,
    t.email,
    t.phone,

    -- Salle
    c.classroom_id,
    c.classroom_name,

    -- Semestre
    sem.semester_id,
    sem.semester_name,

    -- Année académique
    a.academic_year_id,
    a.year_label

FROM schedules s
-- Jointure matière et niveau et filière
JOIN subjects sub ON s.subject_id = sub.subject_id
JOIN levels l ON sub.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id

-- Jointure professeur
JOIN teachers t ON s.teacher_id = t.teacher_id

-- Jointure salle
JOIN classrooms c ON s.classroom_id = c.classroom_id

-- Jointure semestre
JOIN semesters sem ON s.semester_id = sem.semester_id

-- Jointure année académique
JOIN academic_years a ON s.academic_year_id = a.academic_year_id

ORDER BY s.day, s.start_time
";

$informations=$connecter->prepare($sql);
$informations->execute();
$resultat_Emplois=$informations->fetchAll();

// Compter les statistiques
$total_cours = count($resultat_Emplois);
$salles_utilisees = count(array_unique(array_column($resultat_Emplois, 'classroom_id')));
$enseignants_impliques = count(array_unique(array_column($resultat_Emplois, 'teacher_id')));
$departements_concernes = count(array_unique(array_column($resultat_Emplois, 'department_name')));


// supprimer une planifications

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $emplois_id= intval($_GET['id']);
    $deleteEmplois = $connecter->prepare("DELETE FROM schedules WHERE schedule_id = :id");
    $deleteEmplois->bindParam(':id', $emplois_id, PDO::PARAM_INT);
    $deleteEmplois->execute();
    header("Location: gestion-des-emploies-du-temps-admin.php");
    exit();
}






// Initialisation des variables pour éviter les erreurs "undefined"
$infos = [];
$Subjects = [];
$Teachers = [];
$Classrooms = [];
$emesters = [];
$showEditModal = false; // Pour ouvrir automatiquement le modal

// Vérifier si on veut modifier un emploi du temps
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // 1️⃣ Récupérer les infos du cours
    $sql = "
        SELECT 
            s.schedule_id,
            s.day,
            s.start_time,
            s.end_time,
            s.date_start,
            s.date_end,

            sub.subject_id,
            sub.subject_name,

            l.level_name,
            d.department_name,

            t.teacher_id,
            CONCAT(t.first_name, ' ', t.last_name) AS teacher_fullname,
            t.email,
            t.phone,

            c.classroom_id,
            c.classroom_name,

            sem.semester_id,
            sem.semester_name,

            a.academic_year_id,
            a.year_label

        FROM schedules s
        JOIN subjects sub ON s.subject_id = sub.subject_id
        JOIN levels l ON sub.level_id = l.level_id
        JOIN departments d ON l.department_id = d.department_id
        JOIN teachers t ON s.teacher_id = t.teacher_id
        JOIN classrooms c ON s.classroom_id = c.classroom_id
        JOIN semesters sem ON s.semester_id = sem.semester_id
        JOIN academic_years a ON s.academic_year_id = a.academic_year_id
        WHERE s.schedule_id = :schedule_id
    ";

    $informations = $connecter->prepare($sql);
    $informations->bindParam(":schedule_id", $id);
    $informations->execute();
    $infos = $informations->fetchAll(PDO::FETCH_ASSOC);

    // 2️⃣ Récupérer les listes nécessaires pour les <select>
    $Subjects = $connecter->query("SELECT * FROM subjects ORDER BY subject_name")->fetchAll(PDO::FETCH_ASSOC);
    $Teachers = $connecter->query("SELECT * FROM teachers ORDER BY first_name")->fetchAll(PDO::FETCH_ASSOC);
    $Classrooms = $connecter->query("SELECT * FROM classrooms ORDER BY classroom_name")->fetchAll(PDO::FETCH_ASSOC);
    $Semesters = $connecter->query("SELECT * FROM semesters ORDER BY semester_name")->fetchAll(PDO::FETCH_ASSOC);

  

    //Si on a bien trouvé l’emploi du temps, on ouvrira la modal automatiquement
    if (!empty($infos)) {
        $showEditModal = true;
    }
}


// le update du schedules 




// Vérifier si le bouton de mise à jour a été cliqué
if (isset($_POST["update_schedule"])) {

    // Vérifier si tous les champs nécessaires sont envoyés
    if (
        isset($_POST["schedule_id"], $_POST["subject_id"], $_POST["teacher_id"], $_POST["day"],
              $_POST["start_time"], $_POST["end_time"], $_POST["classroom_id"],
              $_POST["semester_id"], $_POST["date_start"], $_POST["date_end"])
    ) {
        // Sécuriser les données
        $schedule_id = intval($_POST["schedule_id"]);
        $subject_id = intval($_POST["subject_id"]);
        $teacher_id = intval($_POST["teacher_id"]);
        $day = $_POST["day"];
        $start_time = $_POST["start_time"];
        $end_time = $_POST["end_time"];
        $classroom_id = intval($_POST["classroom_id"]);
        $semester_id = intval($_POST["semester_id"]);
        $date_start = $_POST["date_start"];
        $date_end = $_POST["date_end"];

        // Requête de mise à jour
        $sql = "
            UPDATE schedules 
            SET 
                subject_id = :subject_id,
                teacher_id = :teacher_id,
                day = :day,
                start_time = :start_time,
                end_time = :end_time,
                classroom_id = :classroom_id,
                semester_id = :semester_id,
                date_start = :date_start,
                date_end = :date_end
            WHERE schedule_id = :schedule_id
        ";

        $stmt = $connecter->prepare($sql);

        // Liaison des paramètres
        $stmt->bindParam(":subject_id", $subject_id, PDO::PARAM_INT);
        $stmt->bindParam(":teacher_id", $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(":day", $day);
        $stmt->bindParam(":start_time", $start_time);
        $stmt->bindParam(":end_time", $end_time);
        $stmt->bindParam(":classroom_id", $classroom_id, PDO::PARAM_INT);
        $stmt->bindParam(":semester_id", $semester_id, PDO::PARAM_INT);
        $stmt->bindParam(":date_start", $date_start);
        $stmt->bindParam(":date_end", $date_end);
        $stmt->bindParam(":schedule_id", $schedule_id, PDO::PARAM_INT);

        // Exécuter la requête
        if ($stmt->execute()) {
            // Rediriger avec un message de succès
            $msg_sucess= "Le cours a été modifié avec succès ✅";
            header("Location:gestion-des-emploies-du-temps-admin.php"); 
            exit();
        } else {
            $message_error= "Erreur lors de la mise à jour ❌";
            header("Location:gestion-des-emploies-du-temps-admin.php");
            exit();
        }
    } else {
        $message_error= "Tous les champs ne sont pas remplis ⚠️";
        header("Location:gestion-des-emploies-du-temps-admin.php");
        exit();
    }
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
    <title>Gestion des Emplois du Temps - Class Connect</title>
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
        
        .stat-courses { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-classrooms { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-teachers { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-departments { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        
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
        
        .schedule-cell {
            min-height: 100px;
            border: 1px solid #e9ecef;
            padding: 8px;
            transition: all 0.3s ease;
        }
        
        .schedule-cell:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
        }
        
        .course-badge {
            font-size: 0.75rem;
            margin: 2px;
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
                    <a class="nav-link" href="gestion-des-enseignants-admin.php">
                        <i class="fas fa-chalkboard-teacher"></i>
                        Gestion des enseignants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="gestion-des-emploies-du-temps-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Gestion des Emplois du Temps</h1>
                    <p class="text-muted mb-0">Planification et organisation des cours</p>
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
                <!-- Total Cours Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Total Cours Planifiés</h6>
                                    <h2 class="fw-bold text-dark"><?= $total_cours ?></h2>
                                    <small class="text-success">Cette semaine</small>
                                </div>
                                <div class="stat-icon stat-courses">
                                    <i class="fas fa-calendar-alt text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salles Utilisées Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Salles Utilisées</h6>
                                    <h2 class="fw-bold text-dark"><?= $salles_utilisees ?></h2>
                                    <small class="text-success">Occupées</small>
                                </div>
                                <div class="stat-icon stat-classrooms">
                                    <i class="fas fa-school text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Enseignants Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Enseignants Impliqués</h6>
                                    <h2 class="fw-bold text-dark"><?= $enseignants_impliques ?></h2>
                                    <small class="text-success">En activité</small>
                                </div>
                                <div class="stat-icon stat-teachers">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Départements Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Départements Concernés</h6>
                                    <h2 class="fw-bold text-dark"><?= $departements_concernes ?></h2>
                                    <small class="text-info">Actifs</small>
                                </div>
                                <div class="stat-icon stat-departments">
                                    <i class="fas fa-folder text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i> Emplois du Temps par Filière et Niveau
                            </h5>
                            <div class="d-flex">
                                <div class="input-group input-group-sm me-2" style="width: 200px;">
                                    <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="dropdown me-2">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-filter"></i> Filtres
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="#" data-filter="all">Tous</a></li>
                                        <li><a class="dropdown-item" href="#" data-filter="informatique">Informatique</a></li>
                                        <li><a class="dropdown-item" href="#" data-filter="gestion">Gestion</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" data-filter="licence">Licence</a></li>
                                        <li><a class="dropdown-item" href="#" data-filter="master">Master</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                    <i class="fas fa-plus me-1"></i> Nouveau Cours
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                            <?php if (!empty($msg_sucess)): ?>
                <div class="alert alert-success"><?= $msg_sucess ?></div>
                <?php unset($msg_sucess); ?>
                <?php endif; ?>

                <?php if (!empty($message_error)): ?>
                <div class="alert alert-danger"><?= $message_error ?></div>
                <?php unset($message_error); ?>
                <?php endif; ?>


            <!-- Schedule Tables -->
            <?php
            $jours_fr = ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"];
            $jours_semaine = ["Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"];

            // Regrouper les emplois par département + niveau
            $emplois_par_groupe = [];
            foreach ($resultat_Emplois as $emploi) {
                $cle = $emploi["department_name"] . " - " . $emploi["level_name"];
                $emplois_par_groupe[$cle][] = $emploi;
            }

            // Boucle sur chaque groupe (filière + niveau)
            foreach ($emplois_par_groupe as $cle => $emplois) :
                $parts = explode(" - ", $cle);
                $departement_nom = $parts[0];
                $niveau_nom = $parts[1];

                // Regrouper les cours par horaire
                $emplois_groupes = [];
                foreach ($emplois as $emploi) {
                    $horaire = $emploi["start_time"] . " - " . $emploi["end_time"];
                    $emplois_groupes[$horaire][] = $emploi;
                }

                // Trier les horaires par heure de début
                uksort($emplois_groupes, function($a, $b) {
                    $heureA = explode(" - ", $a)[0];
                    $heureB = explode(" - ", $b)[0];
                    return strtotime($heureA) - strtotime($heureB);
                });
            ?>
            <div class="row px-3 mb-4 schedule-group" data-department="<?= strtolower($departement_nom) ?>" data-level="<?= strtolower($niveau_nom) ?>">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header py-3 bg-primary text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-calendar-alt me-2"></i> 
                                Emploi du temps – <?= htmlspecialchars($departement_nom) ?> / <?= htmlspecialchars($niveau_nom) ?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Horaire</th>
                                            <th>Lundi</th>
                                            <th>Mardi</th>
                                            <th>Mercredi</th>
                                            <th>Jeudi</th>
                                            <th>Vendredi</th>
                                            <th>Samedi</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($emplois_groupes as $horaire => $cours_par_horaire): ?>
                                            
                                            <tr>
                                                <td class="align-middle">
                                                        <?php      $date_originale = $emploi["day"];
                                                                        $date = DateTime::createFromFormat('Y-m-d', $date_originale);
                                                                        $Date=$date->format('d-m-Y')?>
                                                    <strong class='text-primary'><?=$Date;?></strong>
                                                    <strong><?= $horaire ?></strong>
                                                    <?php ;?>
                                                </td>
                                                <?php foreach ($jours_semaine as $jour): ?>
                                                    <?php
                                                    $cellule = "";
                                                    foreach ($cours_par_horaire as $emploi) {
                                                        $date = DateTime::createFromFormat('Y-m-d', $emploi["day"]);
                                                        $jour_nom = $date ? $jours_fr[$date->format('w')] : "";
                                                        if ($jour_nom === $jour) {
                                                            $date_start = DateTime::createFromFormat('Y-m-d', $emploi["date_start"]);
                                                            $date_end = DateTime::createFromFormat('Y-m-d', $emploi["date_end"]);
                                                            $periode = $date_start->format('d/m/Y') . " au " . $date_end->format('d/m/Y');

                                                            $cellule = "
                                                                <div class='schedule-cell'>
                                                                    <div class='d-flex justify-content-between align-items-start mb-2'>
                                                                        
                                                                        <strong class='text-primary'>{$emploi["subject_name"]}</strong>
                                                                        
                                                                    </div>
                                                                    <small class='d-block text-muted'>{$emploi["teacher_fullname"]}</small>
                                                                    <span class='badge bg-dark course-badge'>{$emploi["classroom_name"]}</span>
                                                                    <br>
                                                                    <small class='text-muted d-block mt-1'>Semaine du $periode</small>
                                                                </div>
                                                            ";
                                                            break;
                                                            
                                                        }
                                                    }

                                                    ?>
                                                    
                                                    <td class="p-2"><?= $cellule ?: "<div class='schedule-cell text-muted'>—</div>" ?></td>
                                                    
                                                <?php endforeach; ?>
                                                 <td>
                                                    <div class='btn-group'>
                                                                            <a class="btn btn-sm btn-outline-primary me-1"
                                                                                    href="gestion-des-emploies-du-temps-admin.php?action=edit&id=<?=$emploi['schedule_id']?>">
                                                                                    <i class="fas fa-edit"></i>
                                                                             </a>

                                                                            <a class='btn btn-sm btn-outline-danger' href='gestion-des-emploies-du-temps-admin.php?action=delete&id=<?=$emploi['schedule_id']?>'>
                                                                                <i class='fas fa-trash'></i>
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
            <?php endforeach; ?>
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

<!-- Add Schedule Modal -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addScheduleModalLabel">
                    <i class="fas fa-plus-circle me-2"></i> Planifier un nouveau cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="subject_id" class="form-label">Cours</label>
                            <select class="form-select" id="subject_id" name="subject_id" onchange="chargerInfosSubjects(this.value)" required>
                                <option value="">Sélectionner un cours</option>
                                <?php foreach($resultat_matiere as $cours) : ?>
                                    <option value="<?= $cours['subject_id'] ?>">
                                        <?= $cours['subject_name'] . " (" . $cours['level_name'] . " en " . $cours['department_name'] . ")" ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="teacher_fullname" class="form-label">Professeur</label>
                            <input type="text" class="form-control" name="teacher_fullname" id="teacher_fullname" readonly>
                            <input type="hidden" name="teacher_id" id="teacher_id">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Jour</label>
                            <input type="date" class="form-control" id="date_input" name="date_input" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Heure début</label>
                            <input type="time" class="form-control" id="startTime" name="start_time" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Heure fin</label>
                            <input type="time" class="form-control" id="endTime" name="end_time" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Salle</label>
                            <select class="form-select" id="roomSelect" name="classroom_id" required>
                                <option value="">Sélectionner une salle</option>
                                <?php foreach($resultatRoom as $classroom ): ?>
                                    <option value="<?=$classroom["classroom_id"] ?>"><?=$classroom["classroom_name"] ?></option>
                                <?php endforeach;?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Semestre</label>
                            <input type="text" id="semester_name" name="semester_name" class="form-control" readonly>
                            <input type="hidden" name="semester_id" id="semester_id">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Année académique</label>
                            <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                <option value="">Sélectionner l'année</option>
                                <?php foreach($resultat_annee as $annee): ?>
                                    <option value="<?= $annee['academic_year_id'] ?>"><?= $annee['year_label'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Période</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control" id="date_start" name="date_start" placeholder="Début">
                                <input type="date" class="form-control" id="date_end" name="date_end" placeholder="Fin">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success" name="emplois_du_temps">
                            <i class="fas fa-save me-1"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- MODALE DE MODIFICATION -->
<?php if (!empty($infos)): ?>
  <?php foreach($infos as $recuperer): ?>
  <!-- MODALE DE MODIFICATION -->
  <div class="modal fade" id="updateScheduleModal<?=$recuperer['schedule_id']?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="editForm" method="POST" action="">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="editModalLabel">Modifier le cours</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" name="schedule_id" value="<?=$recuperer['schedule_id']?>">

            <div class="row g-3">
              <!-- Cours -->
              <div class="col-md-6">
                <label class="form-label">Cours</label>
                <select class="form-select" name="subject_id" required>
                  <option value="">-- Sélectionner un cours --</option>
                  <?php foreach($Subjects as $subject): ?>
                    <option value="<?=$subject['subject_id']?>" 
                      <?=($subject['subject_id'] == ($recuperer['subject_id'] ?? '')) ? 'selected' : ''?>>
                      <?=$subject['subject_name']?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Professeur -->
              <div class="col-md-6">
                <label class="form-label">Professeur</label>
                <select class="form-select" name="teacher_id" required>
                  <option value="">-- Sélectionner un professeur --</option>
                  <?php foreach($Teachers as $teacher): ?>
                    <option value="<?=$teacher['teacher_id']?>" 
                      <?=($teacher['teacher_id'] == ($recuperer['teacher_id'] ?? '')) ? 'selected' : ''?>>
                      <?=$teacher['teacher_fullname'] ?? ($teacher['first_name'].' '.$teacher['last_name'])?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Jour -->
              <div class="col-md-6">
                <label class="form-label">Jour</label>
                <input type="date" class="form-control" name="day" value="<?=$recuperer['day'] ?? ''?>">
              </div>

              <!-- Heures -->
              <div class="col-md-3">
                <label class="form-label">Heure début</label>
                <input type="time" class="form-control" name="start_time" value="<?=$recuperer['start_time'] ?? ''?>" required>
              </div>

              <div class="col-md-3">
                <label class="form-label">Heure fin</label>
                <input type="time" class="form-control" name="end_time" value="<?=$recuperer['end_time'] ?? ''?>" required>
              </div>

              <!-- Salle -->
              <div class="col-md-6">
                <label class="form-label">Salle</label>
                <select class="form-select" name="classroom_id" required>
                  <option value="">-- Sélectionner une salle --</option>
                  <?php foreach($Classrooms as $room): ?>
                    <option value="<?=$room['classroom_id']?>" 
                      <?=($room['classroom_id'] == ($recuperer['classroom_id'] ?? '')) ? 'selected' : ''?>>
                      <?=$room['classroom_name']?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Semestre -->
              <div class="col-md-6">
                <label class="form-label">Semestre</label>
                <select class="form-select" name="semester_id" required>
                  <option value="">-- Sélectionner un semestre --</option>
                  <?php foreach($Semesters as $sem): ?>
                    <option value="<?=$sem['semester_id']?>" 
                      <?=($sem['semester_id'] == ($recuperer['semester_id'] ?? '')) ? 'selected' : ''?>>
                      <?=$sem['semester_name']?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Dates -->
              <div class="col-md-6">
                <label class="form-label">Date début</label>
                <input type="date" class="form-control" name="date_start" value="<?=$recuperer['date_start'] ?? ''?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Date fin</label>
                <input type="date" class="form-control" name="date_end" value="<?=$recuperer['date_end'] ?? ''?>" required>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-success" name="update_schedule">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
<?php endif; ?>




<script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
function chargerInfosSubjects(subject_id) {
    if (subject_id === "") return;

    fetch("get_teacher_by_subject.php?id=" + subject_id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                document.getElementById("teacher_fullname").value = "";
                document.getElementById("teacher_id").value = "";
            } else {
                // Remplit le champ texte et le champ caché
                document.getElementById("teacher_fullname").value = data.teacher_name;
                document.getElementById("teacher_id").value = data.teacher_id;
                document.getElementById("semester_name").value = data.semester_name;
                document.getElementById("semester_id").value = data.semester_id;
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

// Fonctionnalités de recherche et filtrage
document.addEventListener('DOMContentLoaded', function() {
    // Recherche
    document.getElementById('searchBtn').addEventListener('click', function() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const scheduleGroups = document.querySelectorAll('.schedule-group');
        
        scheduleGroups.forEach(group => {
            const text = group.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
            }
        });
    });

    // Filtres
    document.querySelectorAll('.dropdown-item[data-filter]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const filter = this.getAttribute('data-filter');
            const scheduleGroups = document.querySelectorAll('.schedule-group');
            
            scheduleGroups.forEach(group => {
                if (filter === 'all') {
                    group.style.display = 'block';
                } else {
                    const department = group.getAttribute('data-department');
                    const level = group.getAttribute('data-level');
                    
                    if (department.includes(filter) || level.includes(filter)) {
                        group.style.display = 'block';
                    } else {
                        group.style.display = 'none';
                    }
                }
            });
        });
    });

    // Animation pour les cartes
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

setInterval(afficherDateHeure, 1000);
afficherDateHeure();
</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($showEditModal) && isset($id)): ?>
        const modalElement = document.querySelector('#updateScheduleModal<?= $id ?>');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            // Nettoie l’URL pour éviter la réouverture si on fait F5
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    <?php endif; ?>
});
</script>


</body>
</html>