<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_planning extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tickets_model');
        $this->load->model('account_planning_model');
        $this->load->model('staff_model');
    }
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('account_planning', 'table'));
        }
        $data['month'] = $this->account_planning_model->get_month();
        $data['title'] = _l('als_account_planning');
        $this->load->view('account_planning/manage', $data);
    }
    public function table($client_id = '')
    {
        if ($client_id == '') {
            $this->app->get_table_data('account_planning');
        } else {
            $this->app->get_table_data('account_planning', [
                'client_id' => $client_id
            ]);
        }
    }
    public function new_account($id = '')
    {
        if (!has_permission('account_planning', '', 'create')) {
            access_denied('account_planning');
        }
        $data['month'] = $this->account_planning_model->get_month();
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['title'] = _l('new_account');
        $this->load->view('account_planning/new_account', $data);
    }
    public function add()
    {
        $data = $this->input->post();
        $id = $this->account_planning_model->add($data);
        if ($id) {
            set_alert('success', _l('added_successfully', _l('account_planning')));
            redirect(admin_url('account_planning'));
        }
    }

    public function delete($id)
    {
        if (!has_permission('account_planning', '', 'delete')) {
            access_denied('account_planning');
        }
        if (!$id) {
            redirect(admin_url('account_planning'));
        }
        $response = $this->account_planning_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('account_planning')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('account_planning')));
        }
        redirect(admin_url('account_planning'));
    }

    public function update_due_diligence($id)
    {
        if (!has_permission('account_planning', '', 'edit')) {
            access_denied('account_planning/view/' . $id);
        }
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id));
        }
        $data = $this->input->post();
        $response = $this->account_planning_model->update_due_diligence($id, $data);
        if ($response == true) {
            set_alert('success', _l('updated_successfully', _l('account_planning')));
        } else {
            set_alert('warning', _l('problem_updating', _l('account_planning')));
        }
        redirect(admin_url('account_planning/view/' . $id));
    }
    public function update_team_information($id)
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=team_information'));
        }
        if (!has_permission('account_planning', '', 'edit')) {
            access_denied('account_planning/view/' . $id . '?group=team_information');
        }
        $data = $this->input->post();
        $response = $this->account_planning_model->update_team_information($id, $data);
        if ($response == true) {
            set_alert('success', _l('updated_successfully', _l('account_planning')));
        } else {
            set_alert('warning', _l('problem_updating', _l('account_planning')));
        }
        redirect(admin_url('account_planning/view/' . $id . '?group=team_information'));
    }

    public function update_service_ability_offering($id)
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=service_ability_offering'));
        }
        if (!has_permission('account_planning', '', 'edit')) {
            access_denied('account_planning/view/' . $id . '?group=service_ability_offering');
        }
        $data = $this->input->post();
        $response = $this->account_planning_model->update_service_ability_offering($id, $data);
        if ($response == true) {
            set_alert('success', _l('updated_successfully', _l('account_planning')));
        } else {
            set_alert('warning', _l('problem_updating', _l('account_planning')));
        }
        redirect(admin_url('account_planning/view/' . $id . '?group=service_ability_offering'));
    }

    public function update_planning($id)
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
        }
        if (!has_permission('account_planning', '', 'edit')) {
            access_denied('account_planning/view/' . $id . '?group=planning');
        }
        $data = $this->input->post();
        $data['data_tree'] = $this->input->post('data_tree', false);
        $data['objectives'] = $this->input->post('objectives', false);
        $data['threat'] = $this->input->post('threat', false);
        $data['opportunity'] = $this->input->post('opportunity', false);
        $data['criteria_to_success'] = $this->input->post('criteria_to_success', false);
        $data['constraints'] = $this->input->post('constraints', false);

        $response = $this->account_planning_model->update_planning($id, $data);
        if ($response == true) {
            set_alert('success', _l('updated_successfully', _l('account_planning')));
        } else {
            set_alert('warning', _l('problem_updating', _l('account_planning')));
        }
        redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
    }

    public function view($id = '')
    {
        if ($id != '') {
            $data['account'] = $this->account_planning_model->get($id);
            if (!$this->input->get('group')) {
                $group = 'due_diligence';
            } else {
                $group = $this->input->get('group');
            }
            if ($group == 'team_information') {
                $data['teams'] = $this->staff_model->get();
                $data['client_team'] = $this->clients_model->get_contacts($data['account']->client_id);
                $data['data_pmax_team'] = $this->account_planning_model->get_pmax_team($id);
                $data['data_client_team'] = $this->account_planning_model->get_client_team($id);
            } elseif ($group == 'due_diligence') {
                $data['financial'] = $this->account_planning_model->get_financial($id);
                $data['marketing_activities'] = $this->account_planning_model->get_marketing_activities($id);
                $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($data['account']->client_id);
                $data['billing_shipping'] = $data['billing_shipping'][0];
            } elseif ($group == 'service_ability_offering') {
                $data['service_ability_offering'] = $this->account_planning_model->get_service_ability_offering($id);
                $data['current_service'] = $this->account_planning_model->get_current_service($id);
            } elseif ($group == 'planning') {
                $data['staff'] = $this->account_planning_model->get_pic_todolist();
                $data['month'] = $this->account_planning_model->get_month();
                $data['todo_list'] = $this->account_planning_model->get_todo_list($id);
                $data['objectives'] = $this->account_planning_model->get_objectives($id);
                $data['items'] = $this->account_planning_model->get_items($id);
            }


            $data['priorities'] = $this->tickets_model->get_priority();
            $data['group'] = $group;
            $data['title'] = _l('account_planning');
            $this->load->view('account_planning/account_planning', $data);
        } else {
            $data['title'] = _l('als_account_planning');
            $data = add_breadcrumbs($data, $this);
            $this->load->view('account_planning/manage', $data);
        }
    }
    public function client_change_data($customer_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $data = [];
            $data['billing_shipping'] = $this->clients_model->get_customer_billing_and_shipping_details($customer_id);
            $data['billing_shipping'][0]['billing_country'] = get_country_short_name($data['billing_shipping'][0]['billing_country']);
            echo json_encode($data);
        }
    }

    public function objective($id = '')
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                $response = $this->account_planning_model->add_objective($id, $data);
                if ($response == true) {
                    set_alert('success', _l('added_successfully', _l('objective')));
                } else {
                    set_alert('warning', _l('failed_to_insert', _l('objective')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
                if (!is_admin()) {
                    access_denied('Ticket Priorities');
                }
            } else {
                $data = $this->input->post();
                $objective_id = $data['id'];
                unset($data['id']);
                $success = $this->account_planning_model->update_objective($objective_id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('objective')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
            }
            die;
        }
    }
    public function item($id = '')
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                $response = $this->account_planning_model->add_item($id, $data);
                if ($response == true) {
                    set_alert('success', _l('added_successfully', _l('objective_items')));
                } else {
                    set_alert('warning', _l('failed_to_insert', _l('objective_items')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
            } else {
                $data = $this->input->post();
                $item_id = $data['id'];
                unset($data['id']);
                $success = $this->account_planning_model->update_item($item_id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('objective_items')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
            }
            die;
        }
    }
    public function task($id = '')
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                $response = $this->account_planning_model->add_task($id, $data);
                if ($response == true) {
                    set_alert('success', _l('added_successfully', _l('task')));
                } else {
                    set_alert('warning', _l('failed_to_insert', _l('task')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
            } else {
                $data = $this->input->post();
                $task_id = $data['id'];
                unset($data['id']);
                $success = $this->account_planning_model->update_task($task_id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('objective_items')));
                }
                redirect(admin_url('account_planning/view/' . $id . '?group=planning'));
            }
            die;
        }
    }

    public function delete_objective($account_planning_id, $id = '')
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
        }
        $response = $this->account_planning_model->delete_objective($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('objective')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('objective')));
        }
        redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
    }
    public function delete_item($account_planning_id, $id = '')
    {
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
        }
        $data = $this->input->post();
        $response = $this->account_planning_model->delete_item($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('objective')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('objective')));
        }
        redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
    }
    public function delete_task($account_planning_id, $id = '')
    {
        if (!has_permission('account_planning', '', 'edit')) {
            access_denied('account_planning/view/' . $account_planning_id . '?group=planning');
        }
        if (!$id) {
            redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
        }
        $data = $this->input->post();
        $response = $this->account_planning_model->delete_task($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('objective')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('objective')));
        }
        redirect(admin_url('account_planning/view/' . $account_planning_id . '?group=planning'));
    }

    public function copy($account_planning_id)
    {
        if (!has_permission('account_planning', '', 'create')) {
            access_denied('account_planning');
        }
        $id = $this->account_planning_model->copy($account_planning_id, $this->input->post());
        if ($id) {
            set_alert('success', _l('account_planning_copied_successfully'));
            redirect(admin_url('account_planning'));
        } else {
            set_alert('danger', _l('failed_to_copy_account_planning'));
            redirect(admin_url('account_planning'));
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo htmlspecialchars($this->account_planning_model->delete_attachment($id));
        } else {
            header('HTTP/1.0 400 Bad error');
            echo htmlspecialchars(_l('access_denied'));
            die;
        }
    }

    public function file($id, $rel_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin'] = is_admin();
        $data['file'] = $this->account_planning_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->load->view('account_planning/_file', $data);
    }

    public function download_file($attachmentid = '')
    {
        $this->load->helper('download');
        $this->db->where('attachment_key', $attachmentid);
        $attachment = $this->db->get(db_prefix() . 'files')->row();
        if (!$attachment) {
            show_404();
        }
        $path = ACCOUNT_PLANNING_ATTACHMENTS_FOLDER . $attachment->rel_id . '/' . $attachment->file_name;
        force_download($path, null);
    }
}