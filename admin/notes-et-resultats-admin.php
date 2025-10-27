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
    <title>Notes et  Resultats</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/dashboard/">

    <!----font awesome-->

    <!-- Bootstrap core CSS -->
     <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome\css\all.min.css">
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
            <a class="nav-link text-warning active bg-light mx-2 rounded" href="notes-et-resultats-admin.php">
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
            <h1 class="h2 mb-0">Notes et Résultats</h1>
            <small class="text-muted">Aperçu global </small>
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
            <h1><i class="fas fa-graduation-cap me-2"></i> Gestion des Notes et Résultats</h1>
        </header>

        <div class="row">
            <!-- Filtres -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filtres</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filiere-notes" class="form-label">Filière</label>
                                <select id="filiere-notes" class="form-select">
                                    <option value="">Choisir une filière...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="cours-notes" class="form-label">Cours</label>
                                <select id="cours-notes" class="form-select">
                                    <option value="">Choisir un cours...</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="etudiant-notes" class="form-label">Étudiant</label>
                                <select id="etudiant-notes" class="form-select">
                                    <option value="">Choisir un étudiant...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Saisie des notes -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header bg-success text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-edit me-2"></i>Saisie des Notes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="notes-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Interro 1</th>
                                        <th>Interro 2</th>
                                        <th>Interro 3</th>
                                        <th>Devoir 1</th>
                                        <th>Devoir 2</th>
                                        <th>Moyenne</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rempli dynamiquement -->
                                </tbody>
                            </table>
                        </div>
                        <button id="save-notes" class="btn btn-primary mt-3">
                            <i class="fas fa-save me-2"></i>Enregistrer toutes les notes
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bulletin -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0"><i class="fas fa-file-alt me-2"></i>Bulletin</h5>
                    </div>
                    <div class="card-body">
                        <button id="generate-bulletin" class="btn btn-success w-100 mb-3">
                            <i class="fas fa-file-pdf me-2"></i>Générer le Bulletin
                        </button>
                        <div id="bulletin-container" class="bulletin">
                            <div class="bulletin-header">
                                <div class="text-center mb-3">
                                    <h3 class="bulletin-title">BULLETIN SCOLAIRE</h3>
                                    <p>Année Académique 2023-2024</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p><strong>Nom:</strong> <span id="bulletin-nom">-</span></p>
                                        <p><strong>Prénom:</strong> <span id="bulletin-prenom">-</span></p>
                                        <p><strong>Filière:</strong> <span id="bulletin-filiere">-</span></p>
                                        <p><strong>N° Matricule:</strong> <span id="bulletin-matricule">-</span></p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div id="bulletin-notes">
                                <p class="text-center text-muted">Sélectionnez un étudiant pour afficher son bulletin</p>
                            </div>
                            <div id="bulletin-mention" class="mention d-none">
                                <!-- Rempli dynamiquement -->
                            </div>
                        </div>
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

