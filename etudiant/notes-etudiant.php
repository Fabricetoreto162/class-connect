<?php
session_start();
include("../connexion-bases.php");

// Vérifie la session utilisateur
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["first_name"])) {
    header("Location: connexion-etudiant.php");
    exit();
}

$student_id = $_SESSION["user_id"];
$nom_complet = $_SESSION["first_name"] . " " . $_SESSION["last_name"];

// Déconnexion
if (isset($_POST["deconnexion"])) {
    session_destroy();
    header("Location: connexion-etudiant.php");
    exit();
}

// ✅ Récupère le semestre (par défaut : Semestre 1)
$semestre = isset($_GET['semestre']) ? $_GET['semestre'] : 'Semestre 1';

// ✅ Récupération des notes
$sql = "
SELECT 
    s.student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS etudiant,
    s.matricule,
    sub.subject_name AS matiere,
    sub.coefficient,
    sem.semester_name AS semestre,
    ay.year_label AS annee_academique,
    DATE_FORMAT(a.note_date, '%d-%m-%Y') AS date_note,
    a.assignment1 AS interrogation1,
    a.assignment2 AS interrogation2,
    a.assignment3 AS interrogation3,
    a.exam1 AS devoir1,
    a.exam2 AS devoir2,
        ROUND(
    (
        -- Moyenne des assignments
        (
            (IFNULL(a.assignment1, 0) + 
             IFNULL(a.assignment2, 0) + 
             IFNULL(a.assignment3, 0)
            ) / 3
        )
        +
        -- Somme des examens
        (
            IFNULL(a.exam1, 0) + 
            IFNULL(a.exam2, 0)
        )
            
    ) / 3
, 2) AS moyenne
FROM assignments AS a
INNER JOIN students AS s ON a.student_id = s.student_id
INNER JOIN subjects AS sub ON a.subject_id = sub.subject_id
INNER JOIN semesters AS sem ON sub.semester_id = sem.semester_id
INNER JOIN academic_years AS ay ON a.academic_year_id = ay.academic_year_id
WHERE s.student_id = :student_id
  AND sem.semester_name = :semestre
ORDER BY sub.subject_name ASC
";

