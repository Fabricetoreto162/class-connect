<?php
session_start();

if (!isset($_SESSION["Nom"])) {
    header("Location: connexion-enseignant.php");
    exit();
}

if (isset($_POST["deconnexion"])) {
    $_SESSION = [];
    session_destroy();
    header("Location: connexion-enseignant.php");
    exit();
}

include("../connexion-bases.php");

// ✅ Récupération des années académiques
$sql = "SELECT academic_year_id, year_label FROM academic_years";
$stmt = $connecter->prepare($sql);
$stmt->execute();
$academic_years = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Récupération des étudiants liés à l'enseignant connecté
$teacher_id = $_SESSION["user_id_teacher"]; // id du prof connecté

$sql = "
SELECT DISTINCT st.student_id, st.matricule, st.first_name, st.last_name, st.level_id
FROM students AS st
INNER JOIN subjects AS sub ON st.level_id = sub.level_id
INNER JOIN teachers_affectation AS ta ON sub.subject_id = ta.subject_id
WHERE ta.teacher_id = :teacher_id
";
$stmt = $connecter->prepare($sql);
$stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
$stmt->execute();
$etudiants_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$etudiants = [];
foreach ($etudiants_data as $etudiant) {
    $etudiants[] = [
        'id' => $etudiant["student_id"],
        'matricule' => $etudiant["matricule"],
        'nom' => htmlspecialchars($etudiant["first_name"] . " " . $etudiant["last_name"])
    ];
}

// ✅ Récupération des matières enseignées par le prof
$sql_matieres = "
    SELECT 
        s.subject_id, 
        s.subject_name, 
        s.semester_id, 
        s.coefficient, 
        sem.semester_name
    FROM subjects AS s
    INNER JOIN semesters AS sem ON s.semester_id = sem.semester_id
    INNER JOIN teachers_affectation AS ta ON ta.subject_id = s.subject_id
    WHERE ta.teacher_id = :teacher_id
";
$reponse_matieres = $connecter->prepare($sql_matieres);
$reponse_matieres->bindParam(':teacher_id', $teacher_id);
$reponse_matieres->execute();
$matieres_data = $reponse_matieres->fetchAll(PDO::FETCH_ASSOC);

$matieres = [];
foreach ($matieres_data as $matiere) {
    $matieres[] = [
        'id' => $matiere["subject_id"],
        'nom' => htmlspecialchars($matiere["subject_name"]),
        'semestre' => htmlspecialchars($matiere["semester_name"]),
        'coefficient' => htmlspecialchars($matiere["coefficient"])
    ];
}

$notes_data=[];

//Enregistrements ou insertion des notes dans la base de donnes 
if (isset($_POST['save_notes'])) {
    require '../connexion-bases.php'; // ton fichier de connexion PDO

    // Récupération sécurisée des données du formulaire
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $academic_year_id = $_POST['academic_year_id'];
    $note_date = $_POST['note_date'];
    $int1 = $_POST['int1'] ?? null;
    $int2 = $_POST['int2'] ?? null;
    $int3 = $_POST['int3'] ?? null;
    $dev1 = $_POST['dev1'] ?? null;
    $dev2 = $_POST['dev2'] ?? null;

    // ✅ Requête d'insertion
    $sql = "INSERT INTO assignments (
                student_id, 
                subject_id, 
                academic_year_id, 
                note_date, 
                assignment1, 
                assignment2, 
                assignment3, 
                exam1, 
                exam2
            ) 
            VALUES (
                :student_id, 
                :subject_id, 
                :academic_year_id, 
                :note_date, 
                :assignment1, 
                :assignment2, 
                :assignment3, 
                :exam1, 
                :exam2
            )";

    $recuperation_note_input = $connecter->prepare($sql);

    // Association des valeurs avec bindParam
    $recuperation_note_input->bindParam(':student_id', $student_id);
    $recuperation_note_input->bindParam(':subject_id', $subject_id);
    $recuperation_note_input->bindParam(':academic_year_id', $academic_year_id);
    $recuperation_note_input->bindParam(':note_date', $note_date);
    $recuperation_note_input->bindParam(':assignment1', $int1);
    $recuperation_note_input->bindParam(':assignment2', $int2);
    $recuperation_note_input->bindParam(':assignment3', $int3);
    $recuperation_note_input->bindParam(':exam1', $dev1);
    $recuperation_note_input->bindParam(':exam2', $dev2);
    // Exécution
    $recuperation_note_input->execute();
   
}

//Affichages des donnees dans un tableaus
$sql = "
SELECT 
    a.grade_id,
    a.note_date,
    s.student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS etudiant,
    s.matricule,
    sub.subject_name AS matiere,
    sub.coefficient,
    sem.semester_name AS semestre,
    ay.year_label AS annee_academique,

    -- Fusion des notes avec MAX() pour garder la valeur non nulle ou la plus haute
    MAX(a.assignment1) AS interrogation1,
    MAX(a.assignment2) AS interrogation2,
    MAX(a.assignment3) AS interrogation3,
    MAX(a.exam1) AS devoir1,
    MAX(a.exam2) AS devoir2,

    -- Moyenne calculée automatiquement
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
INNER JOIN students AS s 
    ON a.student_id = s.student_id
