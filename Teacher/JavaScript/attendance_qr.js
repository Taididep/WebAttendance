window.addEventListener('beforeunload', function () {
    // Gửi yêu cầu không đồng bộ để cập nhật status thành 0 khi đóng trang
    navigator.sendBeacon(`../Attendance/reset_status.php?class_id=${classId}&schedule_id=${scheduleId}`);
});
