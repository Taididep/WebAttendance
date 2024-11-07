<?php  
// File: Account/register.php  
include '../Connect/connect.php';  
session_start();  

function checkUsernameExists($conn, $username) {  
    $sql_check_username = "SELECT * FROM users WHERE username = :username";  
    $stmt_check_username = $conn->prepare($sql_check_username);  
    $stmt_check_username->bindParam(':username', $username);  
    $stmt_check_username->execute();  
    $count_result = $stmt_check_username->rowCount();  

    return $count_result > 0 ? true : false;  
}  

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $student_id = $_POST['student_id'];  
    $username = $_POST['username'];  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  
    $lastname = $_POST['lastname'];  
    $firstname = $_POST['firstname'];  
    $email = $_POST['email'];  
    $phone = $_POST['phone'];  
    $class = $_POST['class'];  
    $birthday = $_POST['birthday'];  
    $gender = $_POST['gender'];  

    // Kiểm tra xem student_id đã tồn tại chưa  
    $sql_check_student = "SELECT COUNT(*) FROM students WHERE student_id = :student_id";  
    $stmt_check_student = $conn->prepare($sql_check_student);  
    $stmt_check_student->bindParam(':student_id', $student_id);  
    $stmt_check_student->execute();  
    $count_result = $stmt_check_student->fetchColumn();  

    if ($count_result > 0) {  
        $_SESSION['error_message'] = "Mã sinh viên đã tồn tại.";  
        header("Location: ../register.php");  
        exit();  
    }  

    // Kiểm tra xem username đã tồn tại chưa  
    if (checkUsernameExists($conn, $username)) {  
        $_SESSION['error_message'] = "Username đã tồn tại.";  
        header("Location: ../register.php"); 
        exit();  
    }  

   // Đặt chế độ báo lỗi của PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Các phần khác trong mã không thay đổi
    $user_id = $_POST['student_id'];
    try {  
        // Thêm người dùng vào bảng users  
        $sql = "INSERT INTO users (user_id,username, password) VALUES (:user_id, :username, :password)";  
        $stmt = $conn->prepare($sql);  
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(':username', $username);  
        $stmt->bindParam(':password', $password);  
        $stmt->execute();  

        // Lấy user_id mới  
        

        // Gán vai trò mặc định (student)  
        $role_id = 3;  
        $sql_role = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";  
        $stmt_role = $conn->prepare($sql_role);  
        $stmt_role->bindParam(':user_id', $user_id);  // Dùng $user_id thay vì $student_id  
        $stmt_role->bindParam(':role_id', $role_id);  
        $stmt_role->execute();  

        // Thêm thông tin cá nhân vào bảng students  
        $sql_student = "INSERT INTO students (student_id, lastname, firstname, email, phone, class, birthday, gender)   
                        VALUES (:student_id, :lastname, :firstname, :email, :phone, :class, :birthday, :gender)";  
        $stmt_student = $conn->prepare($sql_student);  
        $stmt_student->bindParam(':student_id', $student_id);  
        $stmt_student->bindParam(':lastname', $lastname);  
        $stmt_student->bindParam(':firstname', $firstname);  
        $stmt_student->bindParam(':email', $email);  
        $stmt_student->bindParam(':phone', $phone);  
        $stmt_student->bindParam(':class', $class);  
        $stmt_student->bindParam(':birthday', $birthday);  
        $stmt_student->bindParam(':gender', $gender);  
        $stmt_student->execute();  

        $_SESSION['success_message'] = "Chúc mừng bạn đã đăng ký thành công!";  
        header("Location: ../login_view.php");  
        exit();  
    } catch (PDOException $e) {  
        $_SESSION['error_message'] = "Có lỗi xảy ra khi đăng ký tài khoản: " . $e->getMessage();  
        header("Location: ../login_view.php");  
        exit();  
    }  
}  
?>