<?php
session_start();
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra xem class_id, schedule_id và status có được gửi qua POST hay không
if (!isset($_POST['class_id']) || !isset($_POST['schedule_id'])) {
    echo 'Không tìm thấy thông tin lớp học hoặc lịch học.';
    exit;
}

// Lấy class_id, schedule_id và status từ POST
$class_id = $_POST['class_id'];
$schedule_id = $_POST['schedule_id'];
$status = $_POST['status'];

// Lấy user_id từ session
$user_id = $_SESSION['user_id']; // Giả sử user_id được lưu trong session

// Lấy thông tin ngày giờ buổi học từ bảng schedules
$sqlDate = "SELECT date FROM schedules WHERE schedule_id = ?";
$stmtDate = $conn->prepare($sqlDate);
$stmtDate->execute([$schedule_id]);
$scheduleDate = $stmtDate->fetch(PDO::FETCH_ASSOC);

if (!$scheduleDate) {
    echo 'Không tìm thấy thông tin lịch học.';
    exit;
}

// Chuyển đổi ngày buổi học thành đối tượng DateTime
$scheduleDateTime = new DateTime($scheduleDate['date']);
$currentDateTime = new DateTime(); // Thời gian hiện tại

if ($currentDateTime > $scheduleDateTime) {
    $status = 1; // Buổi học đã qua, điểm danh thành công
} else {
    $status = 2;
}

// Kiểm tra xem điểm danh đã tồn tại hay chưa
$sqlCheck = "SELECT * FROM attendances WHERE schedule_id = ? AND student_id = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->execute([$schedule_id, $user_id]);
$attendanceRecord = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($attendanceRecord) {
    // Nếu đã tồn tại, cập nhật trạng thái
    $sqlUpdate = "UPDATE attendances SET status = ? WHERE schedule_id = ? AND student_id = ?";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->execute([$status, $schedule_id, $user_id]);
} else {
    // Nếu chưa tồn tại, thêm mới
    $sqlInsert = "INSERT INTO attendances (schedule_id, student_id, status) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->execute([$schedule_id, $user_id, $status]);
}

// Chuyển hướng về trang lớp học với thông báo thành công
header("Location: ../Class/class_detail_list.php?class_id=" . urlencode($class_id) . "&message=Thay đổi đã được lưu thành công.");
exit;
