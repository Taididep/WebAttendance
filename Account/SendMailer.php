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

// Lấy lịch học cho ngày mai
$tomorrow = date("Y-m-d", strtotime('+1 day'));

// Truy vấn lấy thông tin lịch học cho từng sinh viên vào ngày mai
$sql = "
    SELECT 
        st.email, 
        st.firstname, 
        st.lastname, 
        sc.date, 
        sc.start_time, 
        sc.end_time, 
        c.course_name
    FROM 
        students AS st
    JOIN 
        class_students AS cs ON st.student_id = cs.student_id
    JOIN 
        schedules AS sc ON cs.class_id = sc.class_id
    JOIN 
        classes AS cl ON sc.class_id = cl.class_id
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
        $start_time = $row['start_time'];
        $end_time = $row['end_time'];
        $date = $row['date'];

        // Thêm lịch học vào danh sách của sinh viên
        if (!isset($emails[$email])) {
            $emails[$email] = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'schedules' => []
            ];
        }

        $emails[$email]['schedules'][] = [
            'course_name' => $course_name,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }

    // Duyệt qua từng sinh viên và gửi email nhắc nhở với tất cả lịch học của họ
    foreach ($emails as $email => $info) {
        $firstname = $info['firstname'];
        $lastname = $info['lastname'];
        $scheduleList = '';

        foreach ($info['schedules'] as $schedule) {
            $scheduleList .= "<li>Môn: {$schedule['course_name']} (Từ {$schedule['start_time']} đến {$schedule['end_time']})</li>";
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
            $mail->addAddress($email, "$firstname $lastname");

            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = "Lịch học ngày mai";
            $mail->Body = "
                <h3>Chào $firstname $lastname,</h3>
                <p>Bạn có lịch học vào ngày mai ($date):</p>
                <ul>$scheduleList</ul>
                <p>Hãy đến đúng giờ và chuẩn bị đầy đủ nhé!</p>
            ";

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
