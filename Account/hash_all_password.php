<?php
    include '../connect/connect.php';

    // Truy vấn để lấy tất cả người dùng với mật khẩu hiện tại
    $sql = "SELECT user_id, password FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Duyệt qua tất cả người dùng để mã hóa mật khẩu
    foreach ($users as $user) {
        $plainPassword = $user->password; // Mật khẩu hiện tại

        // Kiểm tra xem mật khẩu đã được mã hóa hay chưa
        // Mật khẩu mã hóa bằng bcrypt thường dài khoảng 60 ký tự
        if (strlen($plainPassword) < 60) {
            // Mật khẩu chưa được mã hóa, tiến hành mã hóa
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu đã mã hóa vào cơ sở dữ liệu
            $updateSql = "UPDATE users SET password = ? WHERE user_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([$hashedPassword, $user->user_id]);

            echo "Mã hóa mật khẩu cho user_id " . $user->user_id . " thành công.<br>";
        } else {
            // Mật khẩu đã được mã hóa, không cần mã hóa lại
            echo "user_id " . $user->user_id . " đã có mật khẩu mã hóa, bỏ qua.<br>";
        }
    }

    echo "Hoàn thành mã hóa!";
?>
