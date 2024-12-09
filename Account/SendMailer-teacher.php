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

// Truy vấn lấy thông tin lịch dạy của giảng viên vào ngày mai
$sql = "
    SELECT 
        t.email, 
        t.firstname, 
        t.lastname, 
        sc.date, 
        sc.start_time, 
        sc.end_time, 
        c.course_name, 
        cl.class_id
    FROM 
        teachers AS t
    JOIN 
        classes AS cl ON t.teacher_id = cl.teacher_id
    JOIN 
        schedules AS sc ON cl.class_id = sc.class_id
    JOIN 
        courses AS c ON cl.course_id = c.course_id
    WHERE 
        sc.date = :tomorrow
";
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
        $course_name = $row['course_name'];
        $class_id = $row['class_id'];
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $date = $row['date'];

        // Thêm lịch dạy vào danh sách của giảng viên
        if (!isset($emails[$email])) {
            $emails[$email] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'schedules' => []
            ];
        }

        $emails[$email]['schedules'][] = [
            'course_name' => $course_name,
            'class_id' => $class_id,
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
            $scheduleList .= "<li>Lớp: {$schedule['class_id']} - Môn: {$schedule['course_name']} (Từ {$schedule['start_time']} đến {$schedule['end_time']})</li>";
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
            
            
            // Thêm tệp đính kèm hình ảnh
            $mail->AddEmbeddedImage('D:/Nam4/DoAnTN/WebAttendance/Image/LogoEmail.png', 'logo_cid', 'LogoEmail.png');
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = "Lịch học ngày mai";
            $mail->Body = "
                <div style='border: 2px solid #4CAF50; padding: 15px; border-radius: 8px;'>
                    <div style='text-align: center;'>
                        <img src='cid:logo_cid' alt='Logo' style='width: 150px; margin-bottom: 20px;'>
                    </div>
                    <h3 style='color: #4CAF50;'>Chào $firstname $lastname,</h3>
                    <p>Bạn có lịch học vào ngày mai (<strong style='color: #4CAF50;'>$date</strong>):</p>
                    <ul style='padding-left: 20px;'>
                        $scheduleList
                    </ul>
                    <p style='margin-top: 20px;'>Hãy đến đúng giờ và chuẩn bị đầy đủ nhé!</p>
                    <div style='margin-top: 30px; text-align: center; color: #888; font-size: 12px;'>
                        <p style='margin: 0;'>Trường Đại học XYZ</p>
                        <p style='margin: 0;'>Địa chỉ: 140 Đ. Lê Trọng Tấn, Tây Thạnh, Tân Phú, Hồ Chí Minh</p>
                        <p style='margin: 0;'>Liên hệ: huit@huit.edu.vn</p>
                    </div>
                </div>
                ";

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
