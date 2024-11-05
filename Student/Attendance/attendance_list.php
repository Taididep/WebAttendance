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

// Tạo mảng ánh xạ schedule_id với date
$scheduleMap = [];
foreach ($schedules as $schedule) {
    $scheduleMap[$schedule['date']][] = $schedule['schedule_id'];
}

?>

<div id="attendanceTable">
    <div class="table-responsive">
        <table class="table table-striped" id="attendanceList" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 80px;">STT</th>
                    <th style="width: 150px;">Mã sinh viên</th>
                    <th style="width: 200px;">Họ đệm</th>
                    <th style="width: 150px;">Tên</th>
                    <th style="width: 150px;">Lớp</th>
                    <th style="width: 150px;">Ngày sinh</th>
                    <?php foreach ($schedules as $index => $schedule): ?>
                        <th style="width: 100px; text-align: center;" class="list-column" data-index="<?php echo $index; ?>">
                            <a href="../Attendance/attendance_qr.php?class_id=<?php echo urlencode($class_id); ?>&schedule_id=<?php echo urlencode($schedule['schedule_id']); ?>" style="text-decoration: none; color: inherit;">
                                <span><?php echo 'Buổi ' . ($index + 1); ?></span><br>
                                <small><?php echo date('d/m', strtotime($schedule['date'])); ?></small>
                            </a>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $index => $student): ?>
                    <tr>
                        <td style="padding-left: 17px;"><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                        <?php foreach ($schedules as $schedule): ?>
                            <td class="list-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                <?php
                                // Kiểm tra xem có trạng thái điểm danh không
                                if (isset($attendanceMap[$student['student_id']][$schedule['date']])) {
                                    $status = $attendanceMap[$student['student_id']][$schedule['date']];
                                    if ($status === '1') {
                                        echo '1'; // Có mặt
                                    } elseif ($status === '2') {
                                        echo '2'; // Muộn
                                    } else {
                                        echo '0'; // Vắng mặt
                                    }
                                } else {
                                    echo '0';
                                }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>