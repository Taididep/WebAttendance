<?php
session_start();

// Kết nối cơ sở dữ liệu
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
include __DIR__ . '/../../Account/islogin.php';

// Kiểm tra nếu có semester_id trong URL
if (isset($_GET['semester_id']) && is_numeric($_GET['semester_id'])) {
    $semesterId = $_GET['semester_id'];

    // Lấy thông tin học kỳ từ cơ sở dữ liệu
    $stmt = $conn->prepare("CALL GetSemesterById(:semester_id)");
    $stmt->bindParam(':semester_id', $semesterId, PDO::PARAM_INT);
    $stmt->execute();
    $semester = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$semester) {
        // Nếu không tìm thấy học kỳ, chuyển hướng về trang quản lý học kỳ
        header("Location: {$basePath}Semester/semester_manage.php?message=not_found");
        exit();
    }
} else {
    // Nếu không có semester_id hợp lệ, chuyển hướng về trang quản lý học kỳ
    header("Location: {$basePath}Semester/semester_manage.php?message=invalid_id");
    exit();
}

// Kiểm tra nếu form được gửi đi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $semesterId = $_GET['semester_id']; // Nhận semester_id từ URL
    $semesterName = $_POST['semester_name'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Kiểm tra thông tin
    if (empty($semesterName) || empty($startDate) || empty($endDate)) {
        echo "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Cập nhật dữ liệu học kỳ
        $sql = "UPDATE semesters SET semester_name = ?, start_date = ?, end_date = ?, is_active = ? WHERE semester_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt->execute([$semesterName, $startDate, $endDate, $isActive, $semesterId])) {
            echo "Cập nhật học kỳ thành công";
        } else {
            echo "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
