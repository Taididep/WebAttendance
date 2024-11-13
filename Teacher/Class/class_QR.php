<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';

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

// Tạo URL cho class_detail.php với class_id
$detailUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/Teacher/index.php?class_id=' . urlencode($class_id); // Cập nhật đường dẫn đúng
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mã QR lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="../Css/class_QR.css">
</head>

<body>

    <div class="container text-center">
        <h2>Mã QR cho lớp học: <?php echo htmlspecialchars($classData['class_name']); ?></h2>
        <div id="qrCodeContainer">
            <div id="qrCode"></div>
        </div>
        <a href="class_detail.php?class_id=<?php echo urlencode($class_id); ?>" class="btn btn-danger">Đóng</a>
    </div>

    <div class="footer text-center">
        <p>© 2024 Hệ thống điểm danh lớp học</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../JavaScript/class_QR.js"></script>

</body>

</html>