<?php

include("../connexion-bases.php");
session_start();
$teacher_id = $_SESSION["teacher_id"];



if (!empty($_GET['schedule_id'])) {
    $id = $_GET['schedule_id'];
   $query = "
SELECT 
    s.schedule_id,
    sub.subject_name,
    sub.subject_id,
    s.day, 
    s.start_time, 
    s.end_time,
    r.classroom_name,
    d.department_name
FROM schedules s
JOIN classrooms r ON s.classroom_id = r.classroom_id
JOIN subjects sub ON s.subject_id = sub.subject_id
JOIN levels l ON sub.level_id = l.level_id
JOIN departments d ON l.department_id = d.department_id
WHERE s.schedule_id = ?
";

    $stmt = $connecter->prepare($query);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data);
}


















?>