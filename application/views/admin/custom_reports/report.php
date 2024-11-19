<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title"><?php echo $title; ?>: Sources</h3>
                <div class="panel_s">
                    <div class="panel-body">
                        <canvas id="myLeadSourceChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-title"><?php echo $title; ?>: Status</h3>
                <div class="panel_s">
                    <div class="panel-body">
                        <canvas id="myLeadStatusChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" style="direction: rtl;">
        <div class="title">
            <h1>تقرير المبيعات</h1>
            <h3 id="selected-date-range">اختر الفترة الزمنية</h3>
        </div>
        <!-- <form method="POST" action="<?= admin_url('custom_reports/generate_report'); ?>">
            <div>
                <label for="from_date">من</label>
                <input type="date" id="from_date" name="from_date" required>
            </div>
            <div>
                <label for="to_date">إلى</label>
                <input type="date" id="to_date" name="to_date" required>
            </div>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>">
            <button type="submit">توليد التقرير</button>
        </form> -->
        <form method="POST" action="<?= admin_url('custom_reports/generate_report'); ?>" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-2" for="from_date">من</label>
                <div class="col-md-3">
                    <input type="date" id="from_date" name="from_date" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-2" for="to_date">إلى</label>
                <div class="col-md-3">
                    <input type="date" id="to_date" name="to_date" class="form-control" required>
                </div>
            </div>
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
                <div class="col-md-offset-2 col-md-3">
                    <button type="submit" class="btn btn-primary">توليد التقرير</button>
                </div>
            </div>
        </form>

        <table id="report-table">
            <!-- Table data will be populated here dynamically -->
        </table>
        <!-- <div class="footer">
            <p>تاريخ إصدار التقرير م <span id="report-time"></span></p>
            <p>صدر بواسطة: يمان المصري</p>
        </div> -->
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        var ctx = document.getElementById('myLeadSourceChart').getContext('2d');
        var chartData = <?php echo json_encode($report_data); ?>;
        var myLeadSourceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Sources',
                    data: chartData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
</script>

<script>
    $(function () {
        var ctx = document.getElementById('myLeadStatusChart').getContext('2d');
        var chartData = <?php echo json_encode($status_data); ?>;
        var myLeadStatusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Status',
                    data: chartData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });
</script>

<!-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
        document.getElementById("report-time").innerText = currentDate;
    });
</script> -->

</body>

</html>