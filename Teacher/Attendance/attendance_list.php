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

// Lấy thông tin sinh viên trong lớp theo class_id
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor(); // Đóng con trỏ

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

// Lấy danh sách ngày điểm danh và schedule_id từ bảng schedules
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
    <?php if (empty($students)): ?>
        <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped" id="attendanceList" style="table-layout: fixed;">
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
                            <td style="padding-left: 10px;"><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($student['gender']); ?></td>
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
                <tfoot>
                    <tr class="bg-dark-subtle">
                        <td colspan="7" style="text-align: center;">Tổng điểm danh</td>
                        <?php foreach ($schedules as $schedule): ?>
                            <td class="list-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                <?php
                                // Tính số học sinh có mặt cho ngày này
                                $countPresent = 0;
                                foreach ($students as $student) {
                                    if (
                                        isset($attendanceMap[$student['student_id']][$schedule['date']]) &&
                                        $attendanceMap[$student['student_id']][$schedule['date']] === '1'
                                    ) {
                                        $countPresent++;
                                    }
                                }
                                echo $countPresent; // Hiển thị số học sinh có mặt
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex align-items-center mt-3">
            <div class="input-group me-2" style="width: 30%">
                <input type="number" id="attendanceInputList" min="1" max="<?php echo count($schedules); ?>" class="form-control" placeholder="Nhập buổi (1, 2, ...)">
                <button type="button" id="confirmAttendanceBtnList" class="btn btn-primary">Xác nhận</button>
                <button id="showAllBtnList" class="btn btn-success">Hiện tất cả</button>
            </div>
        </div>

    <?php endif; ?>
</div>


<script>
    // Xác nhận buổi học
    document.getElementById('confirmAttendanceBtnList').addEventListener('click', function() {
        const input = document.getElementById('attendanceInputList');
        const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
        const totalDates = <?php echo count($schedules); ?>; // Tổng số buổi

        if (index < 1 || index > totalDates) {
            alert('Vui lòng nhập buổi hợp lệ (từ 1 đến ' + totalDates + ').');
            return;
        }

        // Ẩn tất cả các cột và dữ liệu
        document.querySelectorAll('#attendanceList .list-data, #attendanceList .list-column').forEach(cell => {
            cell.style.display = 'none';
        });

        // Hiện cột buổi đã nhập
        const cells = document.querySelectorAll(`#attendanceList td:nth-child(${index + 7})`); // Cột thứ index (cột 8 là buổi đầu tiên)

        cells.forEach(cell => {
            cell.style.display = ''; // Hiện cột tương ứng
        });

        // Hiện tiêu đề cột tương ứng
        const headerCells = document.querySelectorAll(`#attendanceList th.list-column`);
        headerCells.forEach((headerCell, idx) => {
            if (idx === index - 1) {
                headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
            } else {
                headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
            }
        });
    });

    // Nút hiện tất cả cho bảng danh sách
    document.getElementById('showAllBtnList').addEventListener('click', function(event) {
        event.preventDefault(); // Ngăn chặn hành vi mặc định
        document.querySelectorAll('#attendanceList .list-data').forEach(cell => {
            cell.style.display = '';
        });
        document.querySelectorAll('#attendanceList .list-column').forEach(column => {
            column.style.display = '';
        });
    });
</script>