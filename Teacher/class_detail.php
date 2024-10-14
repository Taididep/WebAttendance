<?php
    session_start();

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user_id'])) {
        echo 'Unauthorized access.';
        exit;
    }

    // Kết nối đến cơ sở dữ liệu
    include '../Connect/connect.php';

    // Lấy class_id từ tham số
    $class_id = $_GET['class_id'] ?? null;

    if ($class_id) {
        // Truy vấn chi tiết lớp học
        $sql_class_detail = "
        SELECT c.class_name, co.course_name
        FROM classes c
        JOIN courses co ON c.course_id = co.course_id
        WHERE c.class_id = :class_id
        ";

        $stmt = $conn->prepare($sql_class_detail);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        $class_detail = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        // Truy vấn danh sách sinh viên trong lớp học
        $sql_students = "
        SELECT 
            s.student_id,
            s.lastname,
            s.firstname,
            s.birthday,
            s.gender,
            s.class
        FROM class_students cs
        JOIN students s ON cs.student_id = s.student_id
        WHERE cs.class_id = :class_id
        ORDER BY s.student_id
        ";

        $stmt_students = $conn->prepare($sql_students);
        $stmt_students->bindParam(':class_id', $class_id);
        $stmt_students->execute();
        $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
        $stmt_students->closeCursor();

        // Truy vấn ngày điểm danh từ bảng schedules
        $sql_schedules = "
        SELECT DISTINCT date FROM schedules
        WHERE class_id = :class_id
        ORDER BY date ASC
        ";

        $stmt_schedules = $conn->prepare($sql_schedules);
        $stmt_schedules->bindParam(':class_id', $class_id);
        $stmt_schedules->execute();
        $attendance_dates = $stmt_schedules->fetchAll(PDO::FETCH_COLUMN);
        $stmt_schedules->closeCursor();
    } else {
        echo 'No class ID provided.';
        exit;
    }

    // Xử lý thông báo
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['attendance_date'], $_POST['start_time'], $_POST['end_time'])) {
            $attendance_date = $_POST['attendance_date'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            // Kiểm tra định dạng ngày
            $date_format = DateTime::createFromFormat('Y-m-d', $attendance_date);
            if (!$date_format || $date_format->format('Y-m-d') !== $attendance_date) {
                echo '<div class="alert alert-danger mt-3" role="alert">Ngày không hợp lệ!</div>';
            } else {
                // Kiểm tra ngày đã tồn tại
                $sql_check_date = "
                SELECT COUNT(*) FROM schedules
                WHERE class_id = :class_id AND date = :attendance_date
                ";

                $stmt_check_date = $conn->prepare($sql_check_date);
                $stmt_check_date->bindParam(':class_id', $class_id);
                $stmt_check_date->bindParam(':attendance_date', $attendance_date);
                $stmt_check_date->execute();
                $date_exists = $stmt_check_date->fetchColumn() > 0;

                // Kiểm tra xem ngày có hợp lệ không
                $current_date = date('Y-m-d');
                if ($attendance_date < $current_date) {
                    echo '<div class="alert alert-danger mt-3" role="alert">Ngày điểm danh không thể trước ngày hiện tại!</div>';
                } elseif ($date_exists) {
                    echo '<div class="alert alert-danger mt-3" role="alert">Ngày đã tồn tại trong hệ thống!</div>';
                } else {
                    // Thêm ngày điểm danh vào bảng schedules
                    $sql_insert_schedule = "
                    INSERT INTO schedules (class_id, date, start_time, end_time)
                    VALUES (:class_id, :attendance_date, :start_time, :end_time)
                    ";

                    $stmt_insert = $conn->prepare($sql_insert_schedule);
                    $stmt_insert->bindParam(':class_id', $class_id); // Sử dụng class_id từ URL
                    $stmt_insert->bindParam(':attendance_date', $attendance_date);
                    $stmt_insert->bindParam(':start_time', $start_time);
                    $stmt_insert->bindParam(':end_time', $end_time);
                    $stmt_insert->execute();

                    echo '<div class="alert alert-success mt-3" role="alert">Tạo ngày điểm danh thành công!</div>';

                    // Cập nhật lại danh sách ngày điểm danh
                    $stmt_schedules->execute();
                    $attendance_dates = $stmt_schedules->fetchAll(PDO::FETCH_COLUMN);
                }
            }
        }
    }

    // Xử lý xóa ngày điểm danh
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_date'])) {
        $delete_date = $_POST['delete_schedule_date'];

        $sql_delete_schedule = "
        DELETE FROM schedules
        WHERE class_id = :class_id AND date = :delete_date
        ";

        $stmt_delete_schedule = $conn->prepare($sql_delete_schedule);
        $stmt_delete_schedule->bindParam(':class_id', $class_id);
        $stmt_delete_schedule->bindParam(':delete_date', $delete_date);
        $stmt_delete_schedule->execute();

        echo '<div class="alert alert-success mt-3" role="alert">Xóa ngày điểm danh thành công!</div>';

        // Cập nhật lại danh sách ngày điểm danh
        $stmt_schedules->execute();
        $attendance_dates = $stmt_schedules->fetchAll(PDO::FETCH_COLUMN);
    }

    // Xử lý xóa sinh viên
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student_id'])) {
        $delete_student_id = $_POST['delete_student_id'];

        $sql_delete_student = "
        DELETE FROM class_students
        WHERE class_id = :class_id AND student_id = :delete_student_id
        ";

        $stmt_delete_student = $conn->prepare($sql_delete_student);
        $stmt_delete_student->bindParam(':class_id', $class_id);
        $stmt_delete_student->bindParam(':delete_student_id', $delete_student_id);
        $stmt_delete_student->execute();

        echo '<div class="alert alert-success mt-3" role="alert">Xóa sinh viên ra khỏi lớp thành công!</div>';

        // Cập nhật lại danh sách sinh viên
        $stmt_students->execute();
        $students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xử lý thêm sinh viên
    if (isset($_POST['student_id'])) {
        $student_id = $_POST['student_id'];

        // Kiểm tra xem sinh viên đã có trong lớp chưa
        $sql_check_student = "
        SELECT COUNT(*) FROM class_students
        WHERE class_id = :class_id AND student_id = :student_id
        ";

        $stmt_check_student = $conn->prepare($sql_check_student);
        $stmt_check_student->bindParam(':class_id', $class_id);
        $stmt_check_student->bindParam(':student_id', $student_id);
        $stmt_check_student->execute();
        $student_exists = $stmt_check_student->fetchColumn() > 0;

        if (!$student_exists) {
            // Thêm sinh viên vào lớp học
            $sql_add_student = "
            INSERT INTO class_students (class_id, student_id) VALUES (:class_id, :student_id)
            ";

            $stmt_add_student = $conn->prepare($sql_add_student);
            $stmt_add_student->bindParam(':class_id', $class_id);
            $stmt_add_student->bindParam(':student_id', $student_id);
            
            if ($stmt_add_student->execute()) {
                echo '<div class="alert alert-success mt-3" role="alert">Thêm thành công!</div>';
            } else {
                echo '<div class="alert alert-danger mt-3" role="alert">Thêm thất bại!</div>';
            }
        } else {
            echo '<div class="alert alert-warning mt-3" role="alert">Sinh viên đã có trong lớp!</div>';
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết lớp học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Chi tiết lớp học</h2>
        <?php if ($class_detail): ?>
            <p><strong>Tên lớp:</strong> <?php echo htmlspecialchars($class_detail['class_name']); ?></p>
            <p><strong>Khóa học:</strong> <?php echo htmlspecialchars($class_detail['course_name']); ?></p>
        <?php else: ?>
            <p>Không tìm thấy thông tin lớp học.</p>
        <?php endif; ?>

        <h3>Danh sách sinh viên</h3>

        <div class="mb-3">
            <!-- Nút mở modal để thêm ngày điểm danh -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#attendanceModal">
                Thêm Ngày Điểm Danh
            </button>

            <!-- Nút mở modal để xóa -->
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                Xóa
            </button>

            <!-- Nút tạo mã QR -->
            <button type="button" class="btn btn-info" id="createQrButton">
                Tạo Mã QR
            </button>

            <!-- Nút thêm sinh viên -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                Thêm Sinh Viên
            </button>

            <!-- Nút quay lại -->
            <button onclick="window.location.href='teacher.php'" class="btn btn-secondary">
                Quay lại trang
            </button>
        </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>MSSV</th>
                    <th>Họ Đệm</th>
                    <th>Tên</th>
                    <th>Ngày Sinh</th>
                    <th>Giới Tính</th>
                    <th>Lớp Học</th>
                    <?php foreach ($attendance_dates as $date): ?>
                        <th><?php echo date('d/m/Y', strtotime($date)); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;

                foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $counter; ?></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($student['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($student['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($student['birthday']); ?></td>
                        <td><?php echo htmlspecialchars($student['gender']); ?></td>
                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                        <?php foreach ($attendance_dates as $date): ?>
                            <?php
                            // Kiểm tra trạng thái điểm danh từ bảng attendance_details
                            $sql_check_attendance = "
                            SELECT COUNT(*) FROM attendance_details
                            WHERE student_id = :student_id AND attendance_date = :attendance_date
                            ";

                            $stmt_check = $conn->prepare($sql_check_attendance);
                            $stmt_check->bindParam(':student_id', $student['student_id']);
                            $stmt_check->bindParam(':attendance_date', $date);
                            $stmt_check->execute();
                            $is_present = $stmt_check->fetchColumn() > 0 ? 1 : 0;
                            ?>
                            <td><?php echo $is_present ? '1' : '0'; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php $counter++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Modal Tạo Ngày -->
        <div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendanceModalLabel">Tạo Thời Gian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="attendance_date" class="form-label">Chọn ngày điểm danh:</label>
                                <input type="date" id="attendance_date" name="attendance_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Thời gian bắt đầu (1-13):</label>
                                <input type="number" id="start_time" name="start_time" class="form-control" required min="1" max="13">
                            </div>
                            <div class="mb-3">
                                <label for="end_time" class="form-label">Thời gian kết thúc (1-13):</label>
                                <input type="number" id="end_time" name="end_time" class="form-control" required min="0" max="13">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Tạo Ngày Điểm Danh</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Xóa Ngày Điểm Danh hoặc Sinh Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs" id="deleteTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="delete-date-tab" data-bs-toggle="tab" href="#deleteDate" role="tab" aria-controls="deleteDate" aria-selected="true">Xóa Ngày Điểm Danh</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="delete-student-tab" data-bs-toggle="tab" href="#deleteStudent" role="tab" aria-controls="deleteStudent" aria-selected="false">Xóa Sinh Viên</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="deleteTabContent">
                            <!-- Tab Xóa Ngày Điểm Danh -->
                            <div class="tab-pane fade show active" id="deleteDate" role="tabpanel" aria-labelledby="delete-date-tab">
                                <form method="POST" action="" class="mt-3">
                                    <div class="mb-3">
                                        <label for="delete_schedule_date" class="form-label">Chọn ngày cần xóa:</label>
                                        <select class="form-select" id="delete_schedule_date" name="delete_schedule_date" required>
                                            <option value="">-- Chọn ngày --</option>
                                            <?php foreach ($attendance_dates as $date): ?>
                                                <option value="<?php echo htmlspecialchars($date); ?>"><?php echo date('d/m/Y', strtotime($date)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" class="btn btn-danger">Xóa Ngày Điểm Danh</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Xóa Sinh Viên -->
                            <div class="tab-pane fade" id="deleteStudent" role="tabpanel" aria-labelledby="delete-student-tab">
                                <form method="POST" action="" class="mt-3">
                                    <div class="mb-3">
                                        <label for="student_search" class="form-label">Tìm kiếm sinh viên theo MSSV:</label>
                                        <input type="text" id="student_search" name="student_search" class="form-control" placeholder="Nhập MSSV" required>
                                    </div>
                                    <div id="student_info" style="display: none;">
                                        <p><strong>Tên:</strong> <span id="student_name"></span></p>
                                        <p><strong>Ngày Sinh:</strong> <span id="student_birthday"></span></p>
                                        <p><strong>Giới Tính:</strong> <span id="student_gender"></span></p>
                                        <p><strong>Lớp Học:</strong> <span id="student_class"></span></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        <button type="submit" id="delete_student_button" class="btn btn-danger" disabled>Xóa Sinh Viên</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thêm Sinh Viên -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Thêm Sinh Viên vào Lớp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Nhập MSSV:</label>
                                <input type="text" id="student_id" name="student_id" class="form-control" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-success">Thêm Sinh Viên</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#student_search').on('input', function() {
                var studentId = $(this).val();
                if (studentId.length > 0) {
                    $.ajax({
                        url: 'get_student_info.php', // Tạo một file PHP để lấy thông tin sinh viên
                        type: 'POST',
                        data: { student_id: studentId },
                        success: function(data) {
                            var result = JSON.parse(data);
                            if (result) {
                                $('#student_info').show();
                                $('#student_name').text(result.lastname + ' ' + result.firstname);
                                $('#student_birthday').text(result.birthday);
                                $('#student_gender').text(result.gender);
                                $('#student_class').text(result.class);
                                $('#delete_student_button').prop('disabled', false);
                            } else {
                                $('#student_info').hide();
                                $('#delete_student_button').prop('disabled', true);
                            }
                        },
                        error: function() {
                            $('#student_info').hide();
                            $('#delete_student_button').prop('disabled', true);
                        }
                    });
                } else {
                    $('#student_info').hide();
                    $('#delete_student_button').prop('disabled', true);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Cập nhật ID sinh viên khi mở modal xóa sinh viên
            $('#deleteStudentModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var studentId = button.data('student-id');
                var studentName = button.data('student-name');

                var modal = $(this);
                modal.find('#delete_student_id').val(studentId);
                modal.find('#student_name').text(studentName);
            });
        });
    </script>
</body>
</html>