<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                
            <div class="panel_s">
                <div class="panel-body">
                    <a href="<?php echo admin_url('account_planning/new_account'); ?>" class="btn btn-info mright5 test pull-left display-block"><?php echo htmlspecialchars(_l('new_account')); ?></a>
                    <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-clients"><?php echo htmlspecialchars(_l('bulk_actions')); ?></a>

                                        <div class="clearfix"></div>
                                    
                    <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                         <div class="modal-content">
                          <div class="modal-header">
                           <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                           <h4 class="modal-title"><?php echo htmlspecialchars(_l('bulk_actions')); ?></h4>
                       </div>
                       <div class="modal-body">
                          <?php if(has_permission('customers','','delete')){ ?>
                          <div class="checkbox checkbox-danger">
                            <input type="checkbox" name="mass_delete" id="mass_delete">
                            <label for="mass_delete"><?php echo htmlspecialchars(_l('mass_delete')); ?></label>
                        </div>
                        <hr class="mass_delete_separator" />
                        <?php } ?>
                        <div id="bulk_change">
                           <?php echo render_select('move_to_groups_customers_bulk[]',$groups,array('id','name'),'customer_groups','', array('multiple'=>true),array(),'','',false); ?>
                           <p class="text-danger"><?php echo htmlspecialchars(_l('bulk_action_customers_groups_warning')); ?></p>
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo htmlspecialchars(_l('close')); ?></button>
                       <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo htmlspecialchars(_l('confirm')); ?></a>
                   </div>
               </div><!-- /.modal-content -->
           </div><!-- /.modal-dialog -->
       </div><!-- /.modal -->
                        
                           <div class="clearfix mtop20"></div>
                           <?php
                           $table_data = array();
                           $_table_data = array(
                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>',
                            _l('id'),
                            _l('subject'),
                            _l('client_name'),
                            _l('time'),
                            _l('objective'),
                            _l('revenue_next_year'),
                            _l('margin'),
                            _l('wallet_share'),
                            _l('client_status'),
                            _l('bcg_model'),
                            );
                           foreach($_table_data as $_t){
                            array_push($table_data,$_t);
                        }
						//print_r($table_data);
                        render_datatable($table_data,'account-planning');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('account_planning/copy_settings'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        var tAPI = initDataTable('.table-account-planning', window.location.href, [0], [0]);
        $('input[name="exclude_inactive"]').on('change',function(){
            tAPI.ajax.reload();
        });
    });
    function customers_bulk_action(event) {
        var r = confirm(appLang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if(mass_delete == false || typeof(mass_delete) == 'undefined'){
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function(){
              $.post(admin_url + 'clients/bulk_action', data).done(function() {
               window.location.reload();
           });
          },50);
        }
    }


</script>
</body>
</html>
