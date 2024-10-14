<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo 'Unauthorized access.';
    exit;
}

// Kết nối đến cơ sở dữ liệu
include '../../Connect/connect.php';

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
        // echo '<h4>Danh sách lớp học</h4>';
        // echo '<hr></hr>';
        echo '<table class="table table-striped">';
        echo '<thead><tr"><th>Số thứ tự</th><th>Tên lớp học</th><th>Tên môn học</th><th style="width: 1%;"></th></tr></thead>';
        echo '<tbody>';
        
        // Khởi tạo biến đếm cho số thứ tự
        $counter = 1;
        foreach ($classes as $class) {
            echo '<tr>';
            echo '<td onclick="window.location.href=\'detail_class.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . $counter . '</td>';
            echo '<td onclick="window.location.href=\'detail_class.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['class_name']) . '</td>';
            echo '<td onclick="window.location.href=\'detail_class.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['course_name']) . '</td>';
            echo '<td>';
            echo '<div class="dropdown">';
            echo '<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton' . $counter . '" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">';
            echo '<i class="bi bi-three-dots-vertical"></i>'; // Biểu tượng 3 chấm màu đen
            echo '</button>';
            echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $counter . '">';
            echo '<li><a class="dropdown-item" href="delete_class.php?class_id=' . htmlspecialchars($class['class_id']) . '" onclick="return confirm(\'Bạn có chắc chắn muốn hủy lớp học này không?\')">Xóa</a></li>';
            echo '<li><a class="dropdown-item" href="edit_class.php?class_id=' . htmlspecialchars($class['class_id']) . '">Sửa</a></li>';
            echo '</ul>';
            echo '</div>';
            echo '</td>';
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
    tbody tr:hover {
        background-color: #f0f8ff;
        cursor: pointer;
    }
    tbody .dropdown-toggle::after {
        display: none; /* Ẩn mũi tên mặc định của Bootstrap */
    }
    thead tr {
    background-color: inherit;
    cursor: default;
    }
</style>
