<?php
session_start();

if (!isset($_SESSION["Nom"])){
    header("Location:connexion-enseignant.php");
    exit();
}

if (isset($_POST["deconnexion"])){
    $_SESSION = array();
    session_destroy();
    header("Location:connexion-enseignant.php");
    exit();
}

include("../connexion-bases.php") ;
$teacher_id = $_SESSION["teacher_id"];

// Récupérer les matières du professeur
$query = "
SELECT 
    s.schedule_id,
    sub.subject_name,
    sub.subject_id,
    s.day, 
    s.start_time, 
    s.end_time,
    r.classroom_name,
    d.department_name
FROM schedules s
JOIN classrooms r ON s.classroom_id = r.classroom_id
JOIN subjects sub ON s.subject_id = sub.subject_id
JOIN levels l ON sub.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id
WHERE s.teacher_id = ?
ORDER BY s.day, s.start_time
";

$stmt = $connecter->prepare($query);
$stmt->execute([$teacher_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Traitement de l'ajout d'une nouvelle entrée
if (isset($_POST['enregistrer'])) {
    $schedule_id = $_POST['schedule_id'];
    $teacher_id = $_POST['teacher_id'];
    

    function verification_saisie($input){
        $input  = trim($input);
        $input = htmlspecialchars($input);
        $input = stripslashes($input);
        $input = strip_tags($input);      
        return $input;
    };

    $contenu_du_cours = verification_saisie($_POST['contenu_du_cours']);
    $objectifs = verification_saisie($_POST['objectifs']);
    $objectifAtteint = verification_saisie($_POST['objectifAtteint']);
    $travail = verification_saisie($_POST['travail']);
    $avis_remarques = verification_saisie($_POST['avis_remarques']);

    $insertQuery = "
        INSERT INTO teacher_notebooks 
        (schedule_id, teacher_id, contenu_du_cours, objectifs, objectifAtteint, travail, avis_remarques) 
        VALUES 
        (:schedule_id, :teacher_id, :contenu_du_cours, :objectifs, :objectifAtteint, :travail, :avis_remarques)
    ";
    
    $insertStmt = $connecter->prepare($insertQuery);
    $insertStmt->bindParam(':schedule_id', $schedule_id);
    $insertStmt->bindParam(':teacher_id', $teacher_id);
    $insertStmt->bindParam(':contenu_du_cours', $contenu_du_cours);
    $insertStmt->bindParam(':objectifs', $objectifs);
    $insertStmt->bindParam(':objectifAtteint', $objectifAtteint);
    $insertStmt->bindParam(':travail', $travail);
    $insertStmt->bindParam(':avis_remarques', $avis_remarques);
   
    $insertStmt->execute();
    
    header("Location: cahier-texte.php?success=1");
    exit();
}

// Traitement de la suppression
if (isset($_GET['supprimer'])) {
    $note_id = $_GET['supprimer'];
    
    // Vérifier que le cahier appartient bien à l'enseignant connecté
    $checkQuery = "SELECT * FROM teacher_notebooks WHERE note_id = ? AND teacher_id = ?";
    $checkStmt = $connecter->prepare($checkQuery);
    $checkStmt->execute([$note_id, $teacher_id]);
    $cahier = $checkStmt->fetch();
    
    if ($cahier) {
        $deleteQuery = "DELETE FROM teacher_notebooks WHERE note_id = ?";
        $deleteStmt = $connecter->prepare($deleteQuery);
        $deleteStmt->execute([$note_id]);
        
        header("Location: cahier-texte.php?success=2");
        exit();
    } else {
        header("Location: cahier-texte.php?error=1");
        exit();
    }
}

// Récupérer l'historique des cours
$sql = $connecter->prepare("
    SELECT 
        tn.note_id,
        t.teacher_id,
        CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,

        -- Matière
        sub.subject_name,

        -- Salle
        c.classroom_name,

        -- Filière
        d.department_name,

        -- Informations cours
        tn.contenu_du_cours,
        tn.objectifs,
        tn.objectifAtteint,
        tn.travail,
        tn.avis_remarques,
        tn.created_at,

        -- Horaire
        s.start_time,
        s.end_time,
        s.day

    FROM teacher_notebooks tn

    -- notes → schedules
    INNER JOIN schedules s ON tn.schedule_id = s.schedule_id

    -- schedules → teachers
    INNER JOIN teachers t ON s.teacher_id = t.teacher_id

    -- schedules → subjects
    INNER JOIN subjects sub ON s.subject_id = sub.subject_id

    -- subjects → levels
    INNER JOIN levels l ON sub.level_id = l.level_id

    -- levels → departments  ⬅️ ajout
    INNER JOIN departments d ON l.department_id = d.department_id

    -- schedules → classrooms
    INNER JOIN classrooms c ON s.classroom_id = c.classroom_id

    WHERE t.teacher_id = ?

    ORDER BY tn.created_at DESC
");

$sql->execute([$teacher_id]);
$cours = $sql->fetchAll(PDO::FETCH_ASSOC);



?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Cahier de texte - Class Connect</title>

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
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
        .primary-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .history-card .card-header {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }
        
        /* Bouton Nouveau Cours */
        .btn-add-course {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-add-course:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }
        
        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        /* Badge Styles */
        .badge-course {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        /* Table Styles */
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        /* Action Buttons */
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.875rem;
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
        
        /* Action buttons in table */
        .btn-table-action {
            padding: 5px 10px;
            font-size: 0.8rem;
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
            
            .btn-table-action {
                padding: 4px 8px;
                font-size: 0.7rem;
                margin: 1px;
            }
        }
        
        /* Animation for success message */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-success {
            animation: fadeIn 0.5s ease-out;
        }
        
        .alert-danger {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
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
                    <a class="nav-link" aria-current="page" href="./enseignant.php">
                        <i class="fas fa-chart-line"></i>
                        Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./emargement.php">
                        <i class="fas fa-calendar-days"></i>
                        Émargement des étudiants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./notation.php">
                        <i class="fas fa-edit"></i>
                        Notation des étudiants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="./cahier-texte.php">
                        <i class="fas fa-book"></i>
                        Cahier de texte
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Cahier de texte</h1>
                    <p class="text-muted mb-0">Gestion du contenu pédagogique</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-add-course" data-bs-toggle="modal" data-bs-target="#modalNouveauCours">
                        <i class="fas fa-plus me-2"></i>Nouveau cours
                    </button>
                    <div class="date-display">
                        <i class="fas fa-clock me-2"></i>
                        <span id="dateHeure"></span>
                    </div>
                    <div class="dropdown">
                       <button class="btn user-dropdown dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?=$_SESSION["Nom"] ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="" method="post">
                                    <button type="submit" name="deconnexion" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Messages d'alerte -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Le cours a été enregistré avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
                <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>Le cours a été supprimé avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>Vous n'avez pas la permission de supprimer ce cours !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Statistiques -->
            <div class="row mb-4 px-4">
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Total des cours</h6>
                                    <h3 class="mb-0"><?= count($cours) ?></h3>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-book text-primary fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Objectifs atteints</h6>
                                    <h3 class="mb-0">
                                        <?= count(array_filter($cours, function($c) { return strtolower($c['objectifAtteint']) === 'oui'; })) ?>
                                    </h3>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Matières enseignées</h6>
                                    <h3 class="mb-0"><?= count($subjects) ?></h3>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-graduation-cap text-warning fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-2">Cours ce mois</h6>
                                    <h3 class="mb-0">
                                        <?= count(array_filter($cours, function($c) { 
                                            return date('Y-m', strtotime($c['day'])) == date('Y-m'); 
                                        })) ?>
                                    </h3>
                                </div>
                                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                    <i class="fas fa-calendar-alt text-info fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des cours -->
            <div class="card history-card mx-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Historique des Cours</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($cours)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun cours enregistré</h5>
                            <p class="text-muted mb-0">Commencez par ajouter votre premier cours</p>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#modalNouveauCours">
                                <i class="fas fa-plus me-2"></i>Ajouter un cours
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Matière</th>
                                        <th>Filière</th>
                                        <th>Salle</th>
                                        <th>Objectif</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cours as $row): ?>
                                    <tr>
                                        <td><?= date("d/m/Y", strtotime($row['day'])) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['subject_name']) ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?>
                                            </small>
                                        </td>
                                        <td><?= htmlspecialchars($row['department_name']) ?></td>
                                        <td><?= htmlspecialchars($row['classroom_name']) ?></td>
                                        <td>
                                            <?php if (strtolower($row['objectifAtteint']) === "oui"): ?>
                                                <span class="badge bg-success badge-course">
                                                    <i class="fas fa-check me-1"></i>Atteint
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger badge-course">
                                                    <i class="fas fa-times me-1"></i>Non atteint
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-table-action btn-details" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalDetailsCours"
                                                        data-subject="<?= htmlspecialchars($row['subject_name']) ?>"
                                                        data-date="<?= date('d/m/Y', strtotime($row['day'])) ?>"
                                                        data-time="<?= htmlspecialchars($row['start_time']) ?> - <?= htmlspecialchars($row['end_time']) ?>"
                                                        data-salle="<?= htmlspecialchars($row['classroom_name']) ?>"
                                                        data-filiere="<?= htmlspecialchars($row['department_name']) ?>"
                                                        data-contenu="<?= htmlspecialchars($row['contenu_du_cours']) ?>"
                                                        data-objectifs="<?= htmlspecialchars($row['objectifs']) ?>"
                                                        data-objectif-atteint="<?= htmlspecialchars($row['objectifAtteint']) ?>"
                                                        data-travail="<?= htmlspecialchars($row['travail']) ?>"
                                                        data-avis="<?= htmlspecialchars($row['avis_remarques']) ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                <button type="button" class="btn btn-sm btn-outline-warning btn-table-action btn-modifier" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalModifierCours"
                                                        data-note-id="<?= $row['note_id'] ?>"
                                                        data-subject="<?= htmlspecialchars($row['subject_name']) ?>"
                                                        data-date="<?= date('Y-m-d', strtotime($row['day'])) ?>"
                                                        data-time-debut="<?= htmlspecialchars($row['start_time']) ?>"
                                                        data-time-fin="<?= htmlspecialchars($row['end_time']) ?>"
                                                        data-salle="<?= htmlspecialchars($row['classroom_name']) ?>"
                                                        data-filiere="<?= htmlspecialchars($row['department_name']) ?>"
                                                        data-contenu="<?= htmlspecialchars($row['contenu_du_cours']) ?>"
                                                        data-objectifs="<?= htmlspecialchars($row['objectifs']) ?>"
                                                        data-objectif-atteint="<?= htmlspecialchars($row['objectifAtteint']) ?>"
                                                        data-travail="<?= htmlspecialchars($row['travail']) ?>"
                                                        data-avis="<?= htmlspecialchars($row['avis_remarques']) ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <a href="?supprimer=<?= $row['note_id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-table-action" 
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible.')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
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

<!-- Modal Nouveau Cours -->
<div class="modal fade" id="modalNouveauCours" tabindex="-1" aria-labelledby="modalNouveauCoursLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNouveauCoursLabel">
                    <i class="fas fa-plus me-2"></i>Nouvelle entrée du cahier de texte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-cahier-texte" method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Matière *</label>
                            <select class="form-select" id="subject_select" name="schedule_id" required>
                                <option value="">Sélectionner une matière...</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['schedule_id'] ?>" 
                                            data-day="<?= $subject['day'] ?>"
                                            data-start="<?= $subject['start_time'] ?>"
                                            data-end="<?= $subject['end_time'] ?>"
                                            data-salle="<?= htmlspecialchars($subject['classroom_name']) ?>"
                                            data-filiere="<?= htmlspecialchars($subject['department_name']) ?>">
                                        <?= htmlspecialchars($subject['subject_name']) ?> 
                                        (<?= $subject['day'] ?> <?= $subject['start_time'] ?>-<?= $subject['end_time'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date du cours *</label>
                            <input type="date" id="date_cours" name="date_cours" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Jour prévu</label>
                            <input type="text" id="jour_prevu" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure début</label>
                            <input type="time" id="heure_debut" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure fin</label>
                            <input type="time" id="heure_fin" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Salle</label>
                            <input type="text" id="salle" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Filière</label>
                            <input type="text" id="filiere" class="form-control" readonly>
                        </div>
                    </div>

                    <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Contenu du cours *</label>
                        <textarea class="form-control" name="contenu_du_cours" style="resize: none;" rows="3" placeholder="Détail du contenu enseigné..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Objectifs pédagogiques *</label>
                        <textarea class="form-control" name="objectifs" style="resize: none;" rows="2" placeholder="Objectifs visés pour cette séance..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Objectif atteint ? *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="objectifAtteint" value="oui" id="objectif-oui" checked required>
                                <label class="form-check-label" for="objectif-oui">
                                    <i class="fas fa-check-circle text-success me-1"></i>Oui, objectif atteint
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="objectifAtteint" value="non" id="objectif-non">
                                <label class="form-check-label" for="objectif-non">
                                    <i class="fas fa-times-circle text-danger me-1"></i>Non, objectif non atteint
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="raisons-container" style="display: none;">
                        <label class="form-label">Raisons de la non-atteinte des objectifs</label>
                        <textarea class="form-control" name="raison" style="resize: none;" rows="2" placeholder="Expliquez les raisons..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Travail à faire</label>
                        <textarea name="travail" class="form-control" style="resize: none;" rows="2" placeholder="Devoirs, exercices, lectures..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Avis/Remarques</label>
                        <textarea name="avis-remarques" class="form-control" style="resize: none;" rows="2" placeholder="Observations sur le déroulement du cours..."></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="enregistrer" class="btn btn-success">
                            <i class="fas fa-check-circle me-1"></i>Enregistrer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails Cours -->
<div class="modal fade" id="modalDetailsCours" tabindex="-1" aria-labelledby="modalDetailsCoursLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailsCoursLabel">
                    <i class="fas fa-info-circle me-2"></i>Détails du cours
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Matière</h6>
                        <h5 id="detail-subject" class="text-primary"></h5>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Date et heure</h6>
                        <h5 id="detail-date-time"></h5>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Filière</h6>
                        <p id="detail-filiere" class="mb-0"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Salle</h6>
                        <p id="detail-salle" class="mb-0"></p>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Objectif atteint</h6>
                    <span id="detail-objectif-badge" class="badge"></span>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Objectifs pédagogiques</h6>
                    <p id="detail-objectifs" class="mb-0"></p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Contenu du cours</h6>
                    <p id="detail-contenu" class="mb-0"></p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Travail à faire</h6>
                    <p id="detail-travail" class="mb-0"></p>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted mb-1">Avis/Remarques</h6>
                    <p id="detail-avis" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier Cours -->
<div class="modal fade" id="modalModifierCours" tabindex="-1" aria-labelledby="modalModifierCoursLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalModifierCoursLabel">
                    <i class="fas fa-edit me-2"></i>Modifier le cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-modifier-cahier" method="POST" action="modifier-cahier.php">
                    <input type="hidden" id="modifier_note_id" name="note_id">
                    <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Matière</label>
                            <input type="text" id="modifier_subject" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date du cours</label>
                            <input type="date" id="modifier_date" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Heure début</label>
                            <input type="time" id="modifier_time_debut" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Heure fin</label>
                            <input type="time" id="modifier_time_fin" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Salle</label>
                            <input type="text" id="modifier_salle" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contenu du cours *</label>
                        <textarea class="form-control" id="modifier_contenu" name="contenu_du_cours" style="resize: none;" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Objectifs pédagogiques *</label>
                        <textarea class="form-control" id="modifier_objectifs" name="objectifs" style="resize: none;" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Objectif atteint ? *</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="objectifAtteint" value="oui" id="modifier_objectif-oui" required>
                                <label class="form-check-label" for="modifier_objectif-oui">
                                    <i class="fas fa-check-circle text-success me-1"></i>Oui, objectif atteint
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="objectifAtteint" value="non" id="modifier_objectif-non">
                                <label class="form-check-label" for="modifier_objectif-non">
                                    <i class="fas fa-times-circle text-danger me-1"></i>Non, objectif non atteint
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="modifier_raisons-container" style="display: none;">
                        <label class="form-label">Raisons de la non-atteinte des objectifs</label>
                        <textarea class="form-control" name="raison" style="resize: none;" rows="2" placeholder="Expliquez les raisons..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Travail à faire</label>
                        <textarea id="modifier_travail" name="travail" class="form-control" style="resize: none;" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Avis/Remarques</label>
                        <textarea id="modifier_avis" name="avis_remarques" class="form-control" style="resize: none;" rows="2"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Afficher la date et l'heure en temps réel
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

    // Gérer l'affichage des raisons si objectif non atteint (nouveau cours)
    document.querySelectorAll('input[name="objectifAtteint"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const container = document.getElementById('raisons-container');
            container.style.display = radio.value === 'non' ? 'block' : 'none';
        });
    });

    // Gérer l'affichage des raisons si objectif non atteint (modification)
    document.querySelectorAll('input[name="objectifAtteint"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const container = document.getElementById('modifier_raisons-container');
            container.style.display = radio.value === 'non' ? 'block' : 'none';
        });
    });

    // Remplir automatiquement les informations lors de la sélection d'une matière
    document.getElementById('subject_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('jour_prevu').value = selectedOption.dataset.day || '';
            document.getElementById('heure_debut').value = selectedOption.dataset.start || '';
            document.getElementById('heure_fin').value = selectedOption.dataset.end || '';
            document.getElementById('salle').value = selectedOption.dataset.salle || '';
            document.getElementById('filiere').value = selectedOption.dataset.filiere || '';
        } else {
            document.getElementById('jour_prevu').value = '';
            document.getElementById('heure_debut').value = '';
            document.getElementById('heure_fin').value = '';
            document.getElementById('salle').value = '';
            document.getElementById('filiere').value = '';
        }
    });

    // Gérer l'affichage des détails du cours
    document.querySelectorAll('.btn-details').forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.querySelector('#modalDetailsCours');
            
            // Remplir les informations du modal
            modal.querySelector('#detail-subject').textContent = this.dataset.subject;
            modal.querySelector('#detail-date-time').textContent = `${this.dataset.date} ${this.dataset.time}`;
            modal.querySelector('#detail-filiere').textContent = this.dataset.filiere;
            modal.querySelector('#detail-salle').textContent = this.dataset.salle;
            modal.querySelector('#detail-objectifs').textContent = this.dataset.objectifs || 'Non spécifié';
            modal.querySelector('#detail-contenu').textContent = this.dataset.contenu || 'Non spécifié';
            modal.querySelector('#detail-travail').textContent = this.dataset.travail || 'Non spécifié';
            modal.querySelector('#detail-avis').textContent = this.dataset.avis || 'Non spécifié';
            
            // Gérer le badge d'objectif
            const badge = modal.querySelector('#detail-objectif-badge');
            if (this.dataset.objectifAtteint === 'oui') {
                badge.className = 'badge bg-success';
                badge.innerHTML = '<i class="fas fa-check me-1"></i>Objectif atteint';
            } else {
                badge.className = 'badge bg-danger';
                badge.innerHTML = '<i class="fas fa-times me-1"></i>Objectif non atteint';
            }
        });
    });

    // Gérer l'affichage du formulaire de modification
    document.querySelectorAll('.btn-modifier').forEach(button => {
        button.addEventListener('click', function() {
            const modal = document.querySelector('#modalModifierCours');
            
            // Remplir les informations du modal
            modal.querySelector('#modifier_note_id').value = this.dataset.noteId;
            modal.querySelector('#modifier_subject').value = this.dataset.subject;
            modal.querySelector('#modifier_date').value = this.dataset.date;
            modal.querySelector('#modifier_time_debut').value = this.dataset.timeDebut;
            modal.querySelector('#modifier_time_fin').value = this.dataset.timeFin;
            modal.querySelector('#modifier_salle').value = this.dataset.salle;
            modal.querySelector('#modifier_contenu').value = this.dataset.contenu || '';
            modal.querySelector('#modifier_objectifs').value = this.dataset.objectifs || '';
            modal.querySelector('#modifier_travail').value = this.dataset.travail || '';
            modal.querySelector('#modifier_avis').value = this.dataset.avis || '';
            
            // Gérer les boutons radio d'objectif
            const objectifOui = modal.querySelector('#modifier_objectif-oui');
            const objectifNon = modal.querySelector('#modifier_objectif-non');
            const raisonsContainer = modal.querySelector('#modifier_raisons-container');
            
            if (this.dataset.objectifAtteint === 'oui') {
                objectifOui.checked = true;
                objectifNon.checked = false;
                raisonsContainer.style.display = 'none';
            } else {
                objectifOui.checked = false;
                objectifNon.checked = true;
                raisonsContainer.style.display = 'block';
            }
        });
    });

    // Fermer les alertes après 5 secondes
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