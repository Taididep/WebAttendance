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

    <style>
        .hero {
            background-color: #f8f9fa;
            padding: 50px 20px;
            text-align: center;
            border-radius: 5px;
        }

        .hero img {
            max-width: 100%;
            height: auto;
        }
    </style>

    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="hero">
            <h1>Welcome</h1>
            <img src="../Image/pngtree-school-cartoon-classroom-background-image_907637.png" alt="Hình ảnh cho trang chủ" class="img-fluid">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>