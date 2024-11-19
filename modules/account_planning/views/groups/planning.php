<?php 
	
// Moving necessary dependencies to the correct place
$checkfolder = FCPATH . 'assets/plugins/tinymce/plugins/leaui_mindmap';
$srcloc = APP_MODULES_PATH . 'account_planning/assets/plugins/tinymce/plugins/leaui_mindmap'; 
$destloc = FCPATH . 'assets/plugins/tinymce/plugins/';

$searchfortinymcevalues = FCPATH . 'application/helpers/fields_helper.php';
$helper_contents = file_get_contents($searchfortinymcevalues);
file_put_contents($searchfortinymcevalues, str_replace('if (strpos($textarea_class, \'tinymce\') !== false)', 'if (strpos($textarea_class, \'tinymce\') !== false && strpos($textarea_class, \'mindmap\') == false )', $helper_contents)); 

if(!is_dir($checkfolder)){
  mkdir($checkfolder);
  shell_exec("cp -r $srcloc $destloc");
}

?>

<h4 class="customer-profile-group-heading"><?php echo htmlspecialchars(_l('planning')); ?></h4>
<div class="clearfix"></div>
<?php echo form_open_multipart(admin_url('account_planning/update_planning/'.$account->id),array('id'=>'service-ability-offering-form')); ?>
    <?php  if (has_permission('account_planning', '', 'edit')) { ?>

    <div class="btn-bottom-toolbar btn-toolbar-container-out text-right ap-calc100-20left">
    <button class="btn btn-info only-save planning-form-submiter">
    <?php echo htmlspecialchars(_l( 'submit')); ?>
    </button>
    </div>
  <?php } ?>
    <div class="row">
      <?php $value = (isset($account->subject) ? $account->subject : '') ?>
      <?php echo render_input('subject', 'subject',$value, 'text', array(), array(),'col-md-6'); ?>
      <?php $value = (isset($account->date) ? _d($account->date) : '') ?>
      <?php echo render_select('date', $month,array('id','name'), 'time', $value, array(), array(), 'col-md-6'); ?>
    </div>
    <h4 class="bold"><?php echo htmlspecialchars(_l('planning_a')); ?></h4>

    <?php $value = (isset($account->objectives) ? $account->objectives : '') ?>
    <?php echo render_textarea('objectives','',$value,array(),array(),'','tinymce'); ?>
    <div class="row" >
    <?php
    $value = (isset($account->revenue_next_year) ? app_format_number($account->revenue_next_year) : '') ?>
    <?php echo render_input('revenue_next_year',_l('revenue_next_year','($)'),$value,'text',array('data-type' => 'currency'), array(),'col-md-6'); ?>
    <?php $value = (isset($account->margin) ? $account->margin : '') ?>
    <?php echo render_input('margin', _l('margin','(%)'),$value,'number', array(), array(),'col-md-6'); ?>
    <?php $value = (isset($account->wallet_share) ? $account->wallet_share : '') ?>
    <?php echo render_input('wallet_share', _l('wallet_share','(%)'),$value,'number', array(), array(),'col-md-6'); ?>
    <?php $bcg_model = ['1' => ['id' => 'Question marks', 'name' => 'Question marks'],
                                    '2' => ['id' => 'Stars', 'name' => 'Stars'],
                                    '3' => ['id' => 'Dogs', 'name' => 'Dogs'],
                                    '4' => ['id' => 'Cash cows', 'name' => 'Cash cows'],
                                    ]; ?>
    <?php $value = (isset($account->client_status) ? $account->client_status : '');
      if($value == 'Green'){
          $color = '#84C529';
        }elseif($value == 'Red'){
          $color = '#fc2d42';
        }elseif($value == 'Yellow'){
          $color = '#FF0';
        }else{
          $color = '#fc2d42';
        }
    ?>

    <div class="form-group select-placeholder col-md-6">
       <label class="control-label"><?php echo htmlspecialchars(_l('client_status')); ?>:<div id="client_status_color" class="calendar-cpicker cpicker cpicker-big" style="float: right; background: <?php echo htmlspecialchars($color); ?>;"></div></label>
       <select class="selectpicker display-block mbot15" name="client_status" data-width="100%" data-none-selected-text="<?php echo htmlspecialchars(_l('dropdown_non_selected_tex')); ?>">
          <option value="Red" class="text-danger" <?php if(isset($account) && $account->client_status == "Red"){echo 'selected';} ?>>Red</option>
          <option value="Yellow" class="text-warning" <?php if(isset($account) && $account->client_status == "Yellow"){echo 'selected';} ?>>Yellow</option>
          <option value="Green" class="text-success" <?php if(isset($account) && $account->client_status == "Green"){echo 'selected';} ?>>Green</option>
       </select>
    </div>
    
    <?php $value = (isset($account->bcg_model) ? $account->bcg_model : '') ?>
    <?php echo render_select('bcg_model',$bcg_model,array('id','name'),'bcg_model', $value,array(),array(), 'col-md-6'); ?>
  </div>
    <h4 class="bold"><?php echo htmlspecialchars(_l('planning_b')); ?></h4>
    <?php $value = (isset($account->threat) ? $account->threat : '') ?>
    <?php echo render_textarea( 'threat', 'threat', $value,array(),array(),'','tinymce'); ?>
    <?php $value = (isset($account->opportunity) ? $account->opportunity : '') ?>
    <?php echo render_textarea( 'opportunity', 'opportunity', $value,array(),array(),'','tinymce'); ?>
    <?php $value = (isset($account->criteria_to_success) ? $account->criteria_to_success : '') ?>
    <?php echo render_textarea( 'criteria_to_success', 'criteria_to_success', $value,array(),array(),'','tinymce'); ?>
    <?php $value = (isset($account->constraints) ? $account->constraints : '') ?>
    <?php echo render_textarea( 'constraints', 'constraints', $value,array(),array(),'','tinymce'); ?>
    <h4 class="bold"><?php echo htmlspecialchars(_l('planning_c')); ?></h4>
    
