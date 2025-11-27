<?php
header('Content-Type: application/json');
include("../connexion-bases.php");

// Vérifie si l'ID de la matière est passé
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(["error" => "ID de matière manquant"]);
    exit;
}

$subject_id = intval($_GET['id']);

try {
    // Requête corrigée : récupérer le professeur et le semestre liés à la matière
    $sql = "
        SELECT 
            t.teacher_id,
            CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
            t.email,
            t.phone,
            s.subject_id,
            s.subject_name,
            sem.semester_id,
            sem.semester_name
        FROM teachers_affectation ta
        JOIN teachers t ON ta.teacher_id = t.teacher_id
        JOIN subjects s ON ta.subject_id = s.subject_id
        JOIN semesters sem ON s.semester_id = sem.semester_id
        WHERE ta.subject_id = :subject_id
        LIMIT 1
    ";

    $stmt = $connecter->prepare($sql);
    $stmt->bindParam(':subject_id', $subject_id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["error" => "Aucun professeur trouvé pour cette matière."]);
    }

} catch (PDOException $e) {
    echo json_encode(["error" => "Erreur base de données : " . $e->getMessage()]);
}
?>
