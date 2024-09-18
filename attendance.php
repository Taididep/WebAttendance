<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sinh viên</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Định dạng cho modal */
        .modal {
            display: none; /* Ẩn mặc định */
            position: fixed; /* Giữ cố định vị trí */
            z-index: 1000; /* Hiển thị trên cùng */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Màu nền mờ phía sau */
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto; /* Khoảng cách từ trên và canh giữa */
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            position: relative; /* Để định vị nút đóng */
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <?php include 'connect/connect.php'; ?>
        <?php
        session_start();

        // Kiểm tra và lấy ID lớp từ URL
        $classId = isset($_GET['class_id']) && is_numeric($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
        $attendanceDate = isset($_GET['attendance_date']) ? $_GET['attendance_date'] : '';

        if ($classId <= 0) {
            echo "<p class='text-danger'>Lỗi: ID lớp không hợp lệ.</p>";
            exit();
        }

        // Lấy thông tin lớp
        $classSql = "SELECT name FROM classes WHERE id = ?";
        $classStm = $conn->prepare($classSql);
        $classStm->execute([$classId]);
        $class = $classStm->fetch(PDO::FETCH_OBJ);

        if (!$class) {
            echo "<p class='text-danger'>Lỗi: Không tìm thấy thông tin lớp.</p>";
            exit();
        }

        // Lấy danh sách ngày điểm danh đã có
        $attendanceDatesSql = "SELECT DISTINCT attendance_date FROM attendances WHERE class_id = ?";
        $attendanceDatesStm = $conn->prepare($attendanceDatesSql);
        $attendanceDatesStm->execute([$classId]);
        $attendanceDates = $attendanceDatesStm->fetchAll(PDO::FETCH_COLUMN);

        // Lấy danh sách học sinh từ lớp cụ thể và ngày điểm danh cụ thể
        $studentsSql = "
            SELECT s.id, s.lastname, s.firstname, s.class, s.gender, s.birthday, a.status, a.note
            FROM students s
            JOIN attendances a ON s.id = a.student_id
            WHERE a.class_id = ? AND a.attendance_date = ?
        ";
        $studentsStm = $conn->prepare($studentsSql);
        $studentsStm->execute([$classId, $attendanceDate]);
        $students = $studentsStm->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <!-- Header với nút quay lại -->
        <header class="mb-4 bg-success">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Danh sách sinh viên lớp <?php echo htmlspecialchars($class->name); ?></h1>
                <a href="teacher.php" class="btn btn-primary">Quay lại</a>
            </div>
        </header>

        <!-- Form chọn ngày điểm danh hiện tại -->
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

        <!-- Hiển thị danh sách học sinh -->
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
                                <td><?php echo htmlspecialchars($student['status']); ?></td>
                                <td><?php echo htmlspecialchars($student['note']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>
