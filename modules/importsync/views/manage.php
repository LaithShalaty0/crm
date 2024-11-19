<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if (has_permission('importsync', '', 'create')) { ?>
                    <div class="tw-mb-2 sm:tw-mb-4">
                        <a href="<?php echo admin_url('importsync/csv_mappings'); ?>" class="btn btn-primary">
                            <i class="fa-regular fa-plus tw-mr-1"></i>
                            <?php echo _l('importsync_create_mapping'); ?>
                        </a>
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php render_datatable([
                            _l('id'),
                            _l('importsync_mapped_by'),
                            _l('importsync_csv_type'),
                            _l('importsync_created_at'),
                            _l('options'),
                        ], 'importsync-mappings'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function () {
        'use strict';
        initDataTable('.table-importsync-mappings', window.location.href, [3], [3], [], [3, 'desc']);
    });

</script>
</body>

</html>