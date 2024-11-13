document.getElementById('confirmAttendanceBtnList').addEventListener('click', function () {
    const input = document.getElementById('attendanceInputList');
    const index = parseInt(input.value); // Lấy giá trị buổi nhập vào
    const totalSchedules = totalDatesList; // Sử dụng biến toàn cục

    // Kiểm tra nếu không nhập giá trị hoặc nhập "0", hiển thị tất cả
    if (!input.value || index === 0) {
        document.querySelectorAll('#attendanceList .list-data').forEach(cell => {
            cell.style.display = ''; // Hiện tất cả các dữ liệu
        });
        document.querySelectorAll('#attendanceList .list-column').forEach(column => {
            column.style.display = ''; // Hiện tất cả các cột
        });
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
