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
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>Emargements</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <!----font awesome-->
<link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome\css\all.min.css">
    <!-- Bootstrap core CSS -->
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
            <a class="nav-link   text-warning mx-2 rounded" aria-current="page" href="./enseignant.php">
             <i class="fa-solid fa-graduation-cap"></i>
              Tableau de bord
            </a>
          </li> 
          <li class="nav-item">
            <a class="nav-link active bg-light text-warning mx-2 rounded" href="./emargement.php">
             <i class="fa-solid fa-calendar-days"></i>
             Emargement des étudiants
                
            </a>
          </li>
         <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="./notation.php">
              <i class="fa-solid fa-sack-dollar"></i>
              Notations des etudiants
            </a>
          </li>
           <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="./cahier-texte.php">
              <i class="fa-solid fa-sack-dollar"></i>
              Cahier de texte
            </a>
          </li>

           
        </ul>

       
        
      </div>
    </nav>

    <main class="col-md-9 main vh-75  w-75 ms-lg-auto col-lg-10 px-md-4">
     <!-- Header Section -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">Emargements</h1>
            <small class="text-muted">Aperçu global</small>
        </div>
        
        <div class="d-flex align-items-center">
            <div class="bg-light text-dark rounded-pill px-3 py-1 me-3 shadow-sm">
                <i class="fas fa-clock me-2"></i>
                <span id="dateHeure"></span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-outline-light bg-warning text-dark dropdown-toggle rounded-pill" type="button" id="userDropdown" data-bs-toggle="dropdown">
                     <i class="fas fa-user-circle me-1"></i><?=$_SESSION["Nom"]?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profil</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                   
                </ul>
            </div>
            
        </div>
    </div>


    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-dark">
                <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Feuille d'Émargement</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Matière</label>
                        <select class="form-select" id="matiere">
                            <option value="">Sélectionner...</option>
                            <option value="algo">Algorithmique</option>
                            <option value="prog">Programmation</option>
                            <option value="bdd">Bases de données</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="date-cours" value="2023-11-15">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Heure début</label>
                        <input type="time" class="form-control" id="heure-debut" value="08:00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Heure fin</label>
                        <input type="time" class="form-control" id="heure-fin" value="10:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Salle</label>
                        <input type="text" class="form-control" id="salle" placeholder="Salle A12">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Matricule</th>
                                <th>Étudiant</th>
                                <th>Présent</th>
                                <th>Signature</th>
                                <th>Remarques</th>
                            </tr>
                        </thead>
                        <tbody id="liste-etudiants">
                            <tr>
                                <td>ETU2023-001</td>
                                <td>Dupont Jean</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" checked>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" placeholder="Initiales">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" placeholder="Retard, etc.">
                                </td>
                            </tr>
                            <!-- Ajouter d'autres étudiants -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-secondary">
                        <i class="fas fa-print me-1"></i>Imprimer
                    </button>
                    <button class="btn btn-success" id="valider-emargement">
                        <i class="fas fa-check-circle me-1"></i>Valider l'émargement
                    </button>
                </div>
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



<script>
        document.getElementById('valider-emargement').addEventListener('click', function() {
            const matiere = document.getElementById('matiere').value;
            const date = document.getElementById('date-cours').value;
            
            if(!matiere || !date) {
                alert('Veuillez sélectionner une matière et une date');
                return;
            }
            
            // Enregistrement des données
            const emargement = {
                matiere: matiere,
                date: date,
                heureDebut: document.getElementById('heure-debut').value,
                heureFin: document.getElementById('heure-fin').value,
                salle: document.getElementById('salle').value,
                etudiants: []
            };
            
            // Récupérer les données des étudiants
            document.querySelectorAll('#liste-etudiants tr').forEach(tr => {
                const matricule = tr.cells[0].textContent;
                const nom = tr.cells[1].textContent;
                const present = tr.cells[2].querySelector('input').checked;
                const signature = tr.cells[3].querySelector('input').value;
                const remarques = tr.cells[4].querySelector('input').value;
                
                emargement.etudiants.push({
                    matricule, nom, present, signature, remarques
                });
            });
            
            console.log('Émargement validé:', emargement);
            alert('Émargement enregistré avec succès !');
        });
    </script>



 
 
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

  </body>
</html>











