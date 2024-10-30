<?php
require __DIR__ . '/../../vendor/autoload.php'; // Đảm bảo đường dẫn đến thư viện PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Kết nối tới cơ sở dữ liệu
$basePath = '../'; // Đường dẫn gốc
include __DIR__ . '/../../Connect/connect.php';

if (!isset($_GET['class_id'])) {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

$class_id = $_GET['class_id'];

// Lấy thông tin sinh viên và điểm danh
$sqlStudents = "CALL GetStudentsByClassId(?)";
$stmtStudents = $conn->prepare($sqlStudents);
$stmtStudents->execute([$class_id]);
$students = $stmtStudents->fetchAll(PDO::FETCH_ASSOC);
$stmtStudents->closeCursor();

$sqlAttendance = "CALL GetAttendanceByClassId(?)";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->execute([$class_id]);
$attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
$stmtAttendance->closeCursor();

$sqlSchedules = "SELECT schedule_id, date FROM schedules WHERE class_id = ?";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor();

$attendanceMap = [];
foreach ($attendanceData as $record) {
    $attendanceMap[$record['student_id']][$record['date']] = $record['status'];
}

// Tạo file Excel mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Attendance");

// Tiêu đề cột
$headers = ["STT", "Mã sinh viên", "Họ đệm", "Tên", "Lớp", "Ngày sinh"];
foreach ($schedules as $index => $schedule) {
    $headers[] = 'Buổi ' . ($index + 1) . ' (' . date('d/m', strtotime($schedule['date'])) . ')';
}

// Thêm tiêu đề vào hàng đầu tiên
$sheet->fromArray($headers, NULL, 'A1');

// Thêm dữ liệu sinh viên và trạng thái điểm danh
$rowIndex = 2;
foreach ($students as $index => $student) {
    $row = [
        $index + 1,
        $student['student_id'],
        $student['lastname'],
        $student['firstname'],
        $student['class'],
        date('d/m/Y', strtotime($student['birthday']))
    ];

    // Điểm danh từng buổi
    foreach ($schedules as $schedule) {
        $status = $attendanceMap[$student['student_id']][$schedule['date']] ?? '0';
        $row[] = $status; // 1, 0, hoặc 2
    }

    // Thêm hàng vào file Excel
    $sheet->fromArray($row, NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Xuất file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="attendance.xlsx"');
$writer = new Xlsx($spreadsheet);
$writer->save("php://output");
exit;
