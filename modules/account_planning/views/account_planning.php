<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php if(isset($client) && $client->registration_confirmed == 0 && is_admin()){ ?>
               <div class="alert alert-warning">
                  <?php echo htmlspecialchars(_l('customer_requires_registration_confirmation')); ?>
                  <br />
                  <a href="<?php echo admin_url('clients/confirm_registration/'.$client->userid); ?>"><?php echo htmlspecialchars(_l('confirm_registration')); ?></a>
               </div>
            <?php } else if(isset($client) && $client->active == 0 && $client->registration_confirmed == 1){ ?>
            <div class="alert alert-warning">
               <?php echo htmlspecialchars(_l('customer_inactive_message')); ?>
               <br />
               <a href="<?php echo admin_url('clients/mark_as_active/'.$client->userid); ?>"><?php echo htmlspecialchars(_l('mark_as_active')); ?></a>
            </div>
            <?php } ?>
            <?php if(isset($client) && $client->leadid != NULL){ ?>
            <div class="alert alert-info">
               <a href="<?php echo admin_url('leads/index/'.$client->leadid); ?>" onclick="init_lead(<?php echo htmlspecialchars($client->leadid); ?>); return false;"><?php echo htmlspecialchars(_l('customer_from_lead',_l('lead'))); ?></a>
            </div>
            <?php } ?>
            <?php if(isset($client) && (!has_permission('customers','','view') && is_customer_admin($client->userid))){?>
            <div class="alert alert-info">
               <?php echo htmlspecialchars(_l('customer_admin_login_as_client_message',get_staff_full_name(get_staff_user_id()))); ?>
            </div>
            <?php } ?>
         </div>
         <div class="col-md-3">
            <div class="panel_s mbot5">
               <div class="panel-body padding-10">
                  <h4 class="bold">
                     <?php if(has_permission('customers','','delete') || is_admin()){ ?>
                     <div class="btn-group pull-left mright10">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-left">
                           <?php if(has_permission('customers','','delete')){ ?>
                           <li>
                              <a href="<?php echo admin_url('account_planning/delete/9'); ?>" class="text-danger delete-text _delete"><i class="fa fa-remove"></i> <?php echo htmlspecialchars(_l('delete')); ?>
                              </a>
                           </li>
                           <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                     #<?php echo htmlspecialchars($account->id.' - '.$account->client_name); ?>
                  </h4>
               </div>
            </div>
            <?php $this->load->view('account_planning/tabs'); ?>
         </div>
         <div class="col-md-9">
            <div class="panel_s">
               <div class="panel-body">
                  <div>
                     <div class="tab-content">
                        <?php $this->load->view('account_planning/groups/'.$group); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<script>
$('select[name="client_id"]').on('change', function() {
     var val = $(this).val();
     requestGetJSON('account_planning/client_change_data/' + val).done(function(response) {
      $('.billing_street').text(response['billing_shipping'][0]['billing_street']);
      $('.billing_city').text(response['billing_shipping'][0]['billing_city']);
      $('.billing_state').text(response['billing_shipping'][0]['billing_state']);
      $('.billing_country').text(response['billing_shipping'][0]['billing_country']);
      $('.billing_zip').text(response['billing_shipping'][0]['billing_zip']);
     });

 });
$('.due-diligence-form-submiter').on('click', function() {
   $('input[name="financial"]').val(hot.getData());
   $('input[name="marketing_activities"]').val(hot_2.getData());
});
$('.service-ability-offering-form-submiter').on('click', function() {
   $('input[name="service_ability_offering"]').val(service_ability_offering.getData());
   $('input[name="current_service"]').val(current_service.getData());
});
$('.planning-form-submiter').on('click', function() {
   $('input[name="todo_list"]').val(hot.getData());
});
$('#radioBtn a').on('click', function(){
    var sel = $(this).data('title');
    var tog = $(this).data('toggle');
    $('input[name="'+tog+'"]').prop('value', sel);
    
    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('notActive');
    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('notActive').addClass('active');
})
</script>
  <script id="code">
    function new_objective(){
      _validate_form($('form'),{ objective_name:'required'});

      $('#objective_hidden').find('input').remove();
      $('#new_objective .add-title').removeClass('hide');
      $('#new_objective .edit-title').addClass('hide');
      $('#new_objective').modal('show');
      $('#new_objective input[name="objective_name"]').val('');
   }
   function new_items(){
      _validate_form($('form'),{ items_name:'required', objective: 'required'});

      $('#item_hidden').find('input').remove();
      $('#new_items .add-title').removeClass('hide');
      $('#new_items .edit-title').addClass('hide');
      $('#new_items').modal('show');
      $('#new_items input[name="items_name"]').val('');
   }

   function edit_objective(invoker,id){
      _validate_form($('form'),{ objective_name:'required'});
      var name = $(invoker).data('name');

      $('#objective_hidden').append(hidden_input('id',id));
      $('#new_objective input[name="objective_name"]').val(name);
      $('#objective_hidden').append(hidden_input('id',id));
      $('#new_objective .edit-title').removeClass('hide');
      $('#new_objective .add-title').addClass('hide');
      $('#new_objective').modal('show');
   }
   function edit_items(invoker,id){
      _validate_form($('form'),{ items_name:'required', objective: 'required'});
      var name = $(invoker).data('name');
      var objective = $(invoker).data('objective');

      $('#item_hidden').append(hidden_input('id',id));
      $('#new_items input[name="items_name"]').val(name);
      $('#new_items select[name="objective"]').val(objective).change();
      $('#new_items .edit-title').removeClass('hide');
      $('#new_items .add-title').addClass('hide');
      $('#new_items').modal('show');
   }
   function edit_task(invoker,id){
      _validate_form($('form'),{ items_id: 'required' ,task_name:'required'});
      var name = $(invoker).data('name');
      var item = $(invoker).data('item');
      var prioritization = $(invoker).data('prioritization');
      var pic = $(invoker).data('pic');
      var deadline = $(invoker).data('deadline');
      var status = $(invoker).data('status');

      $('#task_hidden').append(hidden_input('id',id));
      $('#new_task input[name="task_name"]').val(name);
      $('#new_task input[name="pic"]').val(pic);
      $('#new_task input[name="deadline"]').val(deadline);
      $('#new_task select[name="status"]').val(status).change();
      $('#new_task select[name="prioritization"]').val(prioritization).change();
      $('#new_task select[name="items_id"]').val(item).change();
      $('#new_task .edit-title').removeClass('hide');
      $('#new_task .add-title').addClass('hide');
      $('#new_task').modal('show');
   }
    function edit_user(invoker,id){
      _validate_form($('form'),{user:'required', name:'required', password:'unrequired'});
      $('label[for="password"] small').remove();
      var user = $(invoker).data('user');
      var name = $(invoker).data('name');
      $('#additional').append(hidden_input('id',id));
      $('#user_api input[name="user"]').val(user);
      $('#user_api input[name="name"]').val(name);
      $('input[name="password"]').val('');
      $('#password_note').removeClass('hide');
      $('#user_api').modal('show');
      $('.add-title').addClass('hide');
   }

 $('select[name="client_status"]').on('change', function(){
  if(this.value == 'Green'){
    $('#client_status_color').css('background','#84C529');
  }
  if(this.value == 'Red'){
    $('#client_status_color').css('background','#fc2d42');
  }
  if(this.value == 'Yellow'){
    $('#client_status_color').css('background','#FF0');
  }
 })

 function convert_to_task(invoker ,id, task_id){
     var new_task_url = admin_url + 'tasks/task?rel_id='+id+'&rel_type=account_planning&account_task_id='+task_id+'&account_planning_to_task=true';
     new_task(new_task_url, invoker);

      var subject = $(invoker).data('subject');
      var description = $(invoker).data('description');
      var priority = $(invoker).data('priority');
      var deadline = $(invoker).data('deadline');
      var pic = $(invoker).data('pic')+'|';
      
      
      if(priority == 'Low'){
        priority = 1;
      }else if(priority == 'Medium'){
        priority = 2;
      }else if(priority == 'High'){
        priority = 3;
      }
      $('body').on('shown.bs.modal', '#_task_modal', function() {
            // Init the task description editor
          if(!is_mobile()){
             $(this).find('#description').click();
          } else {
            $(this).find('#description').focus();
         }
         setTimeout(function(){

            $.each(pic.split("|"), function(i,e){
                $("#add_task_assignees option[value='" + e + "']").prop("selected", true);
            });
            $('#_task_modal select[id="add_task_assignees"]').change();
            
            $('#_task_modal input[name="name"]').val(subject);
            $('#_task_modal input[name="duedate"]').val(deadline);
            tinymce.get("description").setContent(description);
            $('#_task_modal select[name="priority"]').val(priority).change();
         },100);
   });
  }

$('a[name="preview-inv-btn"]').on('click', function() {
  var id = $(this).attr('id');
  var rel_id = $(this).attr('rel_id');
  view_inv_file(id, rel_id);
});
function view_inv_file(id, rel_id) {
      $('#inv_file_data').empty();
      $("#inv_file_data").load(admin_url + 'account_planning/file/' + id + '/' + rel_id, function(response, status, xhr) {
          if (status == "error") {
              alert_float('danger', xhr.statusText);
          }
      });
}

// Delete invoice attachment
function delete_invoice_attachment(id) {
    if (confirm_delete()) {
        requestGet('account_planning/delete_attachment/' + id).done(function(success) {
            if (success == 1) {
                $("body").find('[data-attachment-id="' + id + '"]').remove();
                init_invoice($("body").find('input[name="_attachment_sale_id"]').val());
            }
        }).fail(function(error) {
            alert_float('danger', error.responseText);
        });
    }
}

function init_editor_mindmap(selector, settings) {

    selector = typeof(selector) == 'undefined' ? '.tinymce_mindmap' : selector;
    var _editor_selector_check = $(selector);

    if (_editor_selector_check.length === 0) { return; }

    $.each(_editor_selector_check, function() {
        if ($(this).hasClass('tinymce-manual')) {
            $(this).removeClass('tinymce');
        }
    });

    // Original settings
    var _settings = {
        branding: false,
        selector: selector,
        browser_spellcheck: true,
        height: 400,
        theme: 'modern',
        skin: 'perfex',
        language: app.tinymce_lang,
        relative_urls: false,
        inline_styles: true,
        verify_html: false,
        cleanup: false,
        autoresize_bottom_margin: 25,
        valid_elements: '+*[*]',
        valid_children: "+body[style], +style[type]",
        apply_source_formatting: false,
        remove_script_host: false,
        removed_menuitems: 'newdocument restoredraft',
        forced_root_block: false,
        autosave_restore_when_empty: false,
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
        setup: function(ed) {
            // Default fontsize is 12
            ed.on('init', function() {
                this.getDoc().body.style.fontSize = '12pt';
            });
        },
        table_default_styles: {
            // Default all tables width 100%
            width: '100%',
        },
        plugins: [
            'advlist autoresize autosave lists link image print hr codesample',
            'visualblocks code fullscreen',
            'media save table contextmenu',
            'paste textcolor colorpicker',
            'leaui_mindmap',
        ],
        toolbar1: 'leaui_mindmap | fontselect fontsizeselect | forecolor backcolor | bold italic | alignleft aligncenter alignright alignjustify | image link | bullist numlist | restoredraft',
        file_browser_callback: elFinderBrowser,
    };

    // Add the rtl to the settings if is true
    isRTL == 'true' ? _settings.directionality = 'rtl' : '';
    isRTL == 'true' ? _settings.plugins[0] += ' directionality' : '';

    // Possible settings passed to be overwrited or added
    if (typeof(settings) != 'undefined') {
        for (var key in settings) {
            if (key != 'append_plugins') {
                _settings[key] = settings[key];
            } else {
                _settings['plugins'].push(settings[key]);
            }
        }
    }

    // Init the editor
    var editor = tinymce.init(_settings);
    $(document).trigger('app.editor.initialized');

    return editor;
}
init_editor_mindmap();
  </script>
</body>
</html>
