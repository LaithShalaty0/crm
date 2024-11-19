<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php if(get_option(FLEXIBLELEADSCORE_STATUS_OPTION) == FLEXIBLELEADSCORE_INACTIVE_STATUS && get_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION) != FLEXIBLELEADSCORE_CRON_STATUS_RUNNING ): ?>
                <div class="col-md-12">
                    <div class="alert alert-warning"><h4><?php echo flexiblels_lang('lead-scoring-not-active') ?></h4>
                        <p><?php echo flexiblels_lang('lead-scoring-not-active-desc'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-12">
                <div class="tw-flex tw-justify-between tw-items-center tw-mb-2 sm:tw-mb-4">
                    <h4 class="tw-my-0 tw-font-semibold tw-text-lg tw-self-end">
                        <?php echo $title; ?>
                    </h4>
                    <div>
                        <?php if(get_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION) == FLEXIBLELEADSCORE_CRON_STATUS_RUNNING): ?>
                        <span class="text-info"><?php echo flexiblels_lang('old-records-calculation-in-progress') ?></span>
                        <?php endif; ?>
                        <a href="#" data-toggle="modal" data-target="#flexibleleadscore_add_criteria"
                            class="btn btn-primary mright5">
                            <i class="fa fa-plus"></i>
                            <?php echo flexiblels_lang('add-criteria'); ?>
                        </a>
                        <a href="<?php echo admin_url('flexibleleadscore/reports'); ?>"
                            class="btn btn-success mright5">
                            <i class="fa-solid fa-chart-simple"></i>
                            <?php echo flexiblels_lang('reports'); ?>
                        </a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">

                        <table class="table dt-table">
                            <thead>
                                <th>
                                    <?php echo flexiblels_lang('criteria'); ?>
                                </th>
                                <th></th>
                                <th>
                                    <?php echo flexiblels_lang('options'); ?>
                                </th>
                            </thead>
                            <tbody>
                                <?php foreach ($flexiblels_criteria as $criterion) { ?>
                                    <tr>
                                        <td>
                                            <span class="text-info">
                                                <?php echo ucfirst(str_replace('-', ' ', $criterion['flexibleleadscore_criteria'])) ?>
                                            </span>
                                            <?php
                                            echo ' '.strtoupper(flexiblels_lang(str_replace('_','-',$criterion['flexibleleadscore_criteria_operator']))).' ';
                                            ?>
                                            <span class="text-info">
                                                <?php echo $criterion['flexibleleadscore_display_value'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo flexiblels_lang($criterion['flexibleleadscore_add_substract']).' '.$criterion['flexibleleadscore_points'].' '.flexiblels_lang('points') ?>
                                        </td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                                <a data-link='<?php echo admin_url("flexibleleadscore/get_criteria/$criterion[id]") ?>'
                                                    class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 edit_criteria">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>

                                                <a href="<?php echo admin_url('flexibleleadscore/delete_criteria/') . $criterion['id'] ?>"
                                                    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer">
                        <a class="btn btn-success mright5" href="#" data-toggle="modal" data-target="#flexibleleadscore_activate">
                            <i class="fa fa-check"></i>
                            <?php echo flexiblels_lang('save-and-activate'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="flexibleleadscore_activate" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('flexibleleadscore/activate')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo flexiblels_lang('save-and-activate'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="flexibleleadscore_update_old_records" value="1" />
                <div class="form-group">
                    <div class="alert alert-info">
                        <?php echo flexiblels_lang('update_old_record_desc') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo flexiblels_lang('yes-proceed'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<div id="get-criteria-link" class="hidden" data-link='<?php echo admin_url("flexibleleadscore/get_criteria") ?>'></div>
<?php $this->load->view('partials/criteria-modal') ?>
<?php init_tail(); ?>
</body>

</html>

