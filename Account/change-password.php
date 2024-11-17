<?php  
include '../Connect/connect.php';  
session_start();  

$message = ""; // Variable for the message

// Process password change request  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $current_password = $_POST['current_password'];  
    $new_password = $_POST['new_password'];  
    $username = $_POST['username'];  

    try {
        // Check current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();  
        $hashed_password = $stmt->fetchColumn(); // Fetch the password column

        if ($hashed_password && password_verify($current_password, $hashed_password)) {  
            // Update new password  
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);  

            $stmt = $conn->prepare("UPDATE users SET password = :new_password WHERE username = :username");
            $stmt->bindParam(':new_password', $new_hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);

            if ($stmt->execute()) {  
                $message = "<p style='color:green;'>Password changed successfully.</p>";  
            } else {  
                $message = "<p style='color:red;'>Error updating password: " . $stmt->errorInfo()[2] . "</p>";  
            }  
        } else {  
            $message = "<p style='color:red;'>Current password is incorrect.</p>";  
        }  
    } catch (PDOException $e) {
        $message = "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}  

?>

<!DOCTYPE html>  
<html lang="vi">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Đổi Mật Khẩu</title>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">  
    <style>  
        body {  
            background: url('../Image/Index.jpg') no-repeat center center fixed; /* Correct path from current file */
            background-size: cover;  
            display: flex;  
            justify-content: center;  
            align-items: center;  
            height: 100vh;  
            margin: 0;  
            font-family: Arial, sans-serif;  
        }  
        .container {  
            background: white;  
            padding: 20px;  
            border-radius: 10px;  
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);  
            text-align: center;  
            width: 400px;  
        }  
        h2 {  
            margin-bottom: 20px;  
            color: #333;  
        }  
        input[type="text"],  
        input[type="password"] {  
            width: 90%;  
            padding: 10px;  
            margin: 10px 0;  
            border: 1px solid #ccc;  
            border-radius: 5px;  
        }  
        input[type="submit"] {  
            background: #4b6cb7;  
            color: white;  
            padding: 10px;  
            border: none;  
            border-radius: 5px;  
            cursor: pointer;  
            font-size: 16px;  
        }  
        input[type="submit"]:hover {  
            background: #395b99;  
        }  
        p {  
            margin: 10px 0;  
        }  
        a {  
            color: #4b6cb7;  
            text-decoration: none;  
        }  
        a:hover {  
            text-decoration: underline;  
        }  
    </style>  
</head>  
<body>  
    <div class="container">  
        <h2>Đổi Mật Khẩu</h2>  
        <form method="POST" action="">  
            <input type="text" name="username" placeholder="Tên đăng nhập" required>  
            <input type="password" name="current_password" placeholder="Mật khẩu hiện tại" required>  
            <input type="password" name="new_password" placeholder="Mật khẩu mới" required>  
            <input type="submit" value="Đổi mật khẩu">  
        </form>  
        <?php echo $message; ?>  
        <p><a href="../Student/index.php">Quay lại trang chủ</a></p>  
    </div>  
</body>  
</html>  
