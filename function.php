<?php
    include 'connect/connect.php';

    // Lấy danh sách sinh viên trong lớp
    function getStudentsByClassId($conn, $class_id) {
        $stmt = $conn->prepare("
            SELECT cs.stt, s.id AS student_id, s.lastname, s.firstname, s.gender, s.birthday, c.name AS class_name 
            FROM students s 
            JOIN class_students cs ON s.id = cs.student_id
            JOIN classes c ON cs.class_id = c.id
            WHERE c.id = :class_id
            ORDER BY cs.stt ASC
        ");
        $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách các ngày điểm danh
    function getAttendanceDatesByClassId($conn, $class_id) {
        $stmt = $conn->prepare("
            SELECT DISTINCT attendance_date 
            FROM attendances 
            WHERE class_id = :class_id
            ORDER BY attendance_date
        ");
        $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // Lấy dữ liệu điểm danh cho lớp
    function getAttendanceDataByClassId($conn, $class_id) {
        $stmt = $conn->prepare("
            SELECT student_id, attendance_date, status 
            FROM attendances 
            WHERE class_id = :class_id
        ");
        $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        $attendances = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $attendances[$row['student_id']][$row['attendance_date']] = $row['status'];
        }
        return $attendances;
    }

    // Tạo ngày điểm danh mới
    function createAttendanceDate($conn, $classId, $date) {
        $stmt = $conn->prepare("INSERT INTO attendances (class_id, attendance_date) VALUES (:class_id, :date)");
        $stmt->bindValue(':class_id', $classId, PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        return $stmt->execute();
    }

    // Lấy ngày tiếp theo để điểm danh
    function getNextAttendanceDate($conn, $class_id) {
        $stmt = $conn->prepare("
            SELECT MAX(attendance_date) AS max_date 
            FROM attendances 
            WHERE class_id = :class_id
        ");
        $stmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $stmt->execute();
        $max_date = $stmt->fetchColumn();

        return $max_date ? date('Y-m-d', strtotime($max_date . ' +1 day')) : date('Y-m-d');
    }

    // Cập nhật thông tin sinh viên
    function updateStudentInfo($conn, $student_id, $lastname, $firstname, $gender, $birthday) {
        $stmt = $conn->prepare("
            UPDATE students 
            SET lastname = :lastname, firstname = :firstname, gender = :gender, birthday = :birthday 
            WHERE id = :student_id
        ");
        $stmt->bindValue(':lastname', $lastname);
        $stmt->bindValue(':firstname', $firstname);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':birthday', $birthday);
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Lấy thông tin sinh viên theo ID
    function getStudentById($conn, $student_id) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = :student_id");
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Xóa sinh viên
    function deleteStudentById($conn, $student_id) {
        deleteAttendanceByStudentId($conn, $student_id);
        $stmt = $conn->prepare("DELETE FROM class_students WHERE student_id = :student_id");
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt = $conn->prepare("DELETE FROM students WHERE id = :student_id");
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa dữ liệu điểm danh liên quan đến sinh viên
    function deleteAttendanceByStudentId($conn, $student_id) {
        $stmt = $conn->prepare("DELETE FROM attendances WHERE student_id = :student_id");
        $stmt->bindValue(':student_id', $student_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra sinh viên tồn tại
    function studentExists($conn, $student_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE id = :student_id");
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Trả về true nếu sinh viên đã tồn tại
    }

    // Kiểm tra người dùng tồn tại
    function userExists($conn, $student_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE id = :student_id");
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Trả về true nếu người dùng đã tồn tại
    }

    // Thêm sinh viên
    function addStudent($conn, $student_id, $lastname, $firstname, $gender, $birthday) {
        $stmt = $conn->prepare("
            INSERT INTO students (id, lastname, firstname, gender, birthday) 
            VALUES (:id, :lastname, :firstname, :gender, :birthday)
        ");
        $stmt->bindValue(':id', $student_id);
        $stmt->bindValue(':lastname', $lastname);
        $stmt->bindValue(':firstname', $firstname);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':birthday', $birthday);
        return $stmt->execute(); // Trả về kết quả thực thi
    }

    // Thêm người dùng
    function addUser($conn, $student_id, $username, $password, $role) {
        $base_username = $username;
        $count = 1;
        while (true) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            if ($stmt->fetchColumn() == 0) {
                break; // Nếu tên người dùng chưa tồn tại, thoát vòng lặp
            }
            $username = $base_username . $count; // Tạo tên người dùng mới
            $count++;
        }
        $stmt = $conn->prepare("INSERT INTO users (id, username, password, role) VALUES (:id, :username, :password, :role)");
        $stmt->bindValue(':id', $student_id);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':role', $role);
        if (!$stmt->execute()) {
            $errorInfo = $stmt->errorInfo();
            echo "Lỗi thêm người dùng: " . $errorInfo[2];
            return false; 
        }

        return true; 
    }

    // Cập nhật thông tin sinh viên
    function updateStudent($conn, $student_id, $lastname, $firstname, $gender, $birthday) {
        $stmt = $conn->prepare("
            UPDATE students 
            SET lastname = :lastname, firstname = :firstname, gender = :gender, birthday = :birthday 
            WHERE id = :id
        ");
        $stmt->bindValue(':lastname', $lastname);
        $stmt->bindValue(':firstname', $firstname);
        $stmt->bindValue(':gender', $gender);
        $stmt->bindValue(':birthday', $birthday);
        $stmt->bindValue(':id', $student_id);
        return $stmt->execute();
    }

    // Cập nhật thông tin người dùng
    function updateUser($conn, $student_id, $username) {
        $stmt = $conn->prepare("
            UPDATE users 
            SET username = :username 
            WHERE id = :id
        ");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':id', $student_id);
        return $stmt->execute();
    }
?>