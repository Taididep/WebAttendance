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

// Lấy thông tin lớp học để lấy class_name
$sqlClassName = "SELECT class_name FROM classes WHERE class_id = ?";
$stmtClassName = $conn->prepare($sqlClassName);
$stmtClassName->execute([$class_id]);
$class = $stmtClassName->fetch(PDO::FETCH_ASSOC);
$stmtClassName->closeCursor(); // Đóng con trỏ


// Lấy thông tin sinh viên trong lớp theo class_id
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor(); // Đóng con trỏ


// Lấy thông tin điểm danh
$sqlAttendance = "CALL GetAttendanceByClassId(?)";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->execute([$class_id]);
$attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
$stmtAttendance->closeCursor(); // Đóng con trỏ


// // In ra các lịch học để kiểm tra
// print_r($attendanceData);
// // Kiểm tra giá trị của class_id
// echo "Class ID: " . htmlspecialchars($class_id);


// Chuyển đổi dữ liệu điểm danh thành mảng để dễ truy xuất
$attendanceMap = [];
foreach ($attendanceData as $record) {
    $attendanceMap[$record['student_id']][$record['date']] = $record['status'];
}

// Lấy danh sách ngày điểm danh từ bảng schedules
$sqlDates = "CALL GetDistinctDatesByClassId(?)";
$stmtDates = $conn->prepare($sqlDates);
$stmtDates->execute([$class_id]);
$dates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
$stmtDates->closeCursor(); // Đóng con trỏ

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điểm danh lớp học</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>
<style>
    .table td {
        height: 60px;
        vertical-align: middle;
        /* text-align: center; */
    }
</style>
<body>
<div class="container-fluid mt-5">
    <h2 class="text-center">Danh sách điểm danh</h2>
    <hr>
    <!-- Kiểm tra xem có sinh viên nào trong lớp hay không -->
    <?php if (empty($students)): ?>
        <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ</th>
                    <th>Tên</th>
                    <th>Lớp</th>
                    <th>Ngày sinh</th>
                    <?php foreach ($dates as $index => $date): ?>
                        <th data-bs-toggle="tooltip" title="<?php echo date('d/m/Y', strtotime($date)); ?>">
                            <?php echo 'Buổi ' . ($index + 1); ?>
                        </th>
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
                        <?php foreach ($dates as $date): ?>
                            <td style="width: 80px; padding-left: 21px; padding-bottom: 10px;">
                                <?php
                                // Hiển thị trạng thái điểm danh (1: Có mặt, 0: Vắng)
                                if (isset($attendanceMap[$student['student_id']][$date])) {
                                    echo $attendanceMap[$student['student_id']][$date] === '1' ? '1' : '0';
                                } else {
                                    echo '0'; // Nếu chưa có điểm danh thì coi là vắng
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Nút điểm danh -->
    <div class="text-end mt-3">
        <a href="attendance_edit.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-primary">
            Điểm danh
        </a>
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Khởi tạo tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
</body>
</html>
