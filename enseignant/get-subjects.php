<?php

include("../connexion-bases.php");
session_start();
$teacher_id = $_SESSION["user_id_teacher"];



if (!empty($_GET['schedule_id'])) {
    $id = $_GET['schedule_id'];
    $query = "SELECT s.schedule_id, s.day, s.start_time, s.end_time, r.classroom_name
              FROM schedules s
              INNER JOIN classrooms r ON s.classroom_id = r.classroom_id
              WHERE s.schedule_id = ?";
    $stmt = $connecter->prepare($query);
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data);
}


















?>