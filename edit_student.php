<?php
    include 'connect/connect.php'; 
    include 'function.php'; 

    // Kiểm tra xem có dữ liệu POST không
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['student_id'], $data['lastname'], $data['firstname'], $data['gender'], $data['birthday'])) {
            $student_id = $data['student_id'];
            $lastname = $data['lastname'];
            $firstname = $data['firstname'];
            $gender = $data['gender'];
            $birthday = $data['birthday'];
            $username = strtolower($firstname . '.' . $lastname);

            // Kiểm tra sự tồn tại của sinh viên
            if (!studentExists($conn, $student_id)) {
                echo json_encode(['success' => false, 'message' => 'Mã số sinh viên không tồn tại.']);
                exit();
            }

            // Cập nhật thông tin sinh viên
            if (updateStudent($conn, $student_id, $lastname, $firstname, $gender, $birthday) &&
                updateUser($conn, $student_id, $username)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật không thành công.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
    }
?>