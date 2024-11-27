<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';

// Kết nối tới CSDL
$servername = '127.0.0.1';
$username = 'root';
$password = '';
$dbname = 'db_atd'; // Tên cơ sở dữ liệu

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
session_start();

// Lấy lịch dạy cho ngày mai
$tomorrow = date("Y-m-d", strtotime('+1 day'));

// SQL query để lấy danh sách lịch dạy của giảng viên vào ngày mai
$sql = "CALL GetTeachingSchedulesByDate(:tomorrow)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':tomorrow', $tomorrow, PDO::PARAM_STR);
$stmt->execute();

// Tạo danh sách email
$emails = [];

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $email = $row['email'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $date = $row['date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $class_name = $row['class_name'];
        
        // Thêm lịch dạy vào danh sách của giảng viên
        if (!isset($emails[$email])) {
            $emails[$email] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'schedules' => []
            ];
        }
        
        $emails[$email]['schedules'][] = [
            'class_name' => $class_name,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }

    // Duyệt qua từng giảng viên và gửi email nhắc nhở với tất cả lịch dạy của họ
    foreach ($emails as $email => $info) {
        $firstname = $info['firstname'];
        $lastname = $info['lastname'];
        $scheduleList = '';

        foreach ($info['schedules'] as $schedule) {
            $scheduleList .= "<li>Lớp: {$schedule['class_name']}, từ {$schedule['start_time']} đến {$schedule['end_time']}</li>";
        }

        // Cấu hình và gửi email
        $mail = new PHPMailer(true);

        try {
            // Cấu hình server SMTP
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thongnguyen@ittc.edu.vn';  // Thay bằng email của bạn
            $mail->Password = 'xeiq offy nftu upep';      // Thay bằng mật khẩu email của bạn
            $mail->Port = 587;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Người gửi
            $mail->setFrom('thongnguyen@ittc.edu.vn', 'Lịch dạy nhắc nhở');

            // Người nhận
            $mail->addAddress($email, "$firstname $lastname");

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = "Lịch dạy ngày mai";
            $mail->Body = "<h3>Chào $firstname $lastname,</h3><p>Bạn có lịch dạy vào ngày mai ($date):</p><ul>$scheduleList</ul>";

            // Gửi email
            $mail->send();
            echo "Đã gửi email cho $email<br>";
        } catch (Exception $e) {
            echo "Không thể gửi email cho $email: {$mail->ErrorInfo}<br>";
        }
    }
} else {
    echo "Không có lịch dạy vào ngày mai.";
}

$conn = null;  // Đóng kết nối cơ sở dữ liệu
?>
