<?php
// Kết nối cơ sở dữ liệu
include '../Connect/connect.php';

// Hàm lấy thông tin người dùng dựa trên tên đăng nhập
function getUserByUsername($conn, $username) {
    $sql = "SELECT id FROM users WHERE username = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$username]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Hàm lấy thông tin sinh viên dựa trên ID sinh viên
function getStudentById($conn, $studentId) {
    $sql = "SELECT id, lastname, firstname, email, phone, birthday, gender FROM students WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$studentId]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Hàm lấy danh sách các học kỳ đang hoạt động
function getActiveSemesters($conn) {
    $sql = "SELECT * FROM semesters WHERE is_active = 1";
    $stm = $conn->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

// Hàm lấy danh sách các lớp học theo học kỳ đã chọn
function getClassesBySemesterId($conn, $semesterId) {
    $sql = "SELECT c.id, c.name AS class_name, co.name AS course_name, c.student_count
            FROM classes c
            JOIN courses co ON c.course_id = co.id
            WHERE c.semester_id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$semesterId]);
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

// Hàm cập nhật thông tin cá nhân của sinh viên
function updateStudentProfile($conn, $studentId, $lastname, $firstname, $email, $phone, $birthday, $gender) {
    $sql = "UPDATE students SET lastname = ?, firstname = ?, email = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?";
    $stm = $conn->prepare($sql);
    return $stm->execute([$lastname, $firstname, $email, $phone, $birthday, $gender, $studentId]);
}
