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

            <!-- Finance Content -->
            <div class="container-fluid px-3">
                <!-- Header Card -->
                <div class="card finance-card">
                    <div class="card-header text-center">
                        <h3 class="card-title mb-0 text-white"><i class="fas fa-money-bill-wave me-2"></i>Suivi des Paiements par Filière</h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Filière Informatique -->
                    <div class="col-md-12">
                        <div class="card finance-card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-laptop-code me-2"></i>Filière Informatique
                                    </h5>
                                    <div>
                                        <span class="badge bg-success badge-status">15 soldés</span>
                                        <span class="badge bg-warning text-dark badge-status mx-2">5 partiels</span>
                                        <span class="badge bg-danger badge-status">3 impayés</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
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
                                            <!-- Étudiant soldé -->
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Jean Dupont</div>
                                                    <small class="text-muted">ETU001</small>
                                                </td>
                                                <td>200 000 FCFA</td>
                                                <td>200 000 FCFA</td>
                                                <td class="solde solde-positive">0 FCFA</td>
                                                <td>
                                                    <span class="badge bg-success badge-status">Soldé</span>
                                                </td>
                                                <td>
                                                    <div>15/09/2023</div>
                                                    <small class="payment-date">(Il y a 2 mois)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="showPaymentDetails('ETU001', 'Jean Dupont')">
                                                        <i class="fas fa-receipt"></i> Reçu
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Étudiant partiel -->
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Sophie Martin</div>
                                                    <small class="text-muted">ETU002</small>
                                                </td>
                                                <td>200 000 FCFA</td>
                                                <td>120 000 FCFA</td>
                                                <td class="solde solde-negative">80 000 FCFA</td>
                                                <td>
                                                    <span class="badge bg-warning text-dark badge-status">Partiel</span>
                                                </td>
                                                <td>
                                                    <div>05/10/2023</div>
                                                    <small class="payment-date">(Il y a 1 mois)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-success btn-action" onclick="showPaymentDetails('ETU002', 'Sophie Martin')">
                                                        <i class="fas fa-plus"></i> Payer
                                                    </button>
                                                </td>
                                            </tr>
                                            
                                            <!-- Étudiant impayé -->
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Pierre Bernard</div>
                                                    <small class="text-muted">ETU003</small>
                                                </td>
                                                <td>200 000 FCFA</td>
                                                <td>0 FCFA</td>
                                                <td class="solde solde-negative">200 000 FCFA</td>
                                                <td>
                                                    <span class="badge bg-danger badge-status">Impayé</span>
                                                </td>
                                                <td>
                                                    <div>-</div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-danger btn-action" onclick="showPaymentDetails('ETU003', 'Pierre Bernard')">
                                                        <i class="fas fa-bell"></i> Rappeler
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filière Biologie -->
                    <div class="col-md-12 mt-4">
                        <div class="card finance-card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 text-white">
                                        <i class="fas fa-dna me-2"></i>Filière Biologie
                                    </h5>
                                    <div>
                                        <span class="badge bg-success badge-status">8 soldés</span>
                                        <span class="badge bg-warning text-dark badge-status mx-2">2 partiels</span>
                                        <span class="badge bg-danger badge-status">1 impayé</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
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
                                            <!-- Exemple étudiant biologie -->
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">Marie Leroy</div>
                                                    <small class="text-muted">ETU004</small>
                                                </td>
                                                <td>180 000 FCFA</td>
                                                <td>180 000 FCFA</td>
                                                <td class="solde solde-positive">0 FCFA</td>
                                                <td>
                                                    <span class="badge bg-success badge-status">Soldé</span>
                                                </td>
                                                <td>
                                                    <div>20/09/2023</div>
                                                    <small class="payment-date">(Il y a 2 mois)</small>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary btn-action" onclick="showPaymentDetails('ETU004', 'Marie Leroy')">
                                                        <i class="fas fa-receipt"></i> Reçu
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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

<!-- Modal Détails Paiements -->
<div class="modal fade" id="detailsPaiementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historique des Paiements - <span id="modal-etudiant-name"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Filière:</strong> <span id="modal-filiere">Informatique</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Matricule:</strong> <span id="modal-matricule">ETU001</span>
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
                                <th>Action</th>
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
                            <strong>Total payé:</strong> <span id="modal-total-paye">200 000</span> FCFA
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Reste à payer:</strong> <span id="modal-reste">0</span> FCFA
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn user-dropdown">
                    <i class="fas fa-print me-1"></i>Imprimer le reçu
                </button>
            </div>
        </div>
    </div>
</div>

<!-------debut paiements script----->
<script>
    // Données des paiements (exemple)
    const paiementsEtudiants = {
        'ETU001': [
            { date: '2023-09-10', type: 'Scolarité', moyen: 'Mobile Money', montant: 100000, reference: 'MOMO1234' },
            { date: '2023-10-05', type: 'Scolarité', moyen: 'Espèces', montant: 100000, reference: '' }
        ],
        'ETU002': [
            { date: '2023-09-15', type: 'Scolarité', moyen: 'Virement', montant: 120000, reference: 'VIR7890' }
        ],
        'ETU004': [
            { date: '2023-09-20', type: 'Scolarité', moyen: 'Chèque', montant: 180000, reference: 'CHQ4567' }
        ]
    };

    // Afficher les détails des paiements dans le modal
    function showPaymentDetails(matricule, nom) {
        const paiements = paiementsEtudiants[matricule] || [];
        const tbody = document.getElementById('modal-paiements-body');
        tbody.innerHTML = '';
        
        let totalPaye = 0;
        
        paiements.forEach(p => {
            totalPaye += p.montant;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${new Date(p.date).toLocaleDateString('fr-FR')}</td>
                <td>${p.type}</td>
                <td>${p.moyen}</td>
                <td>${p.montant.toLocaleString()} FCFA</td>
                <td>${p.reference || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Mettre à jour les totaux
        document.getElementById('modal-total-paye').textContent = totalPaye.toLocaleString();
        document.getElementById('modal-etudiant-name').textContent = nom;
        document.getElementById('modal-matricule').textContent = matricule;
        
        // Calculer le reste (exemple: frais fixes)
        const fraisTotaux = matricule === 'ETU004' ? 180000 : 200000;
        const reste = fraisTotaux - totalPaye;
        document.getElementById('modal-reste').textContent = reste.toLocaleString();
        
        // Afficher le modal
        const modal = new bootstrap.Modal(document.getElementById('detailsPaiementModal'));
        modal.show();
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la hauteur du sidebar
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
        
        // Animation pour les cartes au chargement
        const cards = document.querySelectorAll('.finance-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
        
        // Démarrer les tooltips Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
<!-------fin paiements script----->

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

<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

</body>
</html>