<?php
include 'connect/connect.php';

function getClassInfo($conn, $classId) {
    $sql = "SELECT name FROM classes WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

function getAttendanceDates($conn, $classId) {
    $sql = "SELECT DISTINCT attendance_date FROM attendances WHERE class_id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId]);
    return $stm->fetchAll(PDO::FETCH_COLUMN);
}

function getStudents($conn, $classId, $attendanceDate, $limit, $offset) {
    $sql = "
        SELECT s.id, s.lastname, s.firstname, s.class, s.gender, s.birthday, a.status, a.note
        FROM students s
        JOIN attendances a ON s.id = a.student_id
        WHERE a.class_id = ? AND a.attendance_date = ?
        LIMIT ?, ?
    ";
    $stm = $conn->prepare($sql);
    $stm->bindValue(1, $classId, PDO::PARAM_INT);
    $stm->bindValue(2, $attendanceDate);
    $stm->bindValue(3, $offset, PDO::PARAM_INT);
    $stm->bindValue(4, $limit, PDO::PARAM_INT);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}



function getTotalStudents($conn, $classId, $attendanceDate) {
    $sql = "SELECT COUNT(*) AS total FROM attendances WHERE class_id = ? AND attendance_date = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId, $attendanceDate]);
    $result = $stm->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

?>


