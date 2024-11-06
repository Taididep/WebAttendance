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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #343a40;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-select {
            border-radius: 0.5rem;
            border: 1px solid #007bff;
            /* Đường viền xanh */
            transition: border-color 0.3s;
        }

        .form-select:focus {
            border-color: #0056b3;
            /* Đổi màu viền khi focus */
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
        }

        .btn-success {
            border-radius: 50px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .class-table {
            margin-top: 20px;
        }

        .class-table th {
            background-color: #007bff;
            color: #ffffff;
        }

        .class-table tr:hover {
            background-color: #f1f1f1;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Tiêu đề -->
            <h2 class="mb-4 text-center">Quản lý danh sách lớp học</h2>

            <!-- Form chọn học kỳ -->
            <form id="semesterForm" class="d-flex justify-content-between align-items-center mb-4">
                <div class="mb-0 me-2" style="flex: 1;">
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

            <!-- Bảng lớp học -->
            <div id="classList" class="class-table">
                <!-- Danh sách lớp sẽ được tải ở đây -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JavaScript/class_manage.js"></script>
</body>

</html>