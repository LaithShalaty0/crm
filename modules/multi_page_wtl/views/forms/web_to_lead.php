<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $form->name; ?></title>
  <?php app_external_form_header($form);?>
  <?php hooks()->do_action('mpwtl_app_web_to_lead_form_head');?>
  <link rel="stylesheet" type="text/css" href="<?php echo module_dir_url(MPWTL_MODULE_NAME, 'assets/css/' . MPWTL_MODULE_NAME . '_form.css') ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo site_url(MPWTL_MODULE_NAME . '/mpwtl_custom_css/' . $form->form_key); ?>">
  <style type="text/css">
    #progressbar li{width: <?php echo 100 / count($form_fields); ?>%;}
  </style>
</head>
<body class="web-to-lead <?php echo $form->form_key; ?>"<?php if (is_rtl(true)) {echo ' dir="rtl"';}?>>
  <div class="container-fluid">
    <div class="row">
      <div class="<?php if ($this->input->get('col')) {echo $this->input->get('col');} else {echo 'col-md-12';}?>">
        <div id="response"></div>
        <?php echo form_open_multipart($this->uri->uri_string(), array('id' => $form->form_key, 'class' => 'steps disable-on-submit')); ?>
        <?php hooks()->do_action('web_to_lead_form_start');?>
        <?php echo form_hidden('key', $form->form_key); ?>
        <ul id="progressbar">
          <?php $t = 0;foreach ($form_fields as $pageSet) {$t++;?>
              <li <?php echo $t == 1 ? 'class="active"' : ''; ?> ></li>
          <?php }?>
        </ul>
          <?php $i = 0;foreach ($form_fields as $pageSet) {$i++;?>
            <fieldset <?php echo ($i > 1) ? "disabled" : ""; ?>>
          <?php foreach ($pageSet as $fieldSet) {?>
               <?php mpwtl_render_form_builder_field($fieldSet);?>

            <?php }?>

          <?php if (count($form_fields) == $i) {?>
              <div class="col-md-12">
               <?php if (show_recaptcha() && $form->recaptcha == 1) {?>
                 <div class="form-group"><div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
                 <div id="recaptcha_response_field" class="text-danger"></div>
               </div>
               <?php }?>
               <?php if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_lead_form') == 1) {?>
               <div class="col-md-12">
                <div class="checkbox chk">
                  <input type="checkbox" name="accept_terms_and_conditions" required="true" id="accept_terms_and_conditions" <?php echo set_checkbox('accept_terms_and_conditions', 'on'); ?>>
                  <label for="accept_terms_and_conditions">
                    <?php echo _l('gdpr_terms_agree', terms_url()); ?>
                  </label>
                </div>
                </div>
                <?php }?>
                 <div class="clearfix"></div>
              </div>
          <?php }?>
            <div class="row">
              <div class="col-md-12">
                <?php if ($i > 1) {?>
                  <input type="button" data-page="<?php echo $i; ?>" name="previous" class="previous action-button" value="Previous" />
                <?php }?>
                <?php if (count($form_fields) != $i) {?>
                <input type="button" data-page="<?php echo $i; ?>" name="next" class="next action-button" value="Next" />
              <?php } else {?>
                <button class="primary large action-button next btn btn-success" id="form_submit" type="submit"><?php echo $form->submit_btn_name; ?></button>
              <?php }?>
              </div>
            </div>
          </fieldset>
          <?php }?>

      <?php hooks()->do_action('web_to_lead_form_end');?>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
<?php app_external_form_footer($form);?>
<script>
 var form_id = '#<?php echo $form->form_key; ?>';
</script>
<?php hooks()->do_action('mpwtl_app_web_to_lead_form_footer');?>
<script type="text/javascript" src="<?php echo site_url('assets/plugins/jquery-ui/jquery-ui.min.js'); ?>"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script type="text/javascript" src="<?php echo module_dir_url(MPWTL_MODULE_NAME, 'assets/js/mpwtl.js') ?>"></script>
<script type="text/javascript" src="<?php echo module_dir_url(MPWTL_MODULE_NAME, 'assets/js/' . MPWTL_MODULE_NAME . '_form.js') ?>"></script>
<script type="text/javascript">
  var form = $("#<?php echo $form->form_key; ?>").show();
</script>
</body>
</html>
