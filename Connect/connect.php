<?php
    // File: connect.php
    $servername = '127.0.0.1'; // Hoặc 'localhost'
    $username = 'root'; // Tên người dùng MySQL
    $password = ''; // Mật khẩu MySQL (nếu có)
    $dbname = 'db_atd'; // Tên cơ sở dữ liệu

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Kết nối thất bại: " . $e->getMessage());
    }
?>