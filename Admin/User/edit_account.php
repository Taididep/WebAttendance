<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role_id = $_POST['role_id'];

    try {
        // Cập nhật tên đăng nhập
        $sql_update_user = "UPDATE users SET username = :username WHERE user_id = :user_id";
        $stmt_update_user = $conn->prepare($sql_update_user);
        $stmt_update_user->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt_update_user->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_update_user->execute();

        // Cập nhật vai trò
        $sql_delete_role = "DELETE FROM user_roles WHERE user_id = :user_id";
        $stmt_delete_role = $conn->prepare($sql_delete_role);
        $stmt_delete_role->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_delete_role->execute();

        $sql_insert_role = "INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)";
        $stmt_insert_role = $conn->prepare($sql_insert_role);
        $stmt_insert_role->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_insert_role->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt_insert_role->execute();

        $_SESSION['success'] = "Cập nhật tài khoản thành công.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi: " . $e->getMessage();
    }
    header('Location: user_manage.php');
    exit;
}
