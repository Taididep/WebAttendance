<?php
session_start();
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

// Kiểm tra xem schedule_id có được gửi qua URL hay không
if (!isset($_GET['schedule_id'])) {
    echo 'Không tìm thấy thông tin buổi học.';
    exit;
}

// Lấy schedule_id từ URL
$schedule_id = $_GET['schedule_id'];

// Truy vấn để lấy thông tin lớp học từ bảng classes
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);

// Lấy kết quả truy vấn
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Kiểm tra xem có kết quả hay không
if (!$classData) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy thông tin ngày buổi học trực tiếp
$sqlSchedule = "SELECT date FROM schedules WHERE schedule_id = ?"; // Thay đổi cột id thành schedule_id
$stmtSchedule = $conn->prepare($sqlSchedule);
$stmtSchedule->execute([$schedule_id]);
$scheduleData = $stmtSchedule->fetch(PDO::FETCH_ASSOC);
$stmtSchedule->closeCursor();

if (!$scheduleData) {
    echo 'Không tìm thấy thông tin buổi học.';
    exit;
}

// Tạo URL cho attendance_view.php với class_id và schedule_id
$detailUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/Student/Attendance/attendance_view.php?class_id=' . urlencode($class_id) . '&schedule_id=' . urlencode($schedule_id);

// Kiểm tra ngày hiện tại so với ngày buổi học
$currentDate = new DateTime();
$scheduleDate = new DateTime($scheduleData['date']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã QR lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #80deea);
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            border-radius: 15px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #00796b;
            margin-bottom: 20px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        #qrCodeContainer {
            text-align: center;
            margin-top: 20px;
        }
        #qrCode {
            width: 300px;
            height: 300px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            padding: 10px;
        }
        .btn-danger {
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2>Mã QR cho lớp học: <?php echo htmlspecialchars($classData['class_name']); ?></h2>
    <?php if ($currentDate >= $scheduleDate): ?>
        <div id="qrCodeContainer">
            <div id="qrCode"></div>
        </div>
        <a href="../Class/class_detail.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-danger">Đóng</a>
    <?php else: ?>
        <p class="alert alert-warning">Chưa đến ngày điểm danh. Vui lòng quay lại sau.</p>
        <a href="../Class/class_detail_list.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-danger">Quay lại trang lớp học</a>
    <?php endif; ?>
</div>

<div class="footer text-center">
    <p>© 2024 Hệ thống điểm danh lớp học</p>
</div>

<script>
    <?php if ($currentDate >= $scheduleDate): ?>
        // Tạo mã QR với URL chuyển hướng
        const detailUrl = '<?php echo $detailUrl; ?>';
        const qrCodeContainer = document.getElementById('qrCode');
        new QRCode(qrCodeContainer, {
            text: detailUrl,
            width: 300,
            height: 300,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    <?php endif; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>