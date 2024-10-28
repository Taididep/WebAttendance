<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_view.php");
    exit;
}

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Chuẩn bị câu lệnh SQL để lấy thông tin giáo viên
$sql = "SELECT * FROM teachers WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin giáo viên
$teacherData = $stmt->fetchObject();
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            animation: backgroundAnimation 15s ease infinite;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        @keyframes backgroundAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            margin-top: 20px;
            border-radius: 15px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
            background: #ffffff;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.3);
        }

        .card-header {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: #fff;
            border-radius: 15px 15px 0 0;
            font-size: 1.8rem;
            font-weight: bold;
            text-align: center;
            padding: 20px 0;
        }

        .card-body {
            padding: 30px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: background-color 0.3s, transform 0.3s;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1.1rem;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
            transform: translateY(-2px);
        }

        .icon {
            margin-right: 5px;
            color: #007bff;
        }

        .text-center a {
            margin-left: 10px;
            font-weight: bold;
        }

        @media (max-width: 576px) {
            .card {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin cá nhân
        </div>
        <div class="card-body">
            <form id="editForm" method="POST" action="update_information.php">
                <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacherData->teacher_id); ?>">
                
                <div class="mb-3">
                    <label for="lastname" class="form-label">Họ</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($teacherData->lastname); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="firstname" class="form-label">Tên</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($teacherData->firstname); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="birthday" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($teacherData->birthday); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="Nam" <?php echo ($teacherData->gender == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo ($teacherData->gender == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="bi bi-envelope-fill icon"></i>Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($teacherData->email); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label"><i class="bi bi-telephone-fill icon"></i>Điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($teacherData->phone); ?>" required>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Cập nhật</button>
                    <a href="information.php" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
