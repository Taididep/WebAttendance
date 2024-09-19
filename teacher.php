<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container mt-5 animate__animated animate__fadeIn">
        <?php
        include 'connect/connect.php';
        session_start();

        // Xử lý đăng xuất
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: ../index.php"); // Chuyển hướng đến trang đăng nhập
            exit();
        }

        $username = $_SESSION['username'] ?? '';

        // Lấy ID người dùng dựa trên tên đăng nhập
        $userSql = "SELECT id FROM users WHERE username = ?";
        $userStm = $conn->prepare($userSql);
        $userStm->execute([$username]);
        $user = $userStm->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // Lấy thông tin giáo viên sử dụng ID người dùng
            $teacherSql = "SELECT lastname, firstname FROM teachers WHERE id = ?";
            $teacherStm = $conn->prepare($teacherSql);
            $teacherStm->execute([$user->id]);
            $teacherData = $teacherStm->fetch(PDO::FETCH_OBJ);
        }

        // Lấy danh sách học kỳ
        $semesterSql = "SELECT * FROM semesters ORDER BY id DESC";
        $semesterStm = $conn->prepare($semesterSql);
        $semesterStm->execute();
        $semesters = $semesterStm->fetchAll(PDO::FETCH_OBJ);

        // Lấy ID học kỳ đã chọn từ form, mặc định là học kỳ đầu tiên nếu không được đặt
        $selectedSemesterId = $_POST['semester_id'] ?? ($semesters[0]->id ?? '');

        // Lấy danh sách các lớp học
        $classesSql = "
            SELECT 
                c.id, 
                c.name AS class_name, 
                cr.name AS course_name
            FROM 
                classes c
            JOIN 
                courses cr ON c.course_id = cr.id
            WHERE 
                c.teacher_id = ? 
                AND c.semester_id = ?
        ";
        $classesStm = $conn->prepare($classesSql);
        $classesStm->execute([$user->id, $selectedSemesterId]);
        $classes = $classesStm->fetchAll(PDO::FETCH_OBJ);
        ?>

        <!-- Header -->
        <header class="mb-4 bg-success">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Dashboard</h1>
                <form method="post">
                    <button type="submit" name="logout" class="btn btn-danger">Đăng xuất</button>
                </form>
            </div>
        </header>

        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="semester_id">Chọn học kỳ:</label>
                <select name="semester_id" id="semester_id" class="form-control" onchange="this.form.submit()">
                    <?php foreach($semesters as $semester) { ?>
                        <option value="<?php echo htmlspecialchars($semester->id); ?>"
                            <?php if ($semester->id == $selectedSemesterId) echo 'selected'; ?> >
                            <?php echo htmlspecialchars($semester->name . " (" . date('d/m/Y', strtotime($semester->start_date)) . " - " . date('d/m/Y', strtotime($semester->end_date)) . ")"); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <!-- Hiển thị thông điệp chào mừng -->
        <?php
        if (!empty($teacherData)) {
            $lastname = htmlspecialchars($teacherData->lastname);
            $firstname = htmlspecialchars($teacherData->firstname);
            echo "<h2 class='mb-4 welcome-message'>Xin chào, $lastname $firstname</h2>";
        }
        ?>

        <!-- Hiển thị danh sách lớp học cho học kỳ đã chọn -->
        <h3 class="mb-4">Danh sách các lớp học:</h3>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID lớp</th>
                    <th>Tên lớp</th>
                    <th>Tên Môn Học</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($classes)) { ?>
                    <?php foreach ($classes as $class) { ?>
                        <tr onclick="window.location.href='../attendance.php?class_id=<?php echo htmlspecialchars($class->id); ?>'">
                            <td><?php echo htmlspecialchars($class->id); ?></td>
                            <td><?php echo htmlspecialchars($class->class_name); ?></td>
                            <td><?php echo htmlspecialchars($class->course_name); ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="3" class="text-center">Không có lớp học nào trong học kỳ này.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
