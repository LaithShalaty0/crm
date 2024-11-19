<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Multi-Page Web to Lead
Description: Module for making Tabbed / Multi-paged / Step Web to lead forms.
Version: 1.0.3
Requires at least: 2.3.*
Author: Swivernet
 */

define('MPWTL_MODULE_NAME', 'multi_page_wtl');

hooks()->add_action('admin_init', 'mpwtl_permissions');
hooks()->add_action('admin_init', 'mpwtl_module_init_menu_items');
hooks()->add_action('before_js_scripts_render', 'mpwtl_variable_script', 9);
hooks()->add_action('before_js_scripts_render', 'mpwtl_script', 10);

/**
 * Register activation module hook
 */
register_activation_hook(MPWTL_MODULE_NAME, 'mpwtl_module_activation_hook');

function mpwtl_module_activation_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/install.php';
}

/**
 * Register deactivation module hook
 */
register_uninstall_hook(MPWTL_MODULE_NAME, 'mpwtl_module_uninstall_hook');

function mpwtl_module_uninstall_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/uninstall.php';
}

/**
 * Register deactivation module hook
 */
register_deactivation_hook(MPWTL_MODULE_NAME, 'mpwtl_module_deactivation_hook');

function mpwtl_module_deactivation_hook() {
	$CI = &get_instance();
	require_once __DIR__ . '/deactivate.php';
}

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper(MPWTL_MODULE_NAME . '/' . MPWTL_MODULE_NAME);

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(MPWTL_MODULE_NAME, [MPWTL_MODULE_NAME]);

//methods
//
function mpwtl_script() {
	$CI = &get_instance();
	return $CI->app_scripts->add(MPWTL_MODULE_NAME . '-js', module_dir_url(MPWTL_MODULE_NAME, 'assets/js/' . MPWTL_MODULE_NAME . '.js') . '?v=' . $CI->app_scripts->core_version(), 'admin', ['app-js']);
}

function mpwtl_variable_script() {
	echo "<script> </script>";
}
/*
 * Init quickbooks module menu items in setup in admin_init hook
 */
function mpwtl_module_init_menu_items() {
	$CI = &get_instance();

	if (has_permission(MPWTL_MODULE_NAME, '', 'view')) {

		$CI->app_menu->add_setup_children_item('leads', [
			'slug' => MPWTL_MODULE_NAME,
			'name' => _l(MPWTL_MODULE_NAME),
			'href' => admin_url(MPWTL_MODULE_NAME . '/leads/forms'),
			'position' => 21,
		]);
	}
}

/*
 * Quickbooks Permissions
 */
function mpwtl_permissions() {
	$capabilities = [];

	$capabilities['capabilities'] = [
		'view' => _l('permission_view') . '(' . _l('permission_global') . ')',
		'create' => _l('permission_create'),
		'edit' => _l('permission_edit'),
		'delete' => _l('permission_delete'),
	];

	register_staff_capabilities(MPWTL_MODULE_NAME, $capabilities, _l(MPWTL_MODULE_NAME));
}