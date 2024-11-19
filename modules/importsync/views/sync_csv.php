<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo _l('importsync'); ?> - <?php echo _l('importsync_column_mapping'); ?>
                </h4>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php echo _l('importsync_upload_csv_file'); ?></h3>
                    </div>
                    <div class="panel-body">
                        <form id="uploadForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="ImportCsv"
                                           class="form-label"><?php echo _l('importsync_select_csv_file_to_map'); ?>
                                        :</label>
                                    <div class=" mb-3">
                                        <input type="file"
                                               accept=".csv"
                                               name="csvFile" id="csvFile" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12 mtop20">
                                    <label for="radioOption"
                                           class="form-label"><?php echo _l('importsync_check_base_csv'); ?>
                                        :</label><br>
                                    <?php
                                    foreach (importsync_supported_csvs() as $item) {
                                        ?>
                                        <label><input type="radio" name="csv_type"
                                                      value="<?php echo $item['value']; ?>"> <?php echo $item['name']; ?>
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <br>
                            <div class="row text-right">
                                <div class="col-md-12">
                                    <button type="submit" id="uploadButton"
                                            class="btn btn-primary transcript-btn"><?php echo _l('importsync_start_mapping'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php
            foreach (supportedCsvImports() as $supported_csv) {
                $supportedImportCsvData = renderCsvTypeColumns($supported_csv);
                ?>
                <div class="col-md-12 <?php echo $supported_csv; ?>_csv csv-pane hide">
                    <div class="panel_s">
                        <div class="panel-body panel-table-full">
                            <div class="">
                                <div class="mapping-container">
                                    <h3><?php echo _l($supported_csv); ?> CSV
                                        - <?php echo _l('importsync_column_mapping'); ?></h3>
                                    <div class="alert alert-info">
                                        <?php echo $supportedImportCsvData['import_guidelines']; ?>
                                    </div>
                                    <?php echo $supportedImportCsvData['sample_table_html']; ?>
                                    <table id="mappingTable" class="table table-hover tw-text-sm">
                                        <thead>
                                        <tr>
                                            <th><?php echo _l('importsync_base_csv_columns'); ?></th>
                                            <th></th>
                                            <th><?php echo _l('importsync_uploaded_csv_columns'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($supportedImportCsvData['import_fields'] as $field) {
                                            ?>
                                            <tr>
                                                <td><?php echo $field; ?></td>
                                                <td>-></td>
                                                <td>
                                                    <select class="form-control csv-dropdown"
                                                            data-main-column="<?php echo $field; ?>"></select>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="btn-bottom-toolbar text-right">
                                <button class="btn-tr btn btn-default mright5 text-right syncButton">
                                    <?php echo _l('importsync_download_synced_csv'); ?></button>
                                <button type="button"
                                        class="btn-tr btn btn-primary syncButtonDownload"><?php echo _l('importsync_start_importing'); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(document).ready(function () {
        "use strict"; // Enable strict mode

        var columnMappings = {};

        $(".csv-dropdown").change(function () {
            var mainColumn = $(this).data("main-column");
            var csvColumn = $(this).val();

            columnMappings[mainColumn] = csvColumn;
        });

        $(".syncButton").click(function () {
            var formData = new FormData();

            var fileInput = document.getElementById('csvFile');
            var selectedCSVType = $("input[name='csv_type']:checked").val();

            if (fileInput.files.length > 0) {
                formData.append('csvFile', fileInput.files[0]);
            }

            formData.append('mappings', JSON.stringify(columnMappings));
            formData.append('csv_type', selectedCSVType);

            if (typeof csrfData !== "undefined") {
                formData.append(csrfData["token_name"], csrfData["hash"]);
            }

            $.ajax({
                url: '<?php echo admin_url('importsync/map_csv') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    var parsedData = JSON.parse(data);

                    var mappedCsvUrl = parsedData.mapped_csv_url;
                    window.open(mappedCsvUrl, '_blank');
                }
            });
        });

        $(".syncButtonDownload").click(function () {
            var formData = new FormData();

            var fileInput = document.getElementById('csvFile');
            var selectedCSVType = $("input[name='csv_type']:checked").val();

            if (fileInput.files.length > 0) {
                formData.append('csvFile', fileInput.files[0]);
            }

            formData.append('mappings', JSON.stringify(columnMappings));
            formData.append('csv_type', selectedCSVType);

            if (typeof csrfData !== "undefined") {
                formData.append(csrfData["token_name"], csrfData["hash"]);
            }

            $.ajax({
                url: '<?php echo admin_url('importsync/map_csv') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    var parsedData = JSON.parse(data);

                    var mappedCsvUrl = parsedData.mapped_csv_url;
                    var redirectUrl = parsedData.redirect_url;

                    window.open(mappedCsvUrl, '_blank');
                    window.location.replace(redirectUrl);
                }
            });
        });

        $("#uploadForm").submit(function (e) {
            e.preventDefault();

            var formData = new FormData(this);
            var selectedCSVType = $("input[name='csv_type']:checked").val();

            if (typeof csrfData !== "undefined") {
                formData.append(csrfData["token_name"], csrfData["hash"]);
            }

            $.ajax({
                url: '<?php echo admin_url('importsync/get_csv_columns') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    var csvColumns = JSON.parse(data);
                    populateCsvDropdowns(csvColumns);

                    $('.csv-pane').addClass('hide');
                    $('.' + selectedCSVType + '_csv').removeClass('hide');
                }
            });
        });
    });

    function populateCsvDropdowns(csvColumns) {
        "use strict"; // Enable strict mode
        $(".csv-dropdown").empty().append("<option value=''>Select</option>");
        $.each(csvColumns, function (index, csvColumn) {
            $(".csv-dropdown").append("<option value='" + csvColumn + "'>" + csvColumn + "</option>");
        });
    }

</script>
</body>

</html>