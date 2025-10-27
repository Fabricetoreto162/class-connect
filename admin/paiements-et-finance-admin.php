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
    <title>Paiements et Finances</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <!----font awesome-->

    <!-- Bootstrap core CSS -->
     <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome\css\all.min.css">
    <meta name="theme-color" content="#7952b3">
    <link rel="stylesheet" href="style.css">
<style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table th {
            vertical-align: middle;
            background-color: #343a40;
            color: white;
        }
        .solde {
            font-weight: bold;
        }
        .solde-positive {
            color: #28a745;
        }
        .solde-negative {
            color: #dc3545;
        }
        .badge-status {
            font-size: 0.85em;
            padding: 5px 8px;
        }
        .chart-container {
            height: 300px;
            position: relative;
        }
        .filiere-header {
            background-color: #e9ecef;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .payment-date {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .progress-thin {
            height: 8px;
        }
        .search-box {
            position: relative;
        }
        .search-box .form-control {
            padding-left: 40px;
        }
        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
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
            <a class="nav-link text-warning mx-2 rounded" href="utilisateur-admin.php">
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
              Matieres
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
            <a class="nav-link text-warning active bg-light mx-2 rounded" href="paiements-et-finance-admin.php">
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
            <h1 class="h2 mb-0">Paiements et Finances</h1>
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
            
        </div>
    </div>

    <!-- Stats Cards - First Row -->
     <div class="container-fluid">
        <header class="bg-primary text-white p-4 mb-4 text-center">
            <h1><i class="fas fa-money-bill-wave me-2"></i> Suivi des Paiements par Filière</h1>
        </header>

        <div class="row">
          
           

            <!-- Liste des étudiants par filière -->
            <div class="col-md-12">
                <!-- Filière Informatique -->
                <div class="card">
                    <div class="card-header bg-secondary text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-laptop-code me-2"></i>Filière Informatique
                            </h5>
                            <div>
                                <span class="badge bg-success">15 soldés</span>
                                <span class="badge bg-warning text-dark mx-2">5 partiels</span>
                                <span class="badge bg-danger">3 impayés</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
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
                                            <div>Jean Dupont</div>
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
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-receipt"></i> Reçu
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Étudiant partiel -->
                                    <tr>
                                        <td>
                                            <div>Sophie Martin</div>
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
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-plus"></i> Payer
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Étudiant impayé -->
                                    <tr>
                                        <td>
                                            <div>Pierre Bernard</div>
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
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-bell"></i> Rappeler
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Filière Biologie -->
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-dna me-2"></i>Filière Biologie
                            </h5>
                            <div>
                                <span class="badge bg-success">8 soldés</span>
                                <span class="badge bg-warning text-dark mx-2">2 partiels</span>
                                <span class="badge bg-danger">1 impayé</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
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
                                            <div>Marie Leroy</div>
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
                                            <button class="btn btn-sm btn-outline-primary">
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

    <!-- Modal Détails Paiements -->
    <div class="modal fade" id="detailsPaiementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Historique des Paiements - <span id="modal-etudiant-name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <button type="button" class="btn btn-success">
                        <i class="fas fa-print me-1"></i>Imprimer le reçu
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

<!-------debut paiements script----->
<!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        function showPaymentDetails(matricule) {
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
            initStatsChart();
            
            // Démarrer les tooltips Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Gérer les clics sur les boutons "Historique"
            document.querySelectorAll('.btn-outline-primary').forEach(btn => {
                btn.addEventListener('click', function() {
                    const matricule = this.closest('tr').querySelector('small').textContent.trim();
                    const nom = this.closest('tr').querySelector('td > div').textContent.trim();
                    
                    document.getElementById('modal-etudiant-name').textContent = nom;
                    document.getElementById('modal-matricule').textContent = matricule;
                    
                    showPaymentDetails(matricule);
                });
            });
        });
    </script>
    
   
<!-------fin paiements script----->








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
 
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>

  </body>
</html>











