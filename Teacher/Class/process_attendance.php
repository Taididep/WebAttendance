<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id có được gửi qua POST hay không
if (!isset($_POST['class_id']) || !isset($_POST['attendance'])) {
    echo 'Dữ liệu không hợp lệ.';
    exit;
}

// Lấy class_id từ POST
$class_id = $_POST['class_id'];

// Lấy dữ liệu điểm danh
$attendanceData = $_POST['attendance'];

// Lặp qua từng sinh viên và trạng thái điểm danh
foreach ($attendanceData as $student_id => $schedules) {
    foreach ($schedules as $schedule_id => $status) {
        // Kiểm tra nếu status không phải là 0 hoặc 1 hoặc 2 thì bỏ qua
        if ($status != 0 && $status != 1 && $status != 2) {
            continue;
        }
        
        // Kiểm tra xem điểm danh đã tồn tại hay chưa
        $sqlCheck = "SELECT * FROM attendances WHERE schedule_id = ? AND student_id = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->execute([$schedule_id, $student_id]);
        $attendanceRecord = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($attendanceRecord) {
            // Nếu đã tồn tại, cập nhật trạng thái
            $sqlUpdate = "UPDATE attendances SET status = ? WHERE schedule_id = ? AND student_id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->execute([$status, $schedule_id, $student_id]);
        } else {
            // Nếu chưa tồn tại, thêm mới
            $sqlInsert = "INSERT INTO attendances (schedule_id, student_id, status) VALUES (?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->execute([$schedule_id, $student_id, $status]);
        }
    }
}

// Chuyển hướng về trang điểm danh với thông báo thành công
header("Location: class_detail.php?class_id=" . urlencode($class_id) . "&message=Thay đổi đã được lưu thành công.");
exit;
