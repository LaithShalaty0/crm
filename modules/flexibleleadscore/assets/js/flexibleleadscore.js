"use strict";

$(document).ready(function () {
    $(document).on('change', '#flexibleleadscore_criteria', function() {
        const criteriaId = $(this).val();
        const container = $('#flexibleleadscore-dropdown-container');
        const url = $(container).data('url');
        const data = {
            action: 'get_criteria_value',
            criteria_id: criteriaId,
        }
        //make post request
        $.post(url, data, function(response) {
            if(response.success){
                $(container).html(response.html)

                if(response.refresh_selectpicker){
                    // Re-render select picker
                    $('#flexibleleadscore_criteria_value').selectpicker('render');
                }
            }
        }, 'json');
        return false;
    });

    function open_edit_criteria_modal(e){
        e.preventDefault();
        let url = $(this).data('link');
        //make post request
        $.post(url, {}, function(response) {
            if(response.success){
                $('#flexibleleadscore_add_criteria').remove()
                $('#wrapper').append(response.html)

                $('#flexibleleadscore_criteria').selectpicker('render');
                $('#flexibleleadscore_criteria_operator').selectpicker('render');
                $('#flexibleleadscore_criteria_value').selectpicker('render');

                $('#flexibleleadscore_add_criteria').modal('show');
            }
        }, 'json');
        return false;
    }
    
    function rebuild_add_criteria_modal(e){
        e.preventDefault();
        let url = $('#get-criteria-link').data('link');
        //make post request
        $.post(url, {}, function(response) {
            if(response.success){
                $('#flexibleleadscore_add_criteria').remove()
                $('#wrapper').append(response.html)

                $('#flexibleleadscore_criteria').selectpicker('render');
                $('#flexibleleadscore_criteria_operator').selectpicker('render');
                $('#flexibleleadscore_criteria_value').selectpicker('render');
            }
        }, 'json');
        return false;
    }

    $('.edit_criteria').on('click', open_edit_criteria_modal)
    $(document).on("hidden.bs.modal", '#flexibleleadscore_add_criteria', rebuild_add_criteria_modal);
});