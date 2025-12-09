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





$sql="SELECT 
    s.student_id,
    s.matricule,
    s.first_name,
    s.last_name,
    l.level_name,
    d.department_name AS dep_name
FROM students s
INNER JOIN levels l ON s.level_id = l.level_id
INNER JOIN departments d ON l.department_id = d.department_id
ORDER BY s.student_id DESC; ";

$recuperer = $connecter->prepare($sql);
$recuperer->execute();
$students = $recuperer->fetchAll(PDO::FETCH_ASSOC);




if (isset($_POST['submit_payment'])) {
   if (!empty($_POST['student_id']) && !empty($_POST['montant_paiement'])) {
    // on recupere les valeurs des champs
    $student_id = $_POST['student_id'];
    $montant_paiement = $_POST['montant_paiement'];
    $type_paiement =$_POST['type_paiement'];
    $methode_paiement = $_POST['methode_paiement'];
    //insertion dans la base de donnee
    $insert_payment_sql = "INSERT INTO payments (student_id, amount,types_paiement , method, payment_date) 
                           VALUES (:student_id, :amount,:types_paiement ,:method, NOW())";

    $insert_payment_stmt = $connecter->prepare($insert_payment_sql);
    $insert_payment_stmt->bindParam(':student_id', $student_id);
    $insert_payment_stmt->bindParam(':amount', $montant_paiement);
    $insert_payment_stmt->bindParam(':types_paiement', $type_paiement);
    $insert_payment_stmt->bindParam(':method', $methode_paiement);
    $insert_payment_stmt->execute();
    // Redirection après l'insertion
    header("Location: paiements-et-finance-admin.php");
    exit();

}
    
    
};





