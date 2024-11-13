// Hàm để ẩn/hiện bình luận cũ
function toggleComments(announcementId) {
    var commentsList = document.getElementById('commentsList_' + announcementId);
    if (commentsList.style.display === 'none') {
        commentsList.style.display = 'block';
    } else {
        commentsList.style.display = 'none';
    }
}