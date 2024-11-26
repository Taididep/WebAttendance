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

$sqlAttendance = "CALL GetSchedulesAndAttendanceByClassId(?)";
$stmtAttendance = $conn->prepare($sqlAttendance);
$stmtAttendance->execute([$class_id]);
$attendanceData = $stmtAttendance->fetchAll(PDO::FETCH_ASSOC);
$stmtAttendance->closeCursor();

$sqlSchedules = "CALL GetDistinctDatesByClassId(?)";
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor();

// Lấy thống kê điểm danh
$sqlReport = "SELECT student_id, total_present, total_late, total_absent FROM attendance_reports WHERE class_id = ?";
$stmtReport = $conn->prepare($sqlReport);
$stmtReport->execute([$class_id]);
$attendanceReports = $stmtReport->fetchAll(PDO::FETCH_ASSOC);
$stmtReport->closeCursor();

// Tạo file Excel mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Attendance");

// Tiêu đề cột
$headers = ["STT", "Mã sinh viên", "Họ đệm", "Tên", "Lớp", "Ngày sinh", "P", "L", "A"];
foreach ($schedules as $index => $schedule) {
    $headers[] = 'Buổi ' . ($index + 1) . ' (' . date('d/m', strtotime($schedule['date'])) . ')';
}

// Thêm tiêu đề vào hàng đầu tiên
$sheet->fromArray($headers, NULL, 'A1');

// Tạo mảng thống kê điểm danh cho từng sinh viên
$attendanceMap = [];
foreach ($attendanceData as $record) {
    $attendanceMap[$record['student_id']][$record['date']] = $record['status'];
}

// Chuyển đổi thống kê điểm danh thành mảng dễ sử dụng
$attendanceStats = [];
foreach ($attendanceReports as $report) {
    $attendanceStats[$report['student_id']] = [
        'total_present' => $report['total_present'] ?? 0,
        'total_late' => $report['total_late'] ?? 0,
        'total_absent' => $report['total_absent'] ?? 0,
    ];
}

// Thêm dữ liệu sinh viên và trạng thái điểm danh
$rowIndex = 2;
foreach ($students as $index => $student) {
    $row = [
        $index + 1,
        $student['student_id'],
        $student['lastname'],
        $student['firstname'],
        $student['class'],
        date('d/m/Y', strtotime($student['birthday'])),
        $attendanceStats[$student['student_id']]['total_present'], // P
        $attendanceStats[$student['student_id']]['total_late'],    // L
        $attendanceStats[$student['student_id']]['total_absent'],  // A
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

// Tính số lượng học sinh có mặt (1) và đi trễ (2) cho mỗi buổi
$attendanceCounts = [];
foreach ($schedules as $schedule) {
    $date = $schedule['date'];
    $countPresentAndLate = 0;
    foreach ($students as $student) {
        if (isset($attendanceMap[$student['student_id']][$date])) {
            $status = $attendanceMap[$student['student_id']][$date];
            // Nếu học sinh có mặt (1) hoặc đi trễ (2), cộng vào tổng điểm danh
            if ($status === '1' || $status === '2') {
                $countPresentAndLate++;
            }
        }
    }
    $attendanceCounts[] = $countPresentAndLate; // Cộng học sinh có mặt và đi trễ
}

// Thêm số lượng học sinh có mặt và đi trễ vào hàng tiếp theo (Tổng điểm danh)
$attendanceCountsRow = ['Tổng điểm danh', '', '', '', '', '', '', '']; // Đặt giá trị cho 8 cột đầu tiên
foreach ($attendanceCounts as $count) {
    $attendanceCountsRow[] = $count;
}

// Ghi dữ liệu số lượng học sinh có mặt và đi trễ vào hàng
$sheet->fromArray($attendanceCountsRow, NULL, 'A' . $rowIndex);

// Hợp nhất ô cho dòng "Tổng điểm danh"
$sheet->mergeCells("A{$rowIndex}:I{$rowIndex}"); // Hợp nhất từ cột A đến cột I ở hàng số $rowIndex
$sheet->getStyle("A{$rowIndex}:I{$rowIndex}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); // Canh giữa

// Debug: Kiểm tra giá trị của attendanceCountsRow
echo '<pre>';
print_r($attendanceCountsRow);
echo '</pre>';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="attendance_report_class_' . $class_id . '.xlsx"');
header('Cache-Control: max-age=0'); // Đảm bảo không lưu lại file trong cache

// Xuất file Excel
$writer = new Xlsx($spreadsheet);

// Dùng ob_clean() và flush() để đảm bảo xuất file đúng cách
ob_clean();
flush();
$writer->save('php://output');
exit;
