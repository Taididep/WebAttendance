<?php
include __DIR__ . '/../../Connect/connect.php';

// Kiểm tra nếu có lớp học ID
if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];
} else {
    echo 'Không tìm thấy thông tin lớp học.';
    exit;
}

// Truy vấn bảng tin
$sql = "CALL GetAnnouncementsByClass(:class_id)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':class_id', $class_id);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();  // Close the cursor to allow the next query

if ($announcements) {
    foreach ($announcements as $announcement) {
?>
        <div class="card mb-3">
            <div class="card-body">
                <!-- Tiêu đề và ngày tạo -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0" contenteditable="true" onblur="updateAnnouncementTitle(<?php echo $announcement['announcement_id']; ?>, this.innerText)">
                        <?php echo htmlspecialchars($announcement['title']); ?>
                    </h5>

                    <div class="d-flex align-items-center">
                        <small class="text-muted ms-3"><?php echo $announcement['created_at']; ?></small>

                        <!-- Nút Xóa thông báo -->
                        <a href="delete_announcement.php?announcement_id=<?php echo $announcement['announcement_id']; ?>&class_id=<?php echo $class_id; ?>" class="btn btn-danger btn-sm ms-3" onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này không?');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>

                <hr>

                <!-- Nội dung thông báo có thể chỉnh sửa -->
                <div class="border p-3 mb-3" style="background-color: #f8f9fa;" contenteditable="true" onblur="updateAnnouncementContent(<?php echo $announcement['announcement_id']; ?>, this.innerText)">
                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                </div>
            </div>

            <hr>

            <!-- Kiểm tra nếu có bình luận -->
            <?php
            $comment_stmt = $conn->prepare("CALL GetCommentCountByAnnouncement(:announcement_id, @comment_count)");
            $comment_stmt->bindParam(':announcement_id', $announcement['announcement_id']);
            $comment_stmt->execute();
            $stmt_result = $conn->query("SELECT @comment_count AS comment_count");
            $comment_count = $stmt_result->fetch(PDO::FETCH_ASSOC);
            $stmt_result->closeCursor();  // Close the cursor to allow the next query

            if ($comment_count['comment_count'] > 0) {
            ?>
                <div class="card-body">
                    <h6 class="mb-3" id="commentsTitle_<?php echo $announcement['announcement_id']; ?>" style="cursor: pointer;" onclick="toggleComments(<?php echo $announcement['announcement_id']; ?>)">
                        Bình luận (<?php echo $comment_count['comment_count']; ?>)
                    </h6>
                    <div id="commentsList_<?php echo $announcement['announcement_id']; ?>">
                        <?php
                        $comment_stmt = $conn->prepare("CALL GetCommentsByAnnouncement(:announcement_id)");
                        $comment_stmt->bindParam(':announcement_id', $announcement['announcement_id']);
                        $comment_stmt->execute();
                        $comments = $comment_stmt->fetchAll(PDO::FETCH_ASSOC);
                        $comment_stmt->closeCursor();  // Close the cursor to allow the next query

                        if ($comments) {
                            foreach ($comments as $comment) {
                                echo '<div class="mb-2 pb-2">';
                                echo '<p class="mb-1"><strong>' . htmlspecialchars($comment['lastname']) . ' ' . htmlspecialchars($comment['firstname']) . '</strong> <small class="text-muted">' . $comment['created_at'] . '</small></p>';
                                echo '<p class="mb-0">' . htmlspecialchars($comment['content']) . '</p>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <hr>
            <?php
            }
            ?>

            <!-- Thanh nhập bình luận -->
            <div class="card-body">
                <form action="add_comment.php?class_id=<?php echo $class_id; ?>" method="POST" class="d-flex align-items-center">
                    <div class="flex-grow-1 me-2">
                        <textarea class="form-control" name="content" rows="2" placeholder="Nhập bình luận của bạn..." required></textarea>
                    </div>
                    <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['announcement_id']); ?>">
                    <button type="submit" class="btn btn-secondary">Bình luận</button>
                </form>
            </div>
        </div>
<?php
    }
} else {
    echo '<p class="text-center">Chưa có bảng tin nào.</p>';
}
?>

<script src="../JavaScript/announcement.js"></script>