<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

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
            cursor: pointer;
        }
        .table td {
            height: 60px;
            vertical-align: middle;
            white-space: nowrap;
        }
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <div class="container mt-5">
        <!-- Card hiển thị thông tin lớp học -->
        <div class="card classroom-card shadow-lg">
            <div class="card-body">
                <h2 data-bs-toggle="modal" data-bs-target="#classModal"><?php echo htmlspecialchars($classData['class_name']); ?></h2>
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

    <!-- Modal -->
    <div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="classModalLabel">Mã lớp học</h5>
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
                <button class="btn btn-secondary" id="editModeBtn">Chế độ chỉnh sửa</button>
                <button class="btn btn-primary" id="toggleTableBtn">Ẩn danh sách</button>
            </div>
        </div>
        <hr>
        
        <div id="attendanceList">
            <?php include 'attendance_list.php'; ?> <!-- Gọi file danh sách điểm danh -->
        </div>

        <div id="attendanceEdit" style="display: none;">
            <?php include 'attendance_edit.php'; ?> <!-- Gọi file chỉnh sửa danh sách điểm danh -->
        </div>
    </div>

    <!-- JavaScript để ẩn/hiện bảng -->
    <script>
        const attendanceList = document.getElementById('attendanceList');
        const attendanceEdit = document.getElementById('attendanceEdit');
        const toggleTableBtn = document.getElementById('toggleTableBtn');
        const editModeBtn = document.getElementById('editModeBtn');

        // Hàm để kiểm tra và ẩn/hiện các thành phần
        function toggleAttendanceList() {
            const isHidden = attendanceList.style.display === 'none';
            attendanceList.style.display = isHidden ? 'block' : 'none';
            editModeBtn.style.display = isHidden ? 'inline-block' : 'none'; // Ẩn hoặc hiện nút chế độ chỉnh sửa
            toggleTableBtn.textContent = isHidden ? 'Ẩn danh sách' : 'Hiện danh sách';
        }

        // Thay đổi chế độ chỉnh sửa
        function toggleEditMode() {
            const isEditVisible = attendanceEdit.style.display === 'block';
            attendanceList.style.display = isEditVisible ? 'block' : 'none';
            attendanceEdit.style.display = isEditVisible ? 'none' : 'block';
            toggleTableBtn.style.display = isEditVisible ? 'inline-block' : 'none'; // Hiện nút ẩn danh sách khi trở về danh sách
            editModeBtn.textContent = isEditVisible ? 'Chế độ chỉnh sửa' : 'Quay lại danh sách';
        }

        toggleTableBtn.addEventListener('click', toggleAttendanceList);
        editModeBtn.addEventListener('click', toggleEditMode);
    </script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
