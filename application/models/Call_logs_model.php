<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Call_logs_model extends App_Model
{
    public function get_total_calls($start_date, $end_date)
    {
        $this->db->select('COUNT(*) AS total_calls');
        $this->db->from('tblcall_logs');
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_start_time <=', $end_date);
        $result = $this->db->get()->row();
        return $result ? $result->total_calls : 0;
    }

    public function get_call_duration($start_date, $end_date)
    {
        $this->db->select('staffid, SUM(TIME_TO_SEC(call_duration)) AS total_duration');
        $this->db->from('tblcall_logs');
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_start_time <=', $end_date);
        $this->db->group_by('staffid');
        return $this->db->get()->result_array();
    }

    public function get_call_outcomes($start_date, $end_date)
    {
        $this->db->select('
            SUM(is_completed = 1) AS successful_calls,
            SUM(is_completed = 0) AS unsuccessful_calls,
            SUM(has_follow_up = 1) AS follow_up_required
        ');
        $this->db->from('tblcall_logs');
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_start_time <=', $end_date);
        return $this->db->get()->row_array();
    }

    public function get_lead_sources_statuses($start_date, $end_date)
    {
        $this->db->select('
            ls.name AS lead_source,
            lstatus.name AS lead_status,
            COUNT(*) AS total_leads
        ');
        $this->db->from('tblleads AS l');
        $this->db->join('tblleads_sources AS ls', 'l.source = ls.id', 'left');
        $this->db->join('tblleads_status AS lstatus', 'l.status = lstatus.id', 'left');
        $this->db->where('l.dateadded >=', $start_date);
        $this->db->where('l.dateadded <=', $end_date);
        $this->db->group_by('lead_source, lead_status');
        return $this->db->get()->result_array();
    }

    public function get_salesperson_performance($start_date, $end_date)
    {
        $this->db->select('
            staffid,
            COUNT(*) AS total_calls,
            SUM(TIME_TO_SEC(call_duration)) AS total_duration,
            SUM(is_completed = 1) AS successful_calls
        ');
        $this->db->from('tblcall_logs');
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_start_time <=', $end_date);
        $this->db->group_by('staffid');
        return $this->db->get()->result_array();
    }

    public function get_calls_results($start_date, $end_date)
    {
        $this->db->select('
            r.name AS result,
            COUNT(*) AS total_count
        ');
        $this->db->from('tblcall_logs AS c');
        $this->db->join('tblcall_logs_rel_types AS r', 'c.rel_type = r.id', 'left');
        $this->db->where('c.call_start_time >=', $start_date);
        $this->db->where('c.call_start_time <=', $end_date);
        $this->db->group_by('result');
        return $this->db->get()->result_array();
    }

    public function get_lead_reports($start_date, $end_date)
    {
        $this->db->select('
            l.name AS lead_name,
            ls.name AS lead_source,
            lstatus.name AS lead_status,
            COUNT(*) AS total_calls
        ');
        $this->db->from('tblcall_logs AS c');
        $this->db->join('tblleads AS l', 'c.clientid = l.id', 'left');
        $this->db->join('tblleads_sources AS ls', 'l.source = ls.id', 'left');
        $this->db->join('tblleads_status AS lstatus', 'l.status = lstatus.id', 'left');
        $this->db->where('c.call_start_time >=', $start_date);
        $this->db->where('c.call_start_time <=', $end_date);
        $this->db->group_by('lead_name, lead_source, lead_status');
        return $this->db->get()->result_array();
    }

    public function get_successful_calls($start_date, $end_date)
    {
        // Successful calls: call_duration is not "0" and is_completed is "1"
        $this->db->where('call_duration !=', '0');
        $this->db->where('is_completed', 1);
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_end_time <=', $end_date);
        return $this->db->count_all_results('tblcall_logs');
    }

    public function get_unsuccessful_calls($start_date, $end_date)
    {
        // Unsuccessful calls: either call_duration is "0" or is_completed is not "1"
        $this->db->group_start()
            ->where('call_duration', '0')
            ->or_where('is_completed !=', 1)
            ->group_end();
        $this->db->where('call_start_time >=', $start_date);
        $this->db->where('call_end_time <=', $end_date);
        return $this->db->count_all_results('tblcall_logs');
    }

    // public function get_total_calls($start_date, $end_date)
    // {
    //     $this->db->where('date >=', $start_date);
    //     $this->db->where('date <=', $end_date);
    //     return $this->db->count_all_results('tblcall_logs');
    // }

    // public function get_call_results($start_date, $end_date)
    // {
    //     $this->db->select('source, SUM(interested) as interested, SUM(not_interested) as not_interested, SUM(not_eligible) as not_eligible, COUNT(*) as total');
    //     $this->db->where('date >=', $start_date);
    //     $this->db->where('date <=', $end_date);
    //     $this->db->group_by('source');
    //     return $this->db->get('tblcall_logs')->result_array();
    // }

    public function get_call_results($start_date, $end_date)
    {
        $this->db->select('tblleads_sources.name as source, 
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 THEN 1 ELSE 0 END) as interested,
                           SUM(CASE WHEN tblcall_logs.call_duration = "0" OR tblcall_logs.is_completed = 0 THEN 1 ELSE 0 END) as not_interested,
                           COUNT(*) as total');
        $this->db->from('tblcall_logs');
        $this->db->join('tblleads', 'tblleads.id = tblcall_logs.clientid', 'left');
        $this->db->join('tblleads_sources', 'tblleads.source = tblleads_sources.id', 'left');
        $this->db->where('tblcall_logs.dateadded >=', $start_date);
        $this->db->where('tblcall_logs.dateadded <=', $end_date);
        $this->db->group_by('tblleads_sources.name');
        return $this->db->get()->result_array();
    }



    public function get_cumulative_call_results()
    {
        $this->db->select('tblleads_sources.name as source, 
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 THEN 1 ELSE 0 END) as interested,
                           SUM(CASE WHEN tblcall_logs.call_duration = "0" OR tblcall_logs.is_completed = 0 THEN 1 ELSE 0 END) as not_interested,
                           COUNT(*) as total');
        $this->db->from('tblcall_logs');
        $this->db->join('tblleads', 'tblleads.id = tblcall_logs.clientid', 'left');
        $this->db->join('tblleads_sources', 'tblleads.source = tblleads_sources.id', 'left');
        $this->db->group_by('tblleads_sources.name');
        return $this->db->get()->result_array();
    }





    public function get_data_source_analysis($start_date, $end_date)
    {
        $this->db->select('tblleads_sources.name as source, 
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 THEN 1 ELSE 0 END) as interested,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "appointment" THEN 1 ELSE 0 END) as appointments,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "visit" THEN 1 ELSE 0 END) as visits,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "not_due" THEN 1 ELSE 0 END) as not_due,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "financial_capacity" THEN 1 ELSE 0 END) as financial_capacity,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "booking" THEN 1 ELSE 0 END) as bookings,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "fee_payment" THEN 1 ELSE 0 END) as fee_payment,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "contract" THEN 1 ELSE 0 END) as contracts');
        $this->db->from('tblcall_logs');
        $this->db->join('tblleads', 'tblcall_logs.clientid = tblleads.id', 'left');
        $this->db->join('tblleads_sources', 'tblleads.source = tblleads_sources.id', 'left');
        $this->db->where('tblcall_logs.dateadded >=', $start_date);
        $this->db->where('tblcall_logs.dateadded <=', $end_date);
        $this->db->group_by('tblleads_sources.name');
        return $this->db->get()->result_array();
    }

    public function get_cumulative_data_source_analysis()
    {
        $this->db->select('tblleads_sources.name as source, 
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 THEN 1 ELSE 0 END) as interested,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "appointment" THEN 1 ELSE 0 END) as appointments,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "visit" THEN 1 ELSE 0 END) as visits,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "not_due" THEN 1 ELSE 0 END) as not_due,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "financial_capacity" THEN 1 ELSE 0 END) as financial_capacity,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "booking" THEN 1 ELSE 0 END) as bookings,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "fee_payment" THEN 1 ELSE 0 END) as fee_payment,
                           SUM(CASE WHEN tblcall_logs.call_duration != "0" AND tblcall_logs.is_completed = 1 AND tblcall_logs.call_purpose = "contract" THEN 1 ELSE 0 END) as contracts');
        $this->db->from('tblcall_logs');
        $this->db->join('tblleads', 'tblcall_logs.clientid = tblleads.id', 'left');
        $this->db->join('tblleads_sources', 'tblleads.source = tblleads_sources.id', 'left');
        $this->db->group_by('tblleads_sources.name');
        return $this->db->get()->result_array();
    }


    // Methods for cumulative data
    public function get_cumulative_successful_calls($start_date = null, $end_date = null)
    {
        $this->db->where('call_duration !=', '0');
        $this->db->where('is_completed', 1);
        if ($start_date && $end_date) {
            $this->db->where('call_start_time >=', $start_date);
            $this->db->where('call_end_time <=', $end_date);
        }
        return $this->db->count_all_results('tblcall_logs');
    }

    public function get_cumulative_unsuccessful_calls($start_date = null, $end_date = null)
    {
        $this->db->group_start()
            ->where('call_duration', '0')
            ->or_where('is_completed !=', 1)
            ->group_end();
        if ($start_date && $end_date) {
            $this->db->where('call_start_time >=', $start_date);
            $this->db->where('call_end_time <=', $end_date);
        }
        return $this->db->count_all_results('tblcall_logs');
    }

    public function get_cumulative_total_calls()
    {
        return $this->db->count_all_results('tblcall_logs');
    }

    // public function get_cumulative_call_results()
    // {
    //     $this->db->select('call_direction, COUNT(*) as total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as successful_calls');
    //     $this->db->group_by('call_direction');
    //     return $this->db->get('tblcall_logs')->result_array();
    // }

    // public function get_cumulative_data_source_analysis()
    // {
    //     $this->db->select('call_direction, COUNT(*) as total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as successful_calls');
    //     $this->db->group_by('call_direction');
    //     return $this->db->get('tblcall_logs')->result_array();
    // }

}
