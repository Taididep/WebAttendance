<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Kết nối đến cơ sở dữ liệu để lấy thông tin chi tiết về sinh viên
include '../Connect/connect.php';

// Chuẩn bị câu lệnh SQL để lấy thông tin sinh viên
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin sinh viên
$studentData = $stmt->fetchObject();
$stmt->closeCursor();  // Đóng kết quả của truy vấn trước

if ($studentData) {
    $greeting = htmlspecialchars($studentData->lastname) . " " . htmlspecialchars($studentData->firstname);
} else {
    $greeting = "Thông tin sinh viên không tìm thấy.";
}

// Truy vấn danh sách học kỳ bằng thủ tục lưu trữ
$sql_semesters = "CALL GetAllSemesters()"; // Gọi thủ tục
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->execute();
$semesters = $stmt_semesters->fetchAll(PDO::FETCH_ASSOC);
$stmt_semesters->closeCursor(); // Đóng kết quả của truy vấn trước
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Thanh điều hướng (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Trang sinh viên</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $greeting; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editModal">Thông tin cá nhân</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="../Account/logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Nội dung trang -->
    <div class="container mt-4">
        <!-- Form chọn học kỳ -->
        <form id="semesterForm">
            <div class="mb-3">
                <select class="form-select" id="semester" name="semester_id" required>
                    <option value="" disabled selected>Chọn học kỳ</option>
                    <?php foreach ($semesters as $semester): ?>
                        <option value="<?php echo $semester['semester_id']; ?>">
                            <?php echo htmlspecialchars($semester['semester_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Bảng lớp học -->
        <div id="classList" class="mt-4">
            <!-- Danh sách lớp -->
        </div>
    </div>

    <!-- Modal chỉnh sửa thông tin -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST" action="update_student.php">
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($studentData->student_id); ?>">
                        <div class="mb-3">
                            <label for="student_code" class="form-label">Mã số sinh viên</label>
                            <input type="text" class="form-control" id="student_code" name="student_code" value="<?php echo htmlspecialchars($user_id); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Họ</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($studentData->lastname); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstname" class="form-label">Tên</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($studentData->firstname); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($studentData->birthday); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="Nam" <?php echo $studentData->gender == 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                <option value="Nữ" <?php echo $studentData->gender == 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($studentData->email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($studentData->phone); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#semester').change(function() {
                var semesterId = $(this).val();
                if (semesterId) {
                    $.ajax({
                        url: 'get_classes.php', // URL đến file xử lý AJAX
                        type: 'POST',
                        data: { semester_id: semesterId },
                        success: function(data) {
                            $('#classList').html(data); // Hiển thị danh sách lớp học
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); // Ghi lại lỗi trong console
                            $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu: ' + error + '</div>');
                        }
                    });
                } else {
                    $('#classList').empty(); // Xóa danh sách lớp học nếu không có học kỳ được chọn
                }
            });
        });
    </script>
</body>
</html>