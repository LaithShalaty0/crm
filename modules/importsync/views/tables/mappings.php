<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'mapped_by',
    'csv_type',
    'csv_filename',
    'created_at'
];

$sIndexColumn = 'id';
$sTable = db_prefix() . 'importsync_csv_mapped';

$join = [];
$where = [];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'id'
]);

$output = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {

        $row[] = $aRow['id'];
        $row[] = '<a href="' . admin_url('staff/profile/' . $aRow['mapped_by']) . '">' . staff_profile_image($aRow['mapped_by'], [
                'staff-profile-image-small',
            ]) . '  ' . get_staff_full_name($aRow['mapped_by']) . '</a>';
        $row[] = strtoupper($aRow['csv_type']);
        $row[] = $aRow['created_at'];

        $csvDownloadUrl = substr(module_dir_url('importsync/uploads/mapped_csv/' . $aRow['id'] . '/' . $aRow['csv_filename']), 0, -1);

        $options = '<div class="tw-flex tw-items-center tw-space-x-3">';
        $options .= '<a href="' . $csvDownloadUrl . '" target="_blank" class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
        <i class="fas fa-download fa-lg"></i>
    </a>';

        $options .= '<a href="' . admin_url('importsync/delete_mapping/' . $aRow['id']) . '"
    class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
        <i class="fa-regular fa-trash-can fa-lg"></i>
    </a>';

        $options .= '</div>';

        $row[] = $options;
    }

    $output['aaData'][] = $row;
}
