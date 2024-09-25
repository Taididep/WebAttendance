<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <?php
        session_start();
        include 'functions.php';

        if (isset($_SESSION['message'])) {
            echo "
            <script>
                $(document).ready(function(){
                    $('#successModal').modal('show');
                });
            </script>";
            unset($_SESSION['message']);
        }

        $classId = isset($_GET['class_id']) && is_numeric($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
        $attendanceDate = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;

        if ($classId <= 0) {
            echo "<p class='text-danger'>Lỗi: ID lớp không hợp lệ.</p>";
            exit();
        }

        $class = getClassInfo($conn, $classId);
        if (!$class) {
            echo "<p class='text-danger'>Lỗi: Không tìm thấy thông tin lớp.</p>";
            exit();
        }

        $attendanceDates = getAttendanceDates($conn, $classId);
        
        if (empty($attendanceDate) && count($attendanceDates) > 0) {
            $attendanceDate = $attendanceDates[0];
        }

        // Lấy danh sách sinh viên phân trang
        $students = getStudents($conn, $classId, $attendanceDate, $limit, $offset);
        $totalStudents = getTotalStudents($conn, $classId, $attendanceDate);
        $totalPages = ceil($totalStudents / $limit);
        ?>

        <div class="container mt-5">
            <header class="mb-4 bg-success">
                <div class="d-flex justify-content-between align-items-center">
                    <h1>Danh sách sinh viên lớp <?php echo htmlspecialchars($class->name); ?></h1>
                    <a href="teacher.php" class="btn btn-primary">Quay lại</a>
                </div>
            </header>

            <!-- Form chọn ngày điểm danh -->
            <form method="get" class="mb-4">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($classId); ?>">
                <input type="hidden" name="page" value="1">
                <div class="form-group">
                    <label for="attendance_date">Ngày điểm danh:</label>
                    <select name="attendance_date" id="attendance_date" class="form-control" onchange="this.form.submit()">
                        <?php foreach ($attendanceDates as $date) { ?>
                            <option value="<?php echo htmlspecialchars($date); ?>"
                                <?php if ($date == $attendanceDate) echo 'selected'; ?>>
                                <?php echo htmlspecialchars(date('d/m/Y', strtotime($date))); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </form>

            <!-- Nút mở modal -->
            <button class="btn btn-info mb-4" data-toggle="modal" data-target="#createAttendanceModal">Tạo ngày điểm danh mới</button>

            <!-- Modal nhập ngày điểm danh mới -->
            <div class="modal fade" id="createAttendanceModal" tabindex="-1" aria-labelledby="createAttendanceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createAttendanceModalLabel">Tạo ngày điểm danh mới</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="create_attendance.php">
                                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($classId); ?>">
                                <div class="form-group">
                                    <label for="new_attendance_date">Ngày điểm danh:</label>
                                    <input type="date" name="new_attendance_date" id="new_attendance_date" class="form-control" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-primary">Tạo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <form method="post" action="update_attendance.php" class="mb-4">
                <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($classId); ?>">
                <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($attendanceDate); ?>">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>ID</th>
                            <th>Họ</th>
                            <th>Tên</th>
                            <th>Lớp</th>
                            <th>Giới tính</th>
                            <th>Ngày sinh</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) > 0): ?>
                            <?php $stt = $offset + 1; ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                                    <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['gender']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($student['birthday']))); ?></td>
                                    <td>
                                        <select name="status[<?php echo $student['id']; ?>]" class="form-control">
                                            <option value="Present" <?php if ($student['status'] == 'Present') echo 'selected'; ?>>Present</option>
                                            <option value="Absent" <?php if ($student['status'] == 'Absent') echo 'selected'; ?>>Absent</option>
                                            <option value="Late" <?php if ($student['status'] == 'Late') echo 'selected'; ?>>Late</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="note[<?php echo $student['id']; ?>]" class="form-control" value="<?php echo htmlspecialchars($student['note']); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <button type="submit" class="btn btn-success">Cập nhật</button>
            </form>

            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?class_id=<?php echo htmlspecialchars($classId); ?>&attendance_date=<?php echo htmlspecialchars($attendanceDate); ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

    <!-- Modal thông báo thành công -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thông báo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Cập nhật thành công!
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
    </div>

</body>
</html>
