<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Custom_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
        $this->load->model('custom_reports_model');
        $this->load->model('call_logs_model');
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

    // public function call_logs_overview()
    // {
    //     $this->load->model('call_logs_model');

    //     $start_date = $this->input->get('start_date') ?? date('Y-m-01');
    //     $end_date = $this->input->get('end_date') ?? date('Y-m-t');

    //     $data['total_calls'] = $this->call_logs_model->get_total_calls($start_date, $end_date);
    //     $data['call_duration'] = $this->call_logs_model->get_call_duration($start_date, $end_date);
    //     $data['call_outcomes'] = $this->call_logs_model->get_call_outcomes($start_date, $end_date);
    //     $data['lead_sources_statuses'] = $this->call_logs_model->get_lead_sources_statuses($start_date, $end_date);
    //     $data['salesperson_performance'] = $this->call_logs_model->get_salesperson_performance($start_date, $end_date);
    //     $data['calls_results'] = $this->call_logs_model->get_calls_results($start_date, $end_date);
    //     $data['lead_reports'] = $this->call_logs_model->get_lead_reports($start_date, $end_date);

    //     // Pass the date variables to the view
    //     $data['start_date'] = $start_date;
    //     $data['end_date'] = $end_date;

    //     $data['title'] = _l('call_logs_overview');
    //     $this->load->view('admin/custom_reports/call_logs_overview', $data);
    // }

    public function call_logs_overview()
    {
        $this->load->model('call_logs_model');

        $start_date = $this->input->get('start_date') ?? date('Y-m-01');
        $end_date = $this->input->get('end_date') ?? date('Y-m-t');

        // Fetch data from the model
        $data['successful_calls'] = $this->call_logs_model->get_successful_calls($start_date, $end_date);
        $data['unsuccessful_calls'] = $this->call_logs_model->get_unsuccessful_calls($start_date, $end_date);
        $data['total_calls'] = $this->call_logs_model->get_total_calls($start_date, $end_date);
        $data['call_results'] = $this->call_logs_model->get_call_results($start_date, $end_date);
        $data['data_source_analysis'] = $this->call_logs_model->get_data_source_analysis($start_date, $end_date);

        // Cumulative data
        $data['cumulative_successful_calls'] = $this->call_logs_model->get_cumulative_successful_calls();
        $data['cumulative_unsuccessful_calls'] = $this->call_logs_model->get_cumulative_unsuccessful_calls();
        $data['cumulative_total_calls'] = $this->call_logs_model->get_cumulative_total_calls();
        $data['cumulative_call_results'] = $this->call_logs_model->get_cumulative_call_results();
        $data['cumulative_data_source_analysis'] = $this->call_logs_model->get_cumulative_data_source_analysis();

        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        $data['title'] = _l('call_logs_overview');
        $this->load->view('admin/custom_reports/call_logs_overview', $data);
    }



}
