<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Si_lead_filters extends AdminController 
{
	public function __construct()
	{
		parent::__construct(); 
		$this->load->model('si_lead_filter_model');
		$this->load->model('leads_model');
		if (!is_admin() && !has_permission('si_lead_filters', '', 'view')) {
			access_denied(_l('si_lead_filters'));
		}
	}
	
	private function get_where_report_period($field = 'date',$months_report='this_month')
	{
		$custom_date_select = '';
		if ($months_report != '') {
			if (is_numeric($months_report)) {
				// Last month
				if ($months_report == '1') {
					$beginMonth = date('Y-m-01', strtotime('first day of last month'));
					$endMonth   = date('Y-m-t', strtotime('last day of last month'));
				} else {
					$months_report = (int) $months_report;
					$months_report--;
					$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
					$endMonth   = date('Y-m-t');
				}

				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
			} elseif ($months_report == 'this_month') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date('Y-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = 'AND (' . $field . ' BETWEEN "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date($this->input->post('report_from'));
				$to_date   = to_sql_date($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
				} else {
					$custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
				}
			}
		}
		
		 return $custom_date_select;
	}
	
	public function leads_filter()
	{
		$overview = [];
		
		$saved_filter_name='';
		$filter_id = $this->input->get('filter_id');
		if($filter_id!='' && is_numeric($filter_id) && empty($this->input->post()))
		{
			$filter_obj = $this->si_lead_filter_model->get($filter_id);
			if(!empty($filter_obj))
			{
				$_POST = unserialize($filter_obj->filter_parameters);
				$saved_filter_name = $filter_obj->filter_name;
			}	
		}	

		$has_permission_view   = has_permission('leads', '', 'view');

		if (!$has_permission_view) {
			$staff_id = get_staff_user_id();
		} elseif ($this->input->post('member')) {
			$staff_id = $this->input->post('member');
		} else {
			$staff_id = '';
		}
		$status = $this->input->post('status');
		if(empty($status))
			$status=array('');
		$source = $this->input->post('source');
		if(empty($source))
			$source=array('');	
		$tag = $this->input->post('tags');
		if(empty($tag))
			$tag=array('');
		$country = $this->input->post('countries');
		if(empty($country))
			$country=array('');	
		
		$type = $this->input->post('type');	
				
		$hide_columns = $this->input->post('hide_columns');
		if(empty($hide_columns))
			$hide_columns=array();
			
		if ($this->input->post('date_by')) {
			$date_by = $this->input->post('date_by');
		} else {
			$date_by = 'dateadded';
		}
		
		$fetch_month_from = $date_by;
		
		if ($this->input->post('report_months')!='')
			$report_months = $this->input->post('report_months');
		elseif($this->input->post('report_months')=='' && $filter_id=='' && $this->input->server('REQUEST_METHOD') !== 'POST')
			$report_months = 'this_month';//by default when loaded
		else
			$report_months = '';
		
		$save_filter = $this->input->post('save_filter');
		$filter_name='';
		$current_user_id = get_staff_user_id();
		if($save_filter==1)
		{
			$filter_name=$this->input->post('filter_name');
			$all_filter = $this->input->post();
			unset($all_filter['save_filter']);
			unset($all_filter['filter_name']);
			$saved_filter_name = $filter_name;
			$filter_parameters = serialize($all_filter);
			$filter_data = array('filter_name'=>$filter_name,
								 'filter_parameters'=>$filter_parameters,
								 'staff_id'=>$current_user_id);
			if($filter_id!='' && is_numeric($filter_id))
				$this->si_lead_filter_model->update($filter_data,$filter_id);
			else					 
				$new_filter_id = $this->si_lead_filter_model->add($filter_data);
		}
		
		//get query Leads
		$sqlLeadsSelect = db_prefix().'leads.*,CONCAT('.db_prefix().'staff.firstname," ",'.db_prefix().'staff.lastname) as staff_name,'.db_prefix().'leads_sources.name as source_name';
		
		$this->db->select($sqlLeadsSelect);
		
		$this->db->join(db_prefix().'staff',db_prefix() . 'staff.staffid = ' . db_prefix() . 'leads.assigned','left');
		$this->db->join(db_prefix() . 'leads_sources' , db_prefix() . 'leads_sources.id = ' . db_prefix() . 'leads.source','left');
		
		if($report_months!=''){
			$custom_date_select = $this->get_where_report_period('DATE('.$fetch_month_from.')',$report_months);
			$this->db->where("1=1 ".$custom_date_select);
		}
		
		if(!$has_permission_view){
			$this->db->where('(assigned =' . $staff_id . ' OR addedfrom = ' . $staff_id . ' OR is_public = 1)');
		}
		elseif ($has_permission_view) {
			if (is_numeric($staff_id)) {
				$this->db->where('assigned',$staff_id);
			}
		}
		
		if ($status && !in_array('',$status)) {
			$this->db->where_in('status', $status);
		}
		
		if ($source && !in_array('',$source)) {
			$this->db->where_in('source', $source);
		}
		
		if ($tag && !in_array('',$tag)) {
			$this->db->join(db_prefix() . 'taggables' , '('.db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'leads.id and rel_type=\'lead\')','left');
			$this->db->where_in('tag_id', $tag);
			$this->db->group_by(db_prefix() . 'leads.id');
		}
		if ($country && !in_array('',$country)) {
			if(in_array(-1,$country))//if country is unknown
				$country[]=0;
			$this->db->where_in('country', $country);
		}
		if($type!='')
		{
			if($type=='lost')
				$this->db->where('lost',1);
			if($type=='junk')
				$this->db->where('junk',1);
			if($type=='public')
				$this->db->where('is_public',1);
			if($type=='not_assigned')
				$this->db->where('assigned',0);			
		}

		$this->db->order_by($fetch_month_from, 'DESC');
		$overview[''] = $this->db->get(db_prefix() . 'leads')->result_array();
		
		$data['title']    = _l('si_lf_submenu_lead_filters');
		$data['lead_statuses'] = $this->leads_model->get_status();
		$data['lead_sources']  = $this->leads_model->get_source();
		$data['lead_countries']  = $this->si_lead_filter_model->get_leads_country_list();
		$data['members']  = $this->staff_model->get();
		$data['staff_id'] = $staff_id;
		$data['saved_filter_name'] = $saved_filter_name;
		$data['date_by'] = $date_by;
		$data['statuses']  =$status;
		$data['sources']  =$source;
		$data['tags']  =$tag;
		$data['countries']  =$country;
		$data['type']=$type;
		$data['report_months'] = $report_months;
		$data['report_from'] = $this->input->post('report_from');
		$data['report_to'] = $this->input->post('report_to');
		$data['hide_columns'] = $hide_columns;
		$data['filter_templates'] = $this->si_lead_filter_model->get_templates($current_user_id);
		$data['overview'] = $overview;
		
		$this->load->view('lead_report', $data);
	}
	
	function list_filters()
	{
		$data=array();
		$data['title']    = _l('si_lf_submenu_filter_templates');
		$current_user_id = get_staff_user_id();
		$data['filter_templates'] = $this->si_lead_filter_model->get_templates($current_user_id);
		$this->load->view('lead_list_filters', $data);
	}
	function del_lead_filter($id)
	{
		$current_user_id = get_staff_user_id();
		$this->si_lead_filter_model->delete($id,$current_user_id);
		redirect('si_lead_filters/list_filters');
	}
}
