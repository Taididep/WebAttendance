<?php
session_start();
$basePath = '../';
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra class_id và schedule_id từ URL
if (!isset($_GET['class_id']) || !isset($_GET['schedule_id'])) {
    echo 'Không tìm thấy thông tin lớp học hoặc buổi học.';
    exit;
}

$class_id = $_GET['class_id'];
$schedule_id = $_GET['schedule_id'];

// Cập nhật status thành 1 cho buổi học
$updateSql = "UPDATE schedules SET status = 1 WHERE schedule_id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->execute([$schedule_id]);

// Lấy thông tin lớp học
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

if (!$classData) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy thông tin ngày của buổi học
$sqlSchedule = "SELECT date FROM schedules WHERE schedule_id = ?";
$stmtSchedule = $conn->prepare($sqlSchedule);
$stmtSchedule->execute([$schedule_id]);
$scheduleData = $stmtSchedule->fetch(PDO::FETCH_ASSOC);
$stmtSchedule->closeCursor();

if (!$scheduleData) {
    echo 'Không tìm thấy thông tin buổi học.';
    exit;
}

$detailUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/Student/Attendance/attendance_view.php?class_id=' . urlencode($class_id) . '&schedule_id=' . urlencode($schedule_id);

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
    <link rel="stylesheet" href="../Css/attendance_qr.css">
</head>

<body>

    <div class="container text-center">
        <h2>Mã QR cho lớp học: <?php echo htmlspecialchars($classData['class_name']); ?></h2>

        <!-- Hàng chứa input và nút xác nhận thời gian -->
        <div class="form-group my-3 d-flex justify-content-center align-items-center gap-3">
            <label for="timeInput" class="form-label m-0">Chọn thời gian điểm danh:</label>
            <input type="time" id="timeInput" class="form-control w-auto" step="1">
            <button id="confirmTimeButton" class="btn btn-primary">Xác nhận thời gian</button>
        </div>

        <?php if ($currentDate >= $scheduleDate): ?>
            <div id="qrCodeContainer">
                <div id="qrCode"></div>
            </div>
            <a href="../Attendance/reset_status.php?class_id=<?php echo urlencode($class_id); ?>&schedule_id=<?php echo urlencode($schedule_id); ?>"
                class="btn btn-danger">Đóng</a>
        <?php else: ?>
            <p class="alert alert-warning">Chưa đến ngày điểm danh. Vui lòng quay lại sau.</p>
            <a href="../Class/class_detail_list.php?class_id=<?php echo urlencode($class_id); ?>"
                class="btn btn-danger">Quay lại trang lớp học</a>
        <?php endif; ?>
    </div>


    <div class="footer text-center">
        <p>© 2024 Hệ thống điểm danh lớp học</p>
    </div>

    <script>
        <?php if ($currentDate >= $scheduleDate): ?>
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

        document.getElementById('confirmTimeButton').addEventListener('click', function () {
            const time = document.getElementById('timeInput').value;

            if (!time) {
                alert('Vui lòng chọn thời gian!');
                return;
            }

            const scheduleId = '<?php echo urlencode($schedule_id); ?>';

            $.ajax({
                url: '../Attendance/save_time.php',
                method: 'POST',
                data: {
                    schedule_id: scheduleId,
                    time: time
                },
                success: function (response) {
                    alert(response);
                    location.reload(); // Tải lại trang để hiển thị cập nhật
                },
                error: function () {
                    alert('Có lỗi xảy ra khi lưu thời gian.');
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        window.addEventListener('beforeunload', function () {
            // Gửi yêu cầu không đồng bộ để cập nhật status thành 0 khi đóng trang
            navigator.sendBeacon(`../Attendance/reset_status.php?class_id=${classId}&schedule_id=${scheduleId}`);
        });
    </script>
</body>

</html>