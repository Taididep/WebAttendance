<?php
include 'connect/connect.php'; // Đảm bảo bạn đã kết nối với CSDL

// Lấy danh sách sinh viên trong lớp
function getStudentsByClassId($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT cs.stt, s.id AS student_id, s.lastname, s.firstname, s.gender, s.birthday, c.name AS class_name 
        FROM students s 
        JOIN class_students cs ON s.id = cs.student_id
        JOIN classes c ON cs.class_id = c.id
        WHERE c.id = :class_id
        ORDER BY cs.stt ASC
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

function createAttendanceDate($conn, $classId, $date) {
    $stmt = $conn->prepare("INSERT INTO attendance_dates (class_id, date) VALUES (:class_id, :date)");
    $stmt->bindValue(':class_id', $classId, PDO::PARAM_INT);
    $stmt->bindValue(':date', $date);
    return $stmt->execute();
}

// Lấy ngày tiếp theo để điểm danh
function getNextAttendanceDate($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT MAX(attendance_date) AS max_date 
        FROM attendances 
        WHERE class_id = :class_id
    ");
    $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
    $stmt->execute();
    $max_date = $stmt->fetchColumn();

    if ($max_date) {
        $next_date = date('Y-m-d', strtotime($max_date . ' +1 day'));
        return $next_date;
    }

    return date('Y-m-d'); // Nếu không có ngày nào, trả về ngày hiện tại
}

?>
