<?php
session_start();

include("../init.php");

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

// Requête pour compter uniquement les étudiants
$sql1=$connecter->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'etudiant' ");
$sql1->execute();
$resultat1=$sql1->fetch();


if ($resultat1) {
    $row1 = $resultat1;
    $nombres_etudiant=$row1['total'];
   
} else {
    $nombres_etudiant= 0; // Valeur par défaut si la requête échoue
}
// fin requête pour compter uniquement les étudiants

// Requête pour compter uniquement les enseignants
$sql2=$connecter->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'enseignant'");
$sql2->execute();
$resultat2=$sql2->fetch();


if ($resultat2) {
    $row2 = $resultat2;
    $nombres_enseignant=$row2['total'];
   
} else {
    $nombres_enseignant= 0; // Valeur par défaut si la requête échoue
}
// fin requête pour compter uniquement les enseignants  

// Requête pour compter toutes les users
$sql3=$connecter->prepare("SELECT COUNT(*) AS total FROM users ");
$sql3->execute();
$resultat3=$sql3->fetch();


if ($resultat3) {
    $row3 = $resultat3;
    $nombres_users=$row3['total'];
   
} else {
    $nombres_users= 0; // Valeur par défaut si la requête échoue
}
// fin requête pour compter toutes les users 

//requette pour compter les salles
    $totalSalles=$connecter->prepare("SELECT COUNT(*) AS total FROM classrooms");
    $totalSalles->execute();
    $resultatSalles=$totalSalles->fetch();
    if ($resultatSalles) {
        $rowSalles = $resultatSalles;
        $nombres_salles=$rowSalles['total'];
       
    } else {
        $nombres_salles= 0; // Valeur par défaut si la requête échoue
    }

// requette pour compter les etudiantes filles et garçons
$totalEtudiantes=$connecter->prepare("SELECT COUNT(*) AS total_females FROM students WHERE  gender = 'F'");
$totalEtudiantes->execute();
$resultatEtudiantes=$totalEtudiantes->fetch();
if ($resultatEtudiantes) {
    $rowEtudiantes = $resultatEtudiantes;
    $nombres_females=$rowEtudiantes['total_females']; 
     } else {
          $nombres_females= 0; // Valeur par défaut si la requête échoue
    }  


    //requette pour compter les etudiants garçons
    $totalEtudiants=$connecter->prepare("SELECT COUNT(*) AS total_males FROM students WHERE  gender = 'M'");
    $totalEtudiants->execute(); 
    $resultatEtudiants=$totalEtudiants->fetch();
    if ($resultatEtudiants) {
        $rowEtudiants = $resultatEtudiants;
        $nombres_males=$rowEtudiants['total_males']; 
         } else {   
              $nombres_males= 0; // Valeur par défaut si la requête échoue
        }

        //requette pour compter compter nombres de departments
    $totalDepartments=$connecter->prepare("SELECT COUNT(*) AS total FROM departments");
    $totalDepartments->execute();
    $resultatDepartments=$totalDepartments->fetch();
    if ($resultatDepartments) {
        $rowDepartments = $resultatDepartments;
        $nombres_departments=$rowDepartments['total'];
       
    } else {
        $nombres_departments= 0; // Valeur par défaut si la requête échoue
    }
       
//requette pour compter compter nombres de subjects
$totalSubjects=$connecter->prepare("SELECT COUNT(*) AS total FROM subjects");
$totalSubjects->execute();
$resultatSubjects=$totalSubjects->fetch();
if ($resultatSubjects) {
    $rowSubjects = $resultatSubjects;
    $nombres_subjects=$rowSubjects['total'];
} else {
    $nombres_subjects= 0; // Valeur par défaut si la requête échoue
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
    <title>Tableau de Bord Administrateur - Class Connect</title>
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
            background-color:white;
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
            color:rgb(255, 193, 7) !important;
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
            color:rgb(255, 193, 7)  !important;
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
        
        .stat-students { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-teachers { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-users { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-classrooms { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .stat-females { background: linear-gradient(135deg, #ff9a9e, #fecfef); }
        .stat-males { background: linear-gradient(135deg, #a1c4fd, #c2e9fb); }
        .stat-filieres { background: linear-gradient(135deg, #ffecd2, #fcb69f); }
        .stat-courses { background: linear-gradient(135deg, #84fab0, #8fd3f4); }
        
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
        
        /* Custom scrollbar for main content */
        .content-wrapper::-webkit-scrollbar {
            width: 15px !important;
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
        
        

        <div class="d-flex align-items-center ">
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
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar w-25 collapse ">
        <div class="pt-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="dashbord-admin.php">
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
                    <h1 class="h2 mb-1 fw-bold text-primary">Tableau de Bord</h1>
                    <p class="text-muted mb-0">Aperçu global du système Class Connect</p>
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

            <!-- Stats Cards - First Row -->
            <div class="row g-4 mb-4 px-3">
                <!-- Salles Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Salles</h6>
                                    <h2 class="fw-bold text-dark"><?= $nombres_salles ?></h2>
                                    <small class="text-success">Disponibles</small>
                                </div>
                                <div class="stat-icon stat-classrooms">
                                    <i class="fas fa-school text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Étudiants Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiants</h6>
                                    <h2 class="fw-bold text-dark"><?=$nombres_etudiant?></h2>
                                    <small class="text-success">Actifs</small>
                                </div>
                                <div class="stat-icon stat-students">
                                    <i class="fas fa-user-graduate text-white"></i>
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
                                    <h2 class="fw-bold text-dark"><?=$nombres_enseignant?></h2>
                                    <small class="text-success">En activité</small>
                                </div>
                                <div class="stat-icon stat-teachers">
                                    <i class="fas fa-chalkboard-teacher text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Utilisateurs Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Utilisateurs</h6>
                                    <h2 class="fw-bold text-dark"><?=$nombres_users?></h2>
                                    <small class="text-success">Total plateforme</small>
                                </div>
                                <div class="stat-icon stat-users">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards - Second Row -->
            <div class="row g-4 px-3">
                <!-- Filles Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiantes</h6>
                                    <h2 class="fw-bold text-dark"><?=  $nombres_females ?></h2>
                                  
                                </div>
                                <div class="stat-icon stat-females">
                                    <i class="fas fa-female text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Garçons Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Étudiants</h6>
                                    <h2 class="fw-bold text-dark"><?= $nombres_males ?></h2>
                                    
                                </div>
                                <div class="stat-icon stat-males">
                                    <i class="fas fa-male text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filières Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Filières</h6>
                                    <h2 class="fw-bold text-dark"><?=  $nombres_departments ?></h2>
                                    <small class="text-success">Actives</small>
                                </div>
                                <div class="stat-icon stat-filieres">
                                    <i class="fas fa-folder text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cours Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card stat-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-2">Matières</h6>
                                    <h2 class="fw-bold text-dark"><?= $nombres_subjects  ?></h2>
                                    <small class="text-success">En progression</small>
                                </div>
                                <div class="stat-icon stat-courses">
                                    <i class="fas fa-book-open text-white"></i>
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
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>
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