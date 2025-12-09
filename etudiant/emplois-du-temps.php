<?php
session_start();
include("../connexion-bases.php");

// Vérifie la session utilisateur
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["first_name"])) {
    header("Location: connexion-etudiant.php");
    exit();
}

$student_id = $_SESSION["user_id"];
$_SESSION["Nom"] = $_SESSION["first_name"] . " " . $_SESSION["last_name"];

// Déconnexion
if (isset($_POST["deconnexion"])) {
    session_destroy();
    header("Location: connexion-etudiant.php");
    exit();
}



$sql = "
SELECT 
    s.schedule_id,
    s.day,
    s.start_time,
    s.end_time,
    s.date_start,
    s.date_end,
    sub.subject_name,
    l.level_name,
    d.department_name,
    CONCAT(t.first_name, ' ', t.last_name) AS teacher_fullname,
    c.classroom_name,
    sem.semester_name,
    a.year_label
FROM students st
JOIN levels l ON st.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id
JOIN subjects sub ON sub.level_id = l.level_id
JOIN schedules s ON s.subject_id = sub.subject_id
JOIN teachers t ON s.teacher_id = t.teacher_id
JOIN classrooms c ON s.classroom_id = c.classroom_id
JOIN semesters sem ON s.semester_id = sem.semester_id
JOIN academic_years a ON s.academic_year_id = a.academic_year_id
WHERE st.student_id = :student_id
ORDER BY s.day, s.start_time
";

$afficher_EMPLOI = $connecter->prepare($sql);
$afficher_EMPLOI->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$afficher_EMPLOI->execute();
$emploi_du_temps = $afficher_EMPLOI->fetchAll(PDO::FETCH_ASSOC);

// Calculer les statistiques
$total_cours = count($emploi_du_temps);
$matieres_uniques = array_unique(array_column($emploi_du_temps, 'subject_name'));
$total_matieres = count($matieres_uniques);
$enseignants_uniques = array_unique(array_column($emploi_du_temps, 'teacher_fullname'));
$total_enseignants = count($enseignants_uniques);

// Compter les cours par jour de cette semaine
$cours_semaine = 0;
$aujourdhui = date('Y-m-d');
$debut_semaine = date('Y-m-d', strtotime('monday this week'));
$fin_semaine = date('Y-m-d', strtotime('sunday this week'));

foreach ($emploi_du_temps as $cours) {
    $date_cours = $cours['day'];
    if ($date_cours >= $debut_semaine && $date_cours <= $fin_semaine) {
        $cours_semaine++;
    }
}

// Liste des jours en français
$jours_semaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

// Fonction pour transformer la date en nom de jour FR
function jourFrancais($dateSQL) {
    $jours_fr = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    $date = DateTime::createFromFormat('Y-m-d', $dateSQL);
    return $date ? $jours_fr[$date->format('w')] : '';
}

