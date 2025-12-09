<?php
session_start();

if (!isset($_SESSION["Nom"])){
    header("Location:connexion-enseignant.php");
    exit();
}

if (!isset($_SESSION["teacher_id"])) {
    // L’utilisateur n'est pas connecté
    header("Location: connexion-enseignant.php");
    exit();
}

$teacher_id = $_SESSION["teacher_id"];

if (isset($_POST["deconnexion"])){
    $_SESSION = array();
    session_destroy();
    header("Location:connexion-enseignant.php");
    exit();
}

include("../connexion-bases.php");


// ✅ Récupération des étudiants liés à l'enseignant connecté


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
";

$stmt = $connecter->prepare($query);
$stmt->execute([$teacher_id]);
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);




if (isset($_POST['save_emargement'])) {
    $student_id = $_POST['student_id'];
    $schedule_id = $_POST['schedule_id'];
    $status = $_POST['status'];
    
    $sql = "INSERT INTO attendance_students (student_id, schedule_id, status, date_attendance) 
            VALUES (:student_id, :schedule_id, :status, NOW())";
    $insert = $connecter->prepare($sql);
    $insert->bindParam("student_id", $student_id);
    $insert->bindParam("schedule_id", $schedule_id);
    $insert->bindParam("status", $status);

    
    $insert->execute(); 
    

    header("Location: emargement.php");
    exit();
}


// affichage des donnes
$sql="SELECT 
    a.attendance_id,
    a.status,
    a.date_attendance,

    -- Infos étudiant
    s.student_id,
    s.matricule,
    s.first_name,
    s.last_name,

    -- Infos cours
    sc.schedule_id,
    sc.day,
    sc.start_time,
    sc.end_time,
    sc.date_start,
    sc.date_end,

    -- Matière
    sub.subject_id,
    sub.subject_name,

    -- Salle
    c.classroom_id,
    c.classroom_name

FROM attendance_students a

JOIN students s 
    ON a.student_id = s.student_id

JOIN schedules sc 
    ON a.schedule_id = sc.schedule_id

JOIN subjects sub 
    ON sc.subject_id = sub.subject_id

JOIN classrooms c
    ON sc.classroom_id = c.classroom_id

ORDER BY a.attendance_id DESC;


";
$stmt = $connecter->prepare($sql);
$stmt->execute();
$datas = $stmt->fetchAll(PDO::FETCH_ASSOC);

//supprimer un enregistrement
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $attendance_id = $_GET['id'];

    $sql = "DELETE FROM attendance_students WHERE attendance_id = :attendance_id";
    $stmt = $connecter->prepare($sql);
    $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: emargement.php");
    exit();
}


