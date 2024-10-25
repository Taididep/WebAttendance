<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy class_id từ URL
$classId = isset($_GET['class_id']) ? $_GET['class_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin lịch học
    $dates = isset($_POST['dates']) ? $_POST['dates'] : [];
    $startTimes = isset($_POST['start_time']) ? $_POST['start_time'] : [];
    $endTimes = isset($_POST['end_time']) ? $_POST['end_time'] : [];

    // Kiểm tra xem có dữ liệu nào không
    if (empty($dates) || empty($startTimes) || empty($endTimes)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Thêm lịch học vào cơ sở dữ liệu
        $sql_schedule = "INSERT INTO schedules (class_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt_schedule = $conn->prepare($sql_schedule);

        foreach ($dates as $index => $date) {
            $startTime = isset($startTimes[$index]) ? $startTimes[$index] : null;
            $endTime = isset($endTimes[$index]) ? $endTimes[$index] : null;

            if ($startTime !== null && $endTime !== null) {
                $stmt_schedule->execute([$classId, $date, $startTime, $endTime]);
            } else {
                $errorMessage = "Thông tin thời gian không hợp lệ.";
                break;
            }
        }

        if (!isset($errorMessage)) {
            // Chuyển hướng về trang quản lý lớp học
            header("Location: {$basePath}Class/class_manage.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm lịch học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Thêm lịch học cho lớp ID: <?php echo htmlspecialchars($classId); ?></h2>
    <hr>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php elseif (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($classId); ?>">
        <div id="scheduleFields">
            <div class="mb-3">
                <label class="form-label">Ngày</label>
                <input type="date" class="form-control" name="dates[]" required>
                <label class="form-label">Thời gian bắt đầu</label>
                <input type="number" class="form-control" name="start_time[]" required>
                <label class="form-label">Thời gian kết thúc</label>
                <input type="number" class="form-control" name="end_time[]" required>
            </div>
        </div>
        <button type="button" id="addDate" class="btn btn-secondary">Thêm ngày</button>
        <button type="submit" class="btn btn-primary">Lưu lịch học</button>
        <a href="<?php echo $basePath; ?>Class/class_manage.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('addDate').addEventListener('click', function() {
    const scheduleFields = document.getElementById('scheduleFields');
    const newField = document.createElement('div');
    newField.classList.add('mb-3');
    newField.innerHTML = `
        <label class="form-label">Ngày</label>
        <input type="date" class="form-control" name="dates[]" required>
        <label class="form-label">Thời gian bắt đầu</label>
        <input type="number" class="form-control" name="start_time[]" required>
        <label class="form-label">Thời gian kết thúc</label>
        <input type="number" class="form-control" name="end_time[]" required>
    `;
    scheduleFields.appendChild(newField);
});
</script>
</body>
</html>
