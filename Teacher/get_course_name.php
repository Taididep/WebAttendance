<?php
    include '../Connect/connect.php'; // Kết nối đến cơ sở dữ liệu

    // Lấy mã khóa học từ yêu cầu AJAX
    $course_code = $_POST['course_code'];

    // Chuẩn bị câu lệnh SQL để lấy tên khóa học
    $sql = "SELECT course_name FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$course_code]);

    // Lấy kết quả
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Đóng kết quả của truy vấn trước

    // Trả về tên khóa học hoặc thông báo nếu không tìm thấy
    if ($course) {
        echo htmlspecialchars($course['course_name']);
    } else {
        echo ''; // Trả về chuỗi rỗng nếu không tìm thấy
    }
?>