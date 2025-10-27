<?php 
header('Content-Type: application/json');
include("../connexion-bases.php");

// Vérifie si l'ID est passé dans l'URL
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

$id = intval($_GET['id']);

// Requête pour récupérer l'enseignant
$sql = "SELECT teacher_id,first_name, last_name, email, phone
        FROM teachers
        WHERE teacher_id = :id";

$recuperer = $connecter->prepare($sql);
$recuperer->bindParam(':id', $id, PDO::PARAM_INT);
$recuperer->execute();

if ($recuperer->rowCount() > 0) {
    echo json_encode($recuperer->fetch(PDO::FETCH_ASSOC));
} else {
    echo json_encode(["error" => "Enseignant introuvable"]);
}
?>
