<?php
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

// Lấy user_id từ session
$user_id = $_SESSION['user_id']; // Giả sử bạn đã lưu user_id trong session

// Lấy thông tin sinh viên trong lớp theo class_id và user_id
$sqlStudents = "CALL GetStudentsByClassIdAndStudentId(?, ?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id, $user_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor(); // Đóng con trỏ

// Kiểm tra xem có sinh viên nào không
if (empty($students)) {
    echo '<div class="alert alert-warning text-center">Không tìm thấy thông tin sinh viên.</div>';
    exit;
}

// Lấy thông tin điểm danh
$sqlAttendance = "CALL GetSchedulesAndAttendanceByClassId(?)";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->execute([$class_id]);
$attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
$stmtAttendance->closeCursor(); // Đóng con trỏ

// Chuyển đổi dữ liệu điểm danh thành mảng để dễ truy xuất
$attendanceMap = [];
foreach ($attendanceData as $record) {
    $attendanceMap[$record['student_id']][$record['date']] = $record['status'];
}

// Lấy danh sách ngày điểm danh từ bảng schedules
$sqlSchedules = "CALL GetDistinctDatesByClassId(?)";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor(); // Đóng con trỏ

// Khởi tạo chuỗi điểm danh cho mỗi sinh viên
$attendanceStreak = [];
foreach ($students as $student) {
    $attendanceStreak[$student['student_id']] = 0; // Bắt đầu từ 0
}

// Kiểm tra trạng thái điểm danh và cập nhật chuỗi điểm danh
foreach ($schedules as $schedule) {
    foreach ($students as $student) {
        if (isset($attendanceMap[$student['student_id']][$schedule['date']])) {
            $status = $attendanceMap[$student['student_id']][$schedule['date']];
            if ($status === '1') {
                // Có mặt
                $attendanceStreak[$student['student_id']]++;
            } elseif ($status === '2') {
                // Muộn, tính nửa buổi
                $attendanceStreak[$student['student_id']] += 0.5;
            } else {
                // Vắng, reset chuỗi điểm danh
                $attendanceStreak[$student['student_id']] = 0;
            }
        } elseif (strtotime($schedule['date']) < time()) {
            // Nếu ngày đã qua mà trạng thái còn trống, gán là "Vắng"
            $attendanceMap[$student['student_id']][$schedule['date']] = '0'; // 0 = Vắng
        }
    }
}
?>

<!-- Bảng 1: Thông tin sinh viên -->
<div id="studentInfoTable">
    <div class="table-responsive">
        <table class="table table-striped" id="studentList" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 80px;">STT</th>
                    <th style="width: 150px;">Mã sinh viên</th>
                    <th style="width: 200px;">Họ đệm</th>
                    <th style="width: 150px;">Tên</th>
                    <th style="width: 150px;">Giới tính</th>
                    <th style="width: 150px;">Lớp</th>
                    <th style="width: 150px;">Ngày sinh</th>
                    <th style="width: 150px;">Chuỗi điểm danh</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td style="padding-left: 10px;"><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                        <td><?php echo $attendanceStreak[$student['student_id']]; ?></td> <!-- Hiển thị chuỗi điểm danh -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bảng 2: Điểm danh theo các buổi (chỉ hiển thị buổi và trạng thái điểm danh) -->
<div id="attendanceTable">
    <div class="table-responsive">
        <table class="table table-striped" id="attendanceList" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 100px;">Buổi</th>
                    <th style="width: 100px;">Ngày</th>
                    <th style="width: 80px;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $index => $schedule): ?>
                    <?php if (strtotime($schedule['date']) < time()): // Chỉ hiển thị các buổi đã qua ?>
                        <tr>
                            <td style="text-align: center;"><?php echo 'Buổi ' . ($index + 1); ?></td>
                            <td style="text-align: center;"><?php echo date('d/m/Y', strtotime($schedule['date'])); ?></td>
                            <td>
                                <?php
                                // Kiểm tra trạng thái điểm danh của sinh viên
                                foreach ($students as $student) {
                                    if (isset($attendanceMap[$student['student_id']][$schedule['date']])) {
                                        $status = $attendanceMap[$student['student_id']][$schedule['date']];
                                        if ($status === '1') {
                                            echo 'Có mặt'; // Có mặt
                                        } elseif ($status === '2') {
                                            echo 'Muộn'; // Muộn
                                        } else {
                                            echo 'Vắng'; // Vắng mặt
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>