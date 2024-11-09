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

// Ngày hiện tại
$currentDate = date('Y-m-d');
?>

<style>
    body {
        background-color: #f8f9fa;
    }

    .container {
        max-width: 1200px;
        margin-top: 100px;
    }

    .present {
        background-color: #d4edda; /* Màu xanh lá */
    }

    .late {
        background-color: #fff3cd; /* Màu vàng */
    }

    .absent {
        background-color: #f8d7da; /* Màu đỏ */
    }
</style>

<div id="attendanceList">
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div class="d-flex" style="width: 24%;">
            <div class="input-group d-flex">
                <input type="number" id="attendanceInputList" min="1" max="<?php echo count($schedules); ?>" class="form-control" placeholder="Nhập buổi">
                <button type="button" id="confirmAttendanceBtnList" class="btn btn-primary">Xác nhận</button>
                <button type="button" id="showAllBtnList" class="btn btn-success">Hiện tất cả</button>
            </div>
        </div>
        <div>
            <span class="mx-3"><strong>0:</strong> Vắng mặt</span>
            <span class="mx-3"><strong>1:</strong> Có mặt</span>
            <span class="mx-3"><strong>2:</strong> Đi trễ</span>
        </div>
        <div>
            <a href="../Attendance/attendance_report.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-info">Thống kê điểm danh</a>
            <a href="export_excel.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-success btn-custom">Xuất Excel</a>
        </div>

    </div>
    <hr>
    <div class="table-responsive">
        <?php if (empty($students)): ?>
            <div class="alert alert-warning text-center">Lớp hiện chưa có học sinh nào</div>
        <?php else: ?>
            <!-- Danh sách -->
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
                        <?php foreach ($schedules as $index => $schedule): ?>
                            <th style="width: 100px; text-align: center;" class="list-column" data-index="<?php echo $index; ?>">
                                <a href="../Attendance/attendance_qr.php?class_id=<?php echo urlencode($class_id); ?>&schedule_id=<?php echo urlencode($schedule['schedule_id']); ?>" style="text-decoration: none; color: inherit;">
                                    <span><?php echo 'Buổi ' . ($index + 1); ?></span><br>
                                    <small><?php echo date('d/m/Y', strtotime($schedule['date'])); ?></small>
                                </a>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student): ?>
                        <tr style="height: 50px">
                            <td style="padding-left: 10px;"><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($student['gender']); ?></td>
                            <td><?php echo htmlspecialchars($student['class']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($student['birthday'])); ?></td>
                            <?php foreach ($schedules as $schedule): ?>
                                <td class="list-data" style="height: 22px; width: 80px; padding-bottom: 10px; text-align: center;">
                                    <?php
                                    // Kiểm tra xem có trạng thái điểm danh không
                                    if (isset($attendanceMap[$student['student_id']][$schedule['date']])) {
                                        $status = $attendanceMap[$student['student_id']][$schedule['date']];
                                        if ($status === '1') {
                                            echo '<span class="present">1</span>'; // Có mặt
                                        } elseif ($status === '2') {
                                            echo '<span class="late">2</span>'; // Muộn
                                        } else {
                                            echo '<span class="absent">0</span>'; // Vắng mặt
                                        }
                                    } else {
                                        echo '<span class="absent">0</span>'; // Không có dữ liệu điểm danh
                                    }
                                    ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-dark-subtle">
                        <td colspan="7" style="text-align: center;">Tổng sinh viên có mặt</td>
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
        <button class="btn btn-secondary btn-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal">Thêm sinh viên</button>
        <button class="btn btn-secondary btn-custom" id="editModeBtn">Chỉnh sửa</button>
    </div>
</div>


<!-- Modal Nhập Mã Lớp Học -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentModalLabel">Thêm sinh viên vào lớp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addStudentForm" method="post">
                <div class="modal-body">
                    <div id="joinClassMessage" class="alert d-none"></div>

                    <div class="mb-3">
                        <label for="studentIdInput" class="form-label">Mã sinh viên</label>
                        <input type="text" class="form-control" id="studentIdInput" name="student_id" required maxlength="11" oninput="this.value = this.value.replace(/\D/g, '')">
                    </div>

                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.getElementById("addStudentForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngăn chặn gửi form theo cách thông thường

        const classId = document.querySelector("input[name='class_id']").value;
        const studentId = document.getElementById("studentIdInput").value;
        const joinClassMessage = document.getElementById("joinClassMessage");

        // Gửi yêu cầu AJAX tới add_student.php
        fetch("<?php echo $basePath; ?>Class/add_student.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `class_id=${encodeURIComponent(classId)}&student_id=${encodeURIComponent(studentId)}`
            })
            .then(response => response.json())
            .then(data => {
                joinClassMessage.classList.remove("d-none");
                if (data.success) {
                    joinClassMessage.classList.add("alert-success");
                    joinClassMessage.classList.remove("alert-danger");
                    joinClassMessage.innerText = data.message;
                    // Reset form sau khi thêm sinh viên thành công
                    document.getElementById("addStudentForm").reset();
                } else {
                    joinClassMessage.classList.add("alert-danger");
                    joinClassMessage.classList.remove("alert-success");
                    joinClassMessage.innerText = data.message;
                }
            })
            .catch(error => {
                joinClassMessage.classList.remove("d-none");
                joinClassMessage.classList.add("alert-danger");
                joinClassMessage.classList.remove("alert-success");
                joinClassMessage.innerText = "Có lỗi xảy ra. Vui lòng thử lại.";
            });
    });
</script>

<script>
    const currentDate = new Date('<?php echo $currentDate; ?>');
    const scheduleCells = document.querySelectorAll('.list-column');

    scheduleCells.forEach(cell => {
        const dateText = cell.querySelector('small').innerText;
        const [day, month, year] = dateText.split('/').map(Number);
        const scheduleDate = new Date(year, month - 1, day); // Khởi tạo đối tượng Date với năm, tháng, ngày

        // So sánh ngày điểm danh với ngày hiện tại
        if (scheduleDate < currentDate) {
            cell.classList.add('table-secondary'); // Thay đổi màu sắc cho các buổi đã qua
            cell.innerHTML += '<br><span class="text-muted"><i class="bi bi-lock-fill"></i></span>'; // Thêm thông báo đã khóa
            const link = cell.querySelector('a');
            if (link) link.style.pointerEvents = 'none'; // Vô hiệu hóa liên kết
            cell.style.pointerEvents = 'none'; // Vô hiệu hóa tương tác với ô điểm danh
        }
    });
</script>

<script>
    const totalDatesList = <?php echo count($schedules); ?>;
</script>
<script src="../JavaScript/attendance_list.js"></script>