<?php  
use PHPMailer\PHPMailer\PHPMailer;  
use PHPMailer\PHPMailer\Exception;  

require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';  
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';  
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';  

$mail = new PHPMailer(true);  

// Kiểm tra xem form đã được gửi hay chưa  
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    try {  
        // Cấu hình server SMTP  
        $mail->isSMTP();   
        $mail->Host       = 'smtp.gmail.com';   
        $mail->SMTPAuth   = true;   
        $mail->Username   = 'thongnguyen@ittc.edu.vn'; // Địa chỉ Gmail của bạn   
        $mail->Password   = 'xeiq offy nftu upep'; // Mật khẩu ứng dụng bạn đã tạo   
        $mail->Port       = 587;   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
        
        $mail->SMTPDebug = 0; // Thay đổi thành 2 nếu bạn muốn xem thông tin chi tiết về xác thực, 0 để ẩn thông tin  
        // Thông tin người gửi  
        $mail->setFrom('thongnguyen@ittc.edu.vn', 'Remind');  
        
        // Nhận thông tin người nhận từ form  
        $recipientEmail = $_POST['recipient_email'];  
        $mail->addAddress($recipientEmail); // Địa chỉ người nhận  

        // Nội dung email  
        $mail->isHTML(true);                                  
        $mail->Subject = $_POST['subject']; // Tiêu đề từ form  
        $mail->Body    = $_POST['body']; // Nội dung từ form  
        $mail->AltBody = strip_tags($_POST['body']); // Phiên bản plain text của email  

        // Gửi email  
        if ($mail->send()) {  
            echo '<div class="alert alert-success">Email đã được gửi thành công!</div>';  
        } else {  
            echo '<div class="alert alert-danger">Gửi email không thành công!</div>';  
        }  
    } catch (Exception $e) {  
        echo '<div class="alert alert-danger">Tin nhắn không thể gửi. Lỗi Mailer: {$mail->ErrorInfo}</div>';  
    }  
}  
?>  

<!DOCTYPE html>  
<html lang="vi">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Gửi Email Tự Động</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">  
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">  
</head>  
<body>  
    <div class="container mt-5">  
        <h2>Gửi Email Tự Động</h2>  
        <form method="POST">  
            <div class="mb-3">  
                <label for="recipient_email" class="form-label">Email Người Nhận</label>  
                <input type="email" class="form-control" id="recipient_email" name="recipient_email" required>  
            </div>  
            <div class="mb-3">  
                <label for="subject" class="form-label">Tiêu Đề</label>  
                <input type="text" class="form-control" id="subject" name="subject" required>  
            </div>  
            <div class="mb-3">  
                <label for="body" class="form-label">Nội Dung</label>  
                <textarea class="form-control" id="body" name="body" rows="4" required></textarea>  
            </div>  
            <button type="submit" class="btn btn-primary">Gửi Email</button>  
        </form>  
    </div>  
</body>  
</html>