INNER JOIN subjects AS sub 
    ON a.subject_id = sub.subject_id
INNER JOIN semesters AS sem 
    ON sub.semester_id = sem.semester_id
INNER JOIN academic_years AS ay 
    ON a.academic_year_id = ay.academic_year_id

GROUP BY 
    s.student_id, 
    sub.subject_id, 
    sem.semester_id, 
    ay.academic_year_id

ORDER BY 
    s.last_name ASC,
    sub.subject_name ASC
";



$Afficher_note = $connecter->prepare($sql);
$Afficher_note->execute();
$notes_data = $Afficher_note->fetchAll();

//suppression d'une note
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $grade_id = $_GET['id'];

    // Requête de suppression
    $sql = "DELETE FROM assignments WHERE grade_id = :grade_id";
    $stmt = $connecter->prepare($sql);
    $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirection après suppression
       header("Location: notation.php?success=deleted");
    exit();
    }
    }
















?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Notation des étudiants - Class Connect</title>
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
        
        .notation-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .notation-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
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
        .table th {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-weight: 500;
        }
        
        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        .subject-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }
        
        .notes-section {
            border-left: 4px solid var(--success);
            padding-left: 15px;
        }
        
        /* Modal Styles */
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }
        
        /* Form Styles */
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
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
        
        .stat-card, .notation-card {
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
                    <a class="nav-link active" href="./notation.php">
                        <i class="fas fa-edit"></i>
                        Notation des étudiants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./cahier-texte.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Notation des étudiants</h1>
                    <p class="text-muted mb-0">Gestion des notes et évaluations</p>
                </div>
                <div class="d-flex align-items-center gap-3">
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

            <!-- Liste des étudiants -->
            <div class="card notation-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Liste des étudiants</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    
                                    <th>Nom complet</th>
                                    <th>Matricule</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <tr>
                                  
                                    <td><?= htmlspecialchars($etudiant['nom']); ?></td>
                                    <td><?= htmlspecialchars($etudiant['matricule']); ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#notationModal"
                                            onclick="ouvrirModal(<?= $etudiant['id']; ?>, '<?= htmlspecialchars($etudiant['nom']); ?>', '<?= htmlspecialchars($etudiant['matricule']); ?>')">
                                            <i class="fas fa-pen me-1"></i> Noter
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <!-- Liste des notes enregistrées -->
<div class="card mt-4">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Notes de chaques étudiants</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Étudiant</th>
                        <th>Matricule</th>
                        <th>Matière</th>
                        <th>Année</th>
                        <th>Semestre</th>
                        <th>Interrogation 1</th>
                        <th>Interrogation 2</th>
                        <th>Interrogation 3</th>
                        <th>Devoir 1</th>
                        <th>Devoir 2</th>
                        <th>Moyenne</th>
                        <th>Coefficient</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($notes_data) > 0): ?>
                        <?php foreach ($notes_data as $note): ?>
                            <?php
                                // Vérifier que toutes les notes sont > 0
                                $allGradesFilled = 
                                    $note['interrogation1'] > 0 &&
                                    $note['interrogation2'] > 0 &&
                                    $note['interrogation3'] > 0 &&
                                    $note['devoir1'] > 0 &&
                                    $note['devoir2'] > 0;

                                if ($allGradesFilled) {
                                    $moyenneInterros = ($note['interrogation1'] + $note['interrogation2'] + $note['interrogation3']) / 3;
                                    $sumDevoirs = $note['devoir1'] + $note['devoir2'];
                                    $moyenneTotale = ($moyenneInterros + $sumDevoirs) / 3;
                                    $moyenneTotale = round($moyenneTotale, 2);
                                    $displayMoyenne = $moyenneTotale;
                                } else {
                                    $displayMoyenne = "Toutes les notes ne sont pas renseignées";
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($note['etudiant']); ?></td>
                                <td><?= htmlspecialchars($note['matricule']); ?></td>
                                <td><?= htmlspecialchars($note['matiere']); ?></td>
                                <td><?= htmlspecialchars($note['annee_academique']); ?></td>
                                <td><?= htmlspecialchars($note['semestre']); ?></td>
                                <td><?= htmlspecialchars($note['interrogation1']); ?></td>
                                <td><?= htmlspecialchars($note['interrogation2']); ?></td>
                                <td><?= htmlspecialchars($note['interrogation3']); ?></td>
                                <td><?= htmlspecialchars($note['devoir1']); ?></td>
                                <td><?= htmlspecialchars($note['devoir2']); ?></td>
                                <td><strong class="text-danger"><?= htmlspecialchars($displayMoyenne); ?></strong></td>
                                <td><strong><?= htmlspecialchars($note['coefficient']); ?></strong></td>
                                <td><?= date("d-m-Y", strtotime($note["note_date"])); ?></td>
                                <td>
                                    <div class="d-flex my-2">
                                        <a class="btn btn-sm btn-outline-danger me-1" href="notation.php?action=delete&id=<?= $note['grade_id'] ?>">
                                        <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14" class="text-center text-muted">Aucune note enregistrée pour le moment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

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

<!-- Modal de notation -->
<div class="modal fade" id="notationModal" tabindex="-1" aria-labelledby="notationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" >
                <div class="modal-header">
                    <h5 class="modal-title" id="notationModalLabel">Noter l'étudiant</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Informations de l'étudiant -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary" id="student-name-display"></h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-id-card me-1"></i>
                                Matricule: <span id="student-matricule-display"></span>
                            </p>
                            
                        </div>

                        

                        <div class="col-md-4 text-end">
                            <label for="note-date" class="form-label">Date de notation</label>
                            <input type="date" class="form-control" id="note-date" name="note_date" required>
                        </div>
                       
                    </div>

                     <div class="row mb-4 mx-2">
                            <!-- ✅ ID caché -->
                            <input type="hidden" class="form-control" id="student-id-display" name="student_id" value="">

                        </div>

                    <!-- Sélection de la matière -->
                    <div class="mb-4">
                        <label for="subject-select" class="form-label">Matière</label>
                        <select class="form-select" id="subject-select" name="subject_id" required>
                            <option value="">Sélectionnez une matière</option>
                            <?php foreach ($matieres as $matiere): ?>
                                <option value="<?= $matiere['id'] ?>" 
                                        data-semester="<?= $matiere['semestre'] ?>"
                                        data-coefficient="<?= $matiere['coefficient'] ?>">
                                    <?= $matiere['nom'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Informations de la matière -->
                    <div class="subject-info" id="subject-info" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-calendar me-1"></i>Semestre :</strong> <span id="subject-semester"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-weight-scale me-1"></i>Coefficient :</strong> <span id="subject-coefficient"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Année académique -->
                    <div class="mb-4">
                        <label for="academic_year" class="form-label">Année académique</label>
                        <select name="academic_year_id" id="academic_year" class="form-select" required>
                            <option value="">-- Sélectionnez une année --</option>
                            <?php foreach ($academic_years as $year): ?>
                                <option value="<?= $year['academic_year_id']; ?>">
                                    <?= htmlspecialchars($year['year_label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="notes-section mt-3">
                        <h6 class="text-success mb-3"><i class="fas fa-pen me-1"></i>Notes</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Interrogations (/20)</h6>
                                <div class="mb-3">
                                    <label class="form-label">Interrogation 1</label>
                                    <input type="number" name="int1" class="form-control" min="0" max="20" step="any" placeholder="Note sur 20">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Interrogation 2</label>
                                    <input type="number" name="int2" class="form-control" min="0" max="20" step="any" placeholder="Note sur 20">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Interrogation 3</label>
                                    <input type="number" name="int3" class="form-control" min="0" max="20" step="any" placeholder="Note sur 20">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Devoirs (/20)</h6>
                                <div class="mb-3">
                                    <label class="form-label">Devoir 1</label>
                                    <input type="number" name="dev1" class="form-control" min="0" max="20" step="any" placeholder="Note sur 20">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Devoir 2</label>
                                    <input type="number" name="dev2" class="form-control" min="0" max="20" step="any" placeholder="Note sur 20">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" name="save_notes" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Enregistrer les notes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ✅ Modal de succès -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center bg-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="successModalLabel"><i class="fas fa-check-circle me-2"></i>Succès</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body ">
        <p>La suppression a été effectuée avec succès ✅</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
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

    function ouvrirModal(id, nom, matricule) {
        document.getElementById('student-id-display').value = id;
        document.getElementById('student-name-display').innerText = nom;
        document.getElementById('student-matricule-display').innerText = matricule;
        
        // Définir la date du jour par défaut
        const aujourdhui = new Date().toISOString().split('T')[0];
        document.getElementById('note-date').value = aujourdhui;
    }

    document.getElementById('subject-select').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const semestre = selected.getAttribute('data-semester');
        const coefficient = selected.getAttribute('data-coefficient');
        if (semestre && coefficient) {
            document.getElementById('subject-semester').innerText = semestre;
            document.getElementById('subject-coefficient').innerText = coefficient;
            document.getElementById('subject-info').style.display = 'block';
        } else {
            document.getElementById('subject-info').style.display = 'none';
        }
    });

    // Affiche la date et met à jour chaque seconde
    setInterval(afficherDateHeure, 1000);
    afficherDateHeure();

    // Animation pour les cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.notation-card');
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === 'deleted') {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        // 🔄 Nettoyer l’URL pour ne pas réafficher le modal après refresh
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});
</script>
</body>
</html>