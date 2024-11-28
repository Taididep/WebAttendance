document.getElementById('editBtn').addEventListener('click', function() {
    document.getElementById('class_name').readOnly = false;
    document.getElementById('course_id').disabled = false;
    document.getElementById('semester_id').disabled = false;
    document.getElementById('updateBtn').style.display = 'inline-block'; // Hiển thị nút cập nhật
    this.style.display = 'none'; // Ẩn nút chỉnh sửa
});