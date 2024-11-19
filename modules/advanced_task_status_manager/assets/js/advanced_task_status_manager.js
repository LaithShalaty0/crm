$(function () {
    initDataTable('.table-task_statuses', window.location.href);
});

function add_status(type) {
    requestGet('advanced_task_status_manager/create_' + type).done(function (response) {
        $('#_task-status').html(response);
        $('#task-status-modal').modal('hide');
        $("body").find('#_task_status_modal').modal({
            show: true,
            backdrop: 'static'
        });
    });
}

function edit_status(taskStatusId, type) {
    requestGet(`advanced_task_status_manager/edit_${type}_status/${taskStatusId}`).done(function (response) {
        $('#_task-status').html(response);
        $('#task-status-modal').modal('hide');
        $("body").find('#_task_status_modal').modal({
            show: true,
            backdrop: 'static'
        });
    });
}