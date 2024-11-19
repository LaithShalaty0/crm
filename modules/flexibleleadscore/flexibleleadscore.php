<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Flexible Lead Scoring Module
Description: Flexible Lead Scoring Module helping you prioritise leads based on configured rules for Perfex CRM
Version: 1.0.1
Requires at least: 2.3.*
Author: FlexiByte Team
Author URI: https://codecanyon.net/user/flexibyte88
*/


define('FLEXIBLELEADSCORE_MODULE_NAME', 'flexibleleadscore');
define('FLEXIBLELEADSCORE_IS_CRITERIA_OPERATOR', 'is');
define('FLEXIBLELEADSCORE_IS_NOT_CRITERIA_OPERATOR', 'is_not');
define('FLEXIBLELEADSCORE_CONTAINS_CRITERIA_OPERATOR', 'contains');
define('FLEXIBLELEADSCORE_DOES_NOT_CONTAINS_CRITERIA_OPERATOR', 'does_not_contains');
define('FLEXIBLELEADSCORE_STARTS_WITH_CRITERIA_OPERATOR', 'starts_with');
define('FLEXIBLELEADSCORE_ENDS_WITH_CRITERIA_OPERATOR', 'ends_with');
define('FLEXIBLELEADSCORE_IS_EMPTY_CRITERIA_OPERATOR', 'is_empty');
define('FLEXIBLELEADSCORE_IS_NOT_EMPTY_CRITERIA_OPERATOR', 'is_not_empty');
define('FLEXIBLELEADSCORE_GREATER_THAN_CRITERIA_OPERATOR', 'greater_than');
define('FLEXIBLELEADSCORE_LESS_THAN_CRITERIA_OPERATOR', 'less_than');
define('FLEXIBLELEADSCORE_EQUAL_TO_CRITERIA_OPERATOR', 'equal_to');
define('FLEXIBLELEADSCORE_NOT_EQUAL_TO_CRITERIA_OPERATOR', 'not_equal_to');
define('FLEXIBLELEADSCORE_GREATER_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR', 'greater_than_equal_to');
define('FLEXIBLELEADSCORE_LESS_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR', 'less_than_or_equal_to');
define('FLEXIBLELEADSCORE_ADD', 'add');
define('FLEXIBLELEADSCORE_SUBTRACT', 'subtract');
//status option
define('FLEXIBLELEADSCORE_STATUS_OPTION', 'flexibleleadscore_status');
define('FLEXIBLELEADSCORE_ACTIVE_STATUS', 1);
define('FLEXIBLELEADSCORE_INACTIVE_STATUS', 0);
//cron status option
define('FLEXIBLELEADSCORE_CRON_STATUS_OPTION', 'flexiblels_cron_action_status');
define('FLEXIBLELEADSCORE_CRON_STATUS_INACTIVE', 0);
define('FLEXIBLELEADSCORE_CRON_STATUS_RUNNING', 1);
hooks()->add_filter('numbers_of_features_using_cron_job', 'flexibleleadscore_numbers_of_features_using_cron_job');
hooks()->add_action('admin_init', 'flexibleleadscore_module_init_menu_items');
hooks()->add_action('after_cron_run', 'flexiblescore_update_old_records');
hooks()->add_action('lead_created', 'flexiblescore_update_single_lead_score');
hooks()->add_action('lead_status_changed', 'flexiblescore_lead_status_change');
hooks()->add_filter('leads_table_row_data', 'flexiblescore_additional_table_column_data');
hooks()->add_filter('leads_table_columns', 'flexiblescore_leads_table_columns');
hooks()->add_filter('leads_table_sql_columns', 'flexiblescore_table_sql');
//hook into the leads popup
hooks()->add_action('after_lead_lead_tabs', 'flexibleleadscore_leads_tab');
hooks()->add_action('after_lead_tabs_content', 'flexibleleadscore_leads_tab_content');



/**
* Register activation module hook
*/
register_activation_hook(FLEXIBLELEADSCORE_MODULE_NAME, FLEXIBLELEADSCORE_MODULE_NAME.'_module_activation_hook');

function flexibleleadscore_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
* Register language files, must be registered if the module is using languages
*/
register_language_files(FLEXIBLELEADSCORE_MODULE_NAME, [FLEXIBLELEADSCORE_MODULE_NAME]);

/**
 * Init Lead scoring module menu items in setup in admin_init hook
 * @return null
 */
function flexibleleadscore_module_init_menu_items()
{
    /**
    * If the logged in user is administrator, add custom menu in Setup
    */
    if (is_admin()) {
        $CI = &get_instance();
        $CI->app_menu->add_sidebar_menu_item('flexible-leadscoring-menu', [
            'name'     => flexiblels_lang('lead-scoring'), // The name if the item
            'href'     => admin_url('flexibleleadscore'), // URL of the item
            'position' => 45, // The menu position, see below for default positions.
            'icon'     => 'fa-solid fa-sort', // Font awesome icon
        ]);
    }
}

