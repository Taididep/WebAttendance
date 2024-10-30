<?php
session_start();
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra xem có gửi semester_id không
if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];

    // Kiểm tra xem có student_id trong session không
    if (isset($_SESSION['user_id'])) {
        $student_id = $_SESSION['user_id']; // Lấy user_id làm student_id

        // Gọi thủ tục lấy danh sách lớp học theo semester_id và student_id
        $sql_classes = "CALL GetClassesBySemesterAndStudent(?, ?)"; // Gọi thủ tục
        $stmt_classes = $conn->prepare($sql_classes);
        $stmt_classes->execute([$semester_id, $student_id]);
        $classes = $stmt_classes->fetchAll(PDO::FETCH_ASSOC);
        $stmt_classes->closeCursor();

        if ($classes) {
            // Hiển thị bảng lớp học
            echo '<table class="table table-striped">';
            echo '<thead><tr"><th>STT</th><th>Tên lớp học</th><th>Tên môn học</th><th>Giáo viên</th><th style="width: 1%;"></th></tr></thead>';
            echo '<tbody>';

            // Khởi tạo biến đếm cho số thứ tự
            $counter = 1;
            foreach ($classes as $class) {
                echo '<tr>';
                echo '<td style="padding-left: 17px;" onclick="window.location.href=\'class_detail.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . $counter . '</td>';
                echo '<td onclick="window.location.href=\'class_detail.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['class_name']) . '</td>';
                echo '<td onclick="window.location.href=\'class_detail.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['course_name']) . '</td>';
                echo '<td>' . htmlspecialchars($class['lastname']) . ' ' . htmlspecialchars($class['firstname']) . '</td>'; // Hiển thị họ và tên giáo viên
                echo '<td>';
                echo '<div class="dropdown">';
                echo '<button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton' . $counter . '" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">';
                echo '<i class="bi bi-three-dots-vertical"></i>'; // Biểu tượng 3 chấm màu đen
                echo '</button>';
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $counter . '">';
                echo '<li><a class="dropdown-item" href="delete_class.php?class_id=' . htmlspecialchars($class['class_id']) . '" onclick="return confirm(\'Bạn có chắc chắn muốn hủy lớp học này không?\')">Hủy lớp</a></li>';
                echo '<li><a class="dropdown-item" href="class_edit.php?class_id=' . htmlspecialchars($class['class_id']) . '">Cập nhật lớp</a></li>';
                echo '<li><a class="dropdown-item" href="class_edit.php?class_id=' . htmlspecialchars($class['class_id']) . '">Xem lịch học</a></li>';
                echo '</ul>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
                $counter++; // Tăng số thứ tự cho mỗi dòng
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-warning">Chưa tham gia lớp học nào trong học kỳ này.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Không tìm thấy thông tin học sinh.</div>';
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
        display: none;
        /* Ẩn mũi tên mặc định của Bootstrap */
    }

    thead tr {
        background-color: inherit;
        cursor: default;
    }
</style>