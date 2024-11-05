<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Nhận giá trị từ URL
$classId = isset($_GET['class_id']) ? $_GET['class_id'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$startPeriod = isset($_GET['start_period']) ? $_GET['start_period'] : null; // Nhận tiết bắt đầu
$endPeriod = isset($_GET['end_period']) ? $_GET['end_period'] : null; // Nhận tiết kết thúc

// Khai báo biến cho các tiết
$theoryPeriods = 0;
$practicePeriods = 0;
$totalDays = 0;

if ($classId) {
    // Lấy thông tin tiết lý thuyết và thực hành từ bảng course_types
    $sql = "CALL GetCoursePeriodsByClassId(?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$classId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Đóng con trỏ

    if ($course) {
        $theoryPeriods = $course['theory_periods'];
        $practicePeriods = $course['practice_periods'];

        // Tính số ngày cần tạo
        $theoryDays = ceil($theoryPeriods / 3); // Mỗi ngày 3 tiết
        $practiceDays = ceil($practicePeriods / 4); // Mỗi ngày 4 tiết
        $totalDays = $theoryDays + $practiceDays;
    } else {
        // Xử lý trường hợp không tìm thấy thông tin lớp
        echo '<div class="alert alert-danger">Không tìm thấy thông tin cho lớp học này.</div>';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin lịch học
    $dates = isset($_POST['dates']) ? $_POST['dates'] : [];
    $startTimes = isset($_POST['start_time']) ? $_POST['start_time'] : [];
    $endTimes = isset($_POST['end_time']) ? $_POST['end_time'] : [];

    // Kiểm tra xem có dữ liệu nào không
    if (empty($dates) || empty($startTimes) || empty($endTimes)) {
        $errorMessage = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Chuyển đổi mảng thành JSON để truyền vào thủ tục
        $datesJson = json_encode($dates);
        $startTimesJson = json_encode($startTimes);
        $endTimesJson = json_encode($endTimes);

        // Gọi thủ tục thêm lịch học
        $sql_schedule = "CALL AddSchedules(?, ?, ?, ?)";
        $stmt_schedule = $conn->prepare($sql_schedule);
        $stmt_schedule->execute([$classId, $datesJson, $startTimesJson, $endTimesJson]);

        if ($stmt_schedule->rowCount() > 0) {
            // Chuyển hướng về trang quản lý lớp học
            header("Location: {$basePath}Class/class_manage.php");
            exit();
        } else {
            $errorMessage = "Đã xảy ra lỗi khi thêm lịch học.";
        }
    }
}

// Chuyển đổi ngày bắt đầu thành timestamp
$startDateTimestamp = strtotime($startDate);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm lịch học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
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
                <div class="row mb-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Ngày học</label>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tiết bắt đầu</label>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tiết kết thúc</label>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label"></label>
                    </div>
                </div>

                <?php for ($i = 0; $i < $totalDays; $i++): ?>
                    <div class="row mb-3 align-items-end schedule-row">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><?php echo $i + 1; ?></span>
                                <input type="date" class="form-control" name="dates[]" required
                                    value="<?php echo date('Y-m-d', strtotime("+$i week", $startDateTimestamp)); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="start_time[]" required min="1" max="17"
                                placeholder="Tiết bắt đầu" value="<?php echo htmlspecialchars($startPeriod); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="end_time[]" required min="1" max="17"
                                placeholder="Tiết kết thúc" value="<?php echo htmlspecialchars($endPeriod); ?>">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-lg remove-date"><i class="bi bi-trash fs-5"></i></button>
                        </div>
                    </div>
                <?php endfor; ?>
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
            const scheduleRows = document.querySelectorAll('.schedule-row');
            const newIndex = scheduleRows.length + 1;

            // Lấy ngày cuối cùng hiện tại
            const lastDateInput = scheduleRows[scheduleRows.length - 1].querySelector('input[name="dates[]"]');
            const lastDate = lastDateInput ? new Date(lastDateInput.value) : new Date();

            // Tính toán ngày mới (tăng thêm 7 ngày)
            lastDate.setDate(lastDate.getDate() + 7);

            const newField = document.createElement('div');
            newField.classList.add('row', 'mb-3', 'align-items-end', 'schedule-row');
            newField.innerHTML = `
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text">${newIndex}</span>
                    <input type="date" class="form-control" name="dates[]" required value="${lastDate.toISOString().split('T')[0]}">
                </div>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="start_time[]" required min="1" max="17" placeholder="Tiết bắt đầu" value="<?php echo htmlspecialchars($startPeriod); ?>">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" name="end_time[]" required min="1" max="17" placeholder="Tiết kết thúc" value="<?php echo htmlspecialchars($endPeriod); ?>">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-lg remove-date"><i class="bi bi-trash fs-5"></i></button>
            </div>
        `;

            scheduleFields.appendChild(newField);
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-date')) {
                event.target.closest('.schedule-row').remove();
            }
        });
    </script>

</body>

</html>