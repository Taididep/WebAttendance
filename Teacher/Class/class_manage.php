<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

$sql_semesters = "CALL GetAllSemesters()"; // Gọi thủ tục
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor(); // Đóng kết quả của truy vấn trước

// Lấy semester_id của học kỳ đầu tiên trong danh sách
$defaultSemesterId = !empty($semesters) ? $semesters[0]['semester_id'] : null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../Css/class_manage.css">
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Title -->
            <h2 class="mb-4 text-center">Quản lý danh sách lớp học</h2>

            <!-- Semester Selection Form -->
            <form id="semesterForm" class="d-flex justify-content-between align-items-center mb-4">
                <div class="mb-0" style="flex: 1;">
                    <select class="form-select" id="semester" name="semester_id" required>
                        <option value="" disabled selected>Chọn học kỳ</option>
                        <?php foreach ($semesters as $semester): ?>
                            <option value="<?php echo $semester['semester_id']; ?>" <?php echo $semester['semester_id'] == $defaultSemesterId ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($semester['semester_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <!-- Class List Table -->
            <div id="classList" class="class-table">
                <!-- Class list will be loaded here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/class_manage.js"></script>
</body>

</html>