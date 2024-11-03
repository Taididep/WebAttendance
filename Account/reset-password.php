<?php
// Kết nối cơ sở dữ liệu MySQL
$servername = "localhost";
$db_username = "root"; // Thay thế bằng tên người dùng của cơ sở dữ liệu
$db_password = ""; // Thay thế bằng mật khẩu của cơ sở dữ liệu
$dbname = "db_atd"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Yêu cầu các thư viện PHPMailer
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function resetPassword($conn, $username, $emailInput) {
    // Tạo mật khẩu ngẫu nhiên
    $newPassword = bin2hex(random_bytes(4)); // Tạo mật khẩu ngẫu nhiên dài 8 ký tự
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // SQL để kiểm tra nếu username (tên đăng nhập) và email tồn tại và khớp trong bảng sinh viên hoặc giáo viên
    $sql = "
        SELECT 'student' AS user_type, student_id AS id, email 
        FROM db_atd.students 
        WHERE email = ? AND student_id = (SELECT user_id FROM db_atd.users WHERE username = ?)
        UNION
        SELECT 'teacher' AS user_type, teacher_id AS id, email 
        FROM db_atd.teachers 
        WHERE email = ? AND teacher_id = (SELECT user_id FROM db_atd.users WHERE username = ?)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $emailInput, $username, $emailInput, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Lấy user ID và email từ kết quả
        $userId = $row['id'];
        $email = $row['email'];
        
        // Cập nhật mật khẩu trong bảng users
        $updateSql = "UPDATE db_atd.users SET password = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $hashedPassword, $userId);
        
        if ($updateStmt->execute()) {
            // Gửi email với mật khẩu mới
            if (sendEmail($email, $newPassword)) {
                echo "Đặt lại mật khẩu thành công. Mật khẩu mới đã được gửi đến email của bạn.";
            } else {
                echo "Đặt lại mật khẩu thành công, nhưng gửi email thất bại.";
            }
        } else {
            echo "Có lỗi xảy ra khi đặt lại mật khẩu.";
        }
    } else {
        echo "Không tìm thấy tài khoản với tên đăng nhập và email khớp.";
    }
    
    // Đóng câu lệnh
    $stmt->close();
    $updateStmt->close();
}

// Hàm gửi email bằng PHPMailer
function sendEmail($toEmail, $newPassword) {
    $mail = new PHPMailer(true);

    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'thongnguyen@ittc.edu.vn'; // Thay thế bằng email của bạn
        $mail->Password = 'xeiq offy nftu upep';      // Thay thế bằng mật khẩu email của bạn
        $mail->Port = 587;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Nội dung email
        $mail->setFrom('your_email@example.com', 'Tên của bạn hoặc Tên ứng dụng');
        $mail->addAddress($toEmail);
        $mail->Subject = 'Yêu cầu đặt lại mật khẩu';
        $mail->Body = "Mật khẩu mới của bạn là: $newPassword\nVui lòng đăng nhập và thay đổi mật khẩu ngay khi có thể.";

        // Gửi email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Lỗi gửi email: " . $mail->ErrorInfo);
        return false;
    }
}

// Sử dụng hàm
$username = $_POST['username'];
$emailInput = $_POST['email'];
resetPassword($conn, $username, $emailInput);

// Đóng kết nối cơ sở dữ liệu
$conn->close();
?>
