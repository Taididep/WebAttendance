<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance List</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php
    include 'function.php';
    // Lấy class_id từ URL
    $class_id = $_GET['class_id'];

    // Lấy danh sách sinh viên và thông tin lớp học
    $result_students = getStudentsByClassId($conn, $class_id);

    // Lấy tên lớp từ kết quả đầu tiên
    $class_name = count($result_students) > 0 ? $result_students[0]['class_name'] : '';

    // Lấy danh sách các ngày điểm danh cho lớp học này
    $dates = getAttendanceDatesByClassId($conn, $class_id);

    // Lấy tất cả dữ liệu điểm danh cho lớp học này
    $attendances = getAttendanceDataByClassId($conn, $class_id);
    ?>

    <h2>Attendance List for Class: <?php echo $class_name; ?></h2>
    <!-- Nút xuất file Excel -->
    <form action="attendance_export.php" method="post">
        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
        <button type="submit">Xuất ra file Excel</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã số SV</th>
                <th>Họ</th>
                <th>Tên</th>
                <th>Giới tính</th>
                <th>Ngày sinh</th>
                <?php foreach ($dates as $date): ?>
                    <th><?php echo date('d/m', strtotime($date)); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
            <tbody>
                <?php
                if (count($result_students) > 0) {
                    foreach ($result_students as $student) {
                        echo "<tr>";
                        echo "<td>" . $student['stt'] . "</td>"; // Lấy STT từ database
                        echo "<td>" . $student['student_id'] . "</td>";
                        echo "<td>" . $student['lastname'] . "</td>";
                        echo "<td>" . $student['firstname'] . "</td>";
                        echo "<td>" . $student['gender'] . "</td>";
                        echo "<td>" . date('d/m/Y', strtotime($student['birthday'])) . "</td>";

                        // Hiển thị trạng thái điểm danh cho mỗi ngày
                        foreach ($dates as $date) {
                            if (isset($attendances[$student['student_id']][$date])) {
                                echo "<td>" . $attendances[$student['student_id']][$date] . "</td>";
                            } else {
                                echo "<td>Absent</td>"; // Nếu không có dữ liệu thì mặc định là vắng mặt
                            }
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No students found for this class.</td></tr>";
                }
                ?>
            </tbody>
    </table>
</body>
</html>
