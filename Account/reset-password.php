<?php
include '../Connect/connect.php';
session_start();

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

    try {
        // SQL để kiểm tra nếu username (tên đăng nhập) và email tồn tại và khớp trong bảng sinh viên hoặc giáo viên
        $stmt = $conn->prepare("CALL GetUserByEmailAndUsername(:email, :username)");
        $stmt->bindParam(':email', $emailInput, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        // Kiểm tra nếu tồn tại user
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Lấy user ID và email từ kết quả
            $userId = $row['id'];
            $userEmail = $row['email'];
            $stmt->closeCursor(); // Close cursor
            
            // Cập nhật mật khẩu trong bảng users
            $updateStmt = $conn->prepare("CALL UpdateUserPassword(:user_id, :new_password)");
            $updateStmt->bindParam(':new_password', $hashedPassword, PDO::PARAM_STR);
            $updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

            if ($updateStmt->execute()) {
                // Gửi email với mật khẩu mới
                if (sendEmail($userEmail, $newPassword)) {
                    $_SESSION['success_message'] = "Đặt lại mật khẩu thành công. Mật khẩu mới đã được gửi đến email của bạn.";
                } else {
                    $_SESSION['error_message'] = "Đặt lại mật khẩu thành công, nhưng gửi email thất bại.";
                }
            } else {
                $_SESSION['error_message'] = "Có lỗi xảy ra khi đặt lại mật khẩu.";
            }
        } else {
            $_SESSION['error_message'] = "Username và email không khớp. Vui lòng nhập lại.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
    }

    // Chuyển hướng lại trang forgot-pass.php để hiển thị thông báo
    header("Location: ./forgot-pass.php");
    exit();
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
        $mail->setFrom('your_email@example.com', 'TLT cấp mật khẩu');
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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $emailInput = $_POST['email'];
    resetPassword($conn, $username, $emailInput);
}

// Đóng kết nối cơ sở dữ liệu
$conn = null;
?>
