<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra xem có gửi semester_id không
if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];

    // Kiểm tra xem có teacher_id trong session không
    if (isset($_SESSION['user_id'])) {
        $teacher_id = $_SESSION['user_id'];

        // Gọi thủ tục lấy danh sách lớp học theo semester_id và teacher_id
        $sql_classes = "CALL GetClassesBySemesterAndTeacher(?, ?)"; // Gọi thủ tục
        $stmt_classes = $conn->prepare($sql_classes);
        $stmt_classes->execute([$semester_id, $teacher_id]);
        $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
        $stmt_classes->closeCursor();

        if ($classes) {
            // Hiển thị bảng lớp học
            echo '<table class="table table-striped">';
            echo '<thead><tr"><th>STT</th><th>Tên lớp học</th><th>Tên môn học</th><th style="width: 1%;"></th></tr></thead>';
            echo '<tbody>';

            // Khởi tạo biến đếm cho số thứ tự
            $counter = 1;
            foreach ($classes as $class) {
                echo '<tr>';
                echo '<td style="padding-left: 17px; vertical-align: middle;" onclick="window.location.href=\'class_detail.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . $counter . '</td>';
                echo '<td style="vertical-align: middle;" onclick="window.location.href=\'class_detail_announcement.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['class_name']) . '</td>';
                echo '<td style="vertical-align: middle;" onclick="window.location.href=\'class_detail_announcement.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['course_name']) . '</td>';
                echo '<td>';
                echo '<div class="dropdown">';
                echo '<button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton' . $counter . '" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">';
                echo '<i class="bi bi-three-dots-vertical"></i>'; // Biểu tượng 3 chấm màu đen
                echo '</button>';
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $counter . '">';
                echo '<li><a class="dropdown-item" href="delete_class.php?class_id=' . htmlspecialchars($class['class_id']) . '" onclick="return confirm(\'Bạn có chắc chắn muốn hủy lớp học này không?\')">Hủy lớp</a></li>';
                echo '<li><a class="dropdown-item" href="class_edit.php?class_id=' . htmlspecialchars($class['class_id']) . '">Cập nhật lớp</a></li>';
                echo '<li><a class="dropdown-item" href="class_view.php?class_id=' . htmlspecialchars($class['class_id']) . '">Xem lịch học</a></li>';
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
        echo '<div class="alert alert-danger">Không tìm thấy thông tin giáo viên.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Không có thông tin học kỳ.</div>';
}
?>

<link rel="stylesheet" href="../Css/get_classes.css">