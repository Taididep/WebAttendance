<?php
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
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
$stmtStudents->closeCursor();

// Lấy thông tin điểm danh
$attendanceMap = [];
foreach ($schedules as $schedule) {
    $schedule_id = $schedule['schedule_id'];
    $date = $schedule['date'];

    // Lấy trạng thái điểm danh cho lịch học
    $sqlAttendance = "CALL GetAttendanceByScheduleId(?, ?)";
    $stmtAttendance = $conn->prepare($sqlAttendance);
    $stmtAttendance->execute([$schedule_id, $class_id]);
    $attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);

    foreach ($attendanceData as $record) {
        $attendanceMap[$record['student_id']][$date] = $record['status'];
    }
    $stmtAttendance->closeCursor();
}

// Lấy thống kê điểm danh cho tất cả sinh viên (cùng một lúc)
$sqlReport = "CALL GetAttendanceReports(?)";
$stmtReport = $conn->prepare($sqlReport);
$stmtReport->execute([$class_id]);
$attendanceReports = $stmtReport->fetchAll(PDO::FETCH_ASSOC);
$stmtReport->closeCursor(); // Đóng con trỏ

// Chuyển đổi kết quả thống kê thành mảng để dễ truy xuất
$attendanceStats = [];
foreach ($attendanceReports as $report) {
    $attendanceStats[$report['student_id']] = [
        'total_present' => $report['total_present'] ?? 0,
        'total_late' => $report['total_late'] ?? 0,
        'total_absent' => $report['total_absent'] ?? 0,
    ];
}

// Lấy ngày hiện tại
$currentDate = date('Y-m-d');
?>

<link rel="stylesheet" href="../Css/attendance_list.css">

<div id="attendanceEdit">
    <?php if (empty($students)): ?>
        <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
    <?php else: ?>
        <form method="POST" action="../Attendance/process_attendance.php">
            <!-- Các nút -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex" style="width: 13%;">
                    <div class="input-group d-flex">
                        <button type="button" id="confirmAttendanceBtnEdit" class="btn btn-primary">Hiện buổi</button>
                        <input type="number" id="attendanceInputEdit" min="0" max="<?php echo count($schedules); ?>"
                            class="form-control" placeholder="0">
                    </div>
                </div>
                <div>
                    <span class="mx-3"><strong>P :</strong> Có mặt</span>
                    <span class="mx-3"><strong>L :</strong> Đi trễ</span>
                    <span class="mx-3"><strong>A :</strong> Vắng mặt</span>
                </div>
                <div>
                    <a href="../Attendance/attendance_report.php?class_id=<?php echo urlencode($class_id); ?>"
                        class="btn btn-info">Thống kê điểm danh</a>
                    <a href="export_excel.php?class_id=<?php echo urlencode($class_id); ?>"
                        class="btn btn-success btn-custom">Xuất Excel</a>
                </div>
            </div>
            <hr>

            <!-- Danh sách -->
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
                            <th style="width: 50px;">P</th>
                            <th style="width: 50px;">L</th>
                            <th style="width: 50px;">A</th>
                            <?php foreach ($schedules as $index => $schedule): ?>
                                <th style="width: 100px; text-align: center;" class="edit-column"
                                    data-index="<?php echo $index; ?>">
                                    <a href="../Attendance/attendance_qr.php?class_id=<?php echo urlencode($class_id); ?>&schedule_id=<?php echo urlencode($schedule['schedule_id']); ?>"
                                        style="text-decoration: none; color: inherit;">
                                        <span><?php echo 'Buổi ' . ($index + 1); ?></span><br>
                                        <small><?php echo date('d/m', strtotime($schedule['date'])); ?></small>
                                    </a>
                                </th>
                            <?php endforeach; ?>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <tr style="height: 60px">
                                <td style="padding-left: 10px;"><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                                <td><?php echo $attendanceStats[$student['student_id']]['total_present'] ?? 0; ?></td>
                                <td><?php echo $attendanceStats[$student['student_id']]['total_late'] ?? 0; ?></td>
                                <td><?php echo $attendanceStats[$student['student_id']]['total_absent'] ?? 0; ?></td>
                                <?php foreach ($schedules as $schedule): ?>
                                    <td class="edit-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                        <select name="attendance[<?php echo htmlspecialchars($student['student_id']); ?>][<?php echo htmlspecialchars($schedule['schedule_id']); ?>]"
                                            class="form-control attendance-select"
                                            style="width: 37px; display: inline-block; padding-top: 8px"
                                            data-date="<?php echo htmlspecialchars($schedule['date']); ?>">
                                            <option value="-1" <?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) && $attendanceMap[$student['student_id']][$schedule['date']] === '0' ? 'selected' : ''; ?>></option>
                                            <option value="0" <?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) && $attendanceMap[$student['student_id']][$schedule['date']] === '0' ? 'selected' : ''; ?>>A</option>
                                            <option value="1" <?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) && $attendanceMap[$student['student_id']][$schedule['date']] === '1' ? 'selected' : ''; ?>>P</option>
                                            <option value="2" <?php echo isset($attendanceMap[$student['student_id']][$schedule['date']]) && $attendanceMap[$student['student_id']][$schedule['date']] === '2' ? 'selected' : ''; ?>>L</option>
                                        </select>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-dark-subtle">
                            <td colspan="10" style="text-align: center;">Tổng sinh viên có mặt</td>
                            <?php foreach ($schedules as $schedule): ?>
                                <td class="edit-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                    <?php
                                    $countPresent = 0;
                                    foreach ($students as $student) {
                                        if (
                                            isset($attendanceMap[$student['student_id']][$schedule['date']]) &&
                                            ($attendanceMap[$student['student_id']][$schedule['date']] === '1' ||
                                                $attendanceMap[$student['student_id']][$schedule['date']] === '2')
                                        ) {
                                            $countPresent++;
                                        }
                                    }
                                    echo $countPresent;
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Thanh nhập, nút xác nhận và nút hiện tất cả nằm ngang -->
            <div class="d-flex align-items-center justify-content-between mt-3">
                <div id="paginationEdit" class="pagination-container"></div>
                <div>
                    <button class="btn btn-secondary btn-custom" id="listModeBtn">Quay lại</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>

        </form>
    <?php endif; ?>
</div>



<script>
    const totalDatesEdit = <?php echo count($schedules); ?>;
</script>
<script src="../JavaScript/attendance_edit.js"></script>