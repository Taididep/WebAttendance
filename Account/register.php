<?php
// File: Account/register.php
include '../Connect/connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $class = $_POST['class'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    try {
        // Thêm người dùng vào bảng users
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Lấy user_id mới
        $user_id = $conn->lastInsertId();

        // Gán vai trò mặc định (ví dụ: student)
        $role_id = 3; // ID của vai trò 'student'
        $sql_role = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
        $stmt_role = $conn->prepare($sql_role);
        $stmt_role->bindParam(':user_id', $user_id);
        $stmt_role->bindParam(':role_id', $role_id);
        $stmt_role->execute();

        // Thêm thông tin cá nhân vào bảng students
        $sql_student = "INSERT INTO students (student_id, lastname, firstname, email, phone, class, birthday, gender) 
                        VALUES (:student_id, :lastname, :firstname, :email, :phone, :class, :birthday, :gender)";
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bindParam(':student_id', $user_id); // Sử dụng user_id vừa tạo
        $stmt_student->bindParam(':lastname', $lastname);
        $stmt_student->bindParam(':firstname', $firstname);
        $stmt_student->bindParam(':email', $email);
        $stmt_student->bindParam(':phone', $phone);
        $stmt_student->bindParam(':class', $class);
        $stmt_student->bindParam(':birthday', $birthday);
        $stmt_student->bindParam(':gender', $gender);
        $stmt_student->execute();

        $_SESSION['success_message'] = "Chúc mừng bạn đã đăng ký thành công!"; // Gán thông báo thành công
        header("Location: ../index.php"); // Chuyển hướng về trang index
        exit();
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>