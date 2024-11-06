// Xác nhận buổi học
document.getElementById('confirmAttendanceBtnList').addEventListener('click', function () {
    const input = document.getElementById('attendanceInputList');
    const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
    const totalSchedules = totalDatesList; // Sử dụng biến toàn cục

    if (index < 1 || index > totalSchedules) {
        alert('Vui lòng nhập buổi hợp lệ (từ 1 đến ' + totalSchedules + ').');
        return;
    }

    // Ẩn tất cả các cột và dữ liệu
    document.querySelectorAll('#attendanceList .list-data, #attendanceList .list-column').forEach(cell => {
        cell.style.display = 'none';
    });

    // Hiện cột buổi đã nhập
    const cells = document.querySelectorAll(`#attendanceList td:nth-child(${index + 7})`); // Cột thứ index (cột 8 là buổi đầu tiên)

    cells.forEach(cell => {
        cell.style.display = ''; // Hiện cột tương ứng
    });

    // Hiện tiêu đề cột tương ứng
    const headerCells = document.querySelectorAll(`#attendanceList th.list-column`);
    headerCells.forEach((headerCell, idx) => {
        if (idx === index - 1) {
            headerCell.style.display = ''; // Hiện tiêu đề cột tương ứng
        } else {
            headerCell.style.display = 'none'; // Ẩn các tiêu đề cột khác
        }
    });
});

// Nút hiện tất cả cho bảng danh sách
document.getElementById('showAllBtnList').addEventListener('click', function (event) {
    event.preventDefault(); // Ngăn chặn hành vi mặc định
    document.querySelectorAll('#attendanceList .list-data').forEach(cell => {
        cell.style.display = '';
    });
    document.querySelectorAll('#attendanceList .list-column').forEach(column => {
        column.style.display = '';
    });
});
