<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: ImportSync
Description: ImportSync is a Perfex module revolutionizing CSV imports. It unifies rules for various CSVs, enabling seamless data integration with different column layouts. Save time, reduce errors, and effortlessly import data from diverse sources into Perfex CRM.
Version: 1.0.0
Author: LenzCreative
Author URI: https://codecanyon.net/user/lenzcreativee/portfolio
Requires at least: 1.0.*
*/

define('IMPORTSYNC_MODULE_NAME', 'importsync');

hooks()->add_action('admin_init', 'importsync_module_init_menu_items');
hooks()->add_action('admin_init', 'importsync_permissions');
hooks()->add_action('importsync_init', IMPORTSYNC_MODULE_NAME . '_appint');
hooks()->add_action('pre_activate_module', IMPORTSYNC_MODULE_NAME . '_preactivate');
hooks()->add_action('pre_deactivate_module', IMPORTSYNC_MODULE_NAME . '_predeactivate');
hooks()->add_action('pre_uninstall_module', IMPORTSYNC_MODULE_NAME . '_uninstall');

require(__DIR__ . '/libraries/Import_staff.php');

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper(IMPORTSYNC_MODULE_NAME . '/importsync'); //on module main file

function importsync_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view' => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit' => _l('permission_edit'),
        'delete' => _l('permission_delete'),
        'import_staff' => _l('importsync_import_staff'),
    ];
    register_staff_capabilities('importsync', $capabilities, _l('importsync'));
}

/**
 * Register activation module hook
 */
register_activation_hook(IMPORTSYNC_MODULE_NAME, 'importsync_module_activation_hook');

function importsync_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(IMPORTSYNC_MODULE_NAME, [IMPORTSYNC_MODULE_NAME]);

/**
 * Init module menu items in setup in admin_init hook
 * @return null
 */
function importsync_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('importsync', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('importsync', [
            'slug' => 'importsync',
            'name' => _l('importsync'),
            'position' => 6,
            'icon' => 'fas fa-exchange-alt'
        ]);
    }

    if (has_permission('importsync', '', 'create')) {
        $CI->app_menu->add_sidebar_children_item('importsync', [
            'slug' => 'importsync-sync-csv',
            'name' => _l('importsync_sync_csv'),
            'position' => 6,
            'icon' => 'fas fa-exchange-alt',
            'href' => admin_url('importsync/csv_mappings')
        ]);
    }

    if (has_permission('importsync', '', 'view')) {
        $CI->app_menu->add_sidebar_children_item('importsync', [
            'slug' => 'importsync-manage-mapping',
            'name' => _l('importsync_manage_mappings'),
            'position' => 6,
            'icon' => 'fas fa-columns',
            'href' => admin_url('importsync/manage_mappings')
        ]);
    }

    if (has_permission('importsync', '', 'import_staff')) {
        $CI->app_menu->add_sidebar_children_item('importsync', [
            'slug' => 'importsync-import_staff',
            'name' => _l('importsync_import_staff'),
            'position' => 6,
            'icon' => 'fas fa-users',
            'href' => admin_url('importsync/import_staff')
        ]);
    }
}

function importsync_appint()
{
    return '';
    $CI = &get_instance();
    require_once 'libraries/leclib.php';
    $module_api = new ImportsyncLic();
    $module_leclib = $module_api->verify_license(true);
    if (!$module_leclib || ($module_leclib && isset($module_leclib['status']) && !$module_leclib['status'])) {
        $CI->app_modules->deactivate(IMPORTSYNC_MODULE_NAME);
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }
}

function importsync_preactivate($module_name)
{
    return '';
    if ($module_name['system_name'] == IMPORTSYNC_MODULE_NAME) {
        require_once 'libraries/leclib.php';
        $module_api = new ImportsyncLic();
        $module_leclib = $module_api->verify_license();
        if (!$module_leclib || ($module_leclib && isset($module_leclib['status']) && !$module_leclib['status'])) {
            $CI = &get_instance();
            $data['submit_url'] = $module_name['system_name'] . '/lecverify/activate';
            $data['original_url'] = admin_url('modules/activate/' . IMPORTSYNC_MODULE_NAME);
            $data['module_name'] = IMPORTSYNC_MODULE_NAME;
            $data['title'] = "Module License Activation";
            echo $CI->load->view($module_name['system_name'] . '/activate', $data, true);
            exit();
        }
    }
}

function importsync_predeactivate($module_name)
{
    return '';
    if ($module_name['system_name'] == IMPORTSYNC_MODULE_NAME) {
        require_once 'libraries/leclib.php';
        $warehouse_api = new ImportsyncLic();
        $warehouse_api->deactivate_license();
    }
}

function importsync_uninstall($module_name)
{
    return '';
    if ($module_name['system_name'] == IMPORTSYNC_MODULE_NAME) {
        require_once 'libraries/leclib.php';
        $warehouse_api = new ImportsyncLic();
        $warehouse_api->deactivate_license();
    }
}