<!--Debut des notes script---->
<script>
        // Données initiales
        const filieres = {
            'informatique': {
                nom: 'Informatique',
                cours: ['programmation', 'algo', 'bdd'],
                etudiants: [
                    { id: 1, nom: 'Dupont', prenom: 'Jean', matricule: 'ETU001' },
                    { id: 2, nom: 'Martin', prenom: 'Sophie', matricule: 'ETU002' },
                    { id: 3, nom: 'Bernard', prenom: 'Pierre', matricule: 'ETU003' }
                ]
            },
            'biologie': {
                nom: 'Biologie',
                cours: ['biochimie', 'genetique', 'ecologie'],
                etudiants: [
                    { id: 4, nom: 'Leroy', prenom: 'Marie', matricule: 'ETU004' },
                    { id: 5, nom: 'Petit', prenom: 'Luc', matricule: 'ETU005' }
                ]
            },
            'gestion': {
                nom: 'Gestion',
                cours: ['comptabilité', 'marketing', 'management'],
                etudiants: [
                    { id: 6, nom: 'Moreau', prenom: 'Alice', matricule: 'ETU006' },
                    { id: 7, nom: 'Simon', prenom: 'Thomas', matricule: 'ETU007' }
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

        // Structure pour stocker les notes
        let notesData = {};

        // Initialisation des filtres
        function initFilters() {
            const filiereSelect = document.getElementById('filiere-notes');
            const coursSelect = document.getElementById('cours-notes');
            const etudiantSelect = document.getElementById('etudiant-notes');

            // Remplir les filières
            filiereSelect.innerHTML = '<option value="">Choisir une filière...</option>';
            for (const [id, filiere] of Object.entries(filieres)) {
                filiereSelect.innerHTML += `<option value="${id}">${filiere.nom}</option>`;
            }

            // Mettre à jour les cours quand une filière est sélectionnée
            filiereSelect.addEventListener('change', function() {
                coursSelect.innerHTML = '<option value="">Choisir un cours...</option>';
                etudiantSelect.innerHTML = '<option value="">Choisir un étudiant...</option>';
                
                if (this.value) {
                    filieres[this.value].cours.forEach(coursId => {
                        coursSelect.innerHTML += `<option value="${coursId}">${coursDetails[coursId]}</option>`;
                    });
                    
                    // Remplir les étudiants de la filière
                    filieres[this.value].etudiants.forEach(etudiant => {
                        etudiantSelect.innerHTML += `<option value="${etudiant.id}">${etudiant.prenom} ${etudiant.nom}</option>`;
                    });
                }
            });

            // Mettre à jour le tableau des notes quand un cours est sélectionné
            coursSelect.addEventListener('change', updateNotesTable);
            
            // Mettre à jour le bulletin quand un étudiant est sélectionné
            etudiantSelect.addEventListener('change', updateBulletin);
        }

        // Mettre à jour le tableau des notes
        function updateNotesTable() {
            const filiereId = document.getElementById('filiere-notes').value;
            const coursId = document.getElementById('cours-notes').value;
            const tableBody = document.querySelector('#notes-table tbody');

            tableBody.innerHTML = '';

            if (!filiereId || !coursId) return;

            const filiere = filieres[filiereId];
            
            filiere.etudiants.forEach(etudiant => {
                const etudiantId = etudiant.id;
                const noteKey = `${filiereId}_${coursId}_${etudiantId}`;
                
                // Initialiser si nécessaire
                if (!notesData[noteKey]) {
                    notesData[noteKey] = {
                        interro1: null,
                        interro2: null,
                        interro3: null,
                        devoir1: null,
                        devoir2: null
                    };
                }
                
                const notes = notesData[noteKey];
                const moyenne = calculateAverage(notes);
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${etudiant.prenom} ${etudiant.nom}</td>
                    <td><input type="number" min="0" max="20" step="0.5" class="form-control note-input interro1" 
                           value="${notes.interro1 || ''}" data-etudiant="${etudiantId}" data-type="interro1"></td>
                    <td><input type="number" min="0" max="20" step="0.5" class="form-control note-input interro2" 
                           value="${notes.interro2 || ''}" data-etudiant="${etudiantId}" data-type="interro2"></td>
                    <td><input type="number" min="0" max="20" step="0.5" class="form-control note-input interro3" 
                           value="${notes.interro3 || ''}" data-etudiant="${etudiantId}" data-type="interro3"></td>
                    <td><input type="number" min="0" max="20" step="0.5" class="form-control note-input devoir1" 
                           value="${notes.devoir1 || ''}" data-etudiant="${etudiantId}" data-type="devoir1"></td>
                    <td><input type="number" min="0" max="20" step="0.5" class="form-control note-input devoir2" 
                           value="${notes.devoir2 || ''}" data-etudiant="${etudiantId}" data-type="devoir2"></td>
                    <td class="text-center">${moyenne !== null ? moyenne.toFixed(2) : '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-success btn-save-individual" data-etudiant="${etudiantId}">
                            <i class="fas fa-save"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            // Gérer la sauvegarde individuelle
            document.querySelectorAll('.btn-save-individual').forEach(btn => {
                btn.addEventListener('click', function() {
                    const etudiantId = parseInt(this.getAttribute('data-etudiant'));
                    saveStudentNotes(filiereId, coursId, etudiantId);
                });
            });
        }

        // Calculer la moyenne
        function calculateAverage(notes) {
            const { interro1, interro2, interro3, devoir1, devoir2 } = notes;
            const interros = [interro1, interro2, interro3].filter(n => n !== null);
            const devoirs = [devoir1, devoir2].filter(n => n !== null);
            
            if (interros.length === 0 && devoirs.length === 0) return null;
            
            const avgInterro = interros.length > 0 ? 
                interros.reduce((a, b) => a + b, 0) / interros.length : 0;
            const avgDevoir = devoirs.length > 0 ? 
                devoirs.reduce((a, b) => a + b, 0) / devoirs.length : 0;
            
            // Pondération: 40% interrogations, 60% devoirs
            return (avgInterro * 0.4 ) + (avgDevoir * 0.6);
        }

        // Sauvegarder les notes d'un étudiant
        function saveStudentNotes(filiereId, coursId, etudiantId) {
            const noteKey = `${filiereId}_${coursId}_${etudiantId}`;
            
            notesData[noteKey] = {
                interro1: parseFloat(document.querySelector(`.interro1[data-etudiant="${etudiantId}"]`).value) || null,
                interro2: parseFloat(document.querySelector(`.interro2[data-etudiant="${etudiantId}"]`).value) || null,
                interro3: parseFloat(document.querySelector(`.interro3[data-etudiant="${etudiantId}"]`).value) || null,
                devoir1: parseFloat(document.querySelector(`.devoir1[data-etudiant="${etudiantId}"]`).value) || null,
                devoir2: parseFloat(document.querySelector(`.devoir2[data-etudiant="${etudiantId}"]`).value) || null
            };
            
            // Recalculer la moyenne
            updateNotesTable();
            
            // Mettre à jour le bulletin si c'est l'étudiant actuellement sélectionné
            const selectedStudent = document.getElementById('etudiant-notes').value;
            if (selectedStudent && parseInt(selectedStudent) === etudiantId) {
                updateBulletin();
            }
        }

        // Sauvegarder toutes les notes
        document.getElementById('save-notes').addEventListener('click', function() {
            const filiereId = document.getElementById('filiere-notes').value;
            const coursId = document.getElementById('cours-notes').value;
            
            if (!filiereId || !coursId) {
                alert('Veuillez sélectionner une filière et un cours');
                return;
            }
            
            const filiere = filieres[filiereId];
            filiere.etudiants.forEach(etudiant => {
                saveStudentNotes(filiereId, coursId, etudiant.id);
            });
            
            alert('Toutes les notes ont été enregistrées avec succès!');
        });

        // Mettre à jour le bulletin
        function updateBulletin() {
            const etudiantId = document.getElementById('etudiant-notes').value;
            const filiereId = document.getElementById('filiere-notes').value;
            
            if (!etudiantId || !filiereId) return;
            
            const etudiant = filieres[filiereId].etudiants.find(e => e.id === parseInt(etudiantId));
            const bulletinNotes = document.getElementById('bulletin-notes');
            
            // Mettre à jour les infos de l'étudiant
            document.getElementById('bulletin-nom').textContent = etudiant.nom;
            document.getElementById('bulletin-prenom').textContent = etudiant.prenom;
            document.getElementById('bulletin-filiere').textContent = filieres[filiereId].nom;
            document.getElementById('bulletin-matricule').textContent = etudiant.matricule;
           
            
            // Générer les notes par matière
            bulletinNotes.innerHTML = '';
            
            let totalNotes = 0;
            let nbMatieres = 0;
            
            filieres[filiereId].cours.forEach(coursId => {
                const noteKey = `${filiereId}_${coursId}_${etudiant.id}`;
                const notes = notesData[noteKey] || {};
                const moyenne = calculateAverage(notes);
                
                if (moyenne !== null) {
                    totalNotes += moyenne;
                    nbMatieres++;
                }
                
                const matiereDiv = document.createElement('div');
                matiereDiv.className = 'matiere-row row';
                matiereDiv.innerHTML = `
                    <div class="col-md-6"><strong>${coursDetails[coursId]}</strong></div>
                    <div class="col-md-6">
                        Interros: ${formatNote(notes.interro1)} / ${formatNote(notes.interro2)} / ${formatNote(notes.interro3)} | 
                        Devoirs: ${formatNote(notes.devoir1)} / ${formatNote(notes.devoir2)} | 
                        <span class="final-note">Moy: ${moyenne !== null ? moyenne.toFixed(2) : '-'}</span>
                    </div>
                `;
                bulletinNotes.appendChild(matiereDiv);
            });
            
            // Calculer la moyenne générale
            const mentionDiv = document.getElementById('bulletin-mention');
            mentionDiv.className = 'mention d-none';
            
            if (nbMatieres > 0) {
                const moyenneGenerale = totalNotes / nbMatieres;
                const mentionText = getMention(moyenneGenerale);
                
                mentionDiv.innerHTML = `
                    <p class="text-center mb-1"><strong>Moyenne Générale: ${moyenneGenerale.toFixed(2)}</strong></p>
                    <p class="text-center mb-0">Mention: ${mentionText}</p>
                `;
                mentionDiv.classList.remove('d-none');
                mentionDiv.classList.add(getMentionClass(moyenneGenerale));
            }
        }

        // Formater une note pour l'affichage
        function formatNote(note) {
            return note !== null ? note.toFixed(2) : '-';
        }

        // Déterminer la mention
        function getMention(moyenne) {
            if (moyenne >= 16) return 'Excellent';
            if (moyenne >= 14) return 'Très Bien';
            if (moyenne >= 12) return 'Bien';
            if (moyenne >= 10) return 'Assez Bien';
            return 'Insuffisant';
        }

        // Classe CSS pour la mention
        function getMentionClass(moyenne) {
            if (moyenne >= 16) return 'mention-excellent';
            if (moyenne >= 14) return 'mention-bien';
            if (moyenne >= 10) return 'mention-passable';
            return 'mention-insuffisant';
        }

        // Générer le bulletin PDF (simulé)
        document.getElementById('generate-bulletin').addEventListener('click', function() {
            const etudiantId = document.getElementById('etudiant-notes').value;
            
            if (!etudiantId) {
                alert('Veuillez sélectionner un étudiant');
                return;
            }
            
            // Dans une vraie application, vous utiliseriez une librairie comme jsPDF
            alert('Fonctionnalité PDF: En production, cela générerait un vrai bulletin PDF');
            console.log('Bulletin PDF généré pour l\'étudiant ID:', etudiantId);
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initFilters();
        });
    </script>
<!--Fin des notes script---->







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











