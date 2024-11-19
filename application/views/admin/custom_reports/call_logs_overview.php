<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="panel_s">
            <div class="panel-body">
                <h4 class="no-margin"><?php echo _l('call_logs_overview'); ?></h4>
                <hr>

                <!-- Date Range Form -->
                <form method="get" action="<?php echo admin_url('custom_reports/call_logs_overview'); ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date"><?php echo _l('start_date'); ?></label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="<?php echo html_escape($start_date); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date"><?php echo _l('end_date'); ?></label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="<?php echo html_escape($end_date); ?>">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><?php echo _l('filter'); ?></button>
                </form>

                <hr>

                <style>
                    body {
                        margin: 0;
                        padding: 0;
                    }

                    #wrapper {
                        font-family: "Cairo", Helvetica, sans-serif !important;
                        font-weight: bold;
                        direction: rtl;
                    }

                    #reportContent {
                        direction: rtl !important;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }

                    th,
                    td {
                        text-align: center;
                        padding: 8px;
                        border: 1px solid #ddd;
                    }

                    th {
                        background-color: #f2f2f2;
                    }

                    .container {
                        padding: 20px;
                    }

                    .title {
                        background-color: #3e1b59;
                        color: #eae0cc;
                        padding: 10px;
                        text-align: center;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }

                    .data {
                        background-color: #f2f2f2;
                        padding: 10px;
                        text-align: center;
                        font-size: 18px;
                        margin-bottom: 20px;
                    }

                    .summary {
                        background-color: #778899;
                        color: white;
                        padding: 10px;
                        text-align: center;
                        font-size: 20px;
                        margin-bottom: 20px;
                    }

                    .summary-data {
                        background-color: #f2f2f2;
                        padding: 10px;
                        text-align: center;
                        font-size: 16px;
                        margin-bottom: 20px;
                    }

                    .footer {
                        text-align: center;
                        font-size: 12px;
                        margin-top: 20px;
                    }
                </style>

                <div id="wrapper" style="width: 100%;">
                    <div class="container">
                        <div id="reportContent">
                            <div class="row">
                                <div class="img-logo-container text-center">
                                    <img width="300px" src="http://localhost/perfex/uploads/company/Folder 1.svg" alt=""
                                        class="img-fluid">
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h2>تقرير الكول سنتر</h2>
                                        <p>من تاريخ <?php echo htmlspecialchars($start_date); ?> إلى تاريخ
                                            <?php echo htmlspecialchars($end_date); ?>
                                        </p>
                                    </div>

                                    <table>
                                        <tr>
                                            <th>اتصالات ناجحة</th>
                                            <td><?php echo $successful_calls; ?></td>
                                        </tr>
                                        <tr>
                                            <th>اتصالات غير ناجحة</th>
                                            <td><?php echo $unsuccessful_calls; ?></td>
                                        </tr>
                                        <tr>
                                            <th>إجمالي الاتصالات</th>
                                            <td><?php echo $total_calls; ?></td>
                                        </tr>
                                    </table>

                                    <div class="title">
                                        <h3>نتائج الاتصالات</h3>
                                    </div>
                                    <table>
                                        <tr>
                                            <th>المصدر</th>
                                            <th>مهتم</th>
                                            <th>غير مهتم</th>
                                            <th>غير مؤهل</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                        <?php foreach ($call_results as $result): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($result['source']); ?></td>
                                                <td><?php echo isset($result['interested']) ? $result['interested'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['not_interested']) ? $result['not_interested'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['not_eligible']) ? $result['not_eligible'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['total']) ? $result['total'] : 0; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <div class="title">
                                        <h3>تحليل مصادر البيانات</h3>
                                    </div>

                                    <table>
                                        <tr>
                                            <th>المصدر</th>
                                            <th>مهتمين</th>
                                            <th>مواعيد</th>
                                            <th>زيارات</th>
                                            <th>غير مستحق</th>
                                            <th>قدرة مالية</th>
                                            <th>حجوزات</th>
                                            <th>تسديد رسوم</th>
                                            <th>هقود</th>
                                        </tr>
                                        <?php foreach ($data_source_analysis as $analysis): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($analysis['source']); ?></td>
                                                <td><?php echo $analysis['interested']; ?></td>
                                                <td><?php echo $analysis['appointments']; ?></td>
                                                <td><?php echo $analysis['visits']; ?></td>
                                                <td><?php echo $analysis['not_due']; ?></td>
                                                <td><?php echo $analysis['financial_capacity']; ?></td>
                                                <td><?php echo $analysis['bookings']; ?></td>
                                                <td><?php echo $analysis['fee_payment']; ?></td>
                                                <td><?php echo $analysis['contracts']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <div class="title">
                                        <h3>الملخص التراكمي</h3>
                                    </div>

                                    <table>
                                        <tr>
                                            <th>اتصالات ناجحة</th>
                                            <td><?php echo $cumulative_successful_calls; ?></td>
                                        </tr>
                                        <tr>
                                            <th>اتصالات غير ناجحة</th>
                                            <td><?php echo $cumulative_unsuccessful_calls; ?></td>
                                        </tr>
                                        <tr>
                                            <th>إجمالي الاتصالات</th>
                                            <td><?php echo $cumulative_total_calls; ?></td>
                                        </tr>
                                    </table>

                                    <div class="title">
                                        <h3>نتائج الاتصالات - تراكمي</h3>
                                    </div>

                                    <table>
                                        <tr>
                                            <th>المصدر</th>
                                            <th>مهتم</th>
                                            <th>غير مهتم</th>
                                            <th>غير مؤهل</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                        <?php foreach ($cumulative_call_results as $result): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($result['source']); ?></td>
                                                <td><?php echo isset($result['interested']) ? $result['interested'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['not_interested']) ? $result['not_interested'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['not_eligible']) ? $result['not_eligible'] : 0; ?>
                                                </td>
                                                <td><?php echo isset($result['total']) ? $result['total'] : 0; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <div class="title">
                                        <h3>تحليل مصادر البيانات - تراكمي</h3>
                                    </div>

                                    <table>
                                        <tr>
                                            <th>المصدر</th>
                                            <th>مهتمين</th>
                                            <th>مواعيد</th>
                                            <th>زيارات</th>
                                            <th>غير مستحق</th>
                                            <th>قدرة مالية</th>
                                            <th>حجوزات</th>
                                            <th>تسديد رسوم</th>
                                            <th>هقود</th>
                                        </tr>
                                        <?php foreach ($cumulative_data_source_analysis as $analysis): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($analysis['source']); ?></td>
                                                <td><?php echo $analysis['interested']; ?></td>
                                                <td><?php echo $analysis['appointments']; ?></td>
                                                <td><?php echo $analysis['visits']; ?></td>
                                                <td><?php echo $analysis['not_due']; ?></td>
                                                <td><?php echo $analysis['financial_capacity']; ?></td>
                                                <td><?php echo $analysis['bookings']; ?></td>
                                                <td><?php echo $analysis['fee_payment']; ?></td>
                                                <td><?php echo $analysis['contracts']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>

                                    <div class="footer">
                                        <p>تاريخ إصدار التقرير: <?= date('H:i:s Y-m-d') ?></p>
                                        <!-- <p>صدر بواسطة: يمان المصري</p> -->
                                    </div>
                                </div>
                            </div>
                            <div class="mx-auto text-center">
                                <button id="exportButton" class="btn btn-primary">تصدير كملف PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function () {
            var element = document.getElementById('reportContent'); // The ID of the div you want to export
            var opt = {
                margin: 0.5,
                filename: 'Tele-Sales-Report.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            html2pdf().from(element).set(opt).save();
        });
    </script>