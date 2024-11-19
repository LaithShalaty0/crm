<?php
defined('BASEPATH') || exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4><?php echo _l('whatsapp_log_details'); ?> </h4>
                                </div>
                                <?php
                                if (has_permission('whatsapp_api', '', 'whatsapp_log_details_clear')) {
                                ?>
                                    <div class="col-md-6">
                                        <a href="<?php echo admin_url(WHATSAPP_API_MODULE . '/whatsapp_log_details/clear_webhook_log'); ?>" class="btn btn-danger pull-right"><?php echo _l('clear_activity_log'); ?></a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php render_datatable([
                            _l('template_name'),
                            _l('response_code'),
                            _l('recorded_on'),
                            _l('actions'),
                        ], 'whatsapp_api_logs');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    initDataTable('.table-whatsapp_api_logs', admin_url + "whatsapp_api/whatsapp_log_details/whatsapp_log_details_table", undefined, undefined, undefined, [2, 'Desc']);
</script>