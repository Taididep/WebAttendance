<?php
require __DIR__ . '/../../vendor/autoload.php'; // Đảm bảo đường dẫn tới thư viện PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Kết nối tới cơ sở dữ liệu
include __DIR__ . '/../../Connect/connect.php';

if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

$class_id = $_GET['class_id'];

// Lấy thông tin lớp học
$sqlClass = "SELECT class_name FROM classes WHERE class_id = ?";
$stmtClass = $conn->prepare($sqlClass);
$stmtClass->execute([$class_id]);
$classInfo = $stmtClass->fetch(PDO::FETCH_ASSOC);
$stmtClass->closeCursor();

if (!$classInfo) {
    echo 'Lớp học không tồn tại.';
    exit;
}

// Lấy thống kê điểm danh
$sqlStatistics = "
    SELECT 
        s.student_id, 
        s.lastname, 
        s.firstname, 
        r.total_present, 
        r.total_late, 
        r.total_absent
    FROM students s
    INNER JOIN attendance_reports r ON s.student_id = r.student_id
    WHERE r.class_id = ?";
$stmtStatistics = $conn->prepare($sqlStatistics);
$stmtStatistics->execute([$class_id]);
$statistics = $stmtStatistics->fetchAll(PDO::FETCH_ASSOC);
$stmtStatistics->closeCursor();

// Tạo file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Thống kê điểm danh");

// Tiêu đề bảng
$headers = ["STT", "Mã sinh viên", "Họ và tên", "Có mặt (P)", "Muộn (L)", "Vắng mặt (A)"];
$sheet->fromArray($headers, NULL, 'A1');

// Thêm dữ liệu vào file Excel
$rowIndex = 2;
foreach ($statistics as $index => $stat) {
    $row = [
        $index + 1,
        $stat['student_id'],
        $stat['lastname'] . ' ' . $stat['firstname'],
        $stat['total_present'] ?? 0,
        $stat['total_late'] ?? 0,
        $stat['total_absent'] ?? 0,
    ];
    $sheet->fromArray($row, NULL, "A{$rowIndex}");
    $rowIndex++;
}

// Định dạng tiêu đề
$headerStyle = $sheet->getStyle('A1:F1');
$headerStyle->getFont()->setBold(true);
$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Canh giữa dữ liệu cột
$dataStyle = $sheet->getStyle("A2:F{$rowIndex}");
$dataStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Định dạng cột
foreach (range('A', 'F') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Xuất file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"ThongKeDiemDanh_" . $classInfo['class_name'] . ".xlsx\"");
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
