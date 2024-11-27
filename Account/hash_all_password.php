<?php
    include '../connect/connect.php';

    // Truy vấn để lấy tất cả người dùng với mật khẩu hiện tại
    $sql = "CALL GetAllUsers()";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $stmt->closeCursor();  // Close the cursor if needed

    // Duyệt qua tất cả người dùng để mã hóa mật khẩu
    foreach ($users as $user) {
        $plainPassword = $user->password; // Mật khẩu hiện tại

        // Kiểm tra xem mật khẩu đã được mã hóa hay chưa
        // Mật khẩu mã hóa bằng bcrypt thường dài khoảng 60 ký tự
        if (strlen($plainPassword) < 60) {
            // Mật khẩu chưa được mã hóa, tiến hành mã hóa
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu đã mã hóa vào cơ sở dữ liệu
            $updateSql = "CALL UpdateUserPassword(?, ?)";
            $updateStmt = $conn->prepare($updateSql);
    
            try {
                $updateStmt->execute([$hashedPassword, $user->user_id]);
                echo "Mã hóa mật khẩu cho user_id " . $user->user_id . " thành công.<br>";
            } catch (PDOException $e) {
                echo "Lỗi khi cập nhật mật khẩu cho user_id " . $user->user_id . ": " . $e->getMessage() . "<br>";
            }
        } else {
            // Mật khẩu đã được mã hóa, không cần mã hóa lại
            echo "user_id " . $user->user_id . " đã có mật khẩu mã hóa, bỏ qua.<br>";
        }
    }

    echo "Hoàn thành mã hóa!";
?>
