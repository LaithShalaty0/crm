<?php $selected = isset($criterion) ? $criterion['flexibleleadscore_criteria_value'] : '' ?>
<?php 
echo render_select('flexibleleadscore_criteria_value', $lead_statuses, ['id', 'name'], '', $selected, [
       'placeholder' => flexiblels_lang('criteria-value')
], [], '', '', false);