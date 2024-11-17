<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\Exception.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\PHPMailer.php';
require 'D:\Nam4\DoAnTN\WebAttendance\Mailer\SMTP.php';

// Kết nối tới CSDL
include '../Connect/connect.php';
session_start();

// Lấy lịch học cho ngày mai
$tomorrow = date("Y-m-d", strtotime('+1 day'));

// SQL query để lấy danh sách lịch học của sinh viên vào ngày mai
$sql = "SELECT students.email, students.firstname, schedules.date, schedules.start_time, schedules.end_time 
        FROM students
        JOIN attendances ON students.student_id = attendances.student_id
        JOIN schedules ON schedules.schedule_id = attendances.schedule_id
        WHERE schedules.date = :tomorrow
        ORDER BY students.email, schedules.start_time";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':tomorrow', $tomorrow, PDO::PARAM_STR);
$stmt->execute();

// Tạo danh sách email
$emails = [];

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $email = $row['email'];
        $firstname = $row['firstname'];
        $date = $row['date'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        
        // Thêm lịch học vào danh sách của sinh viên
        if (!isset($emails[$email])) {
            $emails[$email] = [
                'firstname' => $firstname,
                'schedules' => []
            ];
        }
        
        $emails[$email]['schedules'][] = [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }

    // Duyệt qua từng sinh viên và gửi email nhắc nhở với tất cả lịch học của họ
    foreach ($emails as $email => $info) {
        $firstname = $info['firstname'];
        $scheduleList = '';

        foreach ($info['schedules'] as $schedule) {
            $scheduleList .= "<li>Từ {$schedule['start_time']} đến {$schedule['end_time']}</li>";
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
            $mail->setFrom('thongnguyen@ittc.edu.vn', 'Lịch học nhắc nhở');

            // Người nhận
            $mail->addAddress($email, $firstname);

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = "Lịch học ngày mai";
            $mail->Body = "<h3>Chào $firstname,</h3><p>Bạn có lịch học vào ngày mai ($date):</p><ul>$scheduleList</ul>";

            // Gửi email
            $mail->send();
            echo "Đã gửi email cho $email<br>";
        } catch (Exception $e) {
            echo "Không thể gửi email cho $email: {$mail->ErrorInfo}<br>";
        }
    }
} else {
    echo "Không có lịch học vào ngày mai.";
}

$conn = null;  // Đóng kết nối cơ sở dữ liệu
?>
