<?php
include 'connect/connect.php'; // Đảm bảo bạn đã kết nối với CSDL

// Lấy danh sách sinh viên trong lớp
function getStudentsByClassId($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT s.id as student_id, s.lastname, s.firstname, s.gender, s.birthday, c.name as class_name 
        FROM students s 
        JOIN class_students cs ON s.id = cs.student_id
        JOIN classes c ON cs.class_id = c.id
        WHERE c.id = :class_id
    ");
    $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy danh sách các ngày điểm danh
function getAttendanceDatesByClassId($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT DISTINCT attendance_date 
        FROM attendances 
        WHERE class_id = :class_id
        ORDER BY attendance_date
    ");
    $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Lấy dữ liệu điểm danh cho lớp
function getAttendanceDataByClassId($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT student_id, attendance_date, status 
        FROM attendances 
        WHERE class_id = :class_id
    ");
    $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    $attendances = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $attendances[$row['student_id']][$row['attendance_date']] = $row['status'];
    }
    return $attendances;
}
?>
