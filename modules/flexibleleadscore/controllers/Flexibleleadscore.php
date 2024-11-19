<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');

class Flexibleleadscore extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied(flexiblels_lang('lead-scoring'));
        }
    }

    public function index($criteria_id = '')
    {
        $data['title'] = flexiblels_lang('lead-scoring-criteria');
        $data['criterias'] = $this->get_criterias();
        $data['criteria_operators'] = flexiblels_get_criteria_operators();
        $data['flexiblels_criteria'] = flexiblels_get_criteria();

        foreach($data['flexiblels_criteria'] as &$criterion){
            $criterion = flexiblels_enrich_criterion($criterion);
        }
        
        $this->app_scripts->add('flexibleleadscore-js', module_dir_url('flexibleleadscore', 'assets/js/flexibleleadscore.js'), 'admin', ['app-js']);
        $this->load->view('index', $data);
    }
    
    public function reports()
    {
        $this->load->model('flexibleleadscore/flexibleleadscore_leadscore_model');
        $data['title'] = flexiblels_lang('lead-scoring-reports');
        $data['scores_report_data'] = json_encode($this->flexibleleadscore_leadscore_model->scores_report_data());
        
        $this->app_scripts->add('flexibleleadscore-js', module_dir_url('flexibleleadscore', 'assets/js/flexibleleadscore.js'), 'admin', ['app-js']);
        $this->load->view('reports', $data);
    }

    public function add_criteria(){
        if($post = $this->input->post()){
            $changed_at = to_sql_date(Carbon::now()->toDateTimeString(), true);

            $post = array_merge($post, [
                'date_updated' => $changed_at,
            ]);

            if (array_key_exists('id', $post)) {
                $post['flexibleleadscore_criteria_value'] = serialize($post['flexibleleadscore_criteria_value']);
                if ($insert_id = flexiblels_update_criteria($post)) {
                    update_option(FLEXIBLELEADSCORE_STATUS_OPTION, FLEXIBLELEADSCORE_INACTIVE_STATUS);
                    set_alert('success', flexiblels_lang('criteria-updated-successfully'));
                } else {
                    set_alert('danger', flexiblels_lang('criteria-update-failed'));
                }
            } else {
                $post = array_merge($post, [
                    'date_added' => $changed_at,
                    'user_id' => get_staff_user_id()
                ]);
                $post['flexibleleadscore_criteria_value'] = serialize($post['flexibleleadscore_criteria_value']);

                if ($insert_id = flexiblels_add_criteria($post)) {
                    update_option(FLEXIBLELEADSCORE_STATUS_OPTION, FLEXIBLELEADSCORE_INACTIVE_STATUS);
                    set_alert('success', flexiblels_lang('criteria-added-successfully'));
                } else {
                    set_alert('danger', flexiblels_lang('criteria-addition-failed'));
                }
            }

            if($insert_id){
                redirect(admin_url('flexibleleadscore'));
            }
        }
    }

    public function get_criteria($criteria_id = ''){
        $data['title'] = flexiblels_lang('lead-scoring-criteria');
        $data['criterias'] = $this->get_criterias();
        $data['criteria_operators'] = flexiblels_get_criteria_operators();
        $result = [
            'success' => true,
            'data' => []
        ];

        if($criteria_id){
            $data['criterion'] = flexiblels_get_single_criteria($criteria_id);
            $data['criterion']['flexibleleadscore_criteria_value'] = @unserialize($data['criterion']['flexibleleadscore_criteria_value']);
            $result['html'] = $this->load->view('partials/criteria-modal', $data, true);
        }else{
            $result['html'] = $this->load->view('partials/criteria-modal', $data, true);
        }

        echo json_encode($result);
        die;
    }

    public function activate()
    {
        if ($this->input->post()) {
            update_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION, FLEXIBLELEADSCORE_CRON_STATUS_RUNNING);
            //we will update running status after cron job is done
        }
        redirect(admin_url('flexibleleadscore'));
    }

    public function delete_criteria($criteria_id = ''){
        if(empty($criteria_id)){
            redirect(admin_url('flexibleleadscore'));
        }

        if(flexiblels_delete_criteria($criteria_id)){
            // de-activate lead scoring
            update_option(FLEXIBLELEADSCORE_STATUS_OPTION, FLEXIBLELEADSCORE_INACTIVE_STATUS);
            set_alert('success', flexiblels_lang('criteria-deleted-successfully'));
        }else{
            set_alert('danger', flexiblels_lang('criteria-deletion-failed'));
        }

        redirect(admin_url('flexibleleadscore'));
    }

    public function ajax(){
        $action = $this->input->get('action') ? $this->input->get('action') : $this->input->post('action');
        $result = [
            'success' => false,
            'data' => []
        ];
        if ($action == 'get_criteria_value') {
            $criteria_id = $this->input->post('criteria_id');
            $result['success'] = true;
            $result['data'] = $this->get_criterias();
            $result['refresh_selectpicker'] = true;

            $result['html'] = flexiblels_get_criteria_values($criteria_id, $result);
        }
        echo json_encode($result);
    }

    private function get_criterias(): array
    {
        $fields =  [
            [
                'id'=>'name',
                'name'=>flexiblels_lang('name')
            ],
            [
                'id' => 'lead-source',
                'name' => flexiblels_lang('lead-source'),
            ],
            [
                'id' => 'lead-status',
                'name' => flexiblels_lang('lead-status'),
            ],
            [
                'id' => 'lead-form',
                'name' => flexiblels_lang('lead-form'),
            ],
            [
                'id' => 'lead-value',
                'name' => flexiblels_lang('lead-value'),
            ],
            [
                'id'=>'email',
                'name'=>flexiblels_lang('email')
            ],
            [
                'id'=>'title',
                'name'=>flexiblels_lang('title')
            ],
            [
                'id'=>'company',
                'name'=>flexiblels_lang('company')
            ],
            [
                'id'=>'phonenumber',
                'name'=>flexiblels_lang('phonenumber')
            ],
            [
                'id'=>'address',
                'name'=>flexiblels_lang('address')
            ],
            [
                'id'=>'city',
                'name'=>flexiblels_lang('city')
            ],
            [
                'id'=>'state',
                'name'=>flexiblels_lang('state')
            ],
            [
                'id'=>'country',
                'name'=>flexiblels_lang('country')
            ],
            [
                'id'=>'zip',
                'name'=>flexiblels_lang('zip')
            ],
            [
                'id'=>'description',
                'name'=>flexiblels_lang('description')
            ],
            [
                'id'=>'website',
                'name'=>flexiblels_lang('website')
            ],
//            [
//                'id'=>'lost',
//                'name'=>flexiblels_lang('lost')
//            ],
//            [
//                'id'=>'junk',
//                'name'=>flexiblels_lang('junk')
//            ]
        ];

        $custom_fields    = get_custom_fields('leads', 'type != "link"');
        foreach ($custom_fields as $field) {
            $fields[] = [
                'id' => $field['id'],
                'name' => $field['name'],
            ];
        }
        return $fields;
    }

}
