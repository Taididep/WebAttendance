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
            echo '<thead><tr"><th>STT</th><th>Tên lớp học</th><th>Tên môn học</th><th>Giáo viên</th><th>Trạng thái</th><th style="width: 1%;"></th></tr></thead>';
            echo '<tbody>';

            // Khởi tạo biến đếm cho số thứ tự
            $counter = 1;
            foreach ($classes as $class) {
                // Lấy thông tin điểm danh của sinh viên trong lớp
                $sql_attendance = "SELECT total_present, total_absent, total_late, total FROM attendance_reports WHERE class_id = ? AND student_id = ?";
                $stmt_attendance = $conn->prepare($sql_attendance);
                $stmt_attendance->execute([$class['class_id'], $student_id]);
                $attendance = $stmt_attendance->fetch(PDO::FETCH_ASSOC);
                $stmt_attendance->closeCursor();

                // Tạo trạng thái hiển thị theo dạng "total_present / total"
                $status = '-';
                if ($attendance) {
                    // Lấy tổng số buổi học từ cột total
                    $total_classes = $attendance['total'];
                    $present_late = $attendance['total_present'] + $attendance['total_late'];

                    // Hiển thị trạng thái theo dạng "total_present + total_late / total"
                    $status = ($total_classes > 0) ? $present_late . " / " . $total_classes : "-";
                }
                echo '<tr>';
                echo '<td style="padding-left: 25px; vertical-align: middle;" onclick="window.location.href=\'class_detail_list.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . $counter . '</td>';
                echo '<td style="vertical-align: middle;" onclick="window.location.href=\'class_detail_list.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['class_name']) . '</td>';
                echo '<td style="vertical-align: middle;" onclick="window.location.href=\'class_detail_list.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['course_name']) . '</td>';
                echo '<td style="vertical-align: middle;" onclick="window.location.href=\'class_detail_list.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . htmlspecialchars($class['lastname']) . ' ' . htmlspecialchars($class['firstname']) . '</td>';
                echo '<td style="padding-left: 35px; vertical-align: middle;" onclick="window.location.href=\'class_detail_list.php?class_id=' . htmlspecialchars($class['class_id']) . '\'">' . $status . '</td>';
                echo '<td>';
                echo '<div class="dropdown">';
                echo '<button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton' . $counter . '" data-bs-toggle="dropdown" aria-expanded="false" style="color: black;">';
                echo '<i class="bi bi-three-dots-vertical"></i>'; // Biểu tượng 3 chấm màu đen
                echo '</button>';
                echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $counter . '">';
                echo '<li><a class="dropdown-item" href="delete_class.php?class_id=' . htmlspecialchars($class['class_id']) . '" onclick="return confirm(\'Bạn có chắc chắn muốn hủy đăng ký lớp học này không?\')">Hủy đăng ký</a></li>';
                echo '<li><a class="dropdown-item" href="class_view.php?class_id=' . htmlspecialchars($class['class_id']) . '">Xem lịch học</a></li>';
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

<link rel="stylesheet" href="../Css/get_classes.css">