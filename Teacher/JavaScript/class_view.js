function openEditModal(schedule_id, date, start_time, end_time) {
    document.getElementById('schedule_id').value = schedule_id;
    document.getElementById('date').value = date;
    document.getElementById('start_time').value = start_time;
    document.getElementById('end_time').value = end_time;
    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
}