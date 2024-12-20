<?php
// File: Account/register_teacher.php  
include '../Connect/connect.php';
session_start();

function checkUsernameExists($conn, $username)
{
    $sql_check_username = "SELECT * FROM users WHERE username = :username";
    $stmt_check_username = $conn->prepare($sql_check_username);
    $stmt_check_username->bindParam(':username', $username);
    $stmt_check_username->execute();
    return $stmt_check_username->rowCount() > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $teacher_id = $_POST['teacher_id'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $birthday = $_POST['birthday'];
    $gender = $_POST['gender'];

    // Kiểm tra xem teacher_id đã tồn tại chưa  
    $sql_check_teacher = "SELECT COUNT(*) FROM teachers WHERE teacher_id = :teacher_id";
    $stmt_check_teacher = $conn->prepare($sql_check_teacher);
    $stmt_check_teacher->bindParam(':teacher_id', $teacher_id);
    $stmt_check_teacher->execute();
    $count_result = $stmt_check_teacher->fetchColumn();

    if ($count_result > 0) {
        $_SESSION['error_message'] = "Mã giáo viên đã tồn tại.";
        header("Location: ./register.php");
        exit();
    }

    // Kiểm tra xem username đã tồn tại chưa  
    if (checkUsernameExists($conn, $username)) {
        $_SESSION['error_message'] = "Username đã tồn tại.";
        header("Location: ./register.php");
        exit();
    }

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        // Chuẩn bị câu lệnh SQL để gọi thủ tục
        $sql = "CALL InsertUser(:user_id, :username, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":user_id", $teacher_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $password, PDO::PARAM_STR);
        $stmt->execute();

        // Gán vai trò mặc định (teacher)  
        $role_id = 2;
        $sql_role = "CALL InsertUserRole(:user_id, :role_id)";
        $stmt_role = $conn->prepare($sql_role);
        $stmt_role->bindParam(':user_id', $teacher_id);
        $stmt_role->bindParam(':role_id', $role_id);
        $stmt_role->execute();

        // Thêm thông tin cá nhân vào bảng teachers  
        $sql_teacher = "CALL InsertTeacher(:teacher_id, :lastname, :firstname, :email, :phone, :birthday, :gender)";

        $stmt_teacher = $conn->prepare($sql_teacher);
        $stmt_teacher->bindParam(':teacher_id', $teacher_id);
        $stmt_teacher->bindParam(':lastname', $lastname);
        $stmt_teacher->bindParam(':firstname', $firstname);
        $stmt_teacher->bindParam(':email', $email);
        $stmt_teacher->bindParam(':phone', $phone);
        $stmt_teacher->bindParam(':birthday', $birthday);
        $stmt_teacher->bindParam(':gender', $gender);
        $stmt_teacher->execute();

        $_SESSION['success_message'] = "Chúc mừng bạn đã đăng ký thành công!";
        header("Location: ./register.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi đăng ký tài khoản: " . $e->getMessage();
        header("Location: ./register.php");
        exit();
    }
}
