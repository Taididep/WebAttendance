<?php
    // Kết nối cơ sở dữ liệu
    if (!defined('SERVERNAME')) {
        define('SERVERNAME', '127.0.0.1');
    }

    if (!defined('USERNAME')) {
        define('USERNAME', 'root');
    }

    if (!defined('PASSWORD')) {
        define('PASSWORD', '');
    }

    if (!defined('DB')) {
        define('DB', 'db_atd');
    }

    try {
        $conn = new PDO("mysql:host=" . SERVERNAME . ";dbname=" . DB, USERNAME, PASSWORD);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>