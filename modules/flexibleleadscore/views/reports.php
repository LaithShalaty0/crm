<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2 sm:tw-mb-4">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-self-end">
                        <?php echo $title; ?>
                    </h4>
                    <div>
                        <a href="<?php echo admin_url('flexibleleadscore'); ?>" class="btn btn-primary mright5">
                            <i class="fa-solid fa-arrow-left"></i>
                            <?php echo flexiblels_lang('lead-scoring-criteria'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel_s">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <?php echo flexiblels_lang('scores-report') ?>
                        </div>
                    </div>
                    <div class="panel-body">
                        <canvas class="scores-report" height="150" id="scores-report"
                            data-url="<?php echo admin_url('flexibleleadscore/reports') ?>"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    "use strict";

    window.addEventListener('load', function () {
        const canvas = $('#scores-report');
        new Chart(canvas, {
            type: 'bar',
            data: <?php echo $scores_report_data; ?>,
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                        }
                    }]
                },
            },
        });
    });
</script>
</body>

</html>