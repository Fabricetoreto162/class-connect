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


  // Début connexion à la base de données
        $serveur = "localhost";
        $name = "root";
        $password = "";

        try {
            $connecter = new PDO("mysql:host=$serveur;dbname=gestion_des_etudiants;charset=utf8", $name, $password);
            $connecter->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
        // Fin connexion à la base de données

        
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
// fin requête pour compter uniquement les étudiants  


        
// Requête pour compter uniquement les admins
$sql3=$connecter->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
$sql3->execute();
$resultat3=$sql3->fetch();


if ($resultat3) {
    $row3 = $resultat3;
    $nombres_admin=$row3['total'];
   
} else {
    $nombres_admin= 0; // Valeur par défaut si la requête échoue
}
// fin requête pour compter uniquement les admins



?>




<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Utilisateurs</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <!----font awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap core CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">    <!-- Favicons -->
    <meta name="theme-color" content="#7952b3">
    <link rel="stylesheet" href="style.css">

<style>
    .card-stat {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-stat:nth-child(1):hover {
        border-left-color: #212529;
    }
    
    .card-stat:nth-child(2):hover {
        border-left-color: #dc3545;
    }
    
    .card-stat:nth-child(3):hover {
        border-left-color: #198754;
    }
    
    .icon-container {
        transition: transform 0.3s ease;
    }
    
    .card-stat:hover .icon-container {
        transform: scale(1.1);
    }
</style>



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
            <a class="nav-link  text-warning mx-2 rounded" aria-current="page" href="dashbord-admin.php">
             <i class="fa-solid fa-graduation-cap"></i>
              Tableau de bord
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning active bg-light mx-2 rounded" href="utilisateur-admin.php">
            <i class="fa-solid fa-users"></i>
              Utilisateurs
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="fillieres-admin.php">
             <i class="fa-solid fa-folder"></i>
              Fillieres
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="cours-admin.php">
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

   
<main class="col-md-9 ms-lg-auto col-lg-10 px-md-4 w-75">
    <!-- Header Section -->
     <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">Utilisateurs</h1>
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



<div class="row g-4 justify-content-center">
    <!-- Administrateurs Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat shadow-sm border-0 h-100 hover-scale">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="icon-container bg-dark bg-opacity-10 rounded p-3">
                        <i class="fas fa-user-shield fa-2x text-light"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-2">Administrateurs</h6>
                        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal">
                            <h3 class="mb-0 bg-dark text-light rounded p-2"><?=$nombres_admin?></h3>
                        </button>
                    </div>
                </div>
               
            </div>
        </div>
    </div>

    <!-- Étudiants Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat shadow-sm border-0 h-100  hover-scale">
            <div class="card-body p-2">
                <div class="d-flex flex-direction-column justify-content-between align-items-center">
                    <div class="icon-container bg-danger bg-opacity-10 rounded p-3">
                        <i class="fas fa-user-graduate fa-2x text-light"></i>
                    </div> 
                    <div class="text-bottom">
                        <h6 class="text-muted mb-2">Étudiants</h6>
                        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#etudiantsModal">
                            <h3 class="mb-0 rounded bg-danger text-light p-2"><?=$nombres_etudiant?></h3>
                        </button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Professeurs Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat shadow-sm border-0 h-100 hover-scale">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="icon-container bg-success bg-opacity-10 rounded p-3">
                        <i class="fas fa-chalkboard-teacher fa-2x text-light"></i>
                    </div>
                    <div class="text-end">
                        <h6 class="text-muted mb-2">Professeurs</h6>
                        <button class="btn btn-link p-0 text-decoration-none" data-bs-toggle="modal" data-bs-target="#professeursModal">
                            <h3 class="mb-0 rounded bg-success text-light p-2"><?=$nombres_enseignant?></h3>
                        </button>
                    </div>
                </div>
               
            </div>
        </div>
    </div>




 

      
        <div class="col-lg-8 col-md-6  mb-4 vw-100 mt-4 mx-3 mb-3">
                 <div class="card-body px-0 pb-2">
           <!-- Modal Admin Debut-->
                        <div class="modal  fade" style="width: 100vw !important;" id="adminModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg " >
                            <div class="modal-content">
                            <div class="modal-header">
                                
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body ">
                                <div class="col-12">
                                    <div class="card my-4">
                                        <div class="card-header p-0 position-relative mt-n4  z-index-2">
                                            <div class="bg-warning shadow border-radius-lg p-2">
                                                <h6 class="text-dark text-capitalize ps-3">Listes des Aministrateurs</h6>
                                            </div>
                                        </div>
                                        <div class="card-body px-0 pb-2">
                                        <div class=" sm-table-responsive p-0" style="overflow-x: auto;">
                                            <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom et Prénom</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Email</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Téléphone</th>
                                               
                                               
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>
                                                    
                                                   
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">John Michael</h6>
                                                       
                                                    </div>
                                                   
                                                </td>
                                                <td>
                                                   <p class="text-xs text-secondary mb-0">john@creative-tim.com</p>
                                                   
                                                </td>
                                                
                                                <td class="align-middle text-center  text-dark text-sm">
                                                    <span class="text-xs text-secondary mb-0">+229 0153917722</span>
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
                        </div>
 <!-- Modal Admin fin-->
      
                 </div>
             </div>
       </div>
      





      
        <div class="col-lg-8 col-md-6  mb-4 vw-100 mt-4 mx-3 mb-3">
                 <div class="card-body px-0 pb-2">
           <!-- Modal Etudiants Debut-->
                        <div class="modal  fade" style="width: 100vw !important;" id="etudiantsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg " >
                            <div class="modal-content">
                            <div class="modal-header">
                                
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body ">
                                <div class="col-12">
                                    <div class="card my-4">
                                        <div class="card-header p-0 position-relative mt-n4  z-index-2">
                                            <div class="bg-warning shadow border-radius-lg p-2">
                                                <h6 class="text-dark text-capitalize ps-3">Listes des Etudiants</h6>
                                            </div>
                                        </div>
                                        <div class="card-body px-0 pb-2">
                                        <div class=" sm-table-responsive p-0" style="overflow-x: auto;">
                                            <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom et Prénom</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Matricule</th>
                                                 <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Email</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fillière</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Sexe</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Téléphone</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                               
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                   
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">John Michael</h6>
                                                       
                                                    </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">2025-001</p>
                                                   
                                                </td>
                                                 <td>
                                                   <p class="text-xs text-secondary mb-0">john@creative-tim.com</p>
                                                   
                                                </td>
                                                <td class="align-middle text-center  text-dark text-sm">
                                                    <span class="text-xs font-weight-bold mb-0 ">Mathématique</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">M</span>
                                                </td>

                                                 <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">+229 0153917722</span>
                                                </td>
                                                 <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">    <i class="fas fa-edit me-1"></i> </span>
                                                    <span class="text-secondary text-xs font-weight-bold">    <i class="fa-solid fa-trash"></i> </span>
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
                        </div>
 <!-- Modal Etudiants fin-->
      
                 </div>
             </div>
       </div>





        <div class="col-lg-8 col-md-6  mb-4 vw-100 mt-4 mx-3 mb-3">
                 <div class="card-body px-0 pb-2">
           <!-- Modal professeurs Debut-->
                        <div class="modal  fade" style="width: 100vw !important;" id="professeursModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg " >
                            <div class="modal-content">
                            <div class="modal-header">
                                
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body ">
                                <div class="col-12">
                                    <div class="card my-4">
                                        <div class="card-header p-0 position-relative mt-n4  z-index-2">
                                            <div class="bg-warning shadow border-radius-lg p-2">
                                                <h6 class="text-dark text-capitalize ps-3">Listes des Enseignants</h6>
                                            </div>
                                        </div>
                                        <div class="card-body px-0 pb-2">
                                        <div class=" sm-table-responsive p-0" style="overflow-x: auto;">
                                            <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nom et Prénom</th>
                                                 <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Email</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ">Matière Enseignées</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Téléphone</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date d'embauche</th>
                                               
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                   
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">John Michael</h6>
                                                       
                                                    </div>
                                                    </div>
                                                </td>
                                                 <td>
                                                   <p class="text-xs text-secondary mb-0">john@creative-tim.com</p>
                                                   
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">Mathématique</p>
                                                   
                                                </td>
                                                <td class="align-middle text-center  text-dark text-sm">
                                                    <span class="text-xs font-weight-bold mb-0 ">+229 0153917722</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">23/04/18</span>
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
                        </div>
                        </div>
                        </div>
                        </div>

 <!-- Modal professeurs fin-->
</div>
      

    
</main>






      <footer class="footer py-4  w-75  ms-lg-auto ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-dark text-lg-start w-100">
                © 2025 , made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold " target="_blank">Fabrice DEV</a> Web Developer.
                
                 <a class=" text-success parametre  position-absolute my-4 z-index-100 end-0 px-3 py-2" href="">
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











