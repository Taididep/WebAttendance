<?php
session_start();
include '../Connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    $sql_student_info = "
    SELECT 
        s.lastname,
        s.firstname,
        s.birthday,
        s.gender,
        s.class
    FROM students s
    WHERE s.student_id = :student_id
    ";

    $stmt = $conn->prepare($sql_student_info);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->execute();
    $student_info = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_info) {
        echo json_encode($student_info);
    } else {
        echo json_encode(null);
    }
} else {
    echo json_encode(null);
}
?>