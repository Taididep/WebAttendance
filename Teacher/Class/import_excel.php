<?php
header('Content-Type: application/json'); // Đảm bảo phản hồi là JSON

$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Kiểm tra class_id từ URL
if (!isset($_POST['class_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu thông tin class_id.'
    ]);
    exit;
}
$class_id = $_POST['class_id'];

// Kiểm tra file được tải lên
if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] != 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tải lên file.'
    ]);
    exit;
}

// Đọc file Excel
$fileTmpPath = $_FILES['excel_file']['tmp_name'];
try {
    $spreadsheet = IOFactory::load($fileTmpPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    // Kết nối cơ sở dữ liệu với PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $insertCount = 0; // Đếm số lượng bản ghi được thêm

    foreach ($rows as $index => $row) {
        // Bỏ qua dòng tiêu đề
        if ($index == 1) {
            continue;
        }

        $student_id = trim($row['B']); // Cột B chứa student_id

        if (!empty($student_id)) {
            // Kiểm tra sự tồn tại của student_id trong class_students
            $query = $conn->prepare("SELECT 1 FROM class_students WHERE class_id = :class_id AND student_id = :student_id");
            $query->bindParam(':class_id', $class_id);
            $query->bindParam(':student_id', $student_id);
            $query->execute();

            if ($query->rowCount() == 0) {
                // Thêm mới vào class_students
                $insertQuery = $conn->prepare("INSERT INTO class_students (class_id, student_id, status) VALUES (:class_id, :student_id, 1)");
                $insertQuery->bindParam(':class_id', $class_id);
                $insertQuery->bindParam(':student_id', $student_id);
                if ($insertQuery->execute()) {
                    $insertCount++;
                }
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Thêm thành công $insertCount sinh viên."
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi xử lý file Excel: ' . $e->getMessage()
    ]);
}
exit;
