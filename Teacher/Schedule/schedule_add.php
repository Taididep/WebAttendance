<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Nhận giá trị từ URL
$classId = isset($_GET['class_id']) ? $_GET['class_id'] : null;
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$startPeriod = isset($_GET['start_period']) ? $_GET['start_period'] : null;
$endPeriod = isset($_GET['end_period']) ? $_GET['end_period'] : null;

// Khai báo biến cho các tiết
$theoryPeriods = 0;
$practicePeriods = 0;
$totalDays = 0;

if ($classId) {
    $sql = "CALL GetCoursePeriodsByClassId(?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$classId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($course) {
        $theoryPeriods = $course['theory_periods'];
        $practicePeriods = $course['practice_periods'];
        $theoryDays = ceil($theoryPeriods / 3);
        $practiceDays = ceil($practicePeriods / 4);
        $totalDays = $theoryDays + $practiceDays;
    } else {
        echo '<div class="alert alert-danger">Không tìm thấy thông tin cho lớp học này.</div>';
        exit;
    }
}

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

        <!-- Form thêm lịch học -->
        <form method="POST" action="add_schedule.php">
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
    <script src="../JavaScript/schedule_add.js"></script>

</body>

</html>