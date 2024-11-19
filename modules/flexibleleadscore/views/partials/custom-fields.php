<?php $selected = isset($criterion) ? $criterion['flexibleleadscore_criteria_value'] : '' ?>
<?php
if ($field['type'] == "multiselect") {
    //add multiselect field selectpicker
    ?>
    <select name="flexibleleadscore_criteria_value[]" id="flexibleleadscore_criteria_value" class="form-control" multiple>
        <?php foreach ($custom_values as $custom_value) { ?>
            <option value="<?php echo $custom_value['id']; ?>" <?php echo in_array($custom_value['id'], $selected) ? 'selected' : '' ?>>
                <?php echo $custom_value['name']; ?>
            </option>
        <?php } ?>
    </select>
    <?php
} else {
    echo render_select('flexibleleadscore_criteria_value', $custom_values, ['id', 'name'], '', $selected, [
        'placeholder' => flexiblels_lang('criteria-value')
    ], [], '', '', false);
}