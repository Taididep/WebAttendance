<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '../../../Account/islogin.php';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar_student.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

// Lấy user_id từ phiên làm việc
$student_id = $_SESSION['user_id'];

// Truy vấn để lấy thông tin lớp học từ bảng classes
$sql = "CALL GetClassDetailsById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$class_id]);

// Lấy kết quả truy vấn
$classData = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Kiểm tra xem có kết quả hay không
if (!$classData) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin chi tiết lớp học</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #343a40;
        }

        .classroom-card {
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-color: #ff8554;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .classroom-card .card-body {
            padding: 20px;
        }

        .classroom-card h2 {
            font-weight: bold;
            cursor: pointer;
            font-size: 1.8rem;
        }

        .classroom-card hr {
            border-color: white;
            margin: 10px 0;
        }

        .classroom-card .class-details h5 {
            font-size: 1.1rem;
            font-weight: normal;
        }

        /* Hiệu ứng hover */
        .classroom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 123, 255, 0.2);
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
            text-align: center;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        /* Nút ẩn/hiện */
        .btn-custom {
            margin: 0 5px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            background-color: #0056b3;
        }

        /* Tiêu đề danh sách điểm danh */
        h2.text-center {
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* Ẩn bảng khi ở màn hình nhỏ */
        @media (max-width: 768px) {
            .classroom-card h2 {
                font-size: 1.5rem;
            }

            h2.text-center {
                font-size: 1.8rem;
            }
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <!-- Card hiển thị thông tin lớp học -->
        <div class="card classroom-card shadow-lg">
            <div class="card-body">
                <h2><?php echo htmlspecialchars($classData['class_name']); ?></h2>
                <hr>
                <div class="class-details">
                    <h5><?php echo htmlspecialchars($classData['semester_name']); ?></h5>
                    <h5><?php echo htmlspecialchars($classData['course_name']); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin điểm danh -->
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-center">Thông tin điểm danh</h2>
            <button class="btn btn-primary btn-custom" id="toggleTableBtn">Ẩn</button>
        </div>
        <hr>

        <div id="attendanceList" style="display: inline;">
            <?php include '../Attendance/attendance_list.php'; ?>
        </div>
    </div>

    <script>
        const toggleTableBtn = document.getElementById('toggleTableBtn');
        const attendanceList = document.getElementById('attendanceList'); // Lấy bảng danh sách

        // Hàm để kiểm tra và ẩn/hiện các thành phần
        function toggleAttendanceList() {
            const isHidden = attendanceList.style.display === 'none' || attendanceList.style.display === '';
            attendanceList.style.display = isHidden ? 'block' : 'none';
            toggleTableBtn.textContent = isHidden ? 'Ẩn' : 'Hiện';
        }

        // Gán sự kiện click cho nút
        toggleTableBtn.addEventListener('click', toggleAttendanceList);
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>
