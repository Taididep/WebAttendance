<?php
    include 'connect/connect.php'; // Kết nối CSDL
    include 'function.php';

    // Kiểm tra xem yêu cầu có phải là POST không
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy class_id từ POST
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;
        
        // Lấy ngày và tháng từ POST
        $day = isset($_POST['attendance_day']) ? $_POST['attendance_day'] : null;
        $month = isset($_POST['attendance_month']) ? $_POST['attendance_month'] : null;

        // Lấy năm hiện tại
        $year = date('Y');

        // Tạo chuỗi ngày hợp lệ
        if ($day && $month) {
            $attendance_date = "$year-$month-$day"; // Định dạng YYYY-MM-DD

            // Kiểm tra xem ngày có hợp lệ không
            if (!checkdate($month, $day, $year)) {
                header("Location: attendance_list.php?class_id=" . htmlspecialchars($class_id) . "&message=Lỗi: Ngày không hợp lệ.");
                exit();
            }
        }

        // Lấy danh sách sinh viên trong lớp
        $students = getStudentsByClassId($conn, $class_id);

        // Kiểm tra xem có sinh viên nào không
        if (count($students) === 0) {
            header("Location: attendance_list.php?class_id=" . htmlspecialchars($class_id) . "&message=Lỗi: Không có sinh viên nào trong lớp.");
            exit();
        }

        // Gán trạng thái điểm danh cho tất cả sinh viên
        foreach ($students as $student) {
            // Thêm dữ liệu điểm danh vào database với trạng thái "Absent"
            $stmt = $conn->prepare("INSERT INTO attendances (class_id, student_id, attendance_date, status) VALUES (:class_id, :student_id, :attendance_date, :status)");
            $stmt->bindValue(':class_id', $class_id);
            $stmt->bindValue(':student_id', $student['student_id']);
            $stmt->bindValue(':attendance_date', $attendance_date);
            $stmt->bindValue(':status', 'Absent'); // Mặc định tất cả đều vắng mặt

            // Kiểm tra việc thực thi câu lệnh
            if (!$stmt->execute()) {
                // Nếu có lỗi, chuyển hướng về danh sách điểm danh và hiển thị thông báo
                header("Location: attendance_list.php?class_id=" . htmlspecialchars($class_id) . "&message=Lỗi: Không thể thêm dữ liệu điểm danh.");
                exit();
            }
        }

        // Chuyển hướng trở lại danh sách điểm danh với thông báo thành công
        header("Location: attendance_list.php?class_id=" . htmlspecialchars($class_id) . "&message=Thêm ngày điểm danh thành công!");
        exit();
    } else {
        // Nếu không phải là yêu cầu POST, chuyển hướng về danh sách điểm danh
        header("Location: attendance_list.php?message=Lỗi: Yêu cầu không hợp lệ.");
        exit();
    }
?>