<?php
    include 'function.php';
    include 'connect/connect.php';

    // Lấy class_id từ URL
    $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

    if ($class_id === null) {
        echo "<p class='message error'>Lỗi: Không tìm thấy class_id.</p>";
        exit();
    }

    // Lấy danh sách sinh viên và thông tin lớp
    $result_students = getStudentsByClassId($conn, $class_id);
    $class_name = count($result_students) > 0 ? $result_students[0]['class_name'] : '';

    // Lấy danh sách các ngày điểm danh và dữ liệu điểm danh
    $dates = getAttendanceDatesByClassId($conn, $class_id);
    $attendances = getAttendanceDataByClassId($conn, $class_id);

    // Thông báo
    if (isset($_GET['message'])) {
        echo "<p class='message success'>" . htmlspecialchars($_GET['message']) . "</p>";
    }

    // Xử lý thêm sinh viên
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
        $student_id = $_POST['student_id'];

        // Kiểm tra sinh viên có tồn tại không
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = :student_id");
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // Thêm sinh viên vào bảng điểm danh
            $stmt = $conn->prepare("
                INSERT INTO attendance_list (class_id, student_id)
                VALUES (:class_id, :student_id)
                ON DUPLICATE KEY UPDATE student_id = student_id
            ");
            $stmt->bindValue(':class_id', $class_id);
            $stmt->bindValue(':student_id', $student_id);
            $stmt->execute();

            header("Location: attendance_list.php?class_id=$class_id&message=Thêm sinh viên thành công!");
            exit();
        } else {
            $error_message = "Sinh viên không tồn tại.";
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Danh sách điểm danh</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 20px;
                background-color: #f4f7fa;
                color: #333;
            }
            h2, h3 {
                color: #2c3e50;
                border-bottom: 2px solid #3498db;
                padding-bottom: 10px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 20px;
                background-color: #fff;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow: hidden;
            }
            th, td {
                padding: 15px;
                text-align: center;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #3498db;
                color: white;
                font-weight: bold;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #e1f5fe;
                transition: background-color 0.3s;
            }
            .message {
                margin: 10px 0;
                padding: 10px;
                border-radius: 5px;
            }
            .message.success {
                background-color: #d4edda;
                color: #155724;
            }
            .message.error {
                background-color: #f8d7da;
                color: #721c24;
            }
            select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                transition: border-color 0.3s;
            }
            button {
                margin-top: 20px;
                padding: 10px 20px;
                background-color: #3498db;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            button:hover {
                background-color: #2980b9;
            }
        </style>
    </head>
    <body>
        <h2>Danh sách điểm danh cho lớp: <?php echo htmlspecialchars($class_name); ?></h2>

        <!-- Thêm sinh viên -->
        <form method="POST" style="margin-bottom: 20px;">
            <label for="student_id">Mã số sinh viên:</label>
            <input type="text" id="student_id" name="student_id" required>
            <button type="submit">Thêm Sinh Viên</button>
        </form>

        <!-- Nút lưu trạng thái điểm danh -->
        <button id="save-attendance">Lưu điểm danh</button>

        <!-- Nút xuất file Excel -->
        <form action="attendance_export.php" method="post" style="display: inline;">
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
            <button type="submit">Xuất ra file Excel</button>
        </form>

        <!-- Form để thêm điểm danh cho ngày nhập vào -->
        <form action="attendance_process.php" method="post" style="display: inline;">
            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($class_id); ?>">
            
            <label for="attendance_day">Ngày:</label>
            <input type="number" name="attendance_day" id="attendance_day" min="1" max="31" required>

            <label for="attendance_month">Tháng:</label>
            <input type="number" name="attendance_month" id="attendance_month" min="1" max="12" required>

            <button type="submit">Thêm ngày điểm danh</button>
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
                <?php if (count($result_students) > 0): ?>
                    <?php foreach ($result_students as $index => $student): ?>
                        <tr data-id='<?php echo htmlspecialchars($student['student_id']); ?>'>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($student['lastname'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['firstname'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($student['gender'] ?? ''); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($student['birthday'] ?? '')); ?></td>
                            <?php foreach ($dates as $date): ?>
                                <?php
                                $attendance_status = isset($attendances[$student['student_id']][$date]) ? htmlspecialchars($attendances[$student['student_id']][$date]) : 'Absent';
                                ?>
                                <td>
                                    <select class='attendance-select' data-student-id='<?php echo htmlspecialchars($student['student_id']); ?>' data-date='<?php echo htmlspecialchars($date); ?>'>
                                        <option value='Present' <?php echo ($attendance_status === 'Present' ? 'selected' : ''); ?>>Present</option>
                                        <option value='Late' <?php echo ($attendance_status === 'Late' ? 'selected' : ''); ?>>Late</option>
                                        <option value='Absent' <?php echo ($attendance_status === 'Absent' ? 'selected' : ''); ?>>Absent</option>
                                    </select>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan='6'>Không có sinh viên nào trong lớp này.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <script>
            document.getElementById('save-attendance').addEventListener('click', function() {
                const attendanceData = [];

                document.querySelectorAll('.attendance-select').forEach(select => {
                    const studentId = select.getAttribute('data-student-id');
                    const date = select.getAttribute('data-date');
                    const status = select.value;

                    attendanceData.push({ student_id: studentId, date: date, status: status });
                });

                // Gửi yêu cầu cập nhật tất cả trạng thái điểm danh
                fetch('update_attendance.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(attendanceData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Mã lỗi: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Lưu điểm danh thành công!');
                    } else {
                        alert('Lỗi: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra: ' + error.message);
                });
            });
        </script>
    </body>
</html>