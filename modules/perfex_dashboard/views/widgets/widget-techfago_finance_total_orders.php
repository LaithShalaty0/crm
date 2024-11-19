<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total Orders
  Description: Total Orders
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = $this->input->get('period_from');
  $widget_req_to = $this->input->get('period_to');

  $date_from = null;
  if ($widget_req_from !== null) {
    $date_from = DateTime::createFromFormat('Y-m-d', $widget_req_from);
    if ($date_from === false) {
      $date_from = null; // Invalid date format, set to null
    } else {
      $widget_req_from = $date_from->format('Y-m-d');
    }
  }

  $date_to = null;
  if ($widget_req_to !== null) {
    $date_to = DateTime::createFromFormat('Y-m-d', $widget_req_to);
    if ($date_to === false) {
      $date_to = null; // Invalid date format, set to null
    } else {
      $widget_req_to = $date_to->format('Y-m-d');
    }
  }

  $sql = "
      SELECT COUNT(*) AS TOTAL_ROWS 
      FROM " . db_prefix() . "invoices TBLInvoices
  ";
  if ($date_from !== null && $date_to !== null) {
    $sql .= "
          WHERE TBLInvoices.date >= '" . $widget_req_from . "' 
          AND TBLInvoices.date <= '" . $widget_req_to . "'
      ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();

?>

<div class="widget widget-finance-total-orders widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter warning">
    <i class="fa fa-file"></i>
    <span class="count-numbers"><?= $widget_data[0]['TOTAL_ROWS'] ?></span>
    <span class="count-name"><?= _l('orders') ?></span>
  </div>
</div>