//edit emargements
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $attendance_id = $_GET['id'];

    if (isset($_POST['update_emargement'])) {
        $status = $_POST['status'];

        $sql = "UPDATE attendance_students SET status = :status WHERE attendance_id = :attendance_id";
        $stmt = $connecter->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: emargement.php");
        exit();
    }

    // Récupérer les données actuelles pour pré-remplir le formulaire
    $sql = "SELECT * FROM attendance_students WHERE attendance_id = :attendance_id";
    $stmt = $connecter->prepare($sql);
    $stmt->bindParam(':attendance_id', $attendance_id, PDO::PARAM_INT);
    $stmt->execute();
    $emargement = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Émargements - Class Connect</title>
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
        
        .emargement-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .emargement-card .card-header {
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
        
        .form-check-input:checked {
            background-color: var(--success);
            border-color: var(--success);
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
        
        .stat-card, .emargement-card {
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
                    <a class="nav-link active" href="./emargement.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Émargements</h1>
                    <p class="text-muted mb-0">Gestion des présences des étudiants</p>
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

            <!-- Émargement Card -->
            <div class="container mt-4">
                <div class="card emargement-card">
                  
                    <div class="card-body">
                       
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
                                                        <i class="fas fa-pen me-1"></i> Marquer la présence
                                                    </button>
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
            </div>


            <!--Affichage des infos dans un tableau   --->

            <table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Étudiant</th>
            <th>Matricule</th>
            <th>Matière</th>
            <th>Date</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Salle</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($datas as $row): ?>
        <tr>
            <td><?= $row['first_name'] . ' ' . $row['last_name'] ?></td>
            <td><?= $row['matricule'] ?></td>
            <td><?= $row['subject_name'] ?></td>
            <td><?= $row['day'] ?></td>
            <td><?= $row['start_time'] ?></td>
            <td><?= $row['end_time'] ?></td>
            <td><?= htmlspecialchars($row['classroom_name']) ?></td>
            <td>
    <?php
        $status = $row['status'];
        $badgeClass = '';

        switch ($status) {
            case 'present':
                $badgeClass = 'badge bg-success';
                break;

            case 'absent':
                $badgeClass = 'badge bg-danger';
                break;

            case 'en retard':
            case 'retard':
                $badgeClass = 'badge bg-warning text-dark';
                break;

            default:
                $badgeClass = 'badge bg-secondary';
                break;
        }
       
    ?>

    <span class="<?= $badgeClass ?>">
        <?= ucfirst($status); ?>
    </span>
</td>
           <td>
                <div class="btn-group" role="group">
                                        <!-- Bouton Edit -->
                    <button class="btn btn-sm btn-outline-primary me-1" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal<?= $row['attendance_id'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- Bouton Delete (tu l'avais déjà) -->
                    <a class="btn btn-sm btn-outline-danger me-1" 
                    href="emargement.php?action=delete&id=<?= $row['attendance_id'] ?>">
                        <i class="fas fa-trash"></i>
                    </a>

                </div>

           </td>
        </tr>
        

        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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

            <form method="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="notationModalLabel">Noter l'étudiant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- INFORMATIONS DE L'ÉTUDIANT -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-primary" id="student-name-display"></h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-id-card me-1"></i>
                                Matricule: <span id="student-matricule-display"></span>
                            </p>
                        </div>

                        <!-- ID ÉTUDIANT (Hidden) -->
                        <input type="hidden" id="student-id-display" name="student_id">
                    </div>

                    <!-- MATIÈRE -->
                     <div class="mb-4">
                        <label for="subject_select" class="form-label">Matière :</label>
                     <select id="subject_select" name="schedule_id" class="form-select" required>
                            <option value="">Choisir une matière...</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['schedule_id'] ?>" 
                                        data-filiere="<?= htmlspecialchars($subject['department_name']) ?>">
                                    <?= htmlspecialchars($subject['subject_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                     </div>

                    <!-- INFORMATIONS DU COURS -->
                    <div id="details_cours" class="row mb-4">

                        <div class="col-md-3">
                            <label for="date_cours" class="form-label">Date :</label>
                            <input type="text" id="date_cours" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="heure_debut" class="form-label">Heure début :</label>
                            <input type="text" id="heure_debut" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="heure_fin" class="form-label">Heure fin :</label>
                            <input type="text" id="heure_fin" class="form-control" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="salle" class="form-label">Salle :</label>
                            <input type="text" id="salle" class="form-control" readonly>
                        </div>

                        <!-- SCHEDULE ID (Hidden) -->
                        <input type="hidden" id="schedule_id_hidden" name="schedule_id">
                    </div>

                    <!-- STATUS PRÉSENCE -->
                    <div class="mb-4">
                        <label for="status" class="form-label">Présence :</label>
                        <select id="status" name="status" class="form-select" required>
                            <option value="" disabled selected>Choisir...</option>
                            <option value="present">Présent(e)</option>
                            <option value="absent">Absent(e)</option>
                            <option value="late">En retard</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" name="save_emargement" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer l'émargement
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!--editModal-->

<!-- Modal Edit -->
<div class="modal fade" id="editModal<?= $row['attendance_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="emargement.php?action=edit&id=<?= $row['attendance_id'] ?>">

                <div class="modal-header">
                    <h5 class="modal-title">Modifier l’émargement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label class="form-label">Status :</label>
                        <select name="status" class="form-select" required>

                            <option value="present" 
                                <?= ($row['status'] == 'present') ? 'selected' : '' ?>>
                                Present
                            </option>

                            <option value="absent" 
                                <?= ($row['status'] == 'absent') ? 'selected' : '' ?>>
                                Absent
                            </option>

                            <option value="en retard" 
                                <?= ($row['status'] == 'en retard') ? 'selected' : '' ?>>
                                En retard
                            </option>

                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Annuler
                    </button>

                    <button type="submit" name="update_emargement" class="btn btn-primary">
                        Enregistrer
                    </button>
                </div>

            </form>

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

    setInterval(afficherDateHeure, 1000);
    afficherDateHeure();

    // Animation pour les cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card, .emargement-card');
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

    function ouvrirModal(id, nom, matricule) {
        document.getElementById('student-id-display').value = id;
        document.getElementById('student-name-display').innerText = nom;
        document.getElementById('student-matricule-display').innerText = matricule;
        
       
    }


    
</script>

<script>
document.getElementById('subject_select').addEventListener('change', function() {
  let scheduleId = this.value;

  if (scheduleId) {
    fetch('get-subjects.php?schedule_id=' + scheduleId)
      .then(response => response.json())
      .then(data => {
        document.getElementById('date_cours').value = data.day;
        document.getElementById('heure_debut').value = data.start_time;
        document.getElementById('heure_fin').value = data.end_time;
        document.getElementById('salle').value = data.classroom_name;
        document.getElementById('schedule_id_hidden').value = data.schedule_id;
      });
  }
});
</script>

</body>
</html>