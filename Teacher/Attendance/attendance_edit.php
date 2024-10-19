<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

// Lấy thông tin sinh viên trong lớp
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor(); // Đóng con trỏ

// Lấy thông tin lịch học
$sqlSchedules = "CALL GetSchedulesByClassId(?)";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor(); // Đóng con trỏ

// Lấy thông tin điểm danh
$attendanceMap = [];
foreach ($schedules as $schedule) {
    $schedule_id = $schedule['schedule_id'];
    $date = $schedule['date'];

    // Lấy trạng thái điểm danh cho lịch học
    $sqlAttendance = "CALL GetAttendanceByScheduleId(?)";
    // $sqlAttendance = "SELECT student_id, status FROM attendances WHERE schedule_id = ?";
    $stmtAttendance = $conn->prepare($sqlAttendance);
    $stmtAttendance->execute([$schedule_id]);
    $attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);

    foreach ($attendanceData as $record) {
        $attendanceMap[$record['student_id']][$date] = $record['status'];
    }
    $stmtAttendance->closeCursor(); // Đóng con trỏ sau mỗi lần thực thi
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa điểm danh</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<style>
    .table td {
        height: 60px; /* Thay đổi chiều rộng theo nhu cầu */
    }
</style>
<body>
<div class="container-fluid mt-5">
    <h2>Chỉnh sửa điểm danh lớp: <?php echo htmlspecialchars($students[0]['class']); ?></h2>
    <form method="POST" action="process_attendance.php">
        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ</th>
                    <th>Tên</th>
                    <th>Lớp</th>
                    <th>Ngày sinh</th>
                    <?php foreach ($schedules as $schedule): ?>
                        <th><?php echo date('d/m', strtotime($schedule['date'])); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                        <?php foreach ($schedules as $schedule): ?>
                            <td style="width: 80px; ">
                                <input type="number" min="0" max="1" step="1"
                                    name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>][<?php echo htmlspecialchars($schedule['schedule_id']); ?>]"
                                    value="<?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) ? htmlspecialchars($attendanceMap[$student['student_id']][$schedule['date']]) : 0; ?>"
                                    class="form-control" style="width: 50px;">
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
