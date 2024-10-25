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
$sqlAttendance = "CALL GetAttendanceByClassId(?)";
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
$sqlDates = "CALL GetDistinctDatesByClassId(?)";
$stmtDates = $conn->prepare($sqlDates);
$stmtDates->execute([$class_id]);
$dates = $stmtDates->fetchAll(PDO::FETCH_COLUMN);
$stmtDates->closeCursor(); // Đóng con trỏ
?>

<div id="attendanceTable">
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
                    <?php foreach ($dates as $index => $date): ?>
                        <th style="width: 100px; text-align: center; cursor: pointer;" class="attendance-column" data-index="<?php echo $index; ?>">
                            <span><?php echo 'Buổi ' . ($index + 1); ?></span><br>
                            <small><?php echo date('d/m', strtotime($date)); ?></small>
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
                        <?php foreach ($dates as $date): ?>
                            <td class="attendance-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
                                <?php
                                // Kiểm tra xem có trạng thái điểm danh không
                                if (isset($attendanceMap[$student['student_id']][$date])) {
                                    $status = $attendanceMap[$student['student_id']][$date];
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
    <div class="d-flex justify-content-between">
        <button id="showAllBtn" class="btn btn-success mt-3">Hiện tất cả</button>
    </div>
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