function flexibleleadscore_leads_tab(){
    $CI = &get_instance();
    $CI->load->view('flexibleleadscore/partials/lead-menu');
}

function flexibleleadscore_leads_tab_content($lead){
    // var_dump($lead);
    if($lead){
        $CI = &get_instance();
        $CI->load->library(FLEXIBLELEADSCORE_MODULE_NAME . '/' . 'flexibleleadscore_module');
        $lead_id = $lead->id;
        $CI->load->model('flexibleleadscore/flexleads_model');
        //get the lead
        $lead = $CI->flexleads_model->get(['id'=>$lead_id]);
        $score = 0;
        $applied_criterias = [];
        $criteria = flexiblels_get_criteria();
        foreach ($criteria as $c){
            $criteria_score = $CI->flexibleleadscore_module->calculate_score_based_on_criteria($lead,$c);
            if($criteria_score != 0){
                $score += $criteria_score;
                $applied_criterias[] = flexiblels_enrich_criterion($c);
            }
        }
        $CI->load->view('flexibleleadscore/partials/leads-modal-content', ['score'=>$score,'lead' => $lead,'criteria'=>$applied_criterias]);
    }
}

function flexiblescore_table_sql($sql_columns){
    $sql_columns[]= ' (SELECT score FROM '.db_prefix().'flexiblels_lead_scores WHERE lead_id = '.db_prefix().'leads.id) as score';
    return $sql_columns;
}

function flexiblescore_leads_table_columns($columns){
    $columns[] = [
        'name'     => flexiblels_lang('lead-score'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-flexibleleadscore'],
    ];
    return $columns;
}

function flexiblescore_additional_table_column_data($row){
    if(isset($row['DT_RowId'])){
        $lead_id_with_string = $row['DT_RowId'];
        //extract lead id from string
        $lead_id = filter_var($lead_id_with_string, FILTER_SANITIZE_NUMBER_INT);
        $row[] = flexiblescore_get_lead_score($lead_id);
        return $row;
    }
}

function flexiblescore_get_lead_score($lead_id)
{
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_leadscore_model');
    $lead_score = $CI->flexibleleadscore_leadscore_model->get([
        'lead_id' => $lead_id
    ]);
    if($lead_score){
        return $lead_score['score'];
    }
    return 0;
}

function flexiblescore_update_single_lead_score($lead_id){
    $CI = &get_instance();
    $CI->load->library(FLEXIBLELEADSCORE_MODULE_NAME . '/' . 'flexibleleadscore_module');
    $CI->flexibleleadscore_module->update_single_lead_score($lead_id);
}

function flexiblescore_lead_status_change($data){
    $lead_id = $data['lead_id'];
    flexiblescore_update_single_lead_score($lead_id);
}
function flexibleleadscore_numbers_of_features_using_cron_job($number)
{
    $number += 1;
    return $number;
}

function flexiblescore_update_old_records(){
    $CI = &get_instance();
    if(get_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION) == FLEXIBLELEADSCORE_CRON_STATUS_RUNNING){
        $CI->load->library(FLEXIBLELEADSCORE_MODULE_NAME . '/' . 'flexibleleadscore_module');
        $CI->flexibleleadscore_module->update_old_records();
        //we are resetting cron status to inactive
        update_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION, FLEXIBLELEADSCORE_CRON_STATUS_INACTIVE);
        update_option(FLEXIBLELEADSCORE_STATUS_OPTION, FLEXIBLELEADSCORE_ACTIVE_STATUS);
    }
}
function flexiblels_lang($key)
{
    return _l(FLEXIBLELEADSCORE_MODULE_NAME.'_'.$key);
}

function flexiblels_get_lead_sources(){
    $CI = &get_instance();
    $CI->load->model('leads_model');
    return $CI->leads_model->get_source();
}

function flexiblels_get_lead_statuses(){
    $CI = &get_instance();
    $CI->load->model('leads_model');

    return $CI->leads_model->get_status();
}

function flexiblels_get_lead_forms(){
    $CI = &get_instance();
    $CI->load->model('flexwebtolead_model');

    return $CI->flexwebtolead_model->all();
}

function flexiblels_get_criteria_operators(){
    return [
        [
            'id' => FLEXIBLELEADSCORE_IS_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('is')
        ],
        [
            'id' => FLEXIBLELEADSCORE_IS_NOT_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('is-not')
        ],
        [
            'id' => FLEXIBLELEADSCORE_CONTAINS_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('contains')
        ],
        [
            'id' => FLEXIBLELEADSCORE_DOES_NOT_CONTAINS_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('does-not-contains')
        ],
        [
            'id' => FLEXIBLELEADSCORE_STARTS_WITH_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('starts-with')
        ],
        [
            'id' => FLEXIBLELEADSCORE_ENDS_WITH_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('ends-with')
        ],
        [
            'id' => FLEXIBLELEADSCORE_IS_EMPTY_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('is-empty')
        ],
        [
            'id' => FLEXIBLELEADSCORE_IS_NOT_EMPTY_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('is-not-empty')
        ],
        [
            'id' => FLEXIBLELEADSCORE_GREATER_THAN_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('greater-than')
        ],
        [
            'id' => FLEXIBLELEADSCORE_LESS_THAN_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('less-than')
        ],
        [
            'id' => FLEXIBLELEADSCORE_EQUAL_TO_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('equal-to')
        ],
        [
            'id' => FLEXIBLELEADSCORE_NOT_EQUAL_TO_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('not-equal-to')
        ],
        [
            'id' => FLEXIBLELEADSCORE_GREATER_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('greater-than-or-equal-to')
        ],
        [
            'id' => FLEXIBLELEADSCORE_LESS_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR,
            'label' => flexiblels_lang('less-than-or-equal-to')
        ]
    ];
}

