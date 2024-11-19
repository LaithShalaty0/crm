<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
        $this->load->model('custom_reports_model');
    }

    public function index()
    {
        $data['title'] = _l('custom_report');
        $data['report_data'] = $this->get_report_data();
        $data['status_data'] = $this->get_status_data();
        $this->load->view('admin/custom_reports/report', $data);
    }

    private function get_report_data()
    {
        $query = $this->db->query("
            SELECT COUNT(l.source) AS leads_count, s.name AS source_name
            FROM tblleads l
            INNER JOIN tblleads_sources s ON l.source = s.id
            GROUP BY l.source
        ");
        $data = $query->result_array();

        return [
            'labels' => array_column($data, 'source_name'),
            'data' => array_column($data, 'leads_count')
        ];
    }

    private function get_status_data()
    {
        $query = $this->db->query("
            SELECT COUNT(l.status) AS leads_count, s.name AS status_name
            FROM tblleads l
            INNER JOIN tblleads_status s ON l.status = s.id
            GROUP BY l.status
        ");
        $data = $query->result_array();

        return [
            'labels' => array_column($data, 'status_name'),
            'data' => array_column($data, 'leads_count')
        ];
    }

    public function generate_report()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $csrf_token_name = $this->security->get_csrf_token_name();
            $csrf_hash = $this->security->get_csrf_hash();

            if ($from_date && $to_date) {
                $report_data = $this->custom_reports_model->get_report_data($from_date, $to_date);
                $cumulative_data = $this->custom_reports_model->get_cumulative_report_data();
                // $stuff_report_data = $this->custom_reports_model->get_stuff_report($from_date, $to_date);
                $cumulative_stuff_data = $this->custom_reports_model->get_cumulative_stuff_report();

                $data['report_data'] = $report_data;
                $data['cumulative_data'] = $cumulative_data;
                // $data['stuff_report_data'] = $stuff_report_data;
                $data['cumulative_stuff_data'] = $cumulative_stuff_data;
                $data['from_date'] = $from_date;
                $data['to_date'] = $to_date;
                $data[$csrf_token_name] = $csrf_hash;

                $this->load->view('admin/custom_reports/report_result', $data);
            } else {
                show_error('Please provide both from and to dates.');
            }
        } else {
            show_error('Invalid request method.');
        }
    }
}
