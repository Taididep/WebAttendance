<?php
include '../Connect/connect.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester_id = $_POST['semester_id'];

    $sql = "SELECT class_id, class_name, co.course_name 
            FROM classes c 
            JOIN courses co ON c.course_id = co.course_id 
            WHERE c.semester_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$semester_id]);
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($classes);
}
?>