<?php
// Kết nối cơ sở dữ liệu
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra nếu người dùng gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $classId = isset($_POST['class_id']) ? $_POST['class_id'] : null;
    $scheduleDate = isset($_POST['schedule_date']) ? $_POST['schedule_date'] : null;  // Chỉnh sửa tên tham số
    $startTime = isset($_POST['start_time']) ? $_POST['start_time'] : null;
    $endTime = isset($_POST['end_time']) ? $_POST['end_time'] : null;

    // Kiểm tra xem các trường cần thiết có được nhập hay không
    if ($classId && $scheduleDate && $startTime && $endTime) {
        try {
            // Gọi thủ tục kiểm tra lịch học có tồn tại hay không
            $stmt = $conn->prepare("CALL CheckScheduleExists(?, ?, ?, ?, @exists_flag)");
            $stmt->execute([$classId, $scheduleDate, $startTime, $endTime]);

            // Lấy kết quả trả về từ thủ tục
            $result = $conn->query("SELECT @exists_flag AS exists_flag");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $scheduleExists = $row['exists_flag'];

            if ($scheduleExists > 0) {
                echo "Lỗi: Lịch học này đã tồn tại.";
                exit;
            }

            // Thêm lịch học mới vào bảng schedules
            $sql = "INSERT INTO schedules (class_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$classId, $scheduleDate, $startTime, $endTime]);

            // Kiểm tra kết quả
            echo "<script>alert('Lịch học đã được tạo thành công!');</script>";
            echo "<script>window.location.href = 'class_view.php?class_id=$classId';</script>";

        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    } else {
        // Nếu có trường nào chưa được điền
        echo "Vui lòng điền đầy đủ thông tin!";
    }
}
?>
