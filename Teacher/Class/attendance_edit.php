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

// Lấy thông tin sinh viên trong lớp
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor(); // Đóng con trỏ

if (empty($students)) {
    echo '<div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>';
}

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
    $sqlAttendance = "CALL GetAttendanceByScheduleId(?, ?)";
    $stmtAttendance = $conn->prepare($sqlAttendance);
    $stmtAttendance->execute([$schedule_id, $class_id]); // Cung cấp cả hai tham số
    $attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);

    foreach ($attendanceData as $record) {
        $attendanceMap[$record['student_id']][$date] = $record['status'];
    }
    $stmtAttendance->closeCursor(); // Đóng con trỏ sau mỗi lần thực thi
}

?>

<div id="attendanceTable">
    <form method="POST" action="process_attendance.php">
        <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
        <div class="table-responsive">
            <table class="table table-striped" style="table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 80px;">STT</th>
                        <th style="width: 150px;">Mã sinh viên</th>
                        <th style="width: 200px;">Họ đệm</th>
                        <th style="width: 150px;">Tên</th>
                        <th style="width: 150px;">Lớp</th>
                        <th style="width: 150px;">Ngày sinh</th>
                        <?php foreach ($schedules as $index => $schedule): ?>
                            <th style="width: 100px; text-align: center; cursor: pointer;" class="attendance-column" data-index="<?php echo $index; ?>">
                                <span><?php echo 'Buổi ' . ($schedule['schedule_id']); ?></span><br>
                                <small><?php echo date('d/m', strtotime($schedule['date'])); ?></small>
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
                                <td class="attendance-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                    <input type="number" min="0" max="2" step="1"
                                        name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>][<?php echo htmlspecialchars($schedule['schedule_id']); ?>]"
                                        value="<?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) ? htmlspecialchars($attendanceMap[$student['student_id']][$schedule['date']]) : 0; ?>"
                                        class="form-control" style="width: 60px; display: inline-block;">
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between">
            <button id="showAllBtn" class="btn btn-success mt-3">Hiện tất cả</button>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </div>
        </div>
    </form>
</div>

<script>
    // Đăng ký sự kiện cho các tiêu đề cột buổi
    document.querySelectorAll('.attendance-column').forEach(column => {
        column.addEventListener('click', function() {
            const index = this.dataset.index;
            const cells = document.querySelectorAll(`td:nth-child(${parseInt(index) + 7})`); // 7 là số cột trước cột buổi

            cells.forEach(cell => {
                cell.style.display = cell.style.display === 'none' ? '' : 'none';
            });

            // Ẩn tiêu đề cột
            this.style.display = this.style.display === 'none' ? '' : 'none';
        });
    });

    // Nút hiện tất cả
    document.getElementById('showAllBtn').addEventListener('click', function() {
        document.querySelectorAll('.attendance-data').forEach(cell => {
            cell.style.display = '';
        });
        document.querySelectorAll('.attendance-column').forEach(column => {
            column.style.display = '';
        });
    });
</script>
