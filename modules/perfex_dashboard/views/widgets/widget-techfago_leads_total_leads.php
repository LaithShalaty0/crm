<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total leads
  Description: Total leads
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = $this->input->get('period_from');
  if ($widget_req_from !== null) {
    $widget_req_from = DateTime::createFromFormat('Y-m-d', $widget_req_from);
    if ($widget_req_from !== false) {
      $widget_req_from = $widget_req_from->format('Y-m-d');
    } else {
      $widget_req_from = null;
    }
  }

  $widget_req_to = $this->input->get('period_to');
  if ($widget_req_to !== null) {
    $widget_req_to = DateTime::createFromFormat('Y-m-d', $widget_req_to);
    if ($widget_req_to !== false) {
      $widget_req_to = $widget_req_to->format('Y-m-d');
    } else {
      $widget_req_to = null;
    }
  }

  $sql = "
    SELECT COUNT(*) AS TOTAL_ROWS 
    FROM " . db_prefix() . "leads TBLLeads
  ";
  if ($widget_req_from !== null && $widget_req_to !== null) {
    $sql .= "
      WHERE TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00' AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59'
    ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-leads-total-leads" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter primary">
    <i class="fa fa-user-o"></i>
    <span class="count-numbers"><?= $widget_data[0]['TOTAL_ROWS'] ?></span>
    <span class="count-name"><?= _l('leads') ?></span>
  </div>
</div>