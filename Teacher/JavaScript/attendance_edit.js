// Xác nhận buổi học
document.getElementById('confirmAttendanceBtnEdit').addEventListener('click', function () {
    const input = document.getElementById('attendanceInputEdit');
    const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
    const totalSchedules = totalDatesEdit; // Sử dụng biến truyền từ PHP

    if (index < 1 || index > totalSchedules) {
        alert('Vui lòng nhập buổi hợp lệ (từ 1 đến ' + totalSchedules + ').');
        return;
    }

    // Ẩn tất cả các cột và tiêu đề
    document.querySelectorAll('#attendanceEdit .edit-data, #attendanceEdit .edit-column').forEach(cell => {
        cell.style.display = 'none';
    });

    // Hiện cột buổi đã nhập
    const cells = document.querySelectorAll(`#attendanceEdit td:nth-child(${index + 7})`); // Cột thứ index (cột 8 là buổi đầu tiên)

    cells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng
    });

    // Cập nhật tiêu đề cột
    const headerCells = document.querySelectorAll(`#attendanceEdit th.edit-column`);
    headerCells.forEach((headerCell, idx) => {
        if (idx === index - 1) {
            headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
        } else {
            headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
        }
    });
});

// Nút hiện tất cả cho bảng chỉnh sửa
document.getElementById('showAllBtnEdit').addEventListener('click', function (event) {
    event.preventDefault(); // Ngăn chặn hành vi mặc định
    document.querySelectorAll('#attendanceEdit .edit-data').forEach(cell => {
        cell.style.display = '';
    });
    document.querySelectorAll('#attendanceEdit .edit-column').forEach(column => {
        column.style.display = '';
    });
});
