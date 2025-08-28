<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion-étudiant</title>
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="inscription-etudiant.css">
    
</head>
<body>
   <!--debut nav-->
<nav class="navbar nav navbar-expand-lg  fixed-top" style="background-color: var(--bs-primary-bg-subtle);">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Class <span class="text-warning">Connect</span></a>
   
    <div class="offcanvas offcanvas-end" style="width: 250px !important; height: 100vh;" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Class <span class="connect text-warning">Connect</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body " >
        <ul class="navbar-nav justify-content-center flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link   active" aria-current="page" href="inscription-etudiant.php">Inscription</a>
          </li> 
          <li class="nav-item">
            <a class="nav-link" href="connexion-etudiant.php">Connexion</a>
          </li>
         
          
        </ul>
         
      </div>
    </div>
    <button class="btn btn-outline-warning " type="submit">Notification</button>
     <button class="navbar-toggler " type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon">
      </span>
    </button>
  </div>
</nav>

<!--fin nav-->


<main class="main-content  ">
    <div class="container inscription-container">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center"><i class="fas fa-user-graduate me-2"></i>Inscription Universitaire</h3>
            </div>
            <div class="card-body">
                <form id="form-inscription">
                    <!-- Étape 1 : Informations personnelles -->
                    <div class="etape etape-active" id="etape1">
                        <h4 class="mb-4"><span class="badge bg-primary">1</span> Informations Personnelles</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Genre</label>
                                <select class="form-select" required>
                                    <option value="">Sélectionner...</option>
                                    <option>Masculin</option>
                                    <option>Féminin</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-next btn-primary">Suivant</button>
                        </div>
                    </div>

                    <!-- Étape 2 : Choix de filière -->
                    <div class="etape" id="etape2">
                        <h4 class="mb-4"><span class="badge bg-primary">2</span> Choix de Filière</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Filière</label>
                                <select class="form-select" id="filiere" required>
                                    <option value="">Sélectionner une filière...</option>
                                    <option value="informatique">Informatique (200 000 FCFA/an)</option>
                                    <option value="biologie">Biologie (180 000 FCFA/an)</option>
                                    <option value="gestion">Gestion (150 000 FCFA/an)</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Niveau</label>
                                <select class="form-select" required>
                                    <option value="">Sélectionner...</option>
                                    <option>Licence 1</option>
                                    <option>Licence 2</option>
                                    <option>Licence 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-prev btn-outline-secondary">Précédent</button>
                            <button type="button" class="btn btn-next btn-primary">Suivant</button>
                        </div>
                    </div>

                    <!-- Étape 3 : Paiement des frais -->
                    <div class="etape" id="etape3">
                        <h4 class="mb-4"><span class="badge bg-primary">3</span> Paiement des Frais d'Inscription</h4>
                        <div class="alert alert-info">
                            <h5 id="montant-frais">Montant à payer : 0 FCFA</h5>
                            <p class="mb-0">Frais d'inscription : 20 000 FCFA (non remboursable)</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Moyen de paiement</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner...</option>
                                <option>Mobile Money</option>
                                <option>Carte Bancaire</option>
                                <option>Virement</option>
                                <option>Espèces</option>
                            </select>
                        </div>
                        
                        <div id="paiement-mobile" class="paiement-methode">
                            <div class="mb-3">
                                <label class="form-label">Numéro Mobile</label>
                                <input type="tel" class="form-control" placeholder="Ex: 77 123 45 67">
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="conditions" required>
                            <label class="form-check-label" for="conditions">
                                J'accepte les conditions générales et le règlement intérieur
                            </label>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-prev btn-outline-secondary">Précédent</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Valider l'inscription
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Inscription Validée</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h4>Félicitations !</h4>
                    <p>Votre inscription a été enregistrée avec succès.</p>
                    <div class="alert alert-light">
                        <strong>Identifiant:</strong> ETU2023-125 <br>
                        <strong>Mot de passe:</strong> votre_date_naissance
                    </div>
                    <p>Consultez votre email pour les détails de connexion.</p>
                </div>
                <div class="modal-footer">
                    <a href="notes.html" class="btn btn-primary w-100">Accéder à mon espace</a>
                </div>
            </div>
        </div>
    </div>
   
  </main>








    <script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.min.js"></script>
    <script>
        // Gestion des étapes du formulaire
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const currentEtape = this.closest('.etape');
                const nextEtape = currentEtape.nextElementSibling;
                
                currentEtape.classList.remove('etape-active');
                nextEtape.classList.add('etape-active');
                
                // Calcul du montant quand on choisit la filière
                if(nextEtape.id === 'etape3') {
                    const filiere = document.getElementById('filiere');
                    let frais = 20000; // Frais d'inscription fixes
                    
                    if(filiere.value === 'informatique') frais += 200000;
                    else if(filiere.value === 'biologie') frais += 180000;
                    else if(filiere.value === 'gestion') frais += 150000;
                    
                    document.getElementById('montant-frais').textContent = 
                        `Montant à payer : ${frais.toLocaleString()} FCFA`;
                }
            });
        });
        
        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                const currentEtape = this.closest('.etape');
                const prevEtape = currentEtape.previousElementSibling;
                
                currentEtape.classList.remove('etape-active');
                prevEtape.classList.add('etape-active');
            });
        });
        
        // Soumission du formulaire
        document.getElementById('form-inscription').addEventListener('submit', function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            modal.show();
        });
    </script>
 <script src="inscription-etudiant.js"></script>   
</body>
</html>