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
    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #80deea); /* Gradient màu nền */
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 50px;
            border-radius: 15px;
            padding: 30px;
            background-color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s; /* Hiệu ứng chuyển động */
        }
        .container:hover {
            transform: translateY(-5px); /* Di chuyển nhẹ khi hover */
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); /* Đổi bóng khi hover */
        }
        h2 {
            color: #00796b; /* Màu tiêu đề */
            margin-bottom: 20px;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Đổ bóng cho tiêu đề */
        }
        #qrCodeContainer {
            text-align: center;
            margin-top: 20px;
        }
        #qrCode {
            width: 300px;
            height: 300px;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            background-color: #ffffff; /* Màu nền cho mã QR */
            padding: 10px;
        }
        .btn-danger {
            margin-top: 20px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s; /* Hiệu ứng chuyển màu */
        }
        .btn-danger:hover {
            background-color: #c62828; /* Màu khi hover */
            transform: scale(1.05); /* Phóng to nút khi hover */
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #6c757d;
        }
        @media (max-width: 576px) {
            h2 {
                font-size: 1.5rem; /* Thay đổi kích thước tiêu đề trên màn hình nhỏ */
            }
            #qrCode {
                width: 80%; /* Kích thước mã QR trên màn hình nhỏ */
                height: auto;
            }
        }
    </style>
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

<script>
    // Tạo mã QR với URL chuyển hướng
    const detailUrl = '<?php echo $detailUrl; ?>';
    const qrCodeContainer = document.getElementById('qrCode');
    new QRCode(qrCodeContainer, {
        text: detailUrl,
        width: 300,
        height: 300,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
