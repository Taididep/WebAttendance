const attendanceList = document.getElementById('attendanceList');
const attendanceEdit = document.getElementById('attendanceEdit');
const editModeBtn = document.getElementById('editModeBtn'); // Nút chuyển sang chế độ chỉnh sửa
const listModeBtn = document.getElementById('listModeBtn'); // Nút hủy chỉnh sửa, chỉ hiển thị trong attendanceEdit

// Chuyển đổi giữa chế độ xem danh sách và chế độ chỉnh sửa
function toggleEditMode(event) {
    event.preventDefault(); // Ngăn chặn hành động mặc định của nút
    const isEditVisible = attendanceEdit.style.display === 'block';
    attendanceList.style.display = isEditVisible ? 'block' : 'none'; // Hiện danh sách nếu đang trong chế độ chỉnh sửa
    attendanceEdit.style.display = isEditVisible ? 'none' : 'block'; // Ẩn danh sách nếu không trong chế độ chỉnh sửa
    editModeBtn.textContent = isEditVisible ? 'Chỉnh sửa' : 'Hủy'; // Thay đổi nội dung nút chỉnh sửa
    if (listModeBtn) listModeBtn.style.display = isEditVisible ? 'none' : 'inline-block'; // Hiện nút Hủy khi đang trong chế độ chỉnh sửa
}

// Đặt sự kiện cho nút chỉnh sửa
editModeBtn.addEventListener('click', toggleEditMode);
if (listModeBtn) listModeBtn.addEventListener('click', toggleEditMode); // Đặt sự kiện cho nút Hủy nếu tồn tại