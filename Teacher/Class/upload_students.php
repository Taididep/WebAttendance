<?php
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $class_id = $_POST['class_id'];
    $file = $_FILES['excel_file'];

    // Kiểm tra xem file có lỗi không
    if ($file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error: " . $file['error']); // Ghi log lỗi
        echo json_encode(['success' => false, 'message' => 'Có lỗi khi tải lên file. Mã lỗi: ' . $file['error']]);
        exit;
    }

    // Đọc file Excel
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Lặp qua từng hàng dữ liệu và thêm sinh viên vào cơ sở dữ liệu
    foreach ($sheetData as $index => $row) {
        $student_id = $row['A']; // Mã sinh viên
        $lastname = $row['B']; // Họ đệm
        $firstname = $row['C']; // Tên
        $gender = $row['D']; // Giới tính
        $class = $row['E'];  // Lớp
        $birthday = $row['F']; // Ngày sinh

        // Thêm sinh viên vào cơ sở dữ liệu
        $sql = "INSERT INTO students (student_id, lastname, firstname, gender, class, birthday, class_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt->execute([$student_id, $lastname, $firstname, $gender, $class, $birthday, $class_id])) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi khi thêm sinh viên: ' . implode(", ", $stmt->errorInfo())]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Đã thêm sinh viên thành công.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Không có file nào được tải lên.']);
}
?>