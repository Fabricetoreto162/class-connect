<?php
include("../connexion-bases.php");
session_start();

$student_id = $_SESSION["student_id"];
$semestre = isset($_GET["semestre"]) ? intval($_GET["semestre"]) : 1;

try {
    $stmt = $connecter->prepare("
        SELECT s.name AS subject_name, 
               a.assignment1, a.assignment2, a.assignment3, 
               a.exam1, a.exam2, 
               a.moyenne, a.coefficient
        FROM assignments a
        JOIN subjects s ON a.subject_id = s.id
        WHERE a.student_id = :student_id AND a.semestre = :semestre
    ");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->bindParam(':semestre', $semestre, PDO::PARAM_INT);
    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($notes)) {
        echo "<div class='text-center text-muted py-5'>
                <i class='fas fa-info-circle fa-2x mb-3'></i><br>
                Aucune note trouvée pour ce semestre.
              </div>";
        exit;
    }

    echo '<div class="table-responsive">
            <table class="table table-hover notes-table align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Matière</th>
                        <th>Interro 1</th>
                        <th>Interro 2</th>
                        <th>Interro 3</th>
                        <th>Devoir 1</th>
                        <th>Devoir 2</th>
                        <th>Moyenne</th>
                        <th>Crédits</th>
                    </tr>
                </thead>
                <tbody>';
                
    foreach ($notes as $note) {
        echo "<tr>
                <td><strong>".htmlspecialchars($note['subject_name'])."</strong></td>
                <td>{$note['interro1']}</td>
                <td>{$note['interro2']}</td>
                <td>{$note['interro3']}</td>
                <td>{$note['devoir1']}</td>
                <td>{$note['devoir2']}</td>
                <td class='fw-bold text-primary'>{$note['moyenne']}</td>
                <td><span class='badge bg-success'>{$note['credit']}</span></td>
              </tr>";
    }

    echo '</tbody></table></div>';

} catch (PDOException $e) {
    echo "<p class='text-danger text-center'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