// Regrouper les cours par créneaux horaires
$emplois_groupes = [];
foreach ($emploi_du_temps as $cours) {
    $horaire = $cours['start_time'] . ' - ' . $cours['end_time'];
    $jour = jourFrancais($cours['day']);
    $emplois_groupes[$horaire][$jour][] = $cours;
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
    <title>Emploi du Temps - Class Connect</title>
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
        .stat-subjects { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-teachers { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-week { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        
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
            min-height: 120px;
            border: 1px solid #e9ecef;
            padding: 12px;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .schedule-cell:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
        }
        
        .course-badge {
            font-size: 0.75rem;
            margin: 2px;
        }
        
        .subject-card {
            background: white;
            border-radius: 10px;
            border: none;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .subject-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
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
                    <a class="nav-link" aria-current="page" href="notes-etudiant.php">
                        <i class="fas fa-graduation-cap"></i>
                        Notes
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link active" href="emplois-du-temps.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Mon Emploi du Temps</h1>
                    <p class="text-muted mb-0">Planning de mes cours et activités</p>
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
                                    <h6 class="text-muted mb-2">Total des Cours</h6>
                                    <h2 class="fw-bold text-dark"><?= $total_cours ?></h2>
                                    <small class="text-success">Planifiés</small>
                                </div>
                                <div class="stat-icon stat-courses">
                                    <i class="fas fa-calendar-alt text-white"></i>
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
                                    <small class="text-success">Inscrites</small>
                                </div>
                                <div class="stat-icon stat-subjects">
                                    <i class="fas fa-book text-white"></i>
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
                                    <h6 class="text-muted mb-2">Enseignants</h6>
                                    <h2 class="fw-bold text-dark"><?= $total_enseignants ?></h2>
                                    <small class="text-success">Différents</small>
                                </div>
                                <div class="stat-icon stat-teachers">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cours Cette Semaine Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Cours Cette Semaine</h6>
                                    <h2 class="fw-bold text-dark"><?= $cours_semaine ?></h2>
                                    <small class="text-info">En cours</small>
                                </div>
                                <div class="stat-icon stat-week">
                                    <i class="fas fa-calendar-week text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Étudiant -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-user-graduate me-2"></i> Mes Informations
                            </h5>
                            <div class="d-flex gap-2">
                                <?php if (!empty($emploi_du_temps)): ?>
                                <span class="badge bg-primary">
                                    <?= htmlspecialchars($emploi_du_temps[0]['level_name'] ?? 'Niveau inconnu') ?>
                                </span>
                                <span class="badge bg-success">
                                    <?= htmlspecialchars($emploi_du_temps[0]['department_name'] ?? 'Filière inconnue') ?>
                                </span>
                                <span class="badge bg-info">
                                    <?= htmlspecialchars($emploi_du_temps[0]['semester_name'] ?? 'Semestre inconnu') ?>
                                </span>
                                <span class="badge bg-warning text-dark">
                                    <?= htmlspecialchars($emploi_du_temps[0]['year_label'] ?? 'Année inconnue') ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emploi du Temps -->
            <div class="row px-3 mb-4">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i> Mon Emploi du Temps
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="align-middle">Horaire</th>
                                            <?php foreach ($jours_semaine as $jour): ?>
                                                <th class="align-middle"><?= $jour ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($emplois_groupes as $horaire => $cours_par_jour): ?>
                                            <tr>
                                                <td class="align-middle"><strong><?= $horaire ?></strong></td>
                                                <?php foreach ($jours_semaine as $jour): ?>
                                                    <td class="p-2">
                                                        <?php if (!empty($cours_par_jour[$jour])): ?>
                                                            <?php foreach ($cours_par_jour[$jour] as $cours): ?>
                                                                <div class="schedule-cell">
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <strong class="text-primary"><?= htmlspecialchars($cours['subject_name']) ?></strong> <br>
                                                                        
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                                        <span class="badge bg-success course-badge">
                                                                            <i class="fas fa-chalkboard me-1"></i><?= htmlspecialchars($cours['classroom_name']) ?>
                                                                        </span>
                                                                       
                                                                    </div>
                                                                    <div class="d-flex justify-content-between align-items-start ">
                                                                        <small class="text-muted">
                                                                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($cours['teacher_fullname']) ?>
                                                                        </small>
                                                                    </div>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <div class="schedule-cell text-muted d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-coffee me-2"></i>Libre
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mes Matières -->
            <div class="row px-3">
                <div class="col-12">
                    <div class="card table-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-bookmark me-2"></i> Mes Matières
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if (!empty($matieres_uniques)): ?>
                                    <?php foreach ($matieres_uniques as $matiere): ?>
                                        <div class="col-xl-3 col-md-4 col-sm-6 mb-3">
                                            <div class="card subject-card">
                                                <div class="card-body text-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3 d-inline-flex">
                                                        <i class="fas fa-book text-primary fa-2x"></i>
                                                    </div>
                                                    <h6 class="card-title text-primary"><?= htmlspecialchars($matiere) ?></h6>
                                                    <small class="text-muted">
                                                        <?php 
                                                        // Trouver le professeur pour cette matière
                                                        $professeurs_matiere = [];
                                                        foreach ($emploi_du_temps as $cours) {
                                                            if ($cours['subject_name'] === $matiere) {
                                                                $professeurs_matiere[$cours['teacher_fullname']] = true;
                                                            }
                                                        }
                                                        echo "Enseignant" . (count($professeurs_matiere) > 1 ? "s" : "") . " :<br>";
                                                        echo implode(", ", array_keys($professeurs_matiere));
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-4">
                                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Aucune matière trouvée</p>
                                    </div>
                                <?php endif; ?>
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

setInterval(afficherDateHeure, 1000);
afficherDateHeure();

</script>
</body>
</html>
