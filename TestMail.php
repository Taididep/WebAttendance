<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';

// ***Bỏ qua kết nối CSDL và truy vấn, sử dụng dữ liệu cố định cho việc test***
$email = 'huuthong6363@gmail.com'; // Email của người nhận để test
$firstname = 'Nguyễn Hữu Thông'; // Tên của người nhận để test
$date = '2024-10-26'; // Ngày cố định để test
$start_time = '08:00'; // Thời gian bắt đầu cố định để test
$end_time = '10:00'; // Thời gian kết thúc cố định để test

// Cấu hình và gửi email
$mail = new PHPMailer(true);

try {
    // Cấu hình server SMTP
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'thongnguyen@ittc.edu.vn';
    $mail->Password = 'xeiq offy nftu upep';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

    // Người gửi
    $mail->setFrom('thongnguyen@ittc.edu.vn', 'Remind');

    // Người nhận
    $mail->addAddress($email, $firstname);

    // Nội dung email
    $mail->isHTML(true);
    $mail->Subject = "Lịch học ngày mai";
    $mail->Body = "<h3>Chào $firstname,</h3><p>Bạn có lịch học vào ngày mai ($date) từ $start_time đến $end_time.</p>";

    // Gửi email
    $mail->send();
    echo "Đã gửi email cho $email<br>";
} catch (Exception $e) {
    echo "Không thể gửi email: {$mail->ErrorInfo}";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body>
   <h1>Email Test</h1>
</body>
</html>