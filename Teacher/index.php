<?php
session_start();
$basePath = '';
include __DIR__ . '/../Connect/connect.php';
include __DIR__ . '/../LayoutPages/navbar.php';
include __DIR__ . '/../Account/islogin.php';
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
            background-color: #eef2f3;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding-bottom: 60px; /* Để đảm bảo nội dung không bị che bởi footer */
            position: relative;
            min-height: 100vh; /* Đảm bảo body có chiều cao tối thiểu */
        }

        .hero {
            background-color: #ffffff;
            padding: 60px 20px;
            text-align: center;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .hero img {
            max-width: 80%;
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5rem;
            font-weight: bold;
        }

        p {
            color: #555;
            font-size: 1.2rem;
            margin-top: 15px;
        }

        .footer {
            text-align: center;
            padding: 5px;
            background-color: gray;
            color: white;
            position: absolute; /* Đặt footer ở vị trí cố định */
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
=======
    <link rel="stylesheet" href="Css/teacher.css">
>>>>>>> b2ceb12625af8c88f70f117c87617aeb87e81659
</head>

<body>
    <div class="container mt-5">
        <div class="hero">
            <h1>WELLCOME</h1>
            <img src="../Image/pngtree-school-cartoon-classroom-background-image_907637.png" alt="Hình ảnh cho trang chủ" class="img-fluid">
            <p class="mt-4">Cảm ơn bạn đã tham gia cùng chúng tôi trong hành trình giáo dục!</p>
        </div>
    </div>

    <div class="footer">
        <p class="text-light">&copy; 2024 Khoa Công Nghệ Thông Tin.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>