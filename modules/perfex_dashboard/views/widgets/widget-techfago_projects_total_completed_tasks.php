<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Total completed tasks
  Description: Total completed tasks
  
*/
?>

<?php
$fn_get_data = function () {
  $widget_req_from = $this->input->get('period_from');
  $widget_req_to = $this->input->get('period_to');

  if (!empty ($widget_req_from)) {
    $widget_req_from = DateTime::createFromFormat('Y-m-d', $widget_req_from);
    $widget_req_from = $widget_req_from !== false ? $widget_req_from->format('Y-m-d') : null;
  } else {
    $widget_req_from = null;
  }

  if (!empty ($widget_req_to)) {
    $widget_req_to = DateTime::createFromFormat('Y-m-d', $widget_req_to);
    $widget_req_to = $widget_req_to !== false ? $widget_req_to->format('Y-m-d') : null;
  } else {
    $widget_req_to = null;
  }

  $sql = "
  SELECT 
      COUNT(*) AS total_rows
  FROM
      " . db_prefix() . "tasks TBLTasks
  WHERE TBLTasks.status IN (5) AND TBLTasks.rel_type = 'project'
  ";

  if ($widget_req_from !== null && $widget_req_to !== null) {
    $sql .= "
      AND TBLTasks.datefinished >= '" . $widget_req_from . " 00:00:00' AND TBLTasks.datefinished <= '" . $widget_req_to . " 23:59:59'
      ";
  }

  return $this->db->query($sql)->result_array();
};

$widget_data = $fn_get_data();

?>

<div class="widget widget-projects-total-completed-tasks" data-widget-id="<?= $widget['id'] ?>">
  <div class="widget-dragger"></div>
  <div class="card-counter success">
    <i class="fa fa-check-square"></i>
    <span class="count-numbers"><?= $widget_data[0]['total_rows'] ?></span>
    <span class="count-name"><?= _l('completed_tasks') ?></span>
  </div>
</div>