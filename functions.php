<?php
include 'connect/connect.php';

//lấy thông tin học sinh
function getClassInfo($conn, $classId) {
    $sql = "SELECT name FROM classes WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

//lấy danh sách điểm danh theo ngày
function getAttendanceDates($conn, $classId) {
    $sql = "SELECT DISTINCT attendance_date FROM attendances WHERE class_id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId]);
    return $stm->fetchAll(PDO::FETCH_COLUMN);
}

//lấy danh sách học sinh(phân trang)
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
//lấy tổng học sinh theo lớp và ngày điểm danh
function getTotalStudents($conn, $classId, $attendanceDate) {
    $sql = "SELECT COUNT(*) AS total FROM attendances WHERE class_id = ? AND attendance_date = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$classId, $attendanceDate]);
    $result = $stm->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

//lấy danh sách học sinh theo ngày và lớp
function getAttendanceByStudent($conn, $classId, $studentId, $attendanceDate) {
    $sql = "SELECT * FROM attendances WHERE class_id = ? AND student_id = ? AND attendance_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$classId, $studentId, $attendanceDate]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}





//Hàm dành cho attendance
//
function checkAttendanceExists($conn, $classId, $studentId, $attendanceDate) {
    $sql = "SELECT * FROM attendances WHERE class_id = ? AND student_id = ? AND attendance_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$classId, $studentId, $attendanceDate]);
    return $stmt->fetch(PDO::FETCH_ASSOC); // Trả về một mảng duy nhất
}

function updateAttendance($conn, $classId, $studentId, $attendanceDate, $status, $note) {
    $sql = "UPDATE attendances SET `status` = ?, note = ? WHERE class_id = ? AND student_id = ? AND attendance_date = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$status, $note, $classId, $studentId, $attendanceDate]);
}

function insertAttendance($conn, $classId, $studentId, $attendanceDate, $status, $note) {
    $sql = "INSERT INTO attendances (class_id, student_id, attendance_date, `status`, note) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$classId, $studentId, $attendanceDate, $status, $note]);
}

function createAttendanceDate($conn, $classId, $attendanceDate) {
    // Bắt đầu giao dịch
    $conn->beginTransaction();
    
    try {
        // Lấy danh sách học sinh trong lớp
        $sqlStudents = "SELECT id FROM students WHERE id IN (SELECT student_id FROM class_students WHERE class_id = ?)";
        $stmStudents = $conn->prepare($sqlStudents);
        $stmStudents->execute([$classId]);
        $students = $stmStudents->fetchAll(PDO::FETCH_ASSOC);

        // Chuẩn bị câu lệnh chèn điểm danh
        $sql = "INSERT INTO attendances (class_id, student_id, attendance_date, status) VALUES (?, ?, ?, ?)";
        $stm = $conn->prepare($sql);
        
        // Lặp qua danh sách học sinh và chèn điểm danh
        foreach ($students as $student) {
            $stm->execute([$classId, $student['id'], $attendanceDate, 'Absent']); // Mặc định là 'Absent'
        }

        // Cam kết giao dịch
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Nếu có lỗi, rollback giao dịch
        $conn->rollBack();
        return false;
    }
}





?>


