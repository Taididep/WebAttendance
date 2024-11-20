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
        <link rel="stylesheet" href="Css/teacher.css">
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