<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('customers', '', 'delete');

$custom_fields = get_table_custom_fields('customers');
$this->ci->db->query("SET sql_mode = ''");
$aColumns = [
    '1',
    'id',
    'subject',
    'company',
    'date',
    'objectives',
    'revenue_next_year',
    'wallet_share',
    'client_status',
    'bcg_model',
    'margin',
];

$sIndexColumn = 'id';
$sTable       = 'tblaccount_planning';
$where        = [];

if(isset($client_id)){
    array_push($where, 'AND client_id=' . $client_id);
}
// Add blank where all filter can be stored
$filter = [];

$join = [
    'LEFT JOIN tblclients ON tblclients.userid=tblaccount_planning.client_id'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,['client_id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    // User id
    $row[] = $aRow['id'];

    // Company
    $company  = $aRow['subject'];
    $isPerson = false;

    if ($company == '') {
        $company  = _l('account_planning');
        $isPerson = true;
    }

    $url = admin_url('account_planning/view/' . $aRow['id']);

    $company = '<a href="' . $url . '">' . $company . '</a>';

    $company .= '<div class="row-options">';
    $company .= '<a href="' . $url . '">' . _l('view') . '</a>';
    $url_new = 'account_planning/delete/' . $aRow['id'];
    $company .= ' | <a href="#" onclick="copy_account_planning(' . $aRow['id'] . ',\''.$aRow['client_id'].'\',\''.$aRow['company'].'\', \''.$aRow['subject'].'\');return false;">'._l('copy_account_planning').'</a>';
    if ($hasPermissionDelete) {
        $company .= ' | <a href="' . admin_url($url_new) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $company .= '</div>';
    $row[] = $company;
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client_id']) . '">' . $aRow['company'] .'</a>';
    if($aRow['date'] != ''){
        $row[] = date('F - Y', strtotime($aRow['date']));
    }else {
        $row[] =  _d($aRow['date']);
    }


    $row[] = $aRow['objectives'];
	$basecur = get_base_currency();
    $row[] = app_format_money($aRow['revenue_next_year'],$basecur);
    if($aRow['margin']){
        $row[] = $aRow['margin'].' %';

    }else{
        $row[] = $aRow['margin'];
    }
    if($aRow['wallet_share']){
        $row[] = $aRow['wallet_share'].' %';

    }else{
        $row[] = $aRow['wallet_share'];
    }
    $client_status = '';
    if($aRow['client_status'] == 'Red'){
        $client_status = '<label class="text-danger">'.$aRow['client_status'].'</label>';
    }elseif($aRow['client_status'] == 'Yellow'){
        $client_status = '<label class="text-warning">'.$aRow['client_status'].'</label>';
    }elseif ($aRow['client_status'] == 'Green') {
        $client_status = '<label class="text-success">'.$aRow['client_status'].'</label>';
    }
    $row[] = $client_status;
    $row[] = $aRow['bcg_model'];
    
    $output['aaData'][] = $row;
}
