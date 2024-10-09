<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    // Nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
    header("Location: ../index.php");
    exit;
}

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Kết nối đến cơ sở dữ liệu để lấy thông tin chi tiết về giáo viên
include '../Connect/connect.php';

// Chuẩn bị câu lệnh SQL để gọi thủ tục lưu trữ lấy thông tin giáo viên
$sql = "CALL GetTeacherInfo(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin giáo viên
$teacherData = $stmt->fetchObject();
$stmt->closeCursor();  // Đóng kết quả của truy vấn trước

if ($teacherData) {
    $greeting = htmlspecialchars($teacherData->lastname) . " " . htmlspecialchars($teacherData->firstname);
} else {
    $greeting = "Thông tin giáo viên không tìm thấy.";
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
    <title>Trang giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <!-- Thanh điều hướng (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Trang giáo viên</a>
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
                                <li><a class="dropdown-item" href="#">Thông tin cá nhân</a></li>
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
                        error: function() {
                            $('#classList').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải dữ liệu.</div>');
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
