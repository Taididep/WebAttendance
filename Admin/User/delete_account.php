<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        $sql_delete_user = "DELETE FROM users WHERE user_id = :user_id";
        $stmt_delete_user = $conn->prepare($sql_delete_user);
        $stmt_delete_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_delete_user->execute();

        $_SESSION['success'] = "Xóa tài khoản thành công.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    header('Location: user_manage.php');
    exit;
}
