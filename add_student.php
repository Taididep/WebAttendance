<?php
    include 'connect/connect.php';

    $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student_id = $_POST['student_id'];

        // Kiểm tra sinh viên có tồn tại không
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = :student_id");
        $stmt->bindValue(':student_id', $student_id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // Thêm sinh viên vào lớp
            $stmt = $conn->prepare("
                INSERT INTO class_students (class_id, student_id)
                VALUES (:class_id, :student_id)
                ON DUPLICATE KEY UPDATE student_id = student_id
            ");
            $stmt->bindValue(':class_id', $class_id);
            $stmt->bindValue(':student_id', $student_id);
            $stmt->execute();

            $message = "Thêm sinh viên thành công!";
        } else {
            $message = "Sinh viên không tồn tại.";
        }
    }
?>

<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thêm Sinh Viên Vào Lớp</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f7fa; color: #333; }
            h2 { color: #2c3e50; }
            .message { margin: 10px 0; padding: 10px; border-radius: 5px; }
            .message.success { background-color: #d4edda; color: #155724; }
            .message.error { background-color: #f8d7da; color: #721c24; }
            input[type="text"] { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
            button { padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; }
            button:hover { background-color: #2980b9; }
        </style>
    </head>
    <body>
        <h2>Thêm Sinh Viên Vào Lớp</h2>

        <?php if (isset($message)): ?>
            <div class="message <?php echo (strpos($message, 'thành công') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="student_id">Mã số sinh viên:</label>
            <input type="text" id="student_id" name="student_id" required>
            <button type="submit">Thêm Sinh Viên</button>
        </form>

        <?php if (isset($student)): ?>
            <h3>Thông tin Sinh Viên</h3>
            <p><strong>Mã số:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
            <p><strong>Họ:</strong> <?php echo htmlspecialchars($student['lastname']); ?></p>
            <p><strong>Tên:</strong> <?php echo htmlspecialchars($student['firstname']); ?></p>
            <p><strong>Ngày sinh:</strong> <?php echo date('d/m/Y', strtotime($student['birthday'])); ?></p>
            <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($student['gender']); ?></p>
        <?php endif; ?>
    </body>
</html>