<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Account Planning
Description: Strategic account planning through a customer-centric approach to identifying priority accounts, capturing and analysing critical information, developing a strategy to expand and grow existing customer relationships.
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('ACCOUNT_PLANNING_MODULE_NAME', 'account_planning');
define('ACCOUNT_PLANNING_ATTACHMENTS_FOLDER', module_dir_path(ACCOUNT_PLANNING_MODULE_NAME, 'uploads/'));


hooks()->add_action('admin_init', 'account_planning_module_init_menu_items');
hooks()->add_action('task_related_to_select', 'add_option_task_related_to');
hooks()->add_action('admin_init', 'account_planning_permissions');
hooks()->add_action('app_admin_head', 'account_planning_add_head_components');
hooks()->add_action('app_admin_footer', 'account_planning_add_footer_components');

hooks()->add_filter('get_upload_path_by_type', 'add_upload_path_account_planning', 10, 2);
hooks()->add_filter('before_add_task', 'before_add_task', 10, 2);
hooks()->add_filter('after_add_task', 'after_add_task', 10, 2);
hooks()->add_filter('task_status_changed', 'task_status_changed', 10, 2);

/**
* Load the module helper
*/
$CI = &get_instance();
$CI->load->helper(ACCOUNT_PLANNING_MODULE_NAME . '/account_planning');

/**
* Register activation module hook
*/
register_activation_hook(ACCOUNT_PLANNING_MODULE_NAME, 'account_planning_module_activation_hook');

/**
* Functions of the module
*/
function account_planning_add_head_components(){
    if (get_option('account_planning_enabled') == '1') {
        $CI = &get_instance();
		
		$loaddepdendencies = $_SERVER['REQUEST_URI'];
		
		if ($loaddepdendencies == '/admin/account_planning/new_account') {
			echo '<script src="' . base_url('modules/account_planning/assets/js/handsontable.full.min.js') . '"></script>';
			echo '<link href="' . base_url('modules/account_planning/assets/css/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
		}		
		
		
		if ( strpos($loaddepdendencies,'account_planning/view/') !== false && strpos($loaddepdendencies,'?') == false ) {
			echo '<script src="' . base_url('modules/account_planning/assets/js/handsontable.full.min.js') . '"></script>';
			echo '<link href="' . base_url('modules/account_planning/assets/css/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
		}

		
		if (strpos($loaddepdendencies,'?group=due_diligence') !== false) {
			echo '<script src="' . base_url('modules/account_planning/assets/js/handsontable.full.min.js') . '"></script>';
			echo '<link href="' . base_url('modules/account_planning/assets/css/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
		}
		
		
		if (strpos($loaddepdendencies,'?group=service_ability_offering') !== false) {
			echo '<script src="' . base_url('modules/account_planning/assets/js/handsontable.full.min.js') . '"></script>';
			echo '<link href="' . base_url('modules/account_planning/assets/css/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
		}		
		
		
		if (strpos($loaddepdendencies,'?group=planning') !== false) {
			echo '<script src="' . base_url('modules/account_planning/assets/js/handsontable.full.min.js') . '"></script>';
			echo '<link href="' . base_url('modules/account_planning/assets/css/handsontable.full.min.css') .'"  rel="stylesheet" type="text/css" />';
		}
		
		
        echo '<link href="' . base_url('modules/account_planning/assets/css/account_planning.css') .'"  rel="stylesheet" type="text/css" />';


    }
}

function account_planning_add_footer_components(){

		$loaddepdendencies = $_SERVER['REQUEST_URI'];

		
		if ( strpos($loaddepdendencies,'account_planning/view/') !== false && strpos($loaddepdendencies,'?') == false ) {
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
		}
		
		if ( strpos($loaddepdendencies,'?group=planning') !== false) {
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
		}

		if (strpos($loaddepdendencies,'?group=service_ability_offering') !== false) {
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
			echo '<script src="' . base_url('modules/account_planning/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
		}		
			
}


function account_planning_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function account_planning_relation_values($values, $relation)
{
    if ($values['type'] == 'account_planning') {
        if (is_array($relation)) {
            $values['id']   = $relation['id'];
            $values['name'] = $relation['subject'];
        } else {
            $values['id']   = $relation->id;
            $values['name'] = $relation->subject;
        }
        $values['link'] = admin_url('account_planning/view/' . $values['id']);
    }

    return $values;
}

function account_planning_relation_data($data, $type, $rel_id, $q)
{
    $CI = &get_instance();
    $CI->load->model(ACCOUNT_PLANNING_MODULE_NAME.'/account_planning_model');
    if ($type == 'account_planning') {
        if ($rel_id != '') {
            $data = $CI->account_planning_model->get($rel_id);
        } else {
            $search = $CI->account_planning_model->_search_account_planning($q);
            $data   = $search['result'];
        }
    }
    return $data;
}

function add_option_task_related_to($rel_type)
{
    $selected = '';
    if($rel_type == 'account_planning'){
        $selected = 'selected';
    }
    echo "<option value='account_planning' ".$selected.">". _l('account_planning')."</option>";
}

function add_upload_path_account_planning($path, $type)
{
    if($type == 'account_planning'){
        $path = ACCOUNT_PLANNING_ATTACHMENTS_FOLDER;
    }

    return $path;
}

function before_add_task($data) {
    $CI = &get_instance();

    $account_planning_to_task = false;

    if (isset($data['account_planning_to_task'])) {
        $account_planning_to_task = true;
        unset($data['account_planning_to_task']);
    }

    $account_task_id = false;

    if (isset($data['account_task_id'])) {
        $account_task_id = $data['account_task_id'];

        unset($data['account_task_id']);
    }

    if($account_planning_to_task){
        $data_new = [];
        $data_new['account_task_id'] = $account_task_id;
        $CI->session->set_userdata($data_new);
    }

    return $data;
}

function after_add_task($insert_id)
{   
    $CI = &get_instance();
    $account_task_id = $CI->session->userdata("account_task_id");
    if((isset($account_task_id)) && $account_task_id != ''){
        $CI->session->unset_userdata("account_task_id");

        $CI->db->where('id', $account_task_id);
        $CI->db->update(db_prefix() . 'account_planning_task',['convert_to_task' => $insert_id]);
    }
}

function task_form_hidden(){
    $CI = &get_instance();
     if($CI->input->get('account_planning_to_task')) {
            echo form_hidden('account_planning_to_task');
      }
      if($CI->input->get('account_task_id')) {
        echo form_hidden('account_task_id', $CI->input->get('account_task_id'));
      };
}

function task_status_changed($data){
    if($data['status'] == 5){
        $CI = &get_instance();

        $CI->db->where('convert_to_task', $data['task_id']);
        $CI->db->update(db_prefix() . 'account_planning_task', [
            'status' => 'Complete',
        ]);
    }
}


/**
* Register language files
*/
register_language_files(ACCOUNT_PLANNING_MODULE_NAME, [ACCOUNT_PLANNING_MODULE_NAME]);

/**
 * Init project_roadmap module menu items in setup in admin_init hook
 * @return null
 */
function account_planning_module_init_menu_items()
{
    if (has_permission('account_planning', '', 'view')) {
        $CI = &get_instance();      
        $CI->app_menu->add_sidebar_menu_item('account_planning', [
                'name'     => _l('als_account_planning'),
                'href'     => admin_url('account_planning'),
                'position' => 30,
                'icon'     => 'fa fa-address-card-o',
        ]);
    }
}

function account_planning_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
            'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
            'create' => _l('permission_create'),
            'edit' => _l('permission_edit'),
            'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('account_planning', $capabilities, _l('account_planning'));
}