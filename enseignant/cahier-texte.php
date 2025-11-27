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
        
        .cahier-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }
        
        .cahier-card .card-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            padding: 20px;
        }
        
        .history-card {
            background: white;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .history-card .card-header {
            background: linear-gradient(135deg, #6c757d, #495057);
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
        
        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
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
        
        .form-check-input:checked {
            background-color: var(--success);
            border-color: var(--success);
        }
        
        /* List Group Styles */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #e9ecef;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        /* Badge Styles */
        .badge {
            font-size: 0.75rem;
            padding: 6px 12px;
            border-radius: 20px;
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
        
        .stat-card, .cahier-card, .history-card {
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

            <!-- Formulaire du cahier de texte -->
            <div class="card cahier-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Nouvelle entrée du cahier de texte</h4>
                </div>
                <div class="card-body">
                    <form id="form-cahier-texte">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Matière</label>
                                <select class="form-select" required>
                                    <option value="">Sélectionner...</option>
                                    <option>Algorithmique</option>
                                    <option>Programmation</option>
                                    <option>Bases de données</option>
                                    <option>Réseaux</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date du cours</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heure début</label>
                                <input type="time" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Heure fin</label>
                                <input type="time" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Salle</label>
                                <input type="text" class="form-control" placeholder="Salle A12" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Contenu du cours</label>
                            <textarea class="form-control" style="resize: none;" rows="4" placeholder="Détail du contenu enseigné..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Objectifs pédagogiques</label>
                            <textarea class="form-control" style="resize: none;" rows="2" placeholder="Objectifs visés pour cette séance..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Objectif atteint ?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="objectifAtteint" value="oui" id="objectif-oui" required>
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

                        <div class="mb-4" id="raisons-container" style="display: none;">
                            <label class="form-label">Raisons de la non-atteinte des objectifs</label>
                            <textarea class="form-control" style="resize: none;" rows="3" placeholder="Expliquez les raisons..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Travail à faire</label>
                            <textarea class="form-control" style="resize: none;" rows="2" placeholder="Devoirs, exercices, lectures..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Avis/Remarques</label>
                            <textarea class="form-control" style="resize: none;" rows="2" placeholder="Observations sur le déroulement du cours..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Signature</label>
                            <input type="text" class="form-control" placeholder="Nom et signature" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i>Enregistrer le cours
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historique des cours -->
            <div class="card history-card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Historique des Cours</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-primary">Algorithmique - Structures de données</h6>
                                    <p class="mb-2 text-muted">
                                        <i class="fas fa-calendar me-1"></i>12/11/2023 - 
                                        <i class="fas fa-clock me-1 ms-2"></i>8h-10h - 
                                        <i class="fas fa-door-open me-1 ms-2"></i>Salle A12
                                    </p>
                                    <p class="mb-1">Introduction aux listes chaînées et arbres binaires. Implémentation des opérations de base.</p>
                                    <small class="text-muted">
                                        <i class="fas fa-tasks me-1"></i>Travail à faire: Exercices sur les listes chaînées
                                    </small>
                                </div>
                                <span class="badge bg-success ms-3">
                                    <i class="fas fa-check me-1"></i>Objectif atteint
                                </span>
                            </div>
                        </div>
                        
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-primary">Programmation - POO</h6>
                                    <p class="mb-2 text-muted">
                                        <i class="fas fa-calendar me-1"></i>10/11/2023 - 
                                        <i class="fas fa-clock me-1 ms-2"></i>10h-12h - 
                                        <i class="fas fa-door-open me-1 ms-2"></i>Labo Info
                                    </p>
                                    <p class="mb-1">Problèmes techniques avec les postes étudiants. Cours reporté à la semaine prochaine.</p>
                                    <small class="text-muted">
                                        <i class="fas fa-tasks me-1"></i>Travail à faire: Révision des concepts POO
                                    </small>
                                </div>
                                <span class="badge bg-danger ms-3">
                                    <i class="fas fa-times me-1"></i>Objectif non atteint
                                </span>
                            </div>
                        </div>

                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-primary">Bases de données - SQL Avancé</h6>
                                    <p class="mb-2 text-muted">
                                        <i class="fas fa-calendar me-1"></i>08/11/2023 - 
                                        <i class="fas fa-clock me-1 ms-2"></i>14h-16h - 
                                        <i class="fas fa-door-open me-1 ms-2"></i>Salle B08
                                    </p>
                                    <p class="mb-1">Requêtes complexes avec jointures multiples, sous-requêtes et fonctions d'agrégation.</p>
                                    <small class="text-muted">
                                        <i class="fas fa-tasks me-1"></i>Travail à faire: Projet base de données bibliothèque
                                    </small>
                                </div>
                                <span class="badge bg-success ms-3">
                                    <i class="fas fa-check me-1"></i>Objectif atteint
                                </span>
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

    // Affiche la date et met à jour chaque seconde
    setInterval(afficherDateHeure, 1000);
    afficherDateHeure(); // première exécution immédiate

    // Gestion de l'affichage des raisons si objectif non atteint
    document.querySelectorAll('input[name="objectifAtteint"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const raisonsContainer = document.getElementById('raisons-container');
            raisonsContainer.style.display = this.value === 'non' ? 'block' : 'none';
        });
    });

    // Validation du formulaire
    document.getElementById('form-cahier-texte').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Simulation d'enregistrement réussi
        const toast = document.createElement('div');
        toast.className = 'position-fixed top-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Succès</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    Cours enregistré avec succès dans le cahier de texte !
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        // Supprimer le toast après 3 secondes
        setTimeout(() => {
            toast.remove();
        }, 3000);
        
        // Réinitialiser le formulaire
        this.reset();
        document.getElementById('raisons-container').style.display = 'none';
    });

    // Animation pour les cartes au chargement
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.cahier-card, .history-card');
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