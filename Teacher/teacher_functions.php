<?php
session_start();
include '../Connect/connect.php'; // Gọi kết nối cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
function checkUserAccess() {
    if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
        header("Location: index.php");
        exit;
    }
}

// Hàm lấy thông tin giáo viên từ cơ sở dữ liệu
function getTeacherById($conn, $teacherId) {
    $sql = "SELECT * FROM teachers WHERE id = ?";
    $stm = $conn->prepare($sql);
    $stm->execute([$teacherId]);
    return $stm->fetch(PDO::FETCH_OBJ);
}

// Hàm cập nhật thông tin giáo viên
function updateTeacherInfo($conn, $teacherId, $lastname, $firstname, $email, $phone, $birthday, $gender) {
    $updateSql = "UPDATE teachers SET lastname = ?, firstname = ?, email = ?, phone = ?, birthday = ?, gender = ? WHERE id = ?";
    $updateStm = $conn->prepare($updateSql);
    
    return $updateStm->execute([$lastname, $firstname, $email, $phone, $birthday, $gender, $teacherId]);
}
?>
