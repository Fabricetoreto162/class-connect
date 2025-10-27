<?php

header('Content-Type: application/json');
include("../connexion-bases.php");

$id = intval($_GET['id']);
$result = $conn->query("SELECT nom, prenom 
                        FROM users 
                        WHERE id_user = $id AND role = 'enseignant'");

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Enseignant introuvable"]);
 }

?>

<script>
function chargerInfos(userId) {
    if (userId === "") return;

    fetch("get_user.php?id=" + userId)
        .then(response => response.json())
        .then(data => {
            document.getElementById("nom").value = data.nom;
            document.getElementById("prenom").value = data.prenom;
        })
        .catch(error => console.error("Erreur :", error));
}


</script>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enregistrer un enseignant</title>
    <script>
    function chargerInfos(userId) {
        if (userId === "") return;

        fetch("get_user.php?id=" + userId)
            .then(response => response.json())
            .then(data => {
                document.getElementById("nom").value = data.nom;
                document.getElementById("prenom").value = data.prenom;
            })
            .catch(error => console.error("Erreur :", error));
    }
    </script>
</head>
<body>
    <h2>Formulaire d'inscription d'un enseignant</h2>
    <form action="traitement.php" method="POST">
        <label>Choisir un utilisateur :</label>
        <select name="user_id" onchange="chargerInfos(this.value)" required>
            <option value="">-- Sélectionner --</option>
            <?php
            $conn = new mysqli("localhost", "root", "", "ecole");
            $result = $conn->query("SELECT id_user, nom, prenom FROM users");
            while($row = $result->fetch_assoc()){
                echo "<option value='".$row['id_user']."'>".$row['nom']." ".$row['prenom']."</option>";
            }
            ?>
        </select><br><br>

        <label>Nom :</label>
        <input type="text" id="nom" disabled><br><br>

        <label>Prénom :</label>
        <input type="text" id="prenom" disabled><br><br>

        <label>Filière :</label>
        <select name="id_filiere" required>
            <?php
            $result = $conn->query("SELECT * FROM filiere");
            while($row = $result->fetch_assoc()){
                echo "<option value='".$row['id_filiere']."'>".$row['nom_filiere']."</option>";
            }
            ?>
        </select><br><br>

        <label>Matière :</label>
        <select name="id_matiere" required>
            <?php
            $result = $conn->query("SELECT * FROM matiere");
            while($row = $result->fetch_assoc()){
                echo "<option value='".$row['id_matiere']."'>".$row['nom_matiere']."</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Enregistrer</button>
    </form>
</body>
</html>