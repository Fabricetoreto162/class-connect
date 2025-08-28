<?php
session_start();
$msg1 = "";
$msg2 = "";

if (isset($_POST["connexion"])) {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
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

        // Récupération des données POST
        $email = $_POST["email"];
        $password_input = $_POST["password"];

        // Préparation et exécution de la requête
        $connecter_admin = $connecter->prepare("SELECT * FROM users WHERE Email = ?");
        $connecter_admin->execute([$email]);
        $resultat_admin = $connecter_admin->fetch();

        if ($resultat_admin) {
            $email_defaut = $resultat_admin["Email"];
            $password_defaut = $resultat_admin["Password"];
            $role_defaut = $resultat_admin["Role"];
            $nom = $resultat_admin["Nom"];
            $prenom = $resultat_admin["Prenom"];

            // Vérification des informations
            if ($email_defaut == $email && password_verify($password_input, $password_defaut) && $role_defaut == "admin") {
                $_SESSION["Nom"] = $nom . " " . $prenom;
                header("Location:dashbord-admin.php");
                exit();  
            } else {
                $msg2 = "Email ou mot de passe incorrect.";
            }
        } else {
            $msg2 = "Email ou mot de passe incorrect.";
        }
    } else {
        $msg1 = "Veuillez remplir tous les champs.";
    }
}
?>









<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion-admin</title>
    <link rel="stylesheet" href="../bootstrap-5.3.7/bootstrap-5.3.7/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="inscription-admin.css">
    
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
            <a class="nav-link   active" aria-current="page" href="inscription-admin.php">Inscription</a>
          </li> 
          <li class="nav-item">
            <a class="nav-link" href="connexion-admin.php">Connexion</a>
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
    <section>
      <div class="page-header d-flex vh-100">
        <div class="container ">
          <div class="row d-flex justify-content-center align-item-normal">
           
            <div class="col-xl-4 formulaire position-absolute col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="font-weight-bolder text-center">Connexion</h4>
                  <p class="mb-0">Entrez votre email et votre mot de passe pour vous Connecter.</p>
                </div>
                <div class="card-body">
                  <form role="form"  method="post" action="">
                    
                     
                    <div class="input-group input-group-outline mb-3">
                     
                      <input type="email" name="email" class="form-control" placeholder="Entrez votre Email" required>
                    </div>
                    <div class="input-group input-group-outline border  rounded mb-3">
                      <input type="password" name="password" id="password" class=" form-control rounded outline-none" placeholder="Mot de passe" required>
                      <span class="mx-2 my-2 ">
                        <i class="fa-solid fa-eye " onclick="afficher()" style="color: black; font-size:20px;cursor:pointer;" id="oeil"></i>
                      </span>
                    </div>
                    <div class="mb-3" style="color: red; font-size:20px;">
                      <?=$msg1?>
                    </div>
                    <div class="text-center">
                      <button type="submit" name="connexion" class="btn btn-lg bg-warning btn-lg w-100 my-4 mb-0">Connexion</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center py-0 px-lg-2 px-1">
                  <p class="my-2 text-sm mx-auto">
                    Vous n'avez pas de compte? 
                    <a href="inscription-admin.php" class="text-warning mx-2 Connexion font-weight-bold">Inscription</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>








    <script src="../bootstrap-5.3.7/bootstrap-5.3.7/dist/js/bootstrap.min.js"></script>
 <script src="inscription-admin.js"></script> 
 
 <script>

function afficher(){
  let input=document.getElementById("password");
  if(input.type==="password"){
    input.type="text";
  }else{
    input.type="password";
  }
}

 </script>  
</body>
</html>