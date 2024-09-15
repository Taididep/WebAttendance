<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5 animate__animated animate__fadeIn">
        <?php include '../Connect/connect.php'; ?>
        <?php
        session_start();

        // Handle logout
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: ../index.php"); // Redirect to the login page
            exit();
        }

        $u = $_SESSION['username'];

        // Fetch all active semesters
        $semesterSql = "SELECT * FROM semester WHERE is_active = 1";
        $semesterStm = $conn->prepare($semesterSql);
        $semesterStm->execute();
        $semesters = $semesterStm->fetchAll(PDO::FETCH_OBJ);

        // Get selected semester ID from the form, default to the first active semester if not set
        $selectedSemesterId = $_POST['semester_id'] ?? $semesters[0]->id;
        ?>
        <!---->

        <!-- Header with logout button -->
        <header class="mb-4 bg-success">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Header</h1>
                <form method="post">
                    <button type="submit" name="logout" class="btn btn-danger">Đăng xuất</button>
                </form>
            </div>
        </header>


        <form method="post" class="mb-4">
            <div class="form-group">
                <label for="semester_id">Chọn học kỳ:</label>
                <select name="semester_id" id="semester_id" class="form-control" onchange="this.form.submit()">
                    <?php foreach($semesters as $semester) { ?>
                        <option value="<?php echo htmlspecialchars($semester->id) ?>"
                            <?php if ($semester->id == $selectedSemesterId) echo 'selected' ?>>
                            <?php echo htmlspecialchars($semester->name . " (" . date('d/m/Y', strtotime($semester->start_date)) . " - " . date('d/m/Y', strtotime($semester->end_date)) . ")") ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <!---->
        <?php
        // Display welcome message
        if (!isset($welcomeDisplayed) && !empty($u)) {
            echo "<h2 class='mb-4 welcome-message'>Xin chào, " . htmlspecialchars($u) . "</h2>";
            $welcomeDisplayed = true;
        }
        ?>
    </div>









</body>

</html>