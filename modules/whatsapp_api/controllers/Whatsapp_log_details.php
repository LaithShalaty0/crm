<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Whatsapp_log_details extends AdminController
{
    public function index()
    {
        $data['title']                 = _l('whatsapp_log_details');
        if (!has_permission('whatsapp_api', '', 'whatsapp_log_details_view')) {
            access_denied('whatsapp_log_details_view');
        }
        $this->load->view('whatsapp_log', $data);
    }

    public function whatsapp_log_details_table()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('whatsapp_api', 'tables/whatsapp_log_details_table'));
        }
    }

    public function clear_webhook_log()
    {
        if (!has_permission('whatsapp_api', '', 'whatsapp_log_details_clear')) {
            access_denied("whatsapp_log_details_clear");
        }
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        if ($this->whatsapp_api_model->clear_webhook_log()) {
            set_alert('success', _l('deleted', _l('whatsapp_api_log')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('whatsapp_api_log')));
        }
        redirect(admin_url(WHATSAPP_API_MODULE . '/whatsapp_log_details'));

        return true;
    }

    public function get_whatsapp_api_log_info($log_id)
    {
        $this->load->model(WHATSAPP_API_MODULE . '/whatsapp_api_model');
        if ($log_id) {
            $data['title']    = _l('whatsapp_api_log');
            $data['log_data'] = $this->whatsapp_api_model->get_whatsapp_api_log_info($log_id);
            $this->load->view('whatsapp_api_log_details', $data, false);
        }
    }
}

/* End of file whatsapp_log_details.php */
/* Location: ./modules/whatsapp_api/controllers/whatsapp_log_details.php */
