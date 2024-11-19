<?php if(get_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION) == FLEXIBLELEADSCORE_CRON_STATUS_RUNNING): ?>
    <span class="text-info"><?php echo flexiblels_lang('old-records-calculation-in-progress') ?></span>
<?php else: ?>
<div role="tabpanel" class="tab-pane" id="flexibleleadscore">
    <h3><?php echo $score; ?></h3>
    <h4><?php echo flexiblels_lang('lead-score') ?></h4>
    <table class="table dt-table">
        <thead>
            <th>
                <?php echo flexiblels_lang('criteria-matched'); ?>
            </th>
            <th></th>
        </thead>
        <tbody>
        <?php foreach ($criteria as $criterion): ?>
        <tr>
            <td>
                <span class="text-info">
                  <?php echo ucfirst(str_replace('-', ' ', $criterion['flexibleleadscore_criteria'])) ?>
                </span>
                <?php echo ' '.strtoupper(flexiblels_lang(str_replace('_','-',$criterion['flexibleleadscore_criteria_operator']))).' '; ?>
                <span class="text-info">
                    <?php echo $criterion['flexibleleadscore_display_value'] ?>
                </span>
            </td>
            <td>
                <?php echo flexiblels_lang($criterion['flexibleleadscore_add_substract']).' '.$criterion['flexibleleadscore_points'].' '.flexiblels_lang('points') ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>