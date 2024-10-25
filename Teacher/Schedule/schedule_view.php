<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem tuần có được gửi qua URL hay không, nếu không thì sử dụng tuần hiện tại
if (!isset($_GET['week'])) {
    $startDate = new DateTime('now');
} else {
    $startDate = new DateTime($_GET['week']); // Ngày bắt đầu của tuần
}
$startDate->modify('monday this week'); // Đặt lại ngày bắt đầu về thứ Hai
$endDate = clone $startDate;
$endDate->modify('sunday this week'); // Đặt ngày kết thúc về Chủ Nhật

// Truy vấn để lấy lịch học, thông tin lớp và môn học trong khoảng thời gian từ thứ Hai đến Chủ Nhật
$sql = "
    SELECT 
        c.class_name,
        co.course_name,
        s.date,
        s.start_time,
        s.end_time,
        CASE 
            WHEN s.end_time < 7 THEN 'Sáng'
            WHEN s.end_time >= 7 AND s.end_time < 13 THEN 'Chiều'
            ELSE 'Tối'
        END AS ca_hoc 
    FROM 
        schedules s
    JOIN 
        classes c ON s.class_id = c.class_id
    JOIN 
        courses co ON c.course_id = co.course_id
    WHERE 
        s.date BETWEEN ? AND ?
    ORDER BY 
        s.date, c.class_name
";

$stmt = $conn->prepare($sql);
$stmt->execute([$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor(); // Đóng con trỏ

// Tạo mảng để tổ chức lịch học theo ngày
$weeklySchedules = [];
foreach ($schedules as $schedule) {
    $weeklySchedules[$schedule['date']][] = $schedule;
}

// Tạo một mảng chứa tên ngày trong tuần
$daysOfWeek = ['Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ Nhật'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch học theo tuần</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Lịch học từ <?php echo $startDate->format('d/m/Y'); ?> đến <?php echo $endDate->format('d/m/Y'); ?></h2>
    <hr>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ca học</th>
                <?php foreach ($daysOfWeek as $index => $day): ?>
                    <?php
                    // Lấy ngày cụ thể trong tuần
                    $currentDate = clone $startDate;
                    $currentDate->modify("+$index days");
                    $formattedDate = $currentDate->format('d/m/Y');
                    ?>
                    <th>
                        <?php echo $day; ?><br><?php echo $formattedDate; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Mảng để giữ các ca học
            $shifts = ['Sáng', 'Chiều', 'Tối'];
            foreach ($shifts as $shift): ?>
                <tr>
                    <td><?php echo $shift; ?></td>
                    <?php foreach ($daysOfWeek as $index => $day): ?>
                        <?php
                        // Lấy ngày cụ thể trong tuần
                        $currentDate = clone $startDate;
                        $currentDate->modify("+$index days");
                        $formattedDate = $currentDate->format('Y-m-d');
                        ?>
                        <td>
                            <?php if (isset($weeklySchedules[$formattedDate])): ?>
                                <div>
                                    <?php foreach ($weeklySchedules[$formattedDate] as $schedule): ?>
                                        <?php if ($schedule['ca_hoc'] === $shift): ?>
                                            <?php echo htmlspecialchars($schedule['class_name']); ?><br>
                                            <small><?php echo htmlspecialchars($schedule['course_name']); ?></small><br>
                                            <small>Tiết: <?php echo htmlspecialchars($schedule['start_time']) . ' - ' . htmlspecialchars($schedule['end_time']); ?></small><br>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </td>

                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
