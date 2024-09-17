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
        /* CSS cho hiệu ứng hover toàn bộ hàng */
        .table-hover tbody tr:hover {
            background-color: #FF0000; /* Màu nền khi hover */
            cursor: pointer; /* Con trỏ chuột khi hover */
        }
    </style>
</head>

<body>
    <div class="container mt-5 animate__animated animate__fadeIn">
        <?php include 'Connect/connect.php'; ?>
        <?php
        session_start();

        // Kiểm tra và lấy ID lớp từ URL
        if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
            echo "<p class='text-danger'>Lỗi: ID lớp không hợp lệ.</p>";
            exit();
        }

        $classId = (int)$_GET['class_id'];

        // Lấy thông tin lớp học
        $classSql = "SELECT name FROM classes WHERE id = ?";
        $classStm = $conn->prepare($classSql);
        $classStm->execute([$classId]);
        $class = $classStm->fetch(PDO::FETCH_OBJ);

        if (!$class) {
            echo "<p class='text-danger'>Lỗi: Không tìm thấy lớp học.</p>";
            exit();
        }

        // Lấy danh sách sinh viên trong lớp
        $studentsSql = "SELECT s.id, s.lastname, s.firstname, s.class, s.birthday, s.gender
                        FROM students s
                        JOIN attendances a ON s.id = a.student_id
                        WHERE a.class_id = ?
                        GROUP BY s.id";
        $studentsStm = $conn->prepare($studentsSql);
        $studentsStm->execute([$classId]);
        $students = $studentsStm->fetchAll(PDO::FETCH_OBJ);
        ?>
        <!-- Header với nút quay lại -->
        <header class="mb-4 bg-success">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Danh sách sinh viên lớp <?php echo htmlspecialchars($class->name); ?></h1>
                <a href="Role/teacher.php" class="btn btn-primary">Quay lại</a>
            </div>
        </header>

        <!-- Hiển thị danh sách sinh viên -->
        <h3 class="mb-4">Danh sách sinh viên:</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>MSSV</th>
                    <th>Họ</th>
                    <th>Tên</th>
                    <th>Giới tính</th>
                    <th>Tên Lớp</th>
                    <th>Ngày sinh</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)) { ?>
                    <?php foreach ($students as $student) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student->id); ?></td>
                            <td><?php echo htmlspecialchars($student->lastname); ?></td>
                            <td><?php echo htmlspecialchars($student->firstname); ?></td>
                            <td><?php echo htmlspecialchars($student->gender); ?></td>
                            <td><?php echo htmlspecialchars($student->class); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($student->birthday))); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có sinh viên nào trong lớp này.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
