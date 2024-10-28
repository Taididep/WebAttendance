<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Chuẩn bị câu lệnh SQL để lấy thông tin giáo viên
$sql = "SELECT * FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin giáo viên
$teacherData = $stmt->fetchObject();
$stmt->closeCursor();

// Hàm để ẩn email
function maskEmail($email) {
    $parts = explode('@', $email);
    $masked = substr($parts[0], 0, 3) . '***' . '@' . $parts[1];
    return $masked;
}

// Hàm để ẩn số điện thoại
function maskPhone($phone) {
    return '***' . substr($phone, -3);
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #bbdefb);
        }
        .card {
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 15px 40px rgba(0, 0, 0, 0.25);
        }
        .card-header {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: #fff;
            border-radius: 10px 10px 0 0;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .card-body {
            background: #fff;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s forwards;
            padding: 30px;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            margin-left: 10px;
            color: #333;
            font-size: 1.1rem;
        }
        .btn {
            transition: background-color 0.3s, transform 0.3s;
            padding: 10px 20px;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        .icon {
            margin-right: 10px;
            color: #007bff;
        }
    </style>
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
                <span class="info-value"><?php echo htmlspecialchars($teacherData->lastname); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label">Tên:</span>
                <span class="info-value"><?php echo htmlspecialchars($teacherData->firstname); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label">Ngày sinh:</span>
                <span class="info-value"><?php echo htmlspecialchars($teacherData->birthday); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label">Giới tính:</span>
                <span class="info-value"><?php echo htmlspecialchars($teacherData->gender); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label"><i class="bi bi-envelope-fill icon"></i>Email:</span>
                <span class="info-value"><?php echo htmlspecialchars(maskEmail($teacherData->email)); ?></span>
            </div>
            <div class="mb-3">
                <span class="info-label"><i class="bi bi-telephone-fill icon"></i>Điện thoại:</span>
                <span class="info-value"><?php echo htmlspecialchars(maskPhone($teacherData->phone)); ?></span>
            </div>
            <a href="information_edit.php" class="btn btn-primary">Chỉnh sửa</a>
            <a href="../index.php" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