<body>
<div id="sample">
  <label class="text-danger ap-danger"><?php echo htmlspecialchars(_l('label_mindmap','<i class="mce-ico mce-i-none ap-minmap-icon"></i>')); ?><?php echo htmlspecialchars(_l('label_mindmap_edit','<i class="mce-ico mce-i-none ap-minmap-icon"></i>')); ?></label>
    <?php $value = (isset($account->data_tree) ? $account->data_tree : '') ?>

  <?php echo render_textarea('data_tree','',$value,array(),array(),'','tinymce_mindmap'); ?>
</div>
</body><br>
    <label class="ap-font-500"><?php echo htmlspecialchars(_l('to_do_list')); ?></label><br>

  
    


<div id="todo_list"></div>
  <?php echo form_hidden('todo_list'); ?>
  <?php if(count($account->attachments) > 0){ ?>
   <div class="clearfix"></div>
   <hr />
   <p class="bold text-muted"><?php echo htmlspecialchars(_l('ticket_single_attachments')); ?></p>
   <?php foreach($account->attachments as $attachment){
      $attachment_url = site_url('account_planning/download_file/'.$attachment['attachment_key']);
      if(!empty($attachment['external'])){
        $attachment_url = $attachment['external_link'];
     }
     ?>
     <div class="mbot15 row inline-block full-width" data-attachment-id="<?php echo htmlspecialchars($attachment['id']); ?>">
      <div class="col-md-8">
         <a name="preview-inv-btn" class="ap-margin-right-5" rel_id = "<?php echo htmlspecialchars($attachment['rel_id']);?>" id = "<?php echo htmlspecialchars($attachment['id']);?>" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="<?php echo htmlspecialchars(_l('preview_file')); ?>"><i class="fa fa-eye"></i></a>
         <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
         <a href="<?php echo htmlspecialchars($attachment_url); ?>" target="_blank"><?php echo htmlspecialchars($attachment['file_name']); ?></a>
         <br />
         <small class="text-muted"> <?php echo htmlspecialchars($attachment['filetype']); ?></small>
      </div>
      <div class="col-md-4 text-right">
         <?php if($attachment['staffid'] == get_staff_user_id() || is_admin() || has_permission('account_planning', '', 'edit')){ ?>
         <a href="#" class="text-danger" onclick="delete_invoice_attachment(<?php echo htmlspecialchars($attachment['id']); ?>); return false;"><i class="fa fa-times"></i></a>
         <?php } ?>
      </div>
   </div>

   <?php } ?>
   <?php } ?>
   <hr />
   <div class="row attachments">
      <div class="attachment">
         <div class="col-md-5 mbot15">
            <div class="form-group">
               <label for="attachment" class="control-label">
               <?php echo htmlspecialchars(_l('ticket_single_attachments')); ?>
               </label>
               <div class="input-group">
                  <input type="file" extension="<?php echo str_replace('.','',get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                  <span class="input-group-btn">
                  <button class="btn btn-success add_more_attachments p8-half" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
                  </span>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
   </div>
