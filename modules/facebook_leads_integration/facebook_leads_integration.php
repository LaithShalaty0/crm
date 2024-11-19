<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Facebook leads integration 
Description: Sync leads between Facebook Leads and Perfex Leads
Version: 1.0.0
Requires at least: 2.3.*
Author: Themesic Interactive
Author URI: https://codecanyon.net/user/themesic/portfolio
*/

define('FACEBOOK_LEADS_INTEGRATION_MODULE_NAME', 'facebook_leads_integration');

hooks()->add_action('admin_init', 'facebook_leads_integration_module_init_menu_items');
hooks()->add_action('admin_init', 'add_settings_tab');
hooks()->add_action('admin_init', 'exclude_uri');


// exclude urls from csrf
function exclude_uri() {

    $CI = &get_instance();
    $CI->load->config('migration');
    $update_info = $CI->config->item('migration_version');
    if(!get_option('current_perfex_version'))
    {
        update_option('current_perfex_version',$update_info);
    }
    if(!get_option('excluded_uri_for_facebook_leads_integration_once') || get_option('current_perfex_version') != $update_info)
    {
        
        
        $myfile = fopen(APPPATH."config/config.php", "a") or die("Unable to open file!");
        $txt = "if(!isset(\$config['csrf_exclude_uris']))
        {
            \$config['csrf_exclude_uris']=[];
        }";
        fwrite($myfile, "\n". $txt);
        $txt = "\$config['csrf_exclude_uris'] = array_merge(\$config['csrf_exclude_uris'],array('facebook_leads_integration/webhook'));";
        fwrite($myfile, "\n". $txt);
        $txt = "\$config['csrf_exclude_uris'] = array_merge(\$config['csrf_exclude_uris'],array('facebook_leads_integration/get_lead_data'));";
        fwrite($myfile, "\n". $txt);
        fclose($myfile);
        update_option('current_perfex_version',$update_info);
        update_option('excluded_uri_for_facebook_leads_integration_once', 1);
    }
    
    
}

function add_settings_tab()
{
    $CI = &get_instance();
    $CI->app_tabs->add_settings_tab('facebook_leads_integration', [
        'name'     => _l('facebook_leads_integration'),
        'view'     => FACEBOOK_LEADS_INTEGRATION_MODULE_NAME . '/facebook_leads_integration_view',
        'position' => 101,
    ]);
}


/**
 * Register activation module hook
 */
register_activation_hook(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME, 'facebook_leads_integration_module_activation_hook');

function facebook_leads_integration_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files, must be registered if the module is using languages
 */
register_language_files(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME, [FACEBOOK_LEADS_INTEGRATION_MODULE_NAME]);

/**
 * Init FACEBOOK LEADS INTEGRATION module menu items in setup in admin_init hook
 * @return null
 */
function facebook_leads_integration_module_init_menu_items()
{
    $CI = &get_instance();

    $CI->app->add_quick_actions_link([
        'name'       => _l('facebook_leads_integration'),
        'permission' => 'facebook_leads_integration',
        'url'        => 'facebook_leads_integration',
        'position'   => 69,
    ]);
}

hooks()->add_action('app_init',FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_actLib');
function facebook_leads_integration_actLib()
{
    $CI = & get_instance();
    $CI->load->library(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'/Envapi');
    $envato_res = $CI->envapi->validatePurchase(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME);
    if (!$envato_res) {
        set_alert('danger', "One of your modules failed its verification and got deactivated. Please reactivate or contact support.");
        redirect(admin_url('modules'));
    }
}

hooks()->add_action('pre_activate_module', FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_sidecheck');
function facebook_leads_integration_sidecheck($module_name)
{
    if ($module_name['system_name'] == FACEBOOK_LEADS_INTEGRATION_MODULE_NAME) {
        if (!option_exists(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_verified') && empty(get_option(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_verified')) && !option_exists(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_verification_id') && empty(get_option(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_verification_id'))) {
            $CI = & get_instance();
            $data['submit_url'] = $module_name['system_name'].'/env_ver/activate'; 
            $data['original_url'] = admin_url('modules/activate/'.FACEBOOK_LEADS_INTEGRATION_MODULE_NAME); 
            $data['module_name'] = FACEBOOK_LEADS_INTEGRATION_MODULE_NAME; 
            $data['title'] = "Module activation"; 
            echo $CI->load->view($module_name['system_name'].'/activate', $data, true);
            exit();
        }
    }
}

hooks()->add_action('pre_deactivate_module', FACEBOOK_LEADS_INTEGRATION_MODULE_NAME.'_deregister');
function facebook_leads_integration_deregister($module_name)
{
    if ($module_name['system_name'] == FACEBOOK_LEADS_INTEGRATION_MODULE_NAME) {
        delete_option(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME."_verified");
        delete_option(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME."_verification_id");
        delete_option(FACEBOOK_LEADS_INTEGRATION_MODULE_NAME."_last_verification");
        if(file_exists(__DIR__."/config/token.php")){
            unlink(__DIR__."/config/token.php");
        }
    }
}
