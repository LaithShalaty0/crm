"use strict";

$(function() {

    "use strict";

    $('body').on('blur', '.form-field.editing', function() {
        $.Shortcuts.start();
    });

    $('body').on('focus', '.form-field.editing', function() {
        $.Shortcuts.stop();
    });

    var $create_task_on_duplicate = $('#create_task_on_duplicate');

    $('#allow_duplicate').on('change', function() {
        $('.duplicate-settings-wrapper').toggleClass('hide');
    });

    $('#notify_lead_imported').on('change', function() {
        $('.select-notification-settings').toggleClass('hide');
    });

    $('#track_duplicate_field,#track_duplicate_field_and').on('change', function() {
        var selector = ($(this).hasClass('track_duplicate_field') ? 'track_duplicate_field_and' : 'track_duplicate_field')
        $('#' + selector + ' option').removeAttr('disabled', true);
        var val = $(this).val();
        if (val !== '') {
            $('#' + selector + ' option[value="' + val + '"]').attr('disabled', true);
        }
        $('#' + selector + '').selectpicker('refresh');
    });

    setTimeout(function() {
        $(".form-builder-save").wrap("<div class='btn-bottom-toolbar text-right'></div>");
        var $btnToolbar = $('body').find('#tab_form_build .btn-bottom-toolbar');
        $btnToolbar = $('#tab_form_build').append($btnToolbar);
        $btnToolbar.find('.btn').addClass('btn-info');
    }, 100);


    appValidateForm('#form_info', {
        name: 'required',
        lead_source: 'required',
        lead_status: 'required',
        language: 'required',
        success_submit_msg: 'required',
        submit_btn_name: 'required',
        responsible: {
            required: {
                depends: function(element) {
                    var isRequiredByNotifyType = ($('input[name="notify_type"]:checked').val() == 'assigned') ? true : false;
                    var isRequiredByAssignTask = ($create_task_on_duplicate.is(':checked')) ? true : false;
                    var isRequired = isRequiredByNotifyType || isRequiredByAssignTask;
                    if (isRequired) {
                        $('[for="responsible"]').find('.req').removeClass('hide');
                    } else {
                        $(element).next('p.text-danger').remove();
                        $('[for="responsible"]').find('.req').addClass('hide');
                    }
                    return isRequired;
                }
            }
        }
    });

    var $notifyTypeInput = $('input[name="notify_type"]');
    $notifyTypeInput.on('change', function() {
        $('#form_info').validate().checkForm()
    });
    $notifyTypeInput.trigger('change');

    $create_task_on_duplicate.on('change', function() {
        $('#form_info').validate().checkForm()
    });

    $create_task_on_duplicate.trigger('change');

});