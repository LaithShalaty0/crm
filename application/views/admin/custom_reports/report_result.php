<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
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
        background-color: #778899;
        color: white;
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
<div id="wrapper">
    <div class="content">
        <div id="reportContent">
            <div class="title">
                <h2>تقرير المبيعات</h2>
                <p>تقرير من تاريخ: <?= htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8') ?> إلى تاريخ:
                    <?= htmlspecialchars($to_date, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <div class="title">
                <h3>التقرير التفصيلي</h3>
            </div>

            <table>
                <tr>
                    <th>تسجيل الدخول</th>
                    <th>الإلغاءات</th>
                    <th>حجوزات المعرض</th>
                    <th>متابعة العقود</th>
                    <th>حجوزات المنصة</th>
                </tr>
                <tr>
                    <td><?= $report_data['leads_logged_in'] ?></td>
                    <td><?= $report_data['leads_canceled'] ?></td>
                    <td><?= $report_data['leads_from_exhibition'] ?></td>
                    <td><?= $report_data['leads_under_processing'] ?></td>
                    <td><?= $report_data['leads_with_lack_of_interest'] ?></td>
                </tr>
                <tr>
                    <th>مستفيد جديد</th>
                    <th>سدد الرسوم</th>
                    <th>سدد الرسوم</th>
                    <th>إلغاء حجز منصة</th>
                    <th>جاري التنفيذ</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <th>مستفيد سابق</th>
                    <th>لم يسدد الرسوم</th>
                    <th>لم يسدد الرسوم</th>
                    <th>إلغاء حجز معرض</th>
                    <th>يرغب بالإلغاء</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <th colspan="5">مواعيد الزيارات:</th>
                </tr>
                <tr>
                    <td colspan="5">-</td>
                </tr>
                <tr>
                    <th>حضور</th>
                    <th>أكد ولم يحضر</th>
                    <th>تغيير الموعد</th>
                    <th>غير مهتم</th>
                    <th>لم يتم الرد</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr>
                    <th>سداد الرسوم</th>
                    <th></th>
                    <th>عقود الاستصناغ</th>
                    <th></th>
                    <th>عقود التمويل</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td></td>
                    <td>-</td>
                    <td></td>
                    <td>-</td>
                </tr>
                <tr>
                    <th colspan="5">أسباب قلة الاهتمام:</th>
                </tr>
                <tr>
                    <td>السعر غير مناسب:</td>
                    <td>المدينة غير مناسبة:</td>
                    <td>المنطقة غير مناسبة:</td>
                    <td>غير مهتم بهذه المنطقة:</td>
                    <td>التصميم غير مناسب:</td>
                </tr>

                <tr>
                    <td><?= $report_data['lack_of_interest_price'] ?></td>
                    <td><?= $report_data['lack_of_interest_city'] ?></td>
                    <td><?= $report_data['lack_of_interest_area'] ?></td>
                    <td><?= $report_data['lack_of_interest_not_interested_in_area'] ?></td>
                    <td><?= $report_data['lack_of_interest_design'] ?></td>
                </tr>
                <tr>
                    <td colspan="5">أخرى:</td>
                </tr>
                <tr>
                    <td colspan="5"><?= $report_data['lack_of_interest_other'] ?></td>
                </tr>

                <tr>
                    <th colspan="5">تقرير العملاء غير المؤهلين</th>
                </tr>
                <tr>
                    <th></th>
                    <th>عدم وجود قدرة مالية</th>
                    <th>غير مؤهل</th>
                    <th>استفاد من الدعم</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <td><?= $report_data['not_qualified_financial_capacity'] ?></td>
                    <td><?= $report_data['not_qualified_not_eligible'] ?></td>
                    <td><?= $report_data['not_qualified_benefited_support'] ?></td>
                    <th></th>
                </tr>
            </table>


            <div class="title">
                <h3>تقرير تراكمي</h3>
            </div>
            <table>
                <tr>
                    <th>تسجيل الدخول</th>
                    <th>الإلغاءات</th>
                    <th>حجوزات المعرض</th>
                    <th>متابعة العقود</th>
                    <th>حجوزات المنصة</th>
                </tr>
                <tr>
                    <td><?= $cumulative_data['leads_logged_in'] ?></td>
                    <td><?= $cumulative_data['leads_canceled'] ?></td>
                    <td><?= $cumulative_data['leads_from_exhibition'] ?></td>
                    <td><?= $cumulative_data['leads_under_processing'] ?></td>
                    <td><?= $cumulative_data['leads_with_lack_of_interest'] ?></td>
                </tr>
                <tr>
                    <th colspan="5">أسباب قلة الاهتمام:</th>
                </tr>
                <tr>
                    <td>السعر غير مناسب:</td>
                    <td>المدينة غير مناسبة:</td>
                    <td>المنطقة غير مناسبة:</td>
                    <td>غير مهتم بهذه المنطقة:</td>
                    <td>التصميم غير مناسب:</td>
                </tr>
                <tr>
                    <td><?= $cumulative_data['lack_of_interest_price'] ?></td>
                    <td><?= $cumulative_data['lack_of_interest_city'] ?></td>
                    <td><?= $cumulative_data['lack_of_interest_area'] ?></td>
                    <td><?= $cumulative_data['lack_of_interest_not_interested_in_area'] ?></td>
                    <td><?= $cumulative_data['lack_of_interest_design'] ?></td>
                </tr>
                <tr>
                    <td colspan="5">أخرى:</td>
                </tr>
                <tr>
                    <td colspan="5"><?= $cumulative_data['lack_of_interest_other'] ?></td>
                </tr>

                <tr>
                    <th colspan="5">تقرير العملاء غير المؤهلين</th>
                </tr>
                <tr>
                    <th></th>
                    <th>عدم وجود قدرة مالية</th>
                    <th>غير مؤهل</th>
                    <th>استفاد من الدعم</th>
                    <th></th>
                </tr>
                <tr>
                    <th></th>
                    <td><?= $cumulative_data['not_qualified_financial_capacity'] ?></td>
                    <td><?= $cumulative_data['not_qualified_not_eligible'] ?></td>
                    <td><?= $cumulative_data['not_qualified_benefited_support'] ?></td>
                    <th></th>
                </tr>
            </table>
            <table>
                <tr>
                    <th>إجمالي الوحدات</th>
                    <th>الوحدات المتاحة</th>
                    <th>عدد الحجوزات</th>
                    <th>عدد الإلغاءات</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>

                <tr>
                    <th>إجمالي الزيارات</th>
                    <th>تسديد الرسوم</th>
                    <th>عقود الاستصناع</th>
                    <th>عقود التمويل</th>
                </tr>
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
            </table>

            <!-- <div class="title">
                <h2>تقرير أداء الفريق</h2>
                <p>تقرير من تاريخ: <?= htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8') ?> إلى تاريخ:
                    <?= htmlspecialchars($to_date, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <div class="title">
                <h3>التقرير التفصيلي</h3>
            </div>

            <table>
                <tr>
                    <th>رقم الموظف</th>
                    <th>اسم الموظف</th>
                    <th>عدد مرات تسجيل الدخول</th>
                    <th>عدم التأهل: لا توجد قدرة مالية</th>
                    <th>عدم التأهل: غير مؤهل</th>
                    <th>عدد العقود</th>
                    <th>عدد المقترحات</th>
                    <th>عدد العقود المضافة</th>
                </tr>
                <?php foreach ($stuff_report_data as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['staffid'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['staff_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['signin_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['no_financial_capacity_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['not_eligible_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['contracts'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['proposal_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['contract_count'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table> -->

            <div class="title">
                <h3>أداء الفريق التراكمي</h3>
            </div>

            <table>
                <tr>
                    <th>رقم الموظف</th>
                    <th>اسم الموظف</th>
                    <th>تسجيل الدخول</th>
                    <th>لا توجد قدرة مالية</th>
                    <th>غير مؤهل</th>
                    <th>عدد العقود</th>
                    <th>عدد المقترحات</th>
                    <th>عدد العقود المضافة</th>
                </tr>
                <?php foreach ($cumulative_stuff_data as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['staffid'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['staff_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['signin_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['no_financial_capacity_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['not_eligible_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['contracts'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['proposal_count'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($report['contract_count'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>


            <div class="footer">
                <p>تاريخ إصدار التقرير: <?= date('H:i:s Y-m-d') ?></p>
                <!-- <p>صدر بواسطة: يمان المصري</p> -->
            </div>
        </div>
        <div class="mx-auto text-center">
            <button id="exportButton" class="btn btn-primary">تصدير كملف PDF</button>
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
            filename: 'report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        html2pdf().from(element).set(opt).save();
    });
</script>