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
    <title>Suivis des Emargements</title>
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
            <a class="nav-link text-warning active bg-light mx-2 rounded" href="suivis-des-emargements-admin.php">
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
            <h1 class="h2 mb-0">Suivis des Emargements</h1>
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

    <!-- Stats Cards - First Row -->
     <div class="container-fluid">
        <header class="bg-primary text-white p-4 mb-4">
            <h1 class="text-center"><i class="fas fa-clipboard-check me-2"></i> Émargement par Étudiant</h1>
        </header>

        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-2">
                <label for="filiere" class="form-label">Filière</label>
                <select id="filiere" class="form-select">
                    <option value="toutes">Toutes les filières</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="cours" class="form-label">Cours</label>
                <select id="cours" class="form-select">
                    <option value="tous">Tous les cours</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date-cours" class="form-label">Date du cours</label>
                <input type="date" id="date-cours" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="heure-debut" class="form-label">Heure début</label>
                <input type="time" id="heure-debut" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="heure-fin" class="form-label">Heure fin</label>
                <input type="time" id="heure-fin" class="form-control">
            </div>
        </div>

        <!-- Section principale -->
        <div class="row principal " style="margin-top: 20px;">
            <!-- Liste des étudiants -->
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-info text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Liste des Étudiants</h5>
                    </div>
                    <div class="card-body">
                        <div class="student-list my-2" id="student-list">
                            <!-- Rempli dynamiquement par JS -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feuille d'émargement -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Feuille d'Émargement</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Filière</th>
                                        <th class="presence-cell">Présence</th>
                                        <th>Signature</th>
                                    </tr>
                                </thead>
                                <tbody id="emargement-body">
                                    <!-- Rempli dynamiquement par JS -->
                                </tbody>
                            </table>
                        </div>
                        <button id="valider-emargement" class="btn btn-primary mt-3">
                            <i class="fas fa-check-circle me-2"></i>Valider l'émargement
                        </button>
                    </div>
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

