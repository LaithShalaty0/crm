<?php defined('BASEPATH') or exit('No direct script access allowed');
/*
  Widget Name: Leads and converted leads by sources
  Description: Leads and converted leads by sources
  
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

  $data_labels = [];
  $data_leads = [];
  $data_converted_leads = [];

  $sql = "
      SELECT 
          TBLSources.id AS source_id,
          TBLSources.name AS source_name,
          SUM(CASE
              WHEN (TBLLeads.id IS NOT NULL) THEN 1
              ELSE 0
          END) AS total_leads,
          SUM(CASE
              WHEN (TBLLeads.status = 1) THEN 1
              ELSE 0
          END) AS total_converted_leads,
          SUM(CASE
              WHEN
                  ((TBLLeads.id IS NOT NULL)
                      OR (TBLLeads.status = 1))
              THEN
                  1
              ELSE 0
          END) AS total_rows
      FROM
          " . db_prefix() . "leads_sources TBLSources
              INNER JOIN
          " . db_prefix() . "leads TBLLeads ON TBLSources.id = TBLLeads.source
  ";
  if ($date_from !== null && $date_to !== null) {
    $sql .= "
          WHERE
              ((TBLLeads.dateadded >= '" . $widget_req_from . " 00:00:00'
                  AND TBLLeads.dateadded <= '" . $widget_req_to . " 23:59:59')
                  OR (TBLLeads.date_converted >= '" . $widget_req_from . " 00:00:00'
                  AND TBLLeads.date_converted <= '" . $widget_req_to . " 23:59:59'))
      ";
  }
  $sql .= "
      GROUP BY source_id, source_name
      ORDER BY total_rows DESC
      LIMIT 0 , 10
  ";

  $data_rows = $this->db->query($sql)->result_array();

  foreach ($data_rows as $data_row) {
    array_push($data_labels, $data_row['source_name']);
    array_push($data_leads, intval($data_row['total_leads']));
    array_push($data_converted_leads, intval($data_row['total_converted_leads']));
  }

  $chart = [
    'labels' => $data_labels,
    'datasets' => [
      [
        'label' => _l('leads'),
        'backgroundColor' => 'rgba(37,155,35,0.2)',
        'borderColor' => '#84c529',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_leads,
      ],
      [
        'label' => _l('converted_leads'),
        'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
        'borderColor' => '#c53da9',
        'borderWidth' => 1,
        'tension' => false,
        'data' => $data_converted_leads,
      ],
    ],
  ];

  return $chart;
};

$widget_data = $fn_get_data();


$widget_data = $fn_get_data();
?>

<div class="widget widget-leads-and-converted-leads-by-sources" data-widget-id="<?= $widget['id'] ?>">
  <div class="panel_s">
    <div class="panel-body">
      <div class="widget-dragger"></div>

      <h4 class="pull-left mtop5"><?php echo _l('leads_and_converted_leads_by_sources'); ?></h4>
      <div class="clearfix"></div>
      <div class="row mtop5">
        <hr class="hr-panel-heading-dashboard">
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="relative max-height-400">
            <canvas id="perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data" class="animated fadeIn"
              height="400"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  window['perfex_dashboard_widget_<?= $widget['id'] ?>_chart_data'] = <?= json_encode($widget_data); ?>;
</script>