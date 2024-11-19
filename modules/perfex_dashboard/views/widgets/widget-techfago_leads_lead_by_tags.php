<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Lead by tags
  Description: Lead by tags

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
      SELECT 
          TBLTags.id AS tag_id,
          TBLTags.name AS tag_name,
          COUNT(*) AS total_leads
      FROM
          " . db_prefix() . "tags TBLTags
              INNER JOIN
              " . db_prefix() . "taggables TBLTaggable ON TBLTags.id = TBLTaggable.tag_id
              AND TBLTaggable.rel_type = 'lead'
              INNER JOIN
              " . db_prefix() . "leads TBLLeads ON TBLTaggable.rel_id = TBLLeads.id
  ";
  if ($date_from !== null && $date_to !== null) {
    $sql .= "
          WHERE
              TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00'
              AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59'
      ";
  }
  $sql .= "
      GROUP BY tag_id , tag_name
      ORDER BY total_leads DESC
      LIMIT 0 , 10
  ";
  $data_rows = $this->db->query($sql)->result_array();

  $colors = get_system_favourite_colors();

  $chart = [
    'labels' => [],
    'datasets' => [],
  ];

  $_data = [];
  $_data['data'] = [];
  $_data['backgroundColor'] = [];
  $_data['hoverBackgroundColor'] = [];
  $_data['statusLink'] = [];

  foreach ($data_rows as $data_index => $data_row) {
    array_push($_data['statusLink'], 'javascript:void(0);');
    array_push($chart['labels'], $data_row['tag_name']);
    array_push($_data['backgroundColor'], $colors[$data_index % count($colors)]);
    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($colors[$data_index % count($colors)], -20));
    array_push($_data['data'], intval($data_row['total_leads']));
  }

  $chart['datasets'][] = $_data;

  return $chart;
};

$widget_data = $fn_get_data();

?>

<div class="widget widget-leads-lead-by-tags" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('lead_by_tags'); ?></h4>
      <div class="clearfix"></div>
      <div class="row mtop5">
        <hr class="hr-panel-heading-dashboard">
      </div>

      <div class="row">
        <div class="col-md-12">
          <?php if (count($widget_data['datasets'][0]['data']) > 0) { ?>
            <div class="relative height-250">
              <canvas class="chart" height="250" id="chart-<?= $widget['id'] ?>"></canvas>
            </div>
          <?php } else { ?>
            <p class="text-center"><?= _l('not_found') ?></p>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window['perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data'] = <?= json_encode($widget_data) ?>;
</script>