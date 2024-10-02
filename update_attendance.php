<?php
include 'connect/connect.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if ($data && is_array($data)) {
    try {
        $conn->beginTransaction();
        
        foreach ($data as $attendance) {
            $student_id = $attendance['student_id'];
            $date = $attendance['date'];
            $status = $attendance['status'];

            // Cập nhật điểm danh
            $stmt = $conn->prepare("
                INSERT INTO attendances (class_id, student_id, attendance_date, status)
                VALUES (:class_id, :student_id, :attendance_date, :status)
                ON DUPLICATE KEY UPDATE status = :status, updated_at = CURRENT_TIMESTAMP()
            ");
            $class_id = 1; // Thay đổi class_id theo lớp bạn muốn cập nhật
            $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':attendance_date', $date);
            $stmt->bindParam(':status', $status);
            
            $stmt->execute();
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Cập nhật điểm danh thành công!']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => 'Lỗi thực thi câu lệnh: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Không có dữ liệu nào để cập nhật.']);
}

$conn = null; // Đóng kết nối
?>