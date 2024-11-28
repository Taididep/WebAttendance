<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Chuẩn bị câu lệnh SQL để lấy thông tin admin
$sql = "CALL GetAdminById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin admin
$adminData = $stmt->fetchObject();
$stmt->closeCursor();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../Css/information.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-circle"></i> Thông tin cá nhân
        </div>
        <div class="card-body">
            <div class="mb-3">
                <span class="info-label">Họ:</span>
                <span class="info-value"><?php echo htmlspecialchars($adminData->lastname); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label">Tên:</span>
                <span class="info-value"><?php echo htmlspecialchars($adminData->firstname); ?></span>
            </div>
            <?php if (isset($adminData->birthday)): ?>
            <div class="mb-3">
                <span class="info-label">Ngày sinh:</span>
                <span class="info-value"><?php echo htmlspecialchars($adminData->birthday); ?></span>
            </div>
            <?php endif; ?>
            <div class="mb-3">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo htmlspecialchars($adminData->email); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label">Điện thoại:</span>
                <span class="info-value"><?php echo htmlspecialchars($adminData->phone); ?></span>
            </div>
            <a href="information_edit.php" class="btn btn-primary">Chỉnh sửa</a>
            <a href="../index.php" class="btn btn-primary">Quay lại</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>