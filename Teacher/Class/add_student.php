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
        $checkStudentSql = "CALL CheckStudentExistence(?, @p_exists)";
        $stmtCheckStudent = $conn->prepare($checkStudentSql);
        $stmtCheckStudent->execute([$student_id]);
        $stmtCheckStudent->closeCursor();

        // Lấy giá trị từ biến OUT
        $existsResult = $conn->query("SELECT @p_exists")->fetch(PDO::FETCH_ASSOC);
        $studentExists = $existsResult['@p_exists'];

        if (!$studentExists) {
            $response['message'] = 'Sinh viên không tồn tại trong hệ thống.';
            echo json_encode($response);
            exit;
        }

        // Kiểm tra xem sinh viên đã có trong lớp chưa
        $checkSql = "CALL CheckClassStudentExistence(?, ?, @p_exists)";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->execute([$class_id, $student_id]);
        $stmtCheck->closeCursor();

        // Lấy giá trị từ biến OUT
        $existsResult = $conn->query("SELECT @p_exists")->fetch(PDO::FETCH_ASSOC);
        $exists = $existsResult['@p_exists'];

        if ($exists) {
            $response['message'] = 'Sinh viên đã có trong lớp.';
            echo json_encode($response);
            exit;
        }

        // Thêm sinh viên mới vào lớp
        $insertSql = "CALL InsertClassStudent(?, ?)";
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
