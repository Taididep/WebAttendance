<?php
include __DIR__ . '/../../Connect/connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra dữ liệu đầu vào
    $class_id = $_POST['class_id'] ?? null;
    $student_id = $_POST['student_id'] ?? null;

    if (!$class_id || !$student_id) {
        $response['message'] = 'Thiếu dữ liệu để thêm sinh viên.';
        echo json_encode($response);
        exit;
    }

    try {
        // Kiểm tra sinh viên có tồn tại trong bảng students không
        $checkStudentSql = "SELECT COUNT(*) FROM students WHERE student_id = ?";
        $stmtCheckStudent = $conn->prepare($checkStudentSql);
        $stmtCheckStudent->execute([$student_id]);
        $studentExists = $stmtCheckStudent->fetchColumn();
        $stmtCheckStudent->closeCursor();

        if (!$studentExists) {
            $response['message'] = 'Sinh viên không tồn tại trong hệ thống.';
            echo json_encode($response);
            exit;
        }

        // Kiểm tra xem sinh viên đã có trong lớp chưa
        $checkSql = "SELECT COUNT(*) FROM class_students WHERE class_id = ? AND student_id = ?";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->execute([$class_id, $student_id]);
        $exists = $stmtCheck->fetchColumn();
        $stmtCheck->closeCursor();

        if ($exists) {
            $response['message'] = 'Sinh viên đã có trong lớp.';
            echo json_encode($response);
            exit;
        }

        // Thêm sinh viên mới vào lớp
        $insertSql = "INSERT INTO class_students (class_id, student_id, status) VALUES (?, ?, 0)";
        $stmtInsert = $conn->prepare($insertSql);
        $stmtInsert->execute([$class_id, $student_id]);
        $stmtInsert->closeCursor();

        $response['success'] = true;
        $response['message'] = 'Thêm sinh viên thành công.';
    } catch (PDOException $e) {
        $response['message'] = 'Lỗi: ' . $e->getMessage();
    }

    echo json_encode($response);
}