</div>
<?php echo form_close(); ?>
<div id="inv_file_data"></div>

<div class="modal fade" id="new_items" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
              <span class="add-title"><?php echo htmlspecialchars(_l('new_item')); ?></span>
              <span class="edit-title"><?php echo htmlspecialchars(_l('edit_item')); ?></span>
            </h4>
         </div>
         <div class="modal-body">
            <div class="row">
               <div class="col-md-12">
                <div id="item_hidden"></div>
                  <?php echo render_select('objective',$objectives,array('id','name'),'objective'); ?>
                  <?php echo render_input('items_name','name_api'); ?>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo htmlspecialchars(_l('close')); ?></button>
            <a onclick="add_pic()" class="btn btn-success" data-dismiss="modal"><?php echo htmlspecialchars(_l('submit')); ?></a>
         </div>
      </div><!-- /.modal-content -->
   </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

var hotElement = document.querySelector('#todo_list');
  var hotSettings = {
  data: <?php echo json_encode($todo_list); ?>,
  columns: [
    {
      data: 'objective',
      wordWrap: true,
      type: 'text'
    },
    {
      data: 'item',
      wordWrap: true,
      type: 'text'
    },
    {
      data: 'action_needed',
      wordWrap: true,
      type: 'text'
    },
    {
      data: 'prioritization',
      editor: 'select',
      selectOptions: ['Low', 'Medium', 'High']
    },
    {
      data: 'pic',
      renderer: customDropdownRenderer,
      editor: "chosen",
      width: 150,
      chosenOptions: {
          multiple: true,
          data: <?php echo json_encode($staff); ?>
      }
    },
    {
      data: 'deadline',
      type: 'date',
      dateFormat: 'YYYY-MM-DD',
      correctFormat: true,
      // datePicker additional options (see https://github.com/dbushell/Pikaday#configuration)
      datePickerConfig: {
        // First day of the week (0: Sunday, 1: Monday, etc)
        firstDay: 0,
        showWeekNumber: true,
        numberOfMonths: 3,
      }
    },
    {
      data: 'status',
      editor: 'select',
      selectOptions: ['Processing', 'Delay', 'Complete']
    },
    {
      data: 'id',
      type: 'text'
    },
    {
      data: 'button',
      renderer: "html",
      readOnly: true
    },
  ],
    
  stretchH: 'all',
  autoWrapRow: true,
  rowHeights: 50,
  maxRows: 22,
  rowHeaders: true,
  colWidths: [150, 150, 150, 70, 150,70,70,70,60],
  colHeaders: [
    '<?php echo htmlspecialchars(_l('objective')); ?>',
    '<?php echo htmlspecialchars(_l('objective_items')); ?>',
    '<?php echo htmlspecialchars(_l('action_needed')); ?>',
    '<?php echo htmlspecialchars(_l('prioritization')); ?>',
    '<?php echo htmlspecialchars(_l('pic')); ?>',
    '<?php echo htmlspecialchars(_l('deadline')); ?>',
    '<?php echo htmlspecialchars(_l('status')); ?>',
	'',
    '', 
  ],
    columnSorting: {
    indicator: true
  },
  autoColumnSize: true,
  rowHeaders: true,
  width: '100%',
  height: 500,
  dropdownMenu: true,
  mergeCells: true,
  contextMenu: true,
  manualRowMove: true,
  manualColumnMove: true,
  multiColumnSorting: {
    indicator: true
  },
   hiddenColumns: {
    columns: [7],
    indicators: true
  },
  filters: true,
  manualRowResize: true,
  manualColumnResize: true
};
  var hot = new Handsontable(hotElement, hotSettings);


  function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
    var selectedId;
    var optionsList = cellProperties.chosenOptions.data;

    if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
        Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
        return td;
    }

    var values = (value + "").split("|");
    value = [];
    for (var index = 0; index < optionsList.length; index++) {

        if (values.indexOf(optionsList[index].id + "") > -1) {
            selectedId = optionsList[index].id;
            value.push(optionsList[index].label);
        }
    }
    value = value.join(", ");

    Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
    return td;
}
</script>