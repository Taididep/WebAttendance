<?php
require __DIR__ . '/../../vendor/autoload.php'; // Đảm bảo đường dẫn tới thư viện PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Title;

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
$sqlSummary = "
    SELECT 
        SUM(r.total_present) AS total_present,
        SUM(r.total_late) AS total_late,
        SUM(r.total_absent) AS total_absent
    FROM attendance_reports r
    WHERE r.class_id = ?";
$stmtSummary = $conn->prepare($sqlSummary);
$stmtSummary->execute([$class_id]);
$summary = $stmtSummary->fetch(PDO::FETCH_ASSOC);
$stmtSummary->closeCursor();

if (!$summary) {
    echo 'Không có dữ liệu điểm danh.';
    exit;
}

// Lấy danh sách các buổi học đã qua và hiện tại
$sqlSchedules = "SELECT schedule_id, date FROM schedules WHERE class_id = ? AND date <= CURDATE()"; // Lấy các buổi học không phải tương lai
$stmtSchedules = $conn->prepare($sqlSchedules);
$stmtSchedules->execute([$class_id]);
$schedules = $stmtSchedules->fetchAll(PDO::FETCH_ASSOC);
$stmtSchedules->closeCursor();

// Tạo file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Thống kê điểm danh");

// Thêm dữ liệu thống kê
$data = [
    ["Trạng thái", "Số lượng"],
    ["Có mặt", $summary['total_present'] ?? 0],
    ["Muộn", $summary['total_late'] ?? 0],
    ["Vắng mặt", $summary['total_absent'] ?? 0],
];
$sheet->fromArray($data, NULL, 'A1');

// Định dạng tiêu đề
$headerStyle = $sheet->getStyle('A1:B1');
$headerStyle->getFont()->setBold(true);
$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// Định dạng cột
foreach (range('A', 'B') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Cập nhật tiêu đề cột với buổi và ngày (quá khứ và hiện tại)
$headers = ["Trạng thái", "Số lượng"]; // Tiêu đề ban đầu
foreach ($schedules as $schedule) {
    $headers[] = 'Buổi ' . $schedule['schedule_id'] . ' (' . date('d/m', strtotime($schedule['date'])) . ')';
}

// Thêm tiêu đề vào Excel
$sheet->fromArray($headers, NULL, 'A1');

// Thêm dữ liệu thống kê điểm danh
$data = [
    ["Có mặt", $summary['total_present'] ?? 0],
    ["Muộn", $summary['total_late'] ?? 0],
    ["Vắng mặt", $summary['total_absent'] ?? 0],
];

// Bắt đầu từ dòng 2
$rowIndex = 2;
foreach ($data as $row) {
    $sheet->fromArray($row, NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Thêm biểu đồ
$dataSeriesLabels = [
    new DataSeriesValues('String', "'Thống kê điểm danh'!\$A\$2:\$A\$4", null, 3), // Tên trạng thái: Có mặt, Muộn, Vắng mặt
];
$xAxisTickValues = [
    new DataSeriesValues('String', "'Thống kê điểm danh'!\$A\$2:\$A\$4", null, 3), // Trục X: Trạng thái
];
$dataSeriesValues = [
    new DataSeriesValues('Number', "'Thống kê điểm danh'!\$B\$2:\$B\$4", null, 3), // Giá trị: Số lượng
];

// Tạo biểu đồ
$series = new DataSeries(
    DataSeries::TYPE_BARCHART, // Biểu đồ cột
    DataSeries::GROUPING_CLUSTERED, // Nhóm cột
    range(0, count($dataSeriesValues) - 1), // Các chuỗi dữ liệu
    $dataSeriesLabels,
    $xAxisTickValues,
    $dataSeriesValues
);
$series->setPlotDirection(DataSeries::DIRECTION_COL);

// Tạo khu vực biểu đồ
$plotArea = new PlotArea(null, [$series]);
$legend = new Legend(Legend::POSITION_RIGHT, null, false);
$title = new Title('Thống kê điểm danh');

// Tạo biểu đồ
$chart = new Chart(
    'Thống kê điểm danh', // Tên biểu đồ
    $title,
    $legend,
    $plotArea
);

// Vị trí biểu đồ trong Excel
$chart->setTopLeftPosition('D1');
$chart->setBottomRightPosition('M20');

// Thêm biểu đồ vào sheet
$sheet->addChart($chart);

// Xuất file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"ThongKeDiemDanh_" . $classInfo['class_name'] . ".xlsx\"");
$writer = new Xlsx($spreadsheet);
$writer->setIncludeCharts(true); // Bao gồm biểu đồ
$writer->save("php://output");
exit;