function flexiblels_add_criteria($post){
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_criteria_model');

    return $CI->flexibleleadscore_criteria_model->add($post);
}

function flexiblels_update_criteria($post){
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_criteria_model');

    $id = $post['id'];
    unset($post['id']);
    return $CI->flexibleleadscore_criteria_model->update($id, $post);
}

function flexiblels_get_criteria(){
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_criteria_model');
    return $CI->flexibleleadscore_criteria_model->all();
}

function flexiblels_get_single_criteria($criteria_id){
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_criteria_model');
    return $CI->flexibleleadscore_criteria_model->get([
        'id' => $criteria_id
    ]);
}

function flexiblels_enrich_criterion($criterion,$string_only = true){
    $CI = &get_instance();
    $criteria_label = @unserialize($criterion['flexibleleadscore_criteria_value']);
    switch ($criterion['flexibleleadscore_criteria']) {
        case 'lead-source':
            $CI->load->model('leads_model');
            $lead_source = $CI->leads_model->get_source($criteria_label);
            $criterion['flexibleleadscore_display_value'] = $lead_source->name;
            break;
            
        case 'lead-status':
            $CI->load->model('leads_model');
            $lead_status = $CI->leads_model->get_status($criteria_label);
            $criterion['flexibleleadscore_display_value'] = $lead_status->name;
            break;
        
        case 'lead-form':
            $CI->load->model('flexwebtolead_model');
            $lead_form = $CI->flexwebtolead_model->get([
                'id' => $criteria_label]);
            $criterion['flexibleleadscore_display_value'] = $lead_form['name'];
            break;
        
        default:

            $criteria_source = $criterion['flexibleleadscore_criteria'];
            if(is_numeric($criteria_source)){
                //custom fields are numeric
                $custom_field   = get_custom_fields('leads', 'id ='.$criteria_source);
                $criteria_label = $custom_field[0]['name'];
                $criterion['flexibleleadscore_criteria'] = $criteria_label;
            }

            $flexibleleadscore_criteria_value = @unserialize($criterion['flexibleleadscore_criteria_value']);
            if($string_only){
                if(is_array($flexibleleadscore_criteria_value)){
                    $criterion['flexibleleadscore_display_value'] = implode(', ', $flexibleleadscore_criteria_value);
                }else{
                    $criterion['flexibleleadscore_display_value'] = $flexibleleadscore_criteria_value;
                }
            }

            break;
    }

    return $criterion;
}

function flexiblels_delete_criteria($criteria_id){
    $CI = &get_instance();
    $CI->load->model('flexibleleadscore/flexibleleadscore_criteria_model');

    return $CI->flexibleleadscore_criteria_model->delete($criteria_id);
}

function flexiblels_get_criteria_values($criteria_id, &$result = []){
    $CI = &get_instance();

    switch ($criteria_id){
        case 'lead-source':
            $result['lead_sources'] = flexiblels_get_lead_sources();
            $html = $CI->load->view('partials/leads-sources', $result, true);
            break;

       case 'lead-status':
            $result['lead_statuses'] = flexiblels_get_lead_statuses();
            $html = $CI->load->view('partials/leads-status', $result, true);
            break;

       case 'lead-form':
            $result['lead_forms'] = flexiblels_get_lead_forms();
            $html = $CI->load->view('partials/leads-forms', $result, true);
            break;
        default:
            //target custom fields values and return their corresponding html especially if is a dropdown list
            $is_custom_field = false;
            $custom_field   = get_custom_fields('leads', ['id' => $criteria_id]);

            if($custom_field && ($custom_field[0]['type'] == "select" || $custom_field[0]['type'] =="multiselect")){
                $is_custom_field = true;
                $result['refresh_selectpicker'] = true;
                $options = explode(',',$custom_field[0]['options']);
                $options = array_map(function($option){
                    return [
                        'id' => $option,
                        'name' => ucfirst($option)
                    ];
                }, $options);
                $html = $CI->load->view('partials/custom-fields', array('custom_values' => $options,'field'=>$custom_field[0]), true);
            }
            if(!$is_custom_field){
                $result['refresh_selectpicker'] = false;
                $html = $CI->load->view('partials/criteria-text', $result, true);
            }

            break;
    }

    return $html;
}
