<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo 'Unauthorized access.';
    exit;
}

// Kết nối đến cơ sở dữ liệu
include '../Connect/connect.php';

// Kiểm tra xem có gửi semester_id không
if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];

    // Gọi thủ tục lấy danh sách lớp học theo semester_id
    $sql_classes = "CALL GetClassesBySemester(?)"; // Gọi thủ tục
    $stmt_classes = $conn->prepare($sql_classes);
    $stmt_classes->execute([$semester_id]);
    $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
    $stmt_classes->closeCursor();

    if ($classes) {
        // Hiển thị bảng lớp học
        echo '<h3>Danh sách lớp học</h3>';
        echo '<table class="table table-striped">';
        echo '<thead><tr><th>Số thứ tự</th><th>Tên lớp học</th><th>Tên khóa học</th></tr></thead>';
        echo '<tbody>';
        
        // Khởi tạo biến đếm cho số thứ tự
        $counter = 1;
        foreach ($classes as $class) {
            echo '<tr onclick="window.location.href=\'class_detail.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">';
            echo '<td>' . $counter . '</td>';  // Hiển thị số thứ tự
            echo '<td>' . htmlspecialchars($class['class_name']) . '</td>';
            echo '<td>' . htmlspecialchars($class['course_name']) . '</td>';
            echo '</tr>';
            $counter++; // Tăng số thứ tự cho mỗi dòng
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-warning">Không có lớp học nào trong học kỳ này.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Không có thông tin học kỳ.</div>';
}
?>

<style>
    tr:hover {
        background-color: #f0f8ff;
        cursor: pointer;
    }
</style>
