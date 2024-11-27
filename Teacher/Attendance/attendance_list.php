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

// Ngày giờ hiện tại
$currentDateTime = date('Y-m-d H:i:s'); // Định dạng ngày giờ
?>

<link rel="stylesheet" href="../Css/attendance_list.css">

<div id="attendanceList">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex" style="width: 13%;">
            <div class="input-group d-flex">
                <button type="button" id="confirmAttendanceBtnList" class="btn btn-primary">Hiện buổi</button>
                <input type="number" id="attendanceInputList" min="0" max="<?php echo count($schedules); ?>"
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
    <div class="table-responsive">
        <?php if (empty($students)): ?>
            <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
        <?php else: ?>
            <table class="table table-striped" id="attendanceTable" style="table-layout: fixed;">
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
                            <th style="width: 100px; text-align: center;" class="list-column"
                                data-index="<?php echo $index; ?>">
                                <a href="../Attendance/attendance_qr.php?class_id=<?php echo urlencode($class_id); ?>&schedule_id=<?php echo urlencode($schedule['schedule_id']); ?>"
                                    style="text-decoration: none; color: inherit;">
                                    <span><?php echo 'Buổi ' . ($index + 1); ?></span><br>
                                    <small><?php echo date('d/m/Y', strtotime($schedule['date'])); ?></small>
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
                                <td class="list-data" style="height: 22px; width: 80px; padding-bottom: 10px; text-align: center;">
                                    <?php
                                    // Kiểm tra xem có trạng thái điểm danh không
                                    if (isset($attendanceMap[$student['student_id']][$schedule['date']])) {
                                        $status = $attendanceMap[$student['student_id']][$schedule['date']];
                                        if ($status === '1') {
                                            echo '<span class="present">P</span>'; // Có mặt
                                        } elseif ($status === '2') {
                                            echo '<span class="late">L</span>'; // Muộn
                                        } elseif ($status === '-1') {
                                            echo ''; // Chưa điểm danh
                                        } elseif ($schedule['date'] > date('Y-m-d', strtotime($currentDateTime))) {
                                            echo ''; // Chưa đến ngày
                                        } else {
                                            echo '<span class="absent">A</span>'; // Vắng
                                        }
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr class="bg-dark-subtle">
                        <td colspan="10" style="text-align: center;">Tổng sinh viên có mặt</td>
                        <?php foreach ($schedules as $schedule): ?>
                            <td class="list-data" style="width: 80px; padding-bottom: 10px; text-align: center;">
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
        <?php endif; ?>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-3">
        <button class="btn btn-secondary btn-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal">Quản lý
            sinh
            viên</button>
        <button class="btn btn-secondary btn-custom" id="editModeBtn">Chỉnh sửa</button>
    </div>
</div>

<!-- Modal Quản lý sinh viên -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Quản lý sinh viên trong lớp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="add-tab" data-bs-toggle="tab" href="#addStudent" role="tab">Thêm
                            sinh viên</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="remove-tab" data-bs-toggle="tab" href="#removeStudent" role="tab">Đá
                            sinh viên</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="upload-tab" data-bs-toggle="tab" href="#uploadStudent" role="tab">Tải
                            lên Excel</a>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <!-- Tab Thêm Sinh Viên -->
                    <div class="tab-pane fade show active" id="addStudent" role="tabpanel">
                        <form id="addStudentForm" method="post">
                            <div id="joinClassMessage" class="alert d-none"></div>

                            <div class="mb-3">
                                <label for="studentIdInput" class="form-label">Mã sinh viên</label>
                                <input type="text" class="form-control" id="studentIdInput" name="student_id" required
                                    maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')">
                            </div>

                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Xóa Sinh Viên -->
                    <div class="tab-pane fade" id="removeStudent" role="tabpanel">
                        <form id="removeStudentForm" method="post">
                            <div id="removeClassMessage" class="alert d-none"></div>

                            <div class="mb-3">
                                <label for="removeStudentIdInput" class="form-label">Mã sinh viên</label>
                                <input type="text" class="form-control" id="removeStudentIdInput" name="student_id"
                                    required maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')">
                            </div>

                            <!-- Hiện thông tin sinh viên -->
                            <div id="studentInfo" class="d-none mb-3">
                                <h6>Thông tin sinh viên:</h6>
                                <p id="studentDetails"></p>
                            </div>

                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="button" class="btn btn-danger d-none" id="removeStudentButton">Đá sinh
                                    viên</button>
                            </div>
                        </form>
                    </div>

                    <!-- Tab Tải lên Excel -->
                    <div class="tab-pane fade" id="uploadStudent" role="tabpanel">
                        <form id="uploadStudentForm" method="post" enctype="multipart/form-data" action="import_excel.php">
                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                            <input type="file" class="form-control" id="excelFileInput" name="excel_file" accept=".xls,.xlsx" required>
                            <button type="submit" class="btn btn-primary">Tải lên</button>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const totalDatesList = <?php echo count($schedules); ?>;
    const basePath = "<?php echo $basePath; ?>";
    //
    const currentDateTime = new Date('<?php echo $currentDateTime; ?>');

    // Danh sách sinh viên được truyền từ PHP sang JavaScript
    const students = <?php echo json_encode($students); ?>;
</script>
<script src="../JavaScript/attendance_list.js"></script>