$departments = $connecter->query("
    SELECT department_id, department_name, amount AS total_due
    FROM departments
")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les étudiants d'un département
function getStudentsByDepartment($connecter, $dep_id) {
    $sql = $connecter->prepare("
        SELECT 
            s.student_id,
            s.matricule,
            s.first_name,
            s.last_name,
            d.department_name,
            d.amount AS total_due,

            COALESCE(SUM(p.amount), 0) AS total_paid,
            d.amount - COALESCE(SUM(p.amount), 0) AS reste,

            CASE
                WHEN COALESCE(SUM(p.amount), 0) >= d.amount THEN 'Soldé'
                WHEN COALESCE(SUM(p.amount), 0) = 0 THEN 'Impayé'
                ELSE 'Partiel'
            END AS statut,

            MAX(p.payment_date) AS dernier_paiement

        FROM students s
        JOIN levels l ON s.level_id = l.level_id
        JOIN departments d ON l.department_id = d.department_id
        LEFT JOIN payments p ON s.student_id = p.student_id
        WHERE d.department_id = ?
        GROUP BY s.student_id
        ORDER BY s.last_name ASC
    ");

    $sql->execute([$dep_id]);
    return $sql->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Paiements et Finances - Class Connect</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <!-- Bootstrap core CSS -->
     <link rel="stylesheet" href="../bootstrap-5.3.7\bootstrap-5.3.7\dist\css\bootstrap.min.css">
     <link rel="stylesheet" href="../fontawesome\css\all.min.css">
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
        
        .finance-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        
        .finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .finance-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 20px;
            font-weight: 600;
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

        /* Finance Specific Styles */
        .solde {
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .solde-positive {
            color: #28a745;
        }
        
        .solde-negative {
            color: #dc3545;
        }
        
        .badge-status {
            font-size: 0.85em;
            padding: 6px 10px;
            border-radius: 20px;
        }
        
        .payment-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .filiere-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .table th {
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
            transform: translateX(5px);
            transition: all 0.3s ease;
        }
        
        .btn-action {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: scale(1.05);
        }
        
        /* Modal Styles */
        .modal-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
            
            .stat-card, .finance-card {
                margin-bottom: 1rem;
            }
            
            .main-container {
                flex-direction: column;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-action {
                margin-bottom: 5px;
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
        
        .stat-card, .finance-card {
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
                    <a class="nav-link active" href="paiements-et-finance-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Paiements et Finances</h1>
                    <p class="text-muted mb-0">Gestion des paiements et suivi financier</p>
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

            <!-- Finance Content -->
            <div class="container-fluid px-3">
                <!-- Header Card -->
                <div class="card finance-card">
                    <div class="card-header text-center">
                        <h3 class="card-title mb-0 text-white"><i class="fas fa-money-bill-wave me-2"></i>Suivi des Paiements par Filière</h3>
                    </div>
                </div>

               <button class="btn btn-success btn-lg w-100 mb-3" onclick="ouvrirModalPaiement()">
                        💵 Paiement en Espèces
                    </button>


                
<div class="container py-4">

    <h2 class="mb-4"><i class="fas fa-coins me-2"></i>Gestion Financière des Étudiants</h2>

    <?php foreach ($departments as $dep): ?>
        <?php
            // Charger les étudiants de cette filière
            $studentsDept = getStudentsByDepartment($connecter, $dep['department_id']);

            // Compter les statuts
            $soldes = 0; $partiels = 0; $impayes = 0;

            foreach ( $studentsDept as $s) {
                if ($s['statut'] === 'Soldé') $soldes++;
                elseif ($s['statut'] === 'Partiel') $partiels++;
                else $impayes++;
            }
        ?>

        <!-- ======================================================
              CARTE FILIÈRE / DÉPARTEMENT
        ======================================================= -->
        <div class="card finance-card">

            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building-columns me-2"></i>
                        <?= $dep['department_name'] ?>
                    </h5>

                    <div>
                        <span class="badge bg-success badge-status"><?= $soldes ?> soldés</span>
                        <span class="badge bg-warning text-dark badge-status mx-2"><?= $partiels ?> partiels</span>
                        <span class="badge bg-danger badge-status"><?= $impayes ?> impayés</span>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <?php if (count($studentsDept) === 0): ?>
                    <p class="text-muted">Aucun étudiant dans cette filière.</p>
                <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Total dû</th>
                            <th>Payé</th>
                            <th>Reste</th>
                            <th>Statut</th>
                            <th>Dernier Paiement</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($studentsDept as $stu): ?>

                            <?php
                                // Badge de statut
                                $badge = match($stu['statut']) {
                                    'Soldé' => '<span class="badge bg-success">Soldé</span>',
                                    'Partiel' => '<span class="badge bg-warning text-dark">Partiel</span>',
                                    default => '<span class="badge bg-danger">Impayé</span>',
                                };

                                


                                // Date
                                $date_pay = $stu['dernier_paiement'] ? date("d/m/Y", strtotime($stu['dernier_paiement'])) : "-";
                            ?>

                            <tr>
                                <td>
                                    <strong><?= $stu['first_name'] . ' ' . $stu['last_name'] ?></strong><br>
                                    <small class="text-muted"><?= $stu['matricule'] ?></small>
                                </td>

                                <td><?= number_format($stu['total_due'], 0, "", " ") ?> FCFA</td>
                                <td><?= number_format($stu['total_paid'], 0, "", " ") ?> FCFA</td>

                                <td class="<?= ($stu['reste'] > 0 ? 'solde-negative' : 'solde-positive') ?>">
                                    <?= number_format($stu['reste'], 0, "", " ") ?> FCFA
                                </td>

                                <td><?= $badge ?></td>

                                <td><?= $date_pay ?></td>

                                <td>
                                    <button 
                                        class="btn btn-sm btn-outline-primary btn-action"
                                        data-studentid="<?= $stu['student_id'] ?>"
                                        data-name="<?= $stu['first_name'] . ' ' . $stu['last_name'] ?>"
                                        data-matricule="<?= $stu['matricule'] ?>"
                                        data-filiere="<?= $stu['department_name'] ?>"
                                        onclick="openPaiementModal(this)">
                                        <i class="fa fa-receipt"></i> Reçu
                                    </button>



                                </td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>

                <?php endif; ?>

            </div>
        </div>

    <?php endforeach; ?>

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






<!-- Modal Détails Paiements -->
<div class="modal fade" id="detailsPaiementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title">Historique des Paiements </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="print-area"> 
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Nom et Prénom:</strong> <span id="modal-etudiant-name"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Filière:</strong> <span id="modal-filiere"></span>
                    </div>
                    
                    <div class="col-md-6">
                        <strong>Matricule:</strong> <span id="modal-matricule"></span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Moyen</th>
                                <th>Montant</th>
                                <th>Référence</th>
                               
                            </tr>
                        </thead>
                        <tbody id="modal-paiements-body">
                            <!-- Rempli dynamiquement -->
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Total payé:</strong> <span id="modal-total-paye"></span> FCFA
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Reste à payer:</strong> <span id="modal-reste"></span> FCFA
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" id="btn-print-recu" class="btn user-dropdown">
                    <i class="fas fa-print me-1"></i>Imprimer le reçu
                </button>
            </div>
        </div>
    </div>
</div>
  <!-----  paiements especes -->

 


<!-- ======== MODAL ======== -->
<div id="modalPaiement" style="
    display:none;
    position:fixed;
    z-index:1000;
    left:0;
    top:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
">
    <div style="
        background:white;
        width:450px;
        margin:80px auto;
        padding:25px;
        border-radius:8px;
        box-shadow:0 0 10px #666;
        position:relative;
    ">
        
        <!-- Bouton fermeture -->
        <span onclick="fermerModalPaiement()" 
              style="position:absolute; right:15px; top:10px; cursor:pointer; font-size:20px;">
            &times;
        </span>

        <h3 style="text-align:center; margin-bottom:20px;">
            💵 Paiement en Espèces – Administration
        </h3>

        <form method="POST">

            <label>Nom de l'Étudiant :</label>
            <select name="student_id" required
                style="width:100%; padding:10px; margin-top:8px; border:1px solid #888; border-radius:5px;">
                <option value="" disabled selected>Choisir un étudiant</option>

                <?php foreach ($students as $etudiant): ?>
                    <option value="<?= $etudiant['student_id']; ?>">
                        <?= $etudiant['first_name'] . ' ' . $etudiant['last_name'] . ' ( ' . $etudiant['matricule'] . ' - ' . $etudiant['level_name'] . ' - ' . $etudiant['dep_name'] . ' )'; ?>
                    </option>
                <?php endforeach; ?>

            </select>




            <label style="margin-top:15px; display:block;">Montant à payer (FCFA) :</label>
            <input 
                type="number" 
                name="montant_paiement" 
                placeholder="Ex : 150.000 FCFA" 
                required
                style="width:100%; padding:10px; margin-top:8px; border:1px solid #888; border-radius:5px;"
            >

            <label for="type_paiement" style="margin-top:15px; display:block;">Motif du paiement :</label>
            <select name="type_paiement" required
                style="width:100%; padding:10px; margin-top:8px; border:1px solid #888; border-radius:5px;">
                <option value="" disabled selected>Choisir un motif</option>
                <option value="Scolarité">Scolarité</option>
                <option value="Inscription">Inscription</option>
                <option value="Autre">Autre</option>
            </select>

            <label for="methode_paiement" style="margin-top:15px; display:block;">Méthode de paiement:</label>
            <select name="methode_paiement" id="methode_paiement" required
                style="width:100%; padding:10px; margin-top:8px; border:1px solid #888; border-radius:5px;">
                <option value="" disabled selected>Choisir une méthode</option>
                <option value="Espèces">Espèces</option>
            </select>

            <button 
                type="submit" 
                name="submit_payment"
                style="width:100%; padding:12px; background:#198754; color:white; border:none; border-radius:5px; margin-top:20px; cursor:pointer;">
                Enregistrer le paiement
            </button>
        </form>
    </div>
</div>







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
</script>
<!-- ======== JAVASCRIPT POUR LE MODAL ======== -->
<script>
function ouvrirModalPaiement() {
    document.getElementById("modalPaiement").style.display = "block";
}

function fermerModalPaiement() {
    document.getElementById("modalPaiement").style.display = "none";
}
</script>



<!-- ======== JAVASCRIPT POUR LE MODAL DÉTAILS PAIEMENTS ======== -->
<script>
function openPaiementModal(button) {
    const studentId = button.getAttribute('data-studentid');
    const studentName = button.getAttribute('data-name');
    const matricule = button.getAttribute('data-matricule');
    const filiere = button.getAttribute('data-filiere');

    // Mettre à jour les informations de l'étudiant dans le modal
    document.getElementById('modal-etudiant-name').innerText = studentName;
    document.getElementById('modal-matricule').innerText = matricule;
    document.getElementById('modal-filiere').innerText = filiere;

    // Vider le corps du tableau des paiements
    const paiementsBody = document.getElementById('modal-paiements-body');
    paiementsBody.innerHTML = '';

    // Faire une requête AJAX pour récupérer les paiements
    fetch(`fetch_payments.php?student_id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            let totalPaye = data.total_paye;
            let reste = data.reste;

            // Mettre à jour les totaux dans le modal
            document.getElementById('modal-total-paye').innerText = totalPaye.toLocaleString();
            document.getElementById('modal-reste').innerText = reste.toLocaleString();

            // Remplir le tableau des paiements
            data.paiements.forEach(payment => {
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>${payment.payment_date}</td>
                    <td>${payment.types_paiement}</td>
                    <td>${payment.method}</td>
                    <td>${parseInt(payment.amount).toLocaleString()} FCFA</td>
                    <td>${payment.reference}</td>
                    
                `;

                paiementsBody.appendChild(row);
            });

            // Afficher le modal
            const paiementModal = new bootstrap.Modal(document.getElementById('detailsPaiementModal'));
            paiementModal.show();
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des paiements:', error);
        });
}
</script>




<script>
    document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("btn-print-recu").addEventListener("click", function () {

        // Sélectionner la zone à imprimer
        let content = document.getElementById("print-area").innerHTML;

        // Ouvrir une nouvelle fenêtre temporaire
        let win = window.open('', '', 'width=900,height=700');

        win.document.write(`
            <html>
            <head>
                <title>Reçu de Paiement</title>

                <!-- Importer Bootstrap pour garder le style -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

                <style>
                    body {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    h5 {
                        text-align: center;
                        margin-bottom: 20px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid #ccc;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                </style>

            </head>
            <body>
                <h4 class="text-center">Reçu de Paiement</h4>
                ${content}
            </body>
            </html>
        `);

        win.document.close();

        // Attendre un peu pour charger le CSS avant impression
        setTimeout(() => {
            win.print();
            win.close();
        }, 500);

    });

});

</script>







<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>