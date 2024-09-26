<?php
require 'vendor/autoload.php'; // Đảm bảo đã cài đặt PHPSpreadsheet qua Composer
include 'function.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Lấy class_id từ form
$class_id = $_POST['class_id'];

// Lấy dữ liệu sinh viên và điểm danh
$result_students = getStudentsByClassId($conn, $class_id);
$dates = getAttendanceDatesByClassId($conn, $class_id);
$attendances = getAttendanceDataByClassId($conn, $class_id);

// Tạo đối tượng Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Thiết lập tiêu đề các cột
$sheet->setCellValue('A1', 'STT');
$sheet->setCellValue('B1', 'Mã số SV');
$sheet->setCellValue('C1', 'Họ');
$sheet->setCellValue('D1', 'Tên');
$sheet->setCellValue('E1', 'Giới tính');
$sheet->setCellValue('F1', 'Ngày sinh');

// Thiết lập các tiêu đề cột cho ngày điểm danh
$column = 'G';
foreach ($dates as $date) {
    $sheet->setCellValue($column . '1', date('d/m', strtotime($date)));
    $column++;
}

// Thêm dữ liệu sinh viên và điểm danh
$row = 2;
foreach ($result_students as $student) {
    $sheet->setCellValue('A' . $row, $student['stt']);
    $sheet->setCellValue('B' . $row, $student['student_id']);
    $sheet->setCellValue('C' . $row, $student['lastname']);
    $sheet->setCellValue('D' . $row, $student['firstname']);
    $sheet->setCellValue('E' . $row, $student['gender']);
    $sheet->setCellValue('F' . $row, date('d/m/Y', strtotime($student['birthday'])));

    // Điền dữ liệu điểm danh
    $column = 'G';
    foreach ($dates as $date) {
        if (isset($attendances[$student['student_id']][$date])) {
            $sheet->setCellValue($column . $row, $attendances[$student['student_id']][$date]);
        } else {
            $sheet->setCellValue($column . $row, 'Absent');
        }
        $column++;
    }
    $row++;
}

// Xuất file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'attendance_list_class_' . $class_id . '.xlsx';

// Gửi header để trình duyệt tải file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Xuất file
$writer->save('php://output');
exit;
?>
