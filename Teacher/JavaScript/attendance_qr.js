window.addEventListener('beforeunload', function() {
    const classId = '<?php echo urlencode($class_id); ?>';
    const scheduleId = '<?php echo urlencode($schedule_id); ?>';

    // Gửi yêu cầu không đồng bộ để cập nhật status thành 0 khi đóng trang
    navigator.sendBeacon(`../Attendance/reset_status.php?class_id=${classId}&schedule_id=${scheduleId}`);
});