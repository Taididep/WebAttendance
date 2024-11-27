<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Lấy thông tin người dùng từ phiên
$user_id = $_SESSION['user_id'];

// Chuẩn bị câu lệnh SQL để lấy thông tin sinh viên
$sql = "CALL GetStudentById(?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);

// Lấy kết quả thông tin sinh viên
$studentData = $stmt->fetchObject();
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
    <link rel="stylesheet" href="../Css/information_edit.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin cá nhân
        </div>
        <div class="card-body">
            <form id="editForm" method="POST" action="update_information.php">
                <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($studentData->student_id); ?>">
                
                <div class="mb-3">
                    <label for="lastname" class="form-label">Họ</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($studentData->lastname); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="firstname" class="form-label">Tên</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($studentData->firstname); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="birthday" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($studentData->birthday); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="Nam" <?php echo ($studentData->gender == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                        <option value="Nữ" <?php echo ($studentData->gender == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($studentData->email); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($studentData->phone); ?>" required>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Cập nhật</button>
                    <a href="information.php" class="btn btn-primary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