<!--- Debut de emargements script----->
 <script>
        // Données initiales
        const filieres = {
            'informatique': {
                nom: 'Informatique',
                cours: ['programmation', 'algo', 'bdd'],
                etudiants: [
                    { id: 1, nom: 'Dupont', prenom: 'Jean', numero: 'ETU001' },
                    { id: 2, nom: 'Martin', prenom: 'Sophie', numero: 'ETU002' },
                    { id: 3, nom: 'Bernard', prenom: 'Pierre', numero: 'ETU003' }
                ]
            },
            'biologie': {
                nom: 'Biologie',
                cours: ['biochimie', 'genetique', 'ecologie'],
                etudiants: [
                    { id: 4, nom: 'Leroy', prenom: 'Marie', numero: 'ETU004' },
                    { id: 5, nom: 'Petit', prenom: 'Luc', numero: 'ETU005' }
                ]
            },
            'gestion': {
                nom: 'Gestion',
                cours: ['comptabilité', 'marketing', 'management'],
                etudiants: [
                    { id: 6, nom: 'Moreau', prenom: 'Alice', numero: 'ETU006' },
                    { id: 7, nom: 'Simon', prenom: 'Thomas', numero: 'ETU007' }
                ]
            }
        };

        const coursDetails = {
            'programmation': 'Programmation avancée',
            'algo': 'Algorithmique',
            'bdd': 'Bases de données',
            'biochimie': 'Biochimie',
            'genetique': 'Génétique',
            'ecologie': 'Écologie',
            'comptabilité': 'Comptabilité générale',
            'marketing': 'Marketing digital',
            'management': 'Management'
        };

        let currentEmargement = {
            cours: null,
            filiere: null,
            date: null,
            heureDebut: null,
            heureFin: null,
            etudiants: []
        };

        // Initialisation des filtres
        function initFilters() {
            const filiereSelect = document.getElementById('filiere');
            const coursSelect = document.getElementById('cours');

            // Remplir les filières
            for (const [id, filiere] of Object.entries(filieres)) {
                filiereSelect.innerHTML += `<option value="${id}">${filiere.nom}</option>`;
            }

            // Mettre à jour les cours quand une filière est sélectionnée
            filiereSelect.addEventListener('change', function() {
                coursSelect.innerHTML = '<option value="tous">Tous les cours</option>';
                
                if (this.value !== 'toutes') {
                    filieres[this.value].cours.forEach(coursId => {
                        coursSelect.innerHTML += `<option value="${coursId}">${coursDetails[coursId]}</option>`;
                    });
                }
                
                updateStudentList();
            });

            // Mettre à jour la liste des étudiants quand un cours est sélectionné
            coursSelect.addEventListener('change', updateStudentList);
            
            // Gérer les changements de date et heure
            document.getElementById('date-cours').addEventListener('change', function() {
                currentEmargement.date = this.value;
                updateEmargementHeader();
            });
            
            document.getElementById('heure-debut').addEventListener('change', function() {
                currentEmargement.heureDebut = this.value;
                updateEmargementHeader();
            });
            
            document.getElementById('heure-fin').addEventListener('change', function() {
                currentEmargement.heureFin = this.value;
                updateEmargementHeader();
            });
        }

        // Mettre à jour l'en-tête de l'émargement
        function updateEmargementHeader() {
            const coursSelect = document.getElementById('cours');
            const displayCours = document.getElementById('display-cours');
            const displayHoraire = document.getElementById('display-horaire');
            
            if (coursSelect.value !== 'tous') {
                displayCours.textContent = coursDetails[coursSelect.value];
            } else {
                displayCours.textContent = '-';
            }
            
            let horaireText = '-';
            if (currentEmargement.date && currentEmargement.heureDebut && currentEmargement.heureFin) {
                const dateObj = new Date(currentEmargement.date);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateFormatted = dateObj.toLocaleDateString('fr-FR', options);
                horaireText = `${dateFormatted} de ${currentEmargement.heureDebut} à ${currentEmargement.heureFin}`;
            }
            displayHoraire.textContent = horaireText;
        }

        // Mettre à jour la liste des étudiants
        function updateStudentList() {
            const filiereId = document.getElementById('filiere').value;
            const coursId = document.getElementById('cours').value;
            const studentList = document.getElementById('student-list');

            studentList.innerHTML = '';

            if (filiereId === 'toutes') {
                studentList.innerHTML = '<div class="text-muted">Sélectionnez une filière et un cours</div>';
                return;
            }

            const filiere = filieres[filiereId];
            
            filiere.etudiants.forEach(etudiant => {
                const div = document.createElement('div');
                div.className = 'student-item d-flex justify-content-between align-items-center';
                div.innerHTML = `
                    <span>
                        ${etudiant.prenom} ${etudiant.nom}
                        <span class="badge bg-secondary badge-filiere">${etudiant.numero}</span>
                    </span>
                    <button class="btn btn-sm btn-outline-primary btn-add" data-id="${etudiant.id}">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                `;
                studentList.appendChild(div);
            });

            // Gérer l'ajout d'étudiants à l'émargement
            document.querySelectorAll('.btn-add').forEach(btn => {
                btn.addEventListener('click', function() {
                    const etudiantId = parseInt(this.getAttribute('data-id'));
                    const etudiant = filiere.etudiants.find(e => e.id === etudiantId);
                    
                    if (!currentEmargement.etudiants.some(e => e.id === etudiantId)) {
                        currentEmargement.etudiants.push({
                            ...etudiant,
                            present: false,
                            signature: ''
                        });
                        updateEmargementTable();
                    }
                });
            });
            
            // Mettre à jour le cours dans currentEmargement
            currentEmargement.cours = coursId;
            currentEmargement.filiere = filiereId;
            updateEmargementHeader();
        }

        // Mettre à jour le tableau d'émargement
        function updateEmargementTable() {
            const emargementBody = document.getElementById('emargement-body');
            emargementBody.innerHTML = '';

            currentEmargement.etudiants.forEach(etudiant => {
                const tr = document.createElement('tr');
                tr.className = 'new-row';
                tr.innerHTML = `
                    <td>${etudiant.prenom} ${etudiant.nom} <small class="text-muted">${etudiant.numero}</small></td>
                    <td>${filieres[currentEmargement.filiere].nom}</td>
                    <td class="presence-cell">
                        <select class="form-select form-select-sm presence-select" data-id="${etudiant.id}">
                            <option value="false" ${!etudiant.present ? 'selected' : ''}>Absent</option>
                            <option value="true" ${etudiant.present ? 'selected' : ''}>Présent</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm signature-input" 
                               data-id="${etudiant.id}" value="${etudiant.signature}" placeholder="Initiales">
                    </td>
                `;
                emargementBody.appendChild(tr);
            });

            // Gérer les changements de présence
            document.querySelectorAll('.presence-select').forEach(select => {
                select.addEventListener('change', function() {
                    const etudiantId = parseInt(this.getAttribute('data-id'));
                    const etudiant = currentEmargement.etudiants.find(e => e.id === etudiantId);
                    etudiant.present = this.value === 'true';
                });
            });

            // Gérer les signatures
            document.querySelectorAll('.signature-input').forEach(input => {
                input.addEventListener('change', function() {
                    const etudiantId = parseInt(this.getAttribute('data-id'));
                    const etudiant = currentEmargement.etudiants.find(e => e.id === etudiantId);
                    etudiant.signature = this.value;
                });
            });
        }

        // Valider l'émargement
        document.getElementById('valider-emargement').addEventListener('click', function() {
            if (!currentEmargement.date) {
                alert('Veuillez sélectionner une date pour le cours');
                return;
            }

            if (!currentEmargement.heureDebut || !currentEmargement.heureFin) {
                alert('Veuillez spécifier l\'horaire du cours');
                return;
            }

            if (!currentEmargement.cours) {
                alert('Veuillez sélectionner un cours');
                return;
            }

            if (currentEmargement.etudiants.length === 0) {
                alert('Aucun étudiant ajouté à l\'émargement');
                return;
            }

            // Calcul du nombre de présents
            const nbPresents = currentEmargement.etudiants.filter(e => e.present).length;
            
            // Ici vous pourriez envoyer les données au serveur
            console.log('Émargement validé:', {
                ...currentEmargement,
                nbPresents: nbPresents,
                nbAbsents: currentEmargement.etudiants.length - nbPresents
            });
            
            alert(`Émargement enregistré avec succès!\nPrésents: ${nbPresents}\nAbsents: ${currentEmargement.etudiants.length - nbPresents}`);
            
            // Réinitialiser pour un nouvel émargement
            currentEmargement.etudiants = [];
            updateEmargementTable();
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initFilters();
            
            // Mettre la date du jour par défaut
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('date-cours').value = today;
            currentEmargement.date = today;
            
            // Mettre des heures par défaut (ex: 8h-12h)
            document.getElementById('heure-debut').value = '08:00';
            document.getElementById('heure-fin').value = '12:00';
            currentEmargement.heureDebut = '08:00';
            currentEmargement.heureFin = '12:00';
            
            updateEmargementHeader();
        });
    </script>
<!--- fin de emargements script----->

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











