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
        .classroom-card {
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background-color: #ff8554;
            color: white;
        }

        .classroom-card .card-text {
            padding: 20px;
        }

        .classroom-card h2 {
            font-weight: bold;
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
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <h5><?php echo htmlspecialchars($classData['semester_name']); ?></h5>
                        <h5><?php echo htmlspecialchars($classData['course_name']); ?></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin điểm danh -->
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-center">Thông tin điểm danh</h2>
            <button class="btn btn-primary" id="toggleTableBtn">Ẩn</button>
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