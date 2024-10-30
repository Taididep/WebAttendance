<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';

// Kết nối tới CSDL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_atd";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy lịch học cho ngày mai
$tomorrow = date("Y-m-d", strtotime('+1 day'));

$sql = "SELECT students.email, students.firstname, schedules.date, schedules.start_time, schedules.end_time 
        FROM students 
        JOIN attendances ON students.student_id = attendances.student_id 
        JOIN schedules ON schedules.schedule_id = attendances.schedule_id 
        WHERE schedules.date = '$tomorrow'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Duyệt qua từng sinh viên và gửi email nhắc nhở
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];
        $firstname = $row['firstname'];
        $date = $row['date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        
        // Cấu hình và gửi email
        $mail = new PHPMailer(true);

        try {
            // Cấu hình server SMTP
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

            // // ***Người nhận - sử dụng giá trị cứng để test***
            // $email = 'huuthong6363@gmail.com'; // Email của người nhận để test
            // $firstname = 'Nguyễn Hữu Thông'; // Tên của người nhận để test
            // $mail->addAddress($email, $firstname);

            // // ***Nội dung email - sử dụng giá trị cứng để test***
            // $date = '2024-10-24'; // Ngày cố định để test
            // $start_time = '08:00'; // Thời gian bắt đầu cố định để test
            // $end_time = '10:00'; // Thời gian kết thúc cố định để test

            // // Nội dung email
            // $mail->isHTML(true);
            // $mail->Subject = "Lịch học ngày mai";
            // $mail->Body = "<h3>Chào $firstname,</h3><p>Bạn có lịch học vào ngày mai ($date) từ $start_time đến $end_time.</p>";

            // Gửi email
            $mail->send();
            echo "Đã gửi email cho $email<br>";
        } catch (Exception $e) {
            echo "Không thể gửi email: {$mail->ErrorInfo}";
        }
    }
} else {
    echo "Không có lịch học vào ngày mai.";
}

$conn->close();
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
   <h1>Email</h1>
</body>
</html>