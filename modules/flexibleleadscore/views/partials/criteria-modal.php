<div class="modal fade" id="flexibleleadscore_add_criteria" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php $hidden = isset($criterion) ? [
            'id' => $criterion['id']
        ] : []; ?>
        <?php echo form_open(admin_url('flexibleleadscore/add_criteria'), [], $hidden); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <?php echo isset($criterion) ? flexiblels_lang('edit-criteria')
                        : flexiblels_lang('add-criteria'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-4">
                            <?php $selected = isset($criterion) ? $criterion['flexibleleadscore_criteria'] : ''; ?>
                            <?php echo render_select('flexibleleadscore_criteria', $criterias, ['id', 'name'], '', $selected, [], [], '', '', false); ?>
                        </div>
                        <div class="col-sm-4">
                            <?php $selected = isset($criterion) ? $criterion['flexibleleadscore_criteria_operator'] : ''; ?>
                            <?php echo render_select('flexibleleadscore_criteria_operator', $criteria_operators, ['id', 'label'], '', $selected, [], [], '', '', false); ?>
                        </div>
                        <div class="col-sm-4">
                            <div id="flexibleleadscore-dropdown-container"
                                data-url="<?php echo admin_url('flexibleleadscore/ajax') ?>">

                                <?php if (isset($criterion)) { ?>
                                    <?php echo flexiblels_get_criteria_values($criterion['flexibleleadscore_criteria']) ?>
                                <?php } else { ?>
                                    <input type="text" name="flexibleleadscore_criteria_value"
                                        id="flexibleleadscore_criteria_value" class="form-control"
                                        placeholder="<?php echo flexiblels_lang('criteria-value'); ?>" />

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <!-- create radio checkbox for add and substract -->
                                <div class="radio radio-primary radio-inline">
                                    <?php $checked = isset($criterion) ? ($criterion['flexibleleadscore_add_substract'] == FLEXIBLELEADSCORE_ADD ? 'checked' : '') : 'checked' ?>
                                    <input type="radio" id="flexibleleadscore_add"
                                        name="flexibleleadscore_add_substract"
                                        value="<?php echo FLEXIBLELEADSCORE_ADD ?>" <?php echo $checked ?>>
                                    <label for="flexibleleadscore_add">
                                        <?php echo flexiblels_lang('add'); ?>
                                    </label>
                                </div>
                                <div class="radio radio-primary radio-inline">
                                    <?php $checked = isset($criterion) ? ($criterion['flexibleleadscore_add_substract'] == FLEXIBLELEADSCORE_SUBTRACT ? 'checked' : '') : '' ?>
                                    <input type="radio" id="flexibleleadscore_substract"
                                        name="flexibleleadscore_add_substract"
                                        value="<?php echo FLEXIBLELEADSCORE_SUBTRACT ?>" <?php echo $checked ?>>
                                    <label for="flexibleleadscore_substract">
                                        <?php echo flexiblels_lang('substract'); ?>
                                    </label>
                                </div>
                                <hr />
                                <!-- points input field -->
                                <div class="width200">
                                    <?php $value = isset($criterion) ? $criterion['flexibleleadscore_points'] : 10 ?>
                                    <?php echo render_input('flexibleleadscore_points', '', $value, 'number'); ?>
                                    <span>
                                        <?php echo flexiblels_lang('points'); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo _l('close'); ?>
                </button>
                <button type="submit" class="btn btn-primary">
                    <?php echo flexiblels_lang('save'); ?>
                </button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->