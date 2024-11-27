<?php
session_start();

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../LayoutPages/navbar.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu người dùng đã gửi form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $semesterName = $_POST['semester_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isActive = isset($_POST['is_active']) ? 1 : 0; // Kiểm tra trạng thái hoạt động

    // Kiểm tra thông tin
    if (empty($semesterName) || empty($startDate) || empty($endDate)) {
        echo "Vui lòng điền đầy đủ thông tin.";
        exit();
    } else {
        // Thực hiện truy vấn để thêm học kỳ
        $stmt = $conn->prepare("CALL AddSemester(:semester_name, :start_date, :end_date, :is_active)");
        $stmt->bindParam(':semester_name', $semesterName);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':is_active', $isActive, PDO::PARAM_BOOL);
        
        try {
            // Execute the procedure
            if ($stmt->execute()) {
                echo "Thêm học kỳ thành công!";
                exit();
            } else {
                echo "Có lỗi xảy ra, vui lòng thử lại.";
                exit();
            }
        } catch (PDOException $e) {
            echo "Lỗi: " . $e->getMessage();
            exit();
        }
    }
}
