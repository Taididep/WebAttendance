let isConfirmingTime = false;

// Xử lý khi nhấn nút xác nhận thời gian
document.getElementById('confirmTimeButton').addEventListener('click', function () {
    const time = document.getElementById('timeInput').value;

    if (!time) {
        alert('Vui lòng chọn thời gian!');
        return;
    }

    isConfirmingTime = true;

    $.ajax({
        url: '../Attendance/save_time.php',
        method: 'POST',
        data: {
            schedule_id: scheduleId,
            time: time
        },
        success: function (response) {
            alert(response);
            location.reload();
        },
        error: function () {
            alert('Có lỗi xảy ra khi lưu thời gian.');
        }
    });
});

// Kiểm tra loại thao tác khi người dùng rời khỏi trang
window.addEventListener('beforeunload', function (event) {
    if (!isConfirmingTime) {
        // Nếu không phải xác nhận thời gian, kiểm tra loại thao tác
        const navType = performance.getEntriesByType('navigation')[0].type;

        if (navType === 'reload') {
            // Nếu là reload trang, cập nhật status = 1
            const url = `../Attendance/update_status.php?class_id=${classId}&schedule_id=${scheduleId}&status=1`;
            navigator.sendBeacon(url);
        } else if (navType === 'back_forward') {
            // Nếu là quay lại hoặc tiến tới trong lịch sử, reset status = 0
            const url = `../Attendance/reset_status.php?class_id=${classId}&schedule_id=${scheduleId}`;
            navigator.sendBeacon(url);
        } else {
            // Nếu là chuyển trang, gửi yêu cầu reset status = 0
            const url = `../Attendance/reset_status.php?class_id=${classId}&schedule_id=${scheduleId}`;
            navigator.sendBeacon(url);
        }
    }
});
