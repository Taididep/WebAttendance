<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <?php
        session_start();
        include 'functions.php';

        // Kiểm tra và lấy ID lớp và ngày điểm danh từ URL
        $classId = isset($_GET['class_id']) && is_numeric($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
        $attendanceDate = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : '';
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // Số sinh viên mỗi trang
        $offset = ($page - 1) * $limit;

        if ($classId <= 0) {
            echo "<p class='text-danger'>Lỗi: ID lớp không hợp lệ.</p>";
            exit();
        }

        // Lấy thông tin lớp, ngày điểm danh và danh sách học sinh
        $class = getClassInfo($conn, $classId);
        if (!$class) {
            echo "<p class='text-danger'>Lỗi: Không tìm thấy thông tin lớp.</p>";
            exit();
        }
        $attendanceDates = getAttendanceDates($conn, $classId);

        // Cập nhật để lấy sinh viên phân trang
        $students = getStudents($conn, $classId, $attendanceDate, $limit, $offset);
        $totalStudents = getTotalStudents($conn, $classId, $attendanceDate);
        $totalPages = ceil($totalStudents / $limit);
        ?>

        <header class="mb-4 bg-success">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Danh sách sinh viên lớp <?php echo htmlspecialchars($class->name); ?></h1>
                <a href="teacher.php" class="btn btn-primary">Quay lại</a>
            </div>
        </header>

        <form method="get" class="mb-4">
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($classId); ?>">
            <div class="form-group">
                <label for="attendance_date">Ngày điểm danh:</label>
                <select name="attendance_date" id="attendance_date" class="form-control" onchange="this.form.submit()">
                    <option value="">Chọn ngày</option>
                    <?php foreach ($attendanceDates as $date) { ?>
                        <option value="<?php echo htmlspecialchars($date); ?>"
                            <?php if ($date == $attendanceDate) echo 'selected'; ?>>
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($date))); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if ($attendanceDate): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
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
                        <?php foreach ($students as $student): ?>
                            <tr>
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
                                        <option value="" <?php if ($student['status'] == '') echo 'selected'; ?>></option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="note[<?php echo $student['id']; ?>]" class="form-control" value="<?php echo htmlspecialchars($student['note']); ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Phân trang -->
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
        <?php endif; ?>
    </div>
</body>

</html>
