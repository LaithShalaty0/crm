<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total Customers
  Description: Total Customers
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = $this->input->get('period_from');
  $widget_req_to = $this->input->get('period_to');

  if ($widget_req_from !== null) {
    $date_from = DateTime::createFromFormat('Y-m-d', $widget_req_from);
    $widget_req_from = ($date_from !== false) ? $date_from->format('Y-m-d') : null;
  }

  if ($widget_req_to !== null) {
    $date_to = DateTime::createFromFormat('Y-m-d', $widget_req_to);
    $widget_req_to = ($date_to !== false) ? $date_to->format('Y-m-d') : null;
  }

  $sql = "
      SELECT COUNT(*) AS TOTAL_ROWS 
      FROM " . db_prefix() . "clients TBLClients
  ";
  if ($widget_req_from !== null && $widget_req_to !== null) {
    $sql .= "
          WHERE TBLClients.datecreated >= '" . $widget_req_from . " 00:00:00' 
          AND TBLClients.datecreated <= '" . $widget_req_to . " 23:59:59'
      ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();
?>

<div class="widget widget-finance-total-orders widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter danger">
    <i class="fa fa-users"></i>
    <span class="count-numbers"><?= $widget_data[0]['TOTAL_ROWS'] ?></span>
    <span class="count-name"><?= _l('customers') ?></span>
  </div>
</div>