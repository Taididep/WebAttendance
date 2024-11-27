// Hàm để cập nhật tiêu đề
function updateAnnouncementTitle(announcementId, newTitle) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Announcement/update_announcement_title.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("announcement_id=" + announcementId + "&title=" + encodeURIComponent(newTitle));
}

// Hàm để cập nhật nội dung
function updateAnnouncementContent(announcementId, newContent) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../Announcement/update_announcement_content.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("announcement_id=" + announcementId + "&content=" + encodeURIComponent(newContent));
}

// Hàm để ẩn/hiện bình luận
function toggleComments(announcementId) {
    var commentsList = document.getElementById('commentsList_' + announcementId);
    if (commentsList.style.display === 'none') {
        commentsList.style.display = 'block';
    } else {
        commentsList.style.display = 'none';
    }
}