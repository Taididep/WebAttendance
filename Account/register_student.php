<?php  
include '../Connect/connect.php';  
session_start();  

function checkUsernameExists($conn, $username) {  
    $sql_check_username = "SELECT * FROM users WHERE username = :username";  
    $stmt_check_username = $conn->prepare($sql_check_username);  
    $stmt_check_username->bindParam(':username', $username);  
    $stmt_check_username->execute();  
    return $stmt_check_username->rowCount() > 0;  
}  

$error_message = "";  

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

    // Kiểm tra mã sinh viên
    $sql_check_student = "SELECT COUNT(*) FROM students WHERE student_id = :student_id";  
    $stmt_check_student = $conn->prepare($sql_check_student);  
    $stmt_check_student->bindParam(':student_id', $student_id);  
    $stmt_check_student->execute();  

    if ($stmt_check_student->fetchColumn() > 0) {  
        $_SESSION['error_message'] = "Mã sinh viên đã tồn tại. Vui lòng nhập lại.";  
        header("Location: ../register.php");  
        exit();  
    }  

    // Kiểm tra username
    if (checkUsernameExists($conn, $username)) {  
        $_SESSION['error_message'] = "Username đã tồn tại. Vui lòng nhập lại.";  
        header("Location: ../register.php");  
        exit();  
    }  

    // Nếu không có lỗi, tiếp tục thêm dữ liệu
    if (empty($error_message)) {  
        try {  
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $user_id = $_POST['student_id'];

            // Thêm người dùng vào bảng users  
            $sql = "INSERT INTO users (user_id, username, password) VALUES (:user_id, :username, :password)";  
            $stmt = $conn->prepare($sql);  
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(':username', $username);  
            $stmt->bindParam(':password', $password);  
            $stmt->execute();  

            // Gán vai trò mặc định (student)  
            $role_id = 3;  
            $sql_role = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";  
            $stmt_role = $conn->prepare($sql_role);  
            $stmt_role->bindParam(':user_id', $user_id);  
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
            $_SESSION['error_message'] = "Có lỗi xảy ra: " . $e->getMessage();  
            header("Location: ../register.php");  
            exit();  
        }  
    }  
}  
?>
