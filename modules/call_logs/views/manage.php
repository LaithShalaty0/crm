<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$isGridView = 0;
if ($this->session->has_userdata('cl_grid_view') && $this->session->userdata('cl_grid_view') == 'true') {
    $isGridView = 1;
}
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if(has_permission('call_logs','','create')){ ?>
                            <a href="<?php echo admin_url('call_logs/call_log'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('new_call_log'); ?></a>
                            <?php } ?>
                            <a href="<?php echo admin_url('call_logs/overview'); ?>" data-toggle="tooltip" title="<?php echo _l('cl_gantt_overview'); ?>" class="btn btn-default"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?php echo _l('cl_overview'); ?></a>

                            <a href="<?php echo admin_url('call_logs/switch_grid/'.$switch_grid); ?>" class="btn btn-default hidden-xs">
                                <?php if($switch_grid == 1){ echo _l('cl_switch_to_list_view');}else{echo _l('cl_switch_to_grid_view');}; ?>
                            </a>
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />

                        <div class="clearfix mtop20"></div>
                        <div class="row" id="call-logs-table">
                            <?php if($isGridView ==0){ ?>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p class="bold"><?php echo _l('filter_by'); ?></p>
                                        </div>
                                        <div class="col-md-2 cl-filter-column">
                                            <?php echo render_select('view_assigned',$staffs,array('staffid',array('firstname','lastname')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('cl_filter_staff')),array(),'no-mbot'); ?>
                                        </div>
                                        <div class="col-md-2 cl-filter-column">
                                            <?php echo render_select('view_by_rel_type',$rel_types,array('id',array('name')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('cl_type')),array(),'no-mbot'); ?>
                                        </div>
                                        <div class="col-md-2 cl-filter-column">
                                            <?php echo render_select('view_by_lead',$leads,array('id',array('name')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('cl_lead')),array(),'no-mbot'); ?>
                                        </div>
                                        <div class="col-md-2 cl-filter-column">
                                            <?php echo render_select('view_by_customer',$clcustomers,array('userid',array('company')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('cl_customer')),array(),'no-mbot'); ?>
                                        </div>
                                        <div class="col-md-2 cl-filter-column">
                                            <?php echo render_select('view_by_status',$cl_filter_status,array('id',array('name')),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('cl_filter_status')),array(),'no-mbot'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />
                            <?php } ?>

                            <div class="col-md-12">
                                <?php if($this->session->has_userdata('cl_grid_view') && $this->session->userdata('cl_grid_view') == 'true') { ?>
                                    <div class="grid-tab" id="grid-tab">
                                        <div class="row">
                                            <div id="cl-grid-view" class="container-fluid">

                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <?php render_datatable(array(
                                        _l('cl_type'),
                                        _l('cl_purpose_of_call'),
                                        _l('cl_caller'),
                                        _l('cl_contact'),
                                        _l('cl_start_time'),
                                        _l('cl_end_time'),
                                        _l('cl_duration'),
                                        _l('cl_call_follow_up'),
                                        _l('cl_is_important'),
                                        _l('cl_is_completed'),
                                    ),'call_logs'); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Call Log Modal-->
<div class="modal fade call_log-modal" id="call_log-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content data">

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var _lnth = 12;

    $(function(){
        var TblServerParams = {
            "assigned": "[name='view_assigned']",
            "view_by_rel_type": "[name='view_by_rel_type']",
            "view_by_lead": "[name='view_by_lead']",
            "view_by_customer": "[name='view_by_customer']",
            "view_by_status": "[name='view_by_status']",
        };

        if(<?php echo $isGridView ?> == 0) {

            var tAPI = initDataTable('.table-call_logs', admin_url+'call_logs/table', [2, 3], [2, 3], TblServerParams);
            $.each(TblServerParams, function(i, obj) {
                $('select' + obj).on('change', function() {
                    $('table.table-call_logs').DataTable().ajax.reload()
                        .columns.adjust()
                        .responsive.recalc();
                });
            });
        }else{
            loadGridView();

            $(document).off().on('click','a.paginate',function(e){
                e.preventDefault();
                console.log("$(this)", $(this).data('ci-pagination-page'))
                var pageno = $(this).data('ci-pagination-page');
                var formData = {
                    search: $("input#search").val(),
                    start: (pageno-1),
                    length: _lnth,
                    draw: 1
                }
                gridViewDataCall(formData, function (resposne) {
                    $('div#grid-tab').html(resposne)
                })
            });
        }
    });
</script>
</body>
</html>
