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
<<<<<<< HEAD
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .container {
            max-width: 1200px;
            padding-top: 50px;
        }

        h2 {
            font-size: 1.8rem; /* Reduced font size */
            font-weight: 700;
            color: #2c3e50;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Reduced margin */
        }

        .form-select {
            font-size: 1rem;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
        }

        .class-table {
            margin-top: 30px;
            border-radius: 12px;
            width: 100%;
            background-color: #ffffff;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .class-table th,
        .class-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e1e1;
        }

        .class-table th {
            background-color: #f7f7f7;
            color: #333;
            font-weight: bold;
        }

        .class-table tr:hover {
            background-color: #f2f2f2;
            transform: scale(1.02);
        }

        .class-table td a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .class-table td a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .class-table tr {
            transition: all 0.3s ease;
        }
    </style>
=======
    <link rel="stylesheet" href="../Css/class_manage.css">
>>>>>>> 129b9e0d625e4b8226e486779f844c06f1fdb266
</head>

<body>

    <div class="container">
        <div class="card p-4">
            <!-- Title -->
            <h2 class="mb-4 text-center">Quản lý danh sách lớp học</h2>

            <!-- Semester Selection Form -->
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