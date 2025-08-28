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
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Fillières</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <!----font awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">    <!-- Favicons -->
    <meta name="theme-color" content="#7952b3">
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    
<header class="navbar nav-bar navbar-dark  sticky-top bg-light flex-md-nowrap p-3 z-index-1 shadow"  >
  <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-dark" href="#">Class <span class="connect text-warning">Connect</span></a>
   <button class="navbar-toggler position-absolute bg-dark  mx-2 d-md-none end-0  collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon  " ></span>
  </button>
 
  <div class="d-flex w-100  justify-content-start">
   
    
    <form action=""  class="  " method="post">
          <input type="submit" class="mx-2 lien border-0 no-focus bg-light"  name="deconnexion" value="Deconnexion"> 
        </form>
  </div>
   
</header>

<div class="container-fluid z-index-2">
  <div class="row  ">
    <nav id="sidebarMenu" class="col-md-3  col-lg-2 d-lg-block bg-light sidebar px-0 collapse">
      <div class="position-fixed bg-dark vh-100 pt-2 mx-0">
        <ul class="nav flex-column ul1">
          <li class="nav-item">
            <a class="nav-link   text-warning mx-2 rounded" aria-current="page" href="dashbord-admin.php">
             <i class="fa-solid fa-graduation-cap"></i>
              Tableau de bord
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link  text-warning mx-2 rounded" href="utilisateur-admin.php">
            <i class="fa-solid fa-users"></i>
              Utilisateurs
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-warning active bg-light mx-2 rounded" href="fillieres-admin.php">
            <i class="fa-solid fa-folder"></i>
          
              Fillieres
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link  text-warning mx-2 rounded" href="cours-admin.php">
             <i class="fa-solid fa-book"></i>
              Cours
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="salles-admin.php">
             <i class="fa-solid fa-school "></i>
              Salles
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="gestion-des-etudiant-admin.php">
            <i class="fa-solid fa-user-graduate"></i>
              Gestions des étudiants
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="gestion-des-enseignants-admin.php">
              <i class="fa-solid fa-person-chalkboard"></i>
              Gestions des enseignants
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="gestion-des-emploies-du-temps-admin.php">
             <i class="fa-solid fa-calendar-days"></i>
                Gestions des emplois du temps
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="suivis-des-emargements-admin.php">
             <i class="fa-solid fa-file-signature"></i>
              Suivi des émargements
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="notes-et-resultats-admin.php">
              <i class="fa-solid fa-book-open"></i>
                Notes et résultats
            </a>
          </li>

                    <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="paiements-et-finance-admin.php">
              <i class="fa-solid fa-sack-dollar"></i>
              Paiements et finances
            </a>
          </li>

           
        </ul>

       
        
      </div>
    </nav>

    <main class="col-md-9 main vh-75  w-75 ms-lg-auto col-lg-10 px-md-4">
     <!-- Header Section -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">Fillières</h1>
            <small class="text-muted">Aperçu global</small>
        </div>
        
        <div class="d-flex align-items-center">
            <div class="bg-light text-dark rounded-pill px-3 py-1 me-3 shadow-sm">
                <i class="fas fa-clock me-2"></i>
                <span id="dateHeure"></span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-outline-light bg-warning text-dark dropdown-toggle rounded-pill" type="button" id="userDropdown" data-bs-toggle="dropdown">
                  <i class="fas fa-user-circle me-1"></i> <?=$_SESSION["Nom"]?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                   
                </ul>
            </div>
            
            <img src="../img/image.webp" class="rounded-circle ms-3" style="width:50px; height:50px; object-fit:cover; border: 2px solid #f8f9fa;" alt="Photo de profil">
        </div>
    </div>




<div class="container mt-3">
  <form class="d-flex mb-3" role="search">
    <input class="form-control me-2" type="search" placeholder="Identifiant ou nom" aria-label="Search">
    <button class="btn btn-warning" type="submit">Rechercher</button>
  </form>
</div>

  <div class="col-md-9 ms-sm-auto w-100 col-lg-10 px-md-4 py-4">
                <h2 class="h4 mb-4">
                    <i class="fas fa-project-diagram text-primary me-2"></i> Gestion des Filières
                </h2>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-sm-6 col-12 mb-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted fw-normal">Total Filières</h6>
                                        <h3 class="mb-0">10</h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                       <i class="fa-solid fa-folder"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-sm-6 col-12 mb-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted fw-normal">Filières Actives</h6>
                                        <h3 class="mb-0">10</h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                      <i class="fa-solid fa-folder"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    

                    <div class="col-xl-3 col-sm-6 col-12 mb-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="text-muted fw-normal">Étudiants</h6>
                                        <h3 class="mb-0">25</h3>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                         <i class="fa-solid fa-user-graduate"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filières Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-primary text-dark">
                                <h6 class="mb-0">Informatique</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Dupont</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">75</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">12 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-info text-dark">
                                <h6 class="mb-0">Physique</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Martin</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">45</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">8 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-success text-dark">
                                <h6 class="mb-0">Mathématiques</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Leroy</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">38</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 72%;" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">10 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">Chimie</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Dubois</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">32</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 58%;" aria-valuenow="58" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">7 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-danger text-dark">
                                <h6 class="mb-0">Biologie</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Lambert</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">28</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">5 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card filiere-card shadow-sm h-100">
                            <div class="card-header bg-secondary text-dark">
                                <h6 class="mb-0">Français</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <small class="text-muted">Responsable</small>
                                        <p class="mb-0">Prof. Durand</p>
                                    </div>
                                    <div>
                                        <small class="text-muted">Étudiants</small>
                                        <p class="mb-0">12</p>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary">3 Cours</span>
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Filière -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i> Ajouter une Nouvelle Filière
                        </h6>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="filiereName" class="form-label">Nom de la Filière</label>
                                    <input type="text" class="form-control" id="filiereName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="filiereResponsable" class="form-label">Responsable</label>
                                    <select class="form-select" id="filiereResponsable" required>
                                        <option value="">Sélectionner un enseignant</option>
                                        <option>Prof. Dupont</option>
                                        <option>Prof. Martin</option>
                                        <option>Prof. Leroy</option>
                                        <option>Prof. Dubois</option>
                                        <option>Prof. Lambert</option>
                                        <option>Prof. Durand</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="filiereDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="filiereDescription" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
              
         </div>
           





      




     






    
        
         
    </main>


    
      



    




      <footer class="footer py-4  w-75  ms-lg-auto ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-dark text-lg-start w-100">
                © 2025 , made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold " target="_blank">Fabrice DEV</a> Web Developer.
                
                 <a class=" text-dark parametre  position-absolute my-4 z-index-100 end-0 px-3 py-2" href="">
                     <i class="fa-solid fa-gear " ></i>
               </a>
              </div>
             
            </div>
           
          </div>
        </div>
      </footer>


  </div>
</div>




   





<script>
    function afficherDateHeure() {
      const maintenant = new Date();

      const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
      const mois = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin",
                    "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

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
  </script>



 
 
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
  </body>
</html>