$stmt = $connecter->prepare($sql);
$stmt->execute([
    ':student_id' => $student_id,
    ':semestre' => $semestre
]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer les statistiques
$total_matieres = count($notes);
$total_credits = array_sum(array_column($notes, 'coefficient'));
$moyenne_generale = 0;
$notes_valides = 0;

foreach ($notes as $note) {
    if ($note['moyenne'] > 0) {
        $moyenne_generale += $note['moyenne'];
        $notes_valides++;
    }
}

$moyenne_generale = $notes_valides > 0 ? round($moyenne_generale / $notes_valides, 2) : 0;

// Déterminer l'appréciation
if ($moyenne_generale >= 16) {
    $appreciation = "Très Bien";
    $couleur_appreciation = "success";
} elseif ($moyenne_generale >= 14) {
    $appreciation = "Bien";
    $couleur_appreciation = "primary";
} elseif ($moyenne_generale >= 12) {
    $appreciation = "Assez Bien";
    $couleur_appreciation = "info";
} elseif ($moyenne_generale >= 10) {
    $appreciation = "Passable";
    $couleur_appreciation = "warning";
} else {
    $appreciation = "Insuffisant";
    $couleur_appreciation = "danger";
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
    <title>Notes - Class Connect</title>
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
        
        .stat-average { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-subjects { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-credits { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-appreciation { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        
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
        .notes-table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .notes-table thead {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .notes-table th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .notes-table td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .semester-tabs {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .semester-btn {
            padding: 12px 30px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            color: var(--dark);
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 5px;
        }
        
        .semester-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .semester-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-color: transparent;
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
                    <a class="nav-link active" aria-current="page" href="notes-etudiant.php">
                        <i class="fas fa-graduation-cap"></i>
                        Notes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="emplois-du-temps.php">
                        <i class="fas fa-calendar-days"></i>
                        Emploi du temps
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="paiements.php">
                        <i class="fas fa-sack-dollar"></i>
                        Paiements
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Mes Notes</h1>
                    <p class="text-muted mb-0">Aperçu global de mes résultats académiques</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                        <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$nom_complet;?>
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
                <!-- Moyenne Générale Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Moyenne Générale</h6>
                                    <h2 class="fw-bold text-dark"><?= $moyenne_generale ?></h2>
                                    <small class="text-<?= $couleur_appreciation ?>"><?= $appreciation ?></small>
                                </div>
                                <div class="stat-icon stat-average">
                                    <i class="fas fa-chart-line text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Matières Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Matières</h6>
                                    <h2 class="fw-bold text-dark"><?= $total_matieres ?></h2>
                                    <small class="text-success">En cours</small>
                                </div>
                                <div class="stat-icon stat-subjects">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Crédits Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Crédits Totaux</h6>
                                    <h2 class="fw-bold text-dark"><?= $total_credits ?></h2>
                                    <small class="text-info">Ce semestre</small>
                                </div>
                                <div class="stat-icon stat-credits">
                                    <i class="fas fa-award text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appréciation Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Appréciation</h6>
                                    <h2 class="fw-bold text-dark"><?= $appreciation ?></h2>
                                    <small class="text-<?= $couleur_appreciation ?>">Niveau</small>
                                </div>
                                <div class="stat-icon stat-appreciation">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sélecteur de Semestre -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="semester-tabs text-center">
                        <h5 class="mb-3 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Sélectionnez le Semestre
                        </h5>
                        <div class="d-flex justify-content-center flex-wrap">
                            <a href="?semestre=Semestre 1" class="semester-btn <?= $semestre === 'Semestre 1' ? 'active' : '' ?>">
                                <i class="fas fa-1 me-2"></i>Semestre 1
                            </a>
                            <a href="?semestre=Semestre 2" class="semester-btn <?= $semestre === 'Semestre 2' ? 'active' : '' ?>">
                                <i class="fas fa-2 me-2"></i>Semestre 2
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Étudiant -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-graduate me-2"></i> Mes Informations
                            </h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-primary">
                                    <i class="fas fa-id-card me-1"></i><?= htmlspecialchars($_SESSION["matricule"] ?? 'N/A') ?>
                                </span>
                                <span class="badge bg-success">
                                    <i class="fas fa-calendar me-1"></i><?= $semestre ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détail des Notes -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-header bg-primary text-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="fas fa-book-open me-2"></i>Détail des Notes - <?= $semestre ?>
                                </h4>
                                <span class="badge bg-light text-dark fs-6">
                                    <?= count($notes) ?> matière(s)
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($notes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover notes-table">
                                        <thead>
                                            <tr>
                                                <th>Matière</th>
                                                <th>Interro 1</th>
                                                <th>Interro 2</th>
                                                <th>Interro 3</th>
                                                <th>Devoir 1</th>
                                                <th>Devoir 2</th>
                                                <th>Moyenne</th>
                                                <th>Crédits</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($notes as $note): ?>
                                                <?php 
                                                    // Vérifier que toutes les notes sont supérieures à 0
                                                    $allGradesFilled = 
                                                        $note['interrogation1'] > 0 &&
                                                        $note['interrogation2'] > 0 &&
                                                        $note['interrogation3'] > 0 &&
                                                        $note['devoir1'] > 0 &&
                                                        $note['devoir2'] > 0;

                                                    if ($allGradesFilled) {
                                                        // Calcul de la moyenne
                                                        $moyenneAssignments = ($note['interrogation1'] + $note['interrogation2'] + $note['interrogation3']) / 3;
                                                        $sumExams = $note['devoir1'] + $note['devoir2'];
                                                        $moyenneTotale = ($moyenneAssignments + $sumExams) / 3;
                                                        $moyenneTotale = round($moyenneTotale, 2);

                                                        $statut = $moyenneTotale >= 10 ? 'success' : 'danger';
                                                        $texte_statut = $moyenneTotale >= 10 ? 'Validé' : 'Non validé';
                                                        $displayMoyenne = $moyenneTotale;
                                                    } else {
                                                        $statut = 'danger';
                                                        $texte_statut = 'Toutes les notes ne sont pas renseignées';
                                                        $displayMoyenne = $texte_statut;
                                                    }
                                                ?>
                                                <tr>
                                                    <td><strong class="text-primary"><?= htmlspecialchars($note['matiere']) ?></strong></td>
                                                    <td><span class="<?= $note['interrogation1'] >= 10 ? 'text-success' : 'text-danger' ?>"><?= htmlspecialchars($note['interrogation1'] ?? '-') ?></span></td>
                                                    <td><span class="<?= $note['interrogation2'] >= 10 ? 'text-success' : 'text-danger' ?>"><?= htmlspecialchars($note['interrogation2'] ?? '-') ?></span></td>
                                                    <td><span class="<?= $note['interrogation3'] >= 10 ? 'text-success' : 'text-danger' ?>"><?= htmlspecialchars($note['interrogation3'] ?? '-') ?></span></td>
                                                    <td><span class="<?= $note['devoir1'] >= 10 ? 'text-success' : 'text-danger' ?>"><?= htmlspecialchars($note['devoir1'] ?? '-') ?></span></td>
                                                    <td><span class="<?= $note['devoir2'] >= 10 ? 'text-success' : 'text-danger' ?>"><?= htmlspecialchars($note['devoir2'] ?? '-') ?></span></td>
                                                    
                                                    <td class="fw-bold text-<?= $statut ?>"><?= htmlspecialchars($displayMoyenne) ?></td>
                                                    <td><span class="badge bg-info"><?= htmlspecialchars($note['coefficient']) ?></span></td>
                                                    <td>
                                                        <span class="badge bg-<?= $statut ?>">
                                                            <i class="fas fa-<?= $statut === 'success' ? 'check' : 'times' ?> me-1"></i>
                                                            <?= $texte_statut ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune note disponible</h5>
                                    <p class="text-muted">Les notes pour <?= $semestre ?> ne sont pas encore publiées.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Légende et Informations -->
            <div class="row px-3 mt-4">
                <div class="col-12">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Légende des Notes
                                    </h6>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-success me-2">≥ 10</span>
                                            <small>Note validante</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-danger me-2">< 10</span>
                                            <small>Note non validante</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-info me-2">Crédits</span>
                                            <small>Coefficient de la matière</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Résumé du Semestre
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <h4 class="text-primary mb-0"><?= $total_matieres ?></h4>
                                            <small class="text-muted">Matières</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-success mb-0">
                                                <?= count(array_filter($notes, function($n) { return $n['moyenne'] >= 10; })) ?>
                                            </h4>
                                            <small class="text-muted">Validées</small>
                                        </div>
                                        <div class="col-4">
                                            <h4 class="text-info mb-0"><?= $total_credits ?></h4>
                                            <small class="text-muted">Crédits</small>
                                        </div>
                                    </div>
                                </div>
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
    });
</script>
</body>
</html>