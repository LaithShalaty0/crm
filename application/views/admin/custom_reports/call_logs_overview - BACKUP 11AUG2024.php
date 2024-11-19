<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin"><?php echo _l('call_logs_overview'); ?></h4>
                <hr>

                <!-- Date Range Form -->
                <form method="get" action="<?php echo admin_url('custom_reports/call_logs_overview'); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date"><?php echo _l('start_date'); ?></label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="<?php echo html_escape($start_date); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date"><?php echo _l('end_date'); ?></label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="<?php echo html_escape($end_date); ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo _l('filter'); ?></button>
                </form>

                <hr>

                <div class="report-dates">
                    <p>Report Date Range: From <?php echo htmlspecialchars($start_date); ?> to
                        <?php echo htmlspecialchars($end_date); ?></p>
                </div>

                <!-- Display Total Calls -->
                <h5><?php echo _l('total_calls'); ?>: <?php echo html_escape($total_calls); ?></h5>

                <!-- Display Call Duration -->
                <h5><?php echo _l('call_duration'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo _l('staff'); ?></th>
                            <th><?php echo _l('duration'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($call_duration as $duration): ?>
                            <tr>
                                <td><?php echo staff_profile_image($duration['staffid'], ['staff-profile-image-small']); ?>
                                </td>
                                <td><?php echo seconds_to_time_format($duration['total_duration']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Display Call Outcomes -->
                <h5><?php echo _l('call_outcomes'); ?></h5>
                <ul>
                    <li><?php echo _l('successful_calls'); ?>:
                        <?php echo html_escape($call_outcomes['successful_calls']); ?>
                    </li>
                    <li><?php echo _l('unsuccessful_calls'); ?>:
                        <?php echo html_escape($call_outcomes['unsuccessful_calls']); ?>
                    </li>
                    <li><?php echo _l('follow_up_required'); ?>:
                        <?php echo html_escape($call_outcomes['follow_up_required']); ?>
                    </li>
                </ul>

                <!-- Display Lead Sources and Statuses -->
                <h5><?php echo _l('lead_sources_statuses'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo _l('lead_source'); ?></th>
                            <th><?php echo _l('lead_status'); ?></th>
                            <th><?php echo _l('total_leads'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lead_sources_statuses as $source_status): ?>
                            <tr>
                                <td><?php echo html_escape($source_status['lead_source']); ?></td>
                                <td><?php echo html_escape($source_status['lead_status']); ?></td>
                                <td><?php echo html_escape($source_status['total_leads']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Display Salesperson Performance -->
                <h5><?php echo _l('salesperson_performance'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo _l('staff'); ?></th>
                            <th><?php echo _l('total_calls'); ?></th>
                            <th><?php echo _l('total_duration'); ?></th>
                            <th><?php echo _l('successful_calls'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salesperson_performance as $performance): ?>
                            <tr>
                                <td><?php echo staff_profile_image($performance['staffid'], ['staff-profile-image-small']); ?>
                                </td>
                                <td><?php echo html_escape($performance['total_calls']); ?></td>
                                <td><?php echo seconds_to_time_format($performance['total_duration']); ?></td>
                                <td><?php echo html_escape($performance['successful_calls']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Display Calls Results -->
                <h5><?php echo _l('calls_results'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo _l('result'); ?></th>
                            <th><?php echo _l('total_count'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($calls_results as $result): ?>
                            <tr>
                                <td><?php echo html_escape($result['result']); ?></td>
                                <td><?php echo html_escape($result['total_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Display Report Based on Called Lead, Lead Status, and Lead Source -->
                <h5><?php echo _l('lead_reports'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo _l('lead_name'); ?></th>
                            <th><?php echo _l('lead_source'); ?></th>
                            <th><?php echo _l('lead_status'); ?></th>
                            <th><?php echo _l('total_calls'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lead_reports as $report): ?>
                            <tr>
                                <td><?php echo html_escape($report['lead_name']); ?></td>
                                <td><?php echo html_escape($report['lead_source']); ?></td>
                                <td><?php echo html_escape($report['lead_status']); ?></td>
                                <td><?php echo html_escape($report['total_calls']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>