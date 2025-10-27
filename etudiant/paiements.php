<?php
session_start();

if (!isset($_SESSION["Nom"])){
    header("Location:connexion-etudiant.php");
    exit();
}

if (isset($_POST["deconnexion"])){
    $_SESSION = array();
    session_destroy();
    header("Location:connexion-etudiant.php");
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
    <title>Paiements</title>
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
            <a class="nav-link  text-warning mx-2 rounded" aria-current="page" href="notes-etudiant.php">
             <i class="fa-solid fa-graduation-cap"></i>
              Notes
            </a>
          </li> 
          <li class="nav-item">
            <a class="nav-link text-warning mx-2 rounded" href="emplois-du-temps.php">
             <i class="fa-solid fa-calendar-days"></i>
                Emplois du temps
            </a>
          </li>
         <li class="nav-item">
            <a class="nav-link active bg-light  text-warning mx-2 rounded" href="paiements.php">
              <i class="fa-solid fa-sack-dollar"></i>
              Paiements
            </a>
          </li>

           
        </ul>

       
        
      </div>
    </nav>

    <main class="col-md-9 main vh-75  w-75 ms-lg-auto col-lg-10 px-md-4">
     <!-- Header Section -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">Paiements</h1>
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
        <div class="card shadow">
            <div class="card-header bg-primary text-dark">
                <h3><i class="fas fa-money-bill-wave me-2"></i>Paiement de la Scolarité</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Solde Actuel</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Total dû:</span>
                                    <strong>200 000 FCFA</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Déjà payé:</span>
                                    <strong class="text-success">50 000 FCFA</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Reste à payer:</span>
                                    <strong class="text-danger">150 000 FCFA</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width: 25%">25%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Historique des Paiements</h5>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <div>
                                            <h6>Frais d'inscription</h6>
                                            <small class="text-muted">15/09/2023 - Mobile Money</small>
                                        </div>
                                        <span class="text-success">+50 000 FCFA</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Nouveau Paiement</h5>
                            </div>
                            <div class="card-body">
                                <form id="form-paiement">
                                    <div class="mb-3">
                                        <label class="form-label">Montant (FCFA)</label>
                                        <input type="number" class="form-control" value="150000" min="5000" step="5000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Moyen de paiement</label>
                                        <select class="form-select" required>
                                            <option value="">Choisir...</option>
                                            <option>MTN Money</option>
                                            <option>Moov Money</option>
                                            <option>Celtis Money</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Numero de Téléphone</label>
                                        <input type="tel" class="form-control" placeholder="+2290153917722">
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-lock me-1"></i> Payer Maintenant
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="mt-3 text-center">
                                    <img src="data:image/jpeg;base64,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" class="img-fluid me-2" style="max-height: 40px; border-radius:6px;">
                                    <img src="data:image/jpeg;base64,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" class="img-fluid" style="max-height: 40px; border-radius:6px;">
                                    <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJQAAACUCAMAAABC4vDmAAAAzFBMVEUUPHX///+HwwAAMG+JxgAOOnQAGWW7wdHo6O05TH4AH2gAMnDu8PTEytemrMEANXEwQHkALG0KNHgQOXaNywD4+vzg4+kAKGsAMHkXMXANN3cALXpFXopbapEAJWpdcJd5gKHS1uBKeVBseJwyUoQAKXsoT21mmT5tojSEvxJ2riktVWl8tRdTgkpYiEoAIn1djkUfRm9wpi42X2FHc1aRm7NPWokyWWd5iKYYQHNDbVlTgVFAal1ilUNpnjk8ZV4AEYAAHH8AAF8AEGMZnrnPAAALnElEQVR4nO2aC3eiuhbHsRBRXhHiaxBw8P1qp9bSSjttz73z/b/TTXaCIorSnt52nbP4r1kzQDLhx87O3nkoSaVKlSpVqlSpUqVKlSpVqlSpYyEH5O4eEP6AfCdTfD9genLEA2cD94PJN1KRyZUC6iH+oHfXhPsd5XdALa9ATQHhLuf8wf03QrmPCjAor124d57F/fN3Qv3mEFdz7kTOQkDdfCOUc9/kUAr0F1kLRuXlG6G6gwRq8YvdXie3ry6NFl0mTofguuueb+6ToG4FxVVTIhIii8RSdy5aD/bRAsX8ZvMVkaI3VRKoQVdyN+LmSlkREi+aoDGt57zx699fYarxXQKl3BHk3CR3VyuEnHu4a26oqXq3CqC66AugetBfqxX9e74kXfh3BSwxQl2IWcq0KyEX6jWvu+9oG7lOj2rcdd/Z584KzPKisNA0XlIHU2ZgPGVNpN5NE4bAGpFHHlPfkXwcJ17e30xfX18GmzVx3mFh5MLL5m/w8r8YhPI8A6NMXJqDuKk2Lo8cyqywRzm9t5eV0mQpi/41fx243cJYJOZQMesdBf6+mnCoDQVwYBgoLz3nVdnFsgJynbcFxbnaSVFWz1LRLxK2mP9ifqxcs75c/QUAkAzdJRhoPu7xqC8V6z1nPW2miDhWc7Ep6JA8Biir8YS9fQ6R4T9gHhYh6ODk/v0Ixcp0XIxpM88icWttihnagdSnLHq/du0sxxBPm9c9Vv4ENM8Q96FHL6v3++oUE/v2dSG/cgYANesm+UWZke4zNwuDQtzZZszNlEVcpE1n0zyJxL7qtlcI6hqgpl3yKL5m4CQjDRrgsHPWscptEeu7k3keE3XOX4WgIISz1zkw5pgv04wCXQpuSci+zWWB3kPO6nTfcVOti4wU5wWg6OSJThdYTJmNqfNzKB7vxklyVFZFBo/znNt5DKpQ8HV5IBo4dIIwfXl5mT6ykAkYKwmgkn4Vw/GCknCr0JF8EKhEG3EBKIRmPCi6yYTJZRM9aGAlTO2KlD0v8pU96oJKc357v3ncvA3ujrCK+BSS+PzpYEKC4gMIMZqUaRGPoilcuXsc91xCiOuMyfWBhzVvingAilewnno7gJLm7NlVEpXGEMIKrbnoGKFW31ck3fVsT6WsCrkUil+mVC8HlZF0yx5Ol+LhGNLOvEhCdW5Wy8N6pLeboilXj8UiOjq1Rt8/RK5LJx08KRdJMWQ9yb4X0VAIaq6K5r4LyNLzZvLE3WJZKBejE7W68c1ssZje9z5nJk3oSnWu8ESEPj4PJl03jp33TPPOiXsTxJ19NEegvP+C0sX767/xSRmROHFRZT83Jz+Y8tYPCEp/EFaMeNUfn7vUoJkZuq45f9oxaVabKayfflUnhGJLp9d6xKs2PhUKSZvX+Xy+GKz3Q6qqVphqAgpJ7JWNXe9oBhSbNr2WLbhuNT55VebS2Nz71U0NqUMo7LdrVIaPvxBKkqRMi1moFtz2vxgqo38mVIuqojIo3aqwG+NzHf0DUHgI8iEkePzm44ZCWMenIhzC+OB5GgrptoDyO7quA1gHJENdHa7tTGu4GCSSqx3J80dSR8OHBYFGPM9jz5OWUlCo7vtbDrX1qTzETOcnlxIa8Ztda3YVj3xvJFfty1x2fWsarPFWWx1KcorVs0J4aU0dyvgISv5ZOZCq7x0dgueho9v1qA2ttcLowT7BkRKuRrVUy2FfEwVyoBqpN0r6RSj5EEqMvgqH0rap1gyres5YsteuZNrmockeGgePDU//G1AoUA8rt7MBMCXdq1WyUlk6lYULp6hG+ONQ9jbbWj4UrhuVI/2kLWIvKWgZCV1oH0G1RBkPTnouFKpnv32b71WBmVRqqZYa8gaZU2mioBX1Pd8Srx7Kh6NvNOxHvCTq03jEhlwOFB4mH2ZZJmutfcahEqO2fS3odKooMioGi0C6aCRsBDSwaHV+Z9J+PYhT2BY92LdF/MmBkhP4/waBpg3b5wxFQsEkAgEKPCOCDCHc0mPPkRSIwV1HmYiuJ1DZ3JeBsgWUzxpHMony92+wx6sawS5mYltmJmjwttWqrMv0T2efTT4GpYsuMXwZPv9M9OwkRpUzBdjnBbUwEYeiVvwYFHoQ46Zl9gP5bDwXscPwcKbgaAALWcEHoSTb2jVSi+rBOaiQ1zoKGUF0GkrVPgqF7HDfTC2ys2ZIvZsH8+Pgqu2/65MsRanSbbZ5ejhnqaOhkEC1DvVxn2JU8jaV0Hh6OAnFI+R+2p8o8amfmQKUneS9A4rea9tdrK60s4MrUUf8rygdyTDajz4rQHthWOx+3FLMWHiXHSrDnA5M3t1q7CqgYDuiI7ghUlV9XyDzTj4NFXUuQSGJQA7QGsJaVo6pkCR6OYxl0e9SVLG0fUQP66JAl4eGrx9BJTktTKasuVBVUyXQFg74UzXP1ZOUVKkN5WoQVCXmiy3qg7qfdD0UaGRosjcfdd8uKKqxVg3OQDEvbUeYtvXHvwCF5DBxvJoVWWKmyeZF+zkZFNRaiRtklu2dpAEjVM18KPwA/lAzo8gUTnWURnbSH07Mp5jbojg7IWWdRI6g0rG/Rf0hB0ozs221jtJIqgOH2QmmSDv4aFpG5WcT8i4An4fC3tFbLC2XiTY6zNoqfADDyvUwC/vzuPvoTGMHf8ZS9jDzieb5rKxLVhqrFhFhV2wfLHMMC0ZiFkrSR4mXnIGSdJR+S8vSL6z8UMeLxNSkrW7jDqtOXNclqFOPTN6UYUajgMcpkQX2m2bYHvIRAlDC2sdxijUm3mL5ncurUUxnXnXPG8W6DF9AnPXj22ZCXBqeSMwKsJx8Gh55oHQSxzKmtbwRi468dAQcdX6TbIDKMhJvyfdxruR3b6k9g+56ys7J57NlLylIGxaDMtbe1eKlKKdqdmfitNDg6HTDeRInt4pS6Cjl8+W+KoNxetOdjJ/2J8DNp0+lOrfVnZbzpDRny25iLbc7eU0fO82LnTNAv1x+38PDQ86m8qEIml8p8+lmzPRrvHw5PLxvXhc54sFaVfJG9p/zOzyoHhqGWchU8HMWpaksXl9nCyX7Ewdlcfl4HMl9lW8YRfXcfAZQbZ6pLotM+HkQHDRlT1epLkLhzn4/xRh28iu+A0oSP5LK0yUoJPFsJLY6+AQap0MRDXYYpaBgI/NSq927fCpldQmK28kc+j6bi4V0hOmB/OD3H2QetXHQ8Id+g65ABVRA/H69cyl8kniRS3Xxh5V85b/9o2Ns/7FqBKM4EtMUlS1YdJ8bMoxiDsVnw8bwnPcxuZOc36YUCAmQn9Wq+LzRfn+F5Tgd4dEuDXsYoJItpjPTKS5nkmOriz9FgLng7gUItrEpjalC7o1sTWVTgshqV8wALLXzvrB6AUpy49mJoacot5dOM2GO3j445+hE24Zsd9hetiprIXt9NWhsfcyhWlvPZ9Y1yMWIRXq/V5kYpTQXb71L/xH7Bnh3+lm1Wh9RtdkmW5V5lBl5nQDz0Vf5qSHdrvHlyUU568FqF6rYv6t76XI0R2xrtJbOHcje0sBNxWiw2BE0QurYANVmdWWzIJREut3NzWzFgWY3m16Rg2gks68fihU2oubAqX0MEyN5K2avqr0PnnphKPhZJoonj4+Pk5g4BY/rYeOxJsMaTq/3dThBUvt+v18DKGTXqeUYVV/+EBTjQoS4hBQ/G0eEjaXQq2qa1m9X+lW25ApsXZYYlI5G9aBTlRi5FXwU6v3icakVqhbrSMNnUFudNFToPk01olHjBzNf1Pk6KOlgMyz0YIUX8vWGCSdvrTb03wh/IRQdbslKzLAITiI6AzH1UZgsvyLh6OhLoGi4lCKzbRih5dFVGAq2Zo1eS2oYWoguv6yQ3qp+h4YE+khl7oqtMAxH/+eTZSTrRCLJdEXX2WyFTlDYDAXB4owXISJOTxH5cXn28klomevdD152Rft1Q8EVRKlSpUqVKlWqVKlSpUqVKlWqVKl/n/4HLjUb7p3M1JkAAAAASUVORK5CYII=" class="img-fluid" style="max-height: 40px; border-radius:6px;">
                                </div>
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



 
<script src="../bootstrap-5.3.7\bootstrap-5.3.7\dist\js\bootstrap.bundle.min.js"></script>
 
  </body>
</html>











