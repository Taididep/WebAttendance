<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '../../../Account/islogin.php';
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';

// Kiểm tra xem class_id có được gửi qua URL hay không
if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Lấy class_id từ URL
$class_id = $_GET['class_id'];

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
            background: linear-gradient(135deg, #e9ecef 0%, #ffffff 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #343a40;
        }

        .classroom-card {
            border-radius: 15px;
            overflow: hidden;
            background-color: #007bff;
            color: white;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 8px 30px rgba(0, 123, 255, 0.3);
        }

        .classroom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0, 123, 255, 0.5);
        }

        .classroom-card h2 {
            font-weight: bold;
            cursor: pointer;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .table td, .table th {
            vertical-align: middle;
            white-space: nowrap;
            text-align: center;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }

        .btn-custom {
            margin: 0 5px;
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            background-color: #0056b3;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
            border-bottom: none;
        }

        .modal-content {
            border-radius: 15px;
        }

        .modal-body {
            text-align: center;
            font-size: 1.5rem;
        }

        h2.text-center {
            font-size: 2.2rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            h2.text-center {
                font-size: 1.8rem;
            }

            .classroom-card h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="container mt-5">
        <!-- Card hiển thị thông tin lớp học -->
        <div class="card classroom-card shadow-lg">
            <div class="card-body text-center">
                <h2 data-bs-toggle="modal" data-bs-target="#classModal"><?php echo htmlspecialchars($classData['class_name']); ?></h2>
                <hr>
                <div>
                    <h5><?php echo htmlspecialchars($classData['semester_name']); ?></h5>
                    <h5><?php echo htmlspecialchars($classData['course_name']); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classModalLabel">Mã lớp học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo htmlspecialchars($classData['class_id']); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách điểm danh -->
    <div class="container mt-5 mb-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="text-center">Danh sách điểm danh</h2>
            <div>
                <a href="export_excel.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-success btn-custom">Xuất Excel</a>
                <button class="btn btn-secondary btn-custom" id="editModeBtn">Chỉnh sửa</button>
                <button class="btn btn-primary btn-custom" id="toggleTableBtn">Ẩn danh sách</button>
            </div>
        </div>
        <hr>

        <div id="attendanceList" style="display: inline;">
            <?php include '../Attendance/attendance_list.php'; ?> <!-- Gọi file danh sách điểm danh -->
        </div>

        <div id="attendanceEdit" style="display: none;">
            <?php include '../Attendance/attendance_edit.php'; ?> <!-- Gọi file chỉnh sửa danh sách điểm danh -->
        </div>
    </div>

    <!-- JavaScript để ẩn/hiện bảng -->
    <script>
        const attendanceList = document.getElementById('attendanceList');
        const attendanceEdit = document.getElementById('attendanceEdit');
        const toggleTableBtn = document.getElementById('toggleTableBtn');
        const editModeBtn = document.getElementById('editModeBtn');

        function toggleAttendanceList() {
            const isHidden = attendanceList.style.display === 'none';
            attendanceList.style.display = isHidden ? 'block' : 'none';
            editModeBtn.style.display = isHidden ? 'inline-block' : 'none';
            toggleTableBtn.textContent = isHidden ? 'Ẩn danh sách' : 'Hiện danh sách';
        }

        function toggleEditMode() {
            const isEditVisible = attendanceEdit.style.display === 'block';
            attendanceList.style.display = isEditVisible ? 'block' : 'none';
            attendanceEdit.style.display = isEditVisible ? 'none' : 'block';
            editModeBtn.textContent = isEditVisible ? 'Chỉnh sửa' : 'Hủy';
        }
        
        toggleTableBtn.addEventListener('click', toggleAttendanceList);
        editModeBtn.addEventListener('click', toggleEditMode);
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>

</html>
