<?php  
$servername = "localhost";   
$username = "root";   
$password = "";   
$dbname = "db_atd";   

$conn = new mysqli($servername, $username, $password, $dbname);  

// Kiểm tra kết nối  
if ($conn->connect_error) {  
    die("Kết nối thất bại: " . $conn->connect_error);  
}  

$message = ""; // Biến chứa thông báo

// Xử lý yêu cầu đổi mật khẩu  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    $current_password = $_POST['current_password'];  
    $new_password = $_POST['new_password'];  
    $username = $_POST['username'];  

    // Kiểm tra mật khẩu hiện tại  
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");  
    if (!$stmt) {  
        die("Lỗi câu lệnh: " . $conn->error);  
    }  

    $stmt->bind_param("s", $username);  
    $stmt->execute();  
    $stmt->bind_result($hashed_password);  
    $stmt->fetch();  

    if (password_verify($current_password, $hashed_password)) {  
        // Cập nhật mật khẩu mới  
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);  
        $stmt->close(); // Đóng câu lệnh trước khi chuẩn bị lại  

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");  
        if (!$stmt) {  
            die("Lỗi câu lệnh cập nhật: " . $conn->error);  
        }  

        $stmt->bind_param("ss", $new_hashed_password, $username);  
        
        if ($stmt->execute()) {  
            $message = "<p style='color:green;'>Mật khẩu đã được đổi thành công.</p>";  
        } else {  
            $message = "<p style='color:red;'>Đã xảy ra lỗi trong quá trình cập nhật: " . $stmt->error . "</p>";  
        }  
    } else {  
        $message = "<p style='color:red;'>Mật khẩu hiện tại không đúng.</p>";  
    }  
    $stmt->close();  
}  
$conn->close();  
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
            background: url('../Image/Index.jpg') no-repeat center center fixed; /* Đường dẫn đúng từ vị trí file */
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
        <p><a href="../Teacher/index.php">Quay lại trang chủ</a></p>  
    </div>  
</body>  
</html>