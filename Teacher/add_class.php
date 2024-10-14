<?php
    session_start();
    include '../Connect/connect.php'; // Kết nối đến cơ sở dữ liệu

    // Kiểm tra người dùng đã đăng nhập
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../index.php");
        exit;
    }

    // Lấy thông tin từ form
    $class_name = $_POST['class_name'];
    $course_id = $_POST['course_code'];
    $semester_id = $_POST['semester_id'];
    $teacher_id = $_SESSION['user_id'];

    // Tạo UUID cho class_id
    $class_id = uniqid('', true);

    // Chuẩn bị câu lệnh SQL để thêm lớp học
    $sql = "INSERT INTO classes (class_id, class_name, course_id, semester_id, teacher_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$class_id, $class_name, $course_id, $semester_id, $teacher_id]);
    $stmt->closeCursor();

    if ($result) {
        // Trả về danh sách lớp học
        $sql_classes = "SELECT * FROM classes WHERE semester_id = ?";
        $stmt_classes = $conn->prepare($sql_classes);
        $stmt_classes->execute([$semester_id]);
        $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
        $stmt_classes->closeCursor();

        // Tạo HTML danh sách lớp học
        $output = '<ul class="list-group">';
        foreach ($classes as $class) {
            $output .= '<li class="list-group-item">' . htmlspecialchars($class['class_name']) . '</li>';
        }
        $output .= '</ul>';

        echo json_encode(['message' => 'Thêm lớp học thành công!', 'classList' => $output]);
    } else {
        echo json_encode(['message' => 'Có lỗi xảy ra. Vui lòng thử lại.']);
    }
?>