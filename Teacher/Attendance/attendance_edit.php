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
    <?php if (empty($students)): ?>
        <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
    <?php else: ?>
        <form method="POST" action="../Attendance/process_attendance.php">

            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
            <div class="table-responsive">
                <table class="table table-striped" id="attendanceEdit" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width: 80px;">STT</th>
                            <th style="width: 150px;">Mã sinh viên</th>
                            <th style="width: 200px;">Họ đệm</th>
                            <th style="width: 150px;">Tên</th>
                            <th style="width: 150px;">Giới tính</th>
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
                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                                <?php foreach ($schedules as $schedule): ?>
                                    <td class="edit-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
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

            <!-- Thanh nhập, nút xác nhận và nút hiện tất cả nằm ngang -->
            <div class="d-flex align-items-center justify-content-between mt-3">
                <div class="input-group me-2" style="width: 30%">
                    <input type="number" id="attendanceInputEdit" min="1" max="<?php echo count($schedules); ?>" class="form-control" placeholder="Nhập buổi (1, 2, ...)">
                    <button type="button" id="confirmAttendanceBtnEdit" class="btn btn-primary">Xác nhận</button>
                    <button id="showAllBtnEdit" class="btn btn-dark">Hiện tất cả</button>
                </div>

                <div class="text-end ms-3">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>

        </form>
    <?php endif; ?>
</div>

<script>
    // Xác nhận buổi học
    document.getElementById('confirmAttendanceBtnEdit').addEventListener('click', function() {
        const input = document.getElementById('attendanceInputEdit');
        const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
        const totalSchedules = <?php echo count($schedules); ?>; // Tổng số buổi

        if (index < 1 || index > totalSchedules) {
            alert('Vui lòng nhập buổi hợp lệ (từ 1 đến ' + totalSchedules + ').');
            return;
        }

        // Ẩn tất cả các cột và tiêu đề
        document.querySelectorAll('#attendanceEdit .edit-data, #attendanceEdit .edit-column').forEach(cell => {
            cell.style.display = 'none';
        });

        // Hiện cột buổi đã nhập
        const cells = document.querySelectorAll(`#attendanceEdit td:nth-child(${index + 7})`); // Cột thứ index (cột 8 là buổi đầu tiên)

        cells.forEach(cell => {
            cell.style.display = ''; // Hiện cột tương ứng
        });

        // Cập nhật tiêu đề cột
        const headerCells = document.querySelectorAll(`#attendanceEdit th.edit-column`);
        headerCells.forEach((headerCell, idx) => {
            if (idx === index - 1) {
                headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
            } else {
                headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
            }
        });
    });

    // Nút hiện tất cả cho bảng chỉnh sửa
    document.getElementById('showAllBtnEdit').addEventListener('click', function(event) {
        event.preventDefault(); // Ngăn chặn hành vi mặc định
        document.querySelectorAll('#attendanceEdit .edit-data').forEach(cell => {
            cell.style.display = '';
        });
        document.querySelectorAll('#attendanceEdit .edit-column').forEach(column => {
            column.style.display = '';
        });
    });
</script>