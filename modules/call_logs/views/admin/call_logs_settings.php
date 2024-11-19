<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Show options for call logs in Setup->Settings->Call Logs settings
 */
$enabled = get_option('staff_members_create_inline_cl_types'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('cl_types_enable_option'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_cl_type" name="settings[staff_members_create_inline_cl_types]" value="1" <?= ($enabled == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_cl_type"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_cl_type" name="settings[staff_members_create_inline_cl_types]" value="0" <?= ($enabled == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_cl_type">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>

<?php $enabled = get_option('staff_members_create_inline_call_direction'); ?>
<div class="form-group">
    <label for="pusher_chat" class="control-label clearfix">
        <?php echo _l('cl_call_direction_enable_option'); ?>
    </label>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_1_call_direction" name="settings[staff_members_create_inline_call_direction]" value="1" <?= ($enabled == '1') ? ' checked' : '' ?>>
        <label for="y_opt_1_call_direction"><?php echo _l('settings_yes'); ?></label>
    </div>
    <div class="radio radio-primary radio-inline">
        <input type="radio" id="y_opt_2_call_direction" name="settings[staff_members_create_inline_call_direction]" value="0" <?= ($enabled == '0') ? ' checked' : '' ?>>
        <label for="y_opt_2_call_direction">
            <?php echo _l('settings_no'); ?>
        </label>
    </div>
</div>
<hr>

<div class="form-group">
    <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('cl_goal_based_calls_tooltip') ?>"></i>
    <label class="control-label" for="staff_members_daily_calls_target"><?php echo _l('staff_members_daily_calls_target'); ?></label>
    <input type="text" name="settings[staff_members_daily_calls_target]" id="staff_members_daily_calls_target" class="form-control" value="<?php echo get_option('staff_members_daily_calls_target'); ?>">
</div>
<hr>

<div class="form-group">
    <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('cl_goal_based_calls_tooltip') ?>"></i>
    <label class="control-label" for="staff_members_monthly_calls_target"><?php echo _l('staff_members_monthly_calls_target'); ?></label>
    <input type="text" name="settings[staff_members_monthly_calls_target]" id="staff_members_monthly_calls_target" class="form-control" value="<?php echo get_option('staff_members_monthly_calls_target'); ?>">
</div>