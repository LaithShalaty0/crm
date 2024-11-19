<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: New Customers
  Description: New Customers
  
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
      SELECT company, datecreated, userid 
      FROM " . db_prefix() . "clients 
  ";
  if ($date_from !== null && $date_to !== null) {
    $sql .= "
          WHERE datecreated >= '" . $widget_req_from . " 00:00:00' 
          AND datecreated <= '" . $widget_req_to . " 23:59:59' 
      ";
  }
  $sql .= "
      ORDER BY datecreated DESC 
      LIMIT 0, 10
  ";

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();

?>

<div class="widget widget-new-customers widget-<?= $widget['id'] ?>" data-widget-id="<?= $widget['id'] ?>">
  <div class="">
    <div class="panel_s">
      <div class="panel-body">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?php echo _l('top_10_new_customers'); ?></h4>
        <a href="<?php echo admin_url('clients'); ?>"
          class="pull-right mtop5"><?php echo _l('home_stats_full_report'); ?></a>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>

        <table class="table dataTable no-footer dtr-inline">
          <thead>
            <th><?= _l('company') ?></th>
            <th><?= _l('created_at') ?></th>
          </thead>
          <tbody>
            <?php if (count($widget_data) > 0) { ?>
              <?php foreach ($widget_data as $widget_row) { ?>
                <tr>
                  <td><a
                      href="<?= admin_url('clients/client/' . $widget_row['userid']) ?>"><?= $widget_row['company'] ?></a>
                  </td>
                  <td><?= time_ago($widget_row['datecreated']) ?></td>
                </tr>
              <?php } ?>
            <?php } else { ?>
              <tr>
                <td colspan="2"><?= _l('not_found') ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>