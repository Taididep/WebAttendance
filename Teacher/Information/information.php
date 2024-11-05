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
function maskEmail($email)
{
    $parts = explode('@', $email);
    $masked = substr($parts[0], 0, 3) . '***' . '@' . $parts[1];
    return $masked;
}

// Hàm để ẩn số điện thoại
function maskPhone($phone)
{
    return '***' . substr($phone, -3);
}

// Xử lý tải ảnh lên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar'])) {
    $targetDir = "../../Image/Avatar/";
    $targetFile = $targetDir . basename($_FILES["avatar"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Kiểm tra xem ảnh có thực sự là ảnh không
    $check = getimagesize($_FILES["avatar"]["tmp_name"]);
    if ($check === false) {
        echo "Tập tin không phải là hình ảnh.";
        $uploadOk = 0;
    }

    // Kiểm tra xem tệp đã tồn tại không
    if (file_exists($targetFile)) {
        echo "Xin lỗi, tệp đã tồn tại.";
        $uploadOk = 0;
    }

    // Nếu mọi thứ ổn, hãy tải tệp lên
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
            // Cập nhật đường dẫn ảnh vào cơ sở dữ liệu
            $sql = "UPDATE teachers SET avatar = ? WHERE teacher_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$targetFile, $user_id]);
            $teacherData->avatar = $targetFile; // Cập nhật dữ liệu trong phiên
            echo "Ảnh đã được tải lên.";
        } else {
            echo "Xin lỗi, đã xảy ra lỗi khi tải tệp lên.";
        }
    }
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
        }

        .card-header {
            background: linear-gradient(to right, #007bff, #0056b3);
            color: #fff;
            border-radius: 10px 10px 0 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .card-body {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 30px;
        }

        .info {
            flex: 1;
            /* Chiếm 50% chiều rộng */
            margin-right: 20px;
            /* Khoảng cách giữa thông tin và ảnh */
        }

        .avatar {
            flex: 1;
            /* Chiếm 50% chiều rộng */
            text-align: center;
        }

        .avatar img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            cursor: pointer;
            /* Thay đổi con trỏ chuột khi di chuột vào ảnh */
        }

        .change-text {
            position: absolute;
            bottom: 10px;
            /* Điều chỉnh vị trí theo ý muốn */
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.8);
            /* Nền trắng mờ */
            padding: 5px;
            border-radius: 5px;
            display: none;
            /* Ẩn mặc định */
            font-size: 1rem;
            color: #333;
        }

        .avatar label:hover .change-text {
            display: block;
            /* Hiện khi di chuột vào label */
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

        .hidden-input {
            display: none;
            /* Ẩn input file */
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
                <div class="info">
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

                <div class="avatar">
                    <form action="" method="post" enctype="multipart/form-data" id="avatarForm">
                        <input type="file" name="avatar" id="avatar" class="hidden-input" accept="image/*" onchange="handleFileSelect(event);">
                        <label for="avatar" style="position: relative;">
                            <?php if (!empty($teacherData->avatar)): ?>
                                <img src="<?php echo htmlspecialchars($teacherData->avatar); ?>" alt="Ảnh đại diện" id="avatarImage" style="cursor: pointer;">
                            <?php else: ?>
                                <img src="../../Image/Avatar/avatar_default.png" alt="Ảnh đại diện" style="width: 250px; height: 250px; border-radius: 50%; cursor: pointer;">
                            <?php endif; ?>
                            <span class="change-text">Thay đổi</span>
                        </label>
                    </form>
                </div>



            </div>
        </div>
    </div>

    <script>
        function handleFileSelect(event) {
            // Kích hoạt gửi biểu mẫu chỉ khi tệp đã được chọn
            if (event.target.files.length > 0) {
                document.getElementById('avatarForm').submit(); // Gửi biểu mẫu
            }
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>