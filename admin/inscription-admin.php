<?php
$msg1="";
if(isset($_POST["inscription"])){
// Début connexion à la bases de données  
      $serveur="localhost";
       $name="root";
       $password="";
       $connexion_admin=new PDO("mysql:host=$serveur;dbname=gestion_des_etudiants",$name,$password);
       $connexion_admin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         // fin connexion à la bases de données 


if(!empty($_POST["nom"]) || 
  !empty($_POST["prenom"]) ||
  !empty($_POST["email"])||
  !empty($_POST["password"]) ){
      
//fonction pour la securiter des donneés
 
 function verification_donnees_admin($admin){
            $admin  = trim($admin);
            $admin = htmlspecialchars($admin);
            $admin = stripcslashes($admin);
            $admin = strip_tags($admin);      
            return $admin;

        }

$nom_admin=strtoupper(verification_donnees_admin($_POST["nom"]));
$prenom_admin=ucfirst(strtolower(verification_donnees_admin($_POST["prenom"])));
$email_admin=verification_donnees_admin($_POST["email"]);
$password_admin=password_hash(verification_donnees_admin($_POST["password"]), PASSWORD_DEFAULT); 
$role="admin";

$requete_admin=$connexion_admin->prepare(" INSERT INTO users(Nom,Prenom,Email,Password,Role)
 VALUES(:Nom,:Prenom,:Email,:Password,:Role) ");
$requete_admin->bindParam(":Nom",$nom_admin);
$requete_admin->bindParam(":Prenom",$prenom_admin);
$requete_admin->bindParam(":Email",$email_admin);
$requete_admin->bindParam(":Password",$password_admin);
$requete_admin->bindParam(":Role",$role);
$requete_admin->execute();
      
header("location:reussite.php");

  } else{
    $msg1="* Merci de remplir tous les champs obligatoires. ";
  }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription-admin</title>
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
            <div class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
              <div class="position-relative image-etudiant h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center rounded-1" style=" background-image: url(../img/administrateur.jpg);">
              </div>
            </div>
            <div class="col-xl-4 formulaire col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5">
              <div class="card card-plain">
                <div class="card-header">
                  <h4 class="font-weight-bolder text-center">Inscription</h4>
                  <p class="mb-0">Entrez votre email et votre mot de passe pour vous inscrit.</p>
                </div>
                <div class="card-body">
                  <form class="form "  method="post" action="" >
                    <div class="input-group input-group-outline mb-3">
                      
                      <input type="text" name="nom" class="form-control " placeholder="Entrez votre nom " required>
                    
                    </div>
                     <div class="input-group input-group-outline mb-3">
                      
                      <input type="text" name="prenom" class="form-control " placeholder="Entrez votre prénom " required>
                    </div>
                     
                    <div class="input-group input-group-outline mb-3">
                     
                      <input type="email" name="email" class="form-control" placeholder="Entrez votre Email" required>
                    </div>
                    <div class="input-group input-group-outline border  rounded mb-3">
                      <input type="password" name="password" id="password" class=" form-control rounded outline-none" placeholder="Mot de passe" required>
                      <span class="mx-2 my-2 ">
                        <i class="fa-solid fa-eye " onclick="afficher()" style="color: black; font-size:20px;cursor:pointer;" id="oeil"></i>
                      </span>
                    </div>
                   
                     <div style="color: red; font-size:20px;"><?=$msg1?></div>
                    <div class="text-center">
                      <button type="submit" name="inscription" class="btn btn-lg bg-warning btn-lg w-100 my-4 mb-0" >Inscription</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center py-0 px-lg-2 px-1">
                  <p class="my-2 text-sm mx-auto">
                    Avez-vous déjà un compte?
                    <a href="connexion-admin.php" class="text-warning mx-2 Connexion font-weight-bold">Connexion</a>
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
