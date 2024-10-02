<?php
include 'connect/connect.php';
include 'function.php';

// Kiểm tra xem form xóa đã được gửi chưa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $student_id = $_POST['student_id']; // ID sinh viên nhập vào để xóa

    // Kiểm tra xem sinh viên có tồn tại không
    if (studentExists($conn, $student_id)) {
        deleteAttendanceByStudentId($conn, $student_id);
        if (deleteStudentById($conn, $student_id)) {
            echo "<p class='message success'>Xóa sinh viên thành công!</p>";
        } else {
            echo "<p class='message error'>Có lỗi xảy ra khi xóa sinh viên.</p>";
        }
    } else {
        echo "<p class='message error'>Mã số sinh viên không tồn tại.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Sinh Viên</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #e9ecef;
            color: #343a40;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Xóa Sinh Viên</h2>
        <form action="" method="post">
            <label for="student_id">Mã số SV:</label>
            <input type="text" id="student_id" name="student_id" required>
            <button type="submit" name="delete">Xóa Sinh Viên</button>
        </form>
    </div>
</body>
</html>