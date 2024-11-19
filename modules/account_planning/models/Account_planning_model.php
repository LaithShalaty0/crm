<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_planning_model extends App_Model
{
	public function __construct()
    {
        parent::__construct();
    }

     public function add($data)
    {
    	if(isset($data['financial'])){
	    	 $data['financial'] = explode ( ',', $data['financial']);
	    	 $financial_col = ['year','revenue','sales_spent','traffic','loss'];
	    	 $row = [];
	    	 $financial = [];
	    	 for ($i=0; $i < count($data['financial']); $i++) {
	    	 		$row[] = $data['financial'][$i];
	    	 	if((($i+1)%5) == 0){
	    	 		$financial[] = array_combine($financial_col, $row);
	    	 		$row = [];
	    	 	}
	    	 }
	    	unset($data['financial']);
    	}
        if(isset($data['marketing_activities'])){
             $data['marketing_activities'] = explode ( ',', $data['marketing_activities']);
             $marketing_activities_col = ['item','reference'];
             $row = [];
             $marketing_activities = [];
             for ($i=0; $i < count($data['marketing_activities']); $i++) {
                    $row[] = $data['marketing_activities'][$i];
                if((($i+1)%2) == 0){
                    $marketing_activities[] = array_combine($marketing_activities_col, $row);
                    $row = [];
                }
             }
            unset($data['marketing_activities']);
        }
    	if(isset($data['revenue'])){
        	unset($data['revenue']);
    	}
    	if(isset($data['sales_spent'])){
        	unset($data['sales_spent']);
    	}
    	if(isset($data['traffic'])){
        	unset($data['traffic']);
    	}
    	if(isset($data['loss'])){
        	unset($data['loss']);
    	}

        if(isset($data['date'])){
            $data['date'] = to_sql_date($data['date']);
        }
        
        $data['latest_update'] = date('Y-m-d H:i:s');
        $this->db->insert('tblaccount_planning', $data);

        $userid = $this->db->insert_id();
        if ($userid) {
        	if(isset($financial)){
        		foreach ($financial as $value) {
        			$value['account_planning_id'] = $userid;
        			$this->db->insert('tblaccount_planning_financial', $value);
        		}
        	}
            if(isset($marketing_activities)){
                foreach ($marketing_activities as $value) {
                    $value['account_planning_id'] = $userid;
                    $this->db->insert('tblaccount_planning_marketing_activities', $value);
                }
            }
            $log = 'ID: ' . $userid;

            if ($log == '' && isset($contact_id)) {
                $log = get_contact_full_name($contact_id);
            }

            $isStaff = null;
            if (!is_client_logged_in() && is_staff_logged_in()) {
                $log .= ', From Staff: ' . get_staff_user_id();
                $isStaff = get_staff_user_id();
            }

            log_activity('New Account planning Created [' . $log . ']', $isStaff);
        }

        return $userid;
    }

    public function delete($id)
    {

        $this->db->where('id', $id);
        $this->db->delete('tblaccount_planning');
        if ($this->db->affected_rows() > 0) {
            log_activity('Account Planning Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }
    public function update_due_diligence($id, $data)
    {
        if(isset($data['financial'])){
	    	 $data['financial'] = explode ( ',', $data['financial']);
	    	 $financial_col = ['year','revenue','traffic','sales_spent','loss'];
	    	 $row = [];
	    	 $financial = [];
	    	 for ($i=0; $i < count($data['financial']); $i++) {
	    	 		$row[] = $data['financial'][$i];
	    	 	if((($i+1)%5) == 0){
	    	 		$financial[] = array_combine($financial_col, $row);
	    	 		$row = [];
	    	 	}
	    	 }
	    	unset($data['financial']);
    	}
        if(isset($data['marketing_activities'])){
             $data['marketing_activities'] = explode ( ',', $data['marketing_activities']);
             $marketing_activities_col = ['item','reference'];
             $row = [];
             $marketing_activities = [];
             for ($i=0; $i < count($data['marketing_activities']); $i++) {
                    $row[] = $data['marketing_activities'][$i];
                if((($i+1)%2) == 0){
                    $marketing_activities[] = array_combine($marketing_activities_col, $row);
                    $row = [];
                }
             }
            unset($data['marketing_activities']);
        }
    	$data['new_update'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblaccount_planning', $data);

        $this->db->where('account_planning_id', $id);
        $this->db->delete('tblaccount_planning_financial');
    	if(isset($financial)){
    		foreach ($financial as $value) {
    			$value['account_planning_id'] = $id;
    			$this->db->insert('tblaccount_planning_financial', $value);
    		}
    	}

        $this->db->where('account_planning_id', $id);
        $this->db->delete('tblaccount_planning_marketing_activities');
        if(isset($marketing_activities)){
                foreach ($marketing_activities as $value) {
                    $value['account_planning_id'] = $id;
                    $this->db->insert('tblaccount_planning_marketing_activities', $value);
                }
            }
    	log_activity('Account Planning updated [ID: ' . $id . ']');
        return true;
    }
	public function update_team_information($id, $data)
    {
    	if(isset($data['DataTables_Table_0_length'])){
    		unset($data['DataTables_Table_0_length']);
    	}
    	if(isset($data['DataTables_Table_1_length'])){
    		unset($data['DataTables_Table_1_length']);
    	}
    	$this->db->where('account_planning_id', $id);
        $this->db->delete('tblaccount_planning_team');
        if(isset($data['client_team'])){
	    	foreach ($data['client_team'] as $value) {
	    	 	$this->db->insert('tblaccount_planning_team', [
	    	 		'account_planning_id' => $id,
	    	 		'rel_id' => $value,
	    	 		'rel_type' => 'client_team'
	    	 	]);
	    	 } 
    	}
    	if(isset($data['pmax_team'])){
	    	foreach ($data['pmax_team'] as $value) {
	    	 	$this->db->insert('tblaccount_planning_team', [
	    	 		'account_planning_id' => $id,
	    	 		'rel_id' => $value,
	    	 		'rel_type' => 'pmax_team'
	    	 	]);
	    	 } 
    	}

        $new_update['new_update'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblaccount_planning', $new_update);

    	log_activity('Account Planning updated [ID: ' . $id . ']');
        return true;
    }
    public function update_service_ability_offering($id, $data)
    {
        if(isset($data['service_ability_offering'])){
             $data['service_ability_offering'] = explode ( ',', $data['service_ability_offering']);
             $service_ability_offering_col = ['service','potential','scale','convert','prioritization'];
             $row = [];
             $service_ability_offering = [];
             for ($i=0; $i < count($data['service_ability_offering']); $i++) {
                    $row[] = $data['service_ability_offering'][$i];
                if((($i+1)%5) == 0){
                    $service_ability_offering[] = array_combine($service_ability_offering_col, $row);
                    $row = [];
                }
             }
            unset($data['service_ability_offering']);
        }

        if(isset($data['current_service'])){
             $data['current_service'] = explode ( ',', $data['current_service']);
             $current_service_col = ['name','potential'];
             $row = [];
             $current_service = [];
             for ($i=0; $i < count($data['current_service']); $i++) {
                    $row[] = $data['current_service'][$i];
                if((($i+1)%2) == 0){
                    $current_service[] = array_combine($current_service_col, $row);
                    $row = [];
                }
             }
            unset($data['current_service']);
        }

        $data['new_update'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblaccount_planning', $data);

        $this->db->where('account_planning_id', $id);
        $this->db->delete('tblaccount_planning_current_service');
        if(isset($current_service)){
            foreach ($current_service as $value) {
                $value['account_planning_id'] = $id;
                $this->db->insert('tblaccount_planning_current_service', $value);
            }
        }

        $this->db->where('account_planning_id', $id);
        $this->db->delete('tblaccount_planning_service_ability_offering');
        if(isset($service_ability_offering)){
            foreach ($service_ability_offering as $value) {
                $value['account_planning_id'] = $id;
                $this->db->insert('tblaccount_planning_service_ability_offering', $value);
            }
        }

        log_activity('Account Planning updated [ID: ' . $id . ']');
        return true;
    }

    public function update_planning($id, $data)
    {
        if(isset($data['todo_list'])){
             $data['todo_list'] = explode ( ',', $data['todo_list']);
             $todo_list_col = ['objective','items','action_needed','prioritization','pic','deadline','status','task_id','option'];
             $row = [];
             $todo_list = [];
             for ($i=0; $i < count($data['todo_list']); $i++) {
                    $row[] = $data['todo_list'][$i];
                if((($i+1)%9) == 0){
                    $todo_list[] = array_combine($todo_list_col, $row);
                    $row = [];
                }
             }
            unset($data['todo_list']);
        }
        if(isset($data['date'])){
            $data['date'] = to_sql_date($data['date']);
        }
        $data['data_tree']     = nl2br($data['data_tree']);
        $data['objectives']     = nl2br($data['objectives']);
        $data['threat']     = nl2br($data['threat']);
        $data['opportunity']     = nl2br($data['opportunity']);
        $data['criteria_to_success']     = nl2br($data['criteria_to_success']);
        $data['constraints']     = nl2br($data['constraints']);

        $data['revenue_next_year'] = reformat_currency($data['revenue_next_year']);
        if(isset($data['DataTables_Table_0_length'])){
            unset($data['DataTables_Table_0_length']);
        }
        $data['new_update'] = date('Y-m-d H:i:s');

        foreach ($todo_list as $key => $value) {
            if($value['option'] != ''){
                $data_row = [];
                $data_row['action_needed'] = $value['action_needed'];
                $data_row['prioritization'] = $value['prioritization'];
                $data_row['pic'] = $value['pic'];
                $data_row['deadline'] = to_sql_date($value['deadline']);
                $data_row['status'] = $value['status'];
                $data_row['item'] = $value['items'];
                $data_row['objective'] = $value['objective'];
                $this->db->where('id',$value['task_id']);
                $this->db->update('tblaccount_planning_task', $data_row);
            }else{
                $data_row = [];
                $data_row['items_id'] = 0;
                $data_row['account_planning_id'] = $id;
                $data_row['item'] = $value['items'];
                $data_row['objective'] = $value['objective'];
                $data_row['action_needed'] = $value['action_needed'];
                $data_row['prioritization'] = $value['prioritization'];
                $data_row['pic'] = $value['pic'];
                $data_row['deadline'] = to_sql_date($value['deadline']);
                $data_row['status'] = $value['status'];
                $this->db->insert('tblaccount_planning_task', $data_row);
            }

            handle_account_planning($id);
        }

        $this->db->where('id', $id);
        $this->db->update('tblaccount_planning', $data);

        log_activity('Account Planning updated [ID: ' . $id . ']');
        return true;

    }

    public function get($id = '', $where = [])
    {
        $this->db->select('*, (select company from tblclients where tblclients.userid = client_id) as company, (select company from tblclients where tblclients.userid = client_id) as client_name');


        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tblaccount_planning.id', $id);
            $account = $this->db->get('tblaccount_planning')->row();
            $account->attachments = $this->get_attachments($id);
            return $account;

        }
        return $this->db->get('tblaccount_planning')->result_array();
    }

    public function get_financial($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_financial')->result_array();
        }
        return $this->db->get('tblaccount_planning_financial')->result_array();
    }

    public function get_service_ability_offering($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_service_ability_offering')->result_array();
        }
        return $this->db->get('tblaccount_planning_service_ability_offering')->result_array();
    }
    public function get_current_service($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_current_service')->result_array();
        }
        return $this->db->get('tblaccount_planning_current_service')->result_array();
    }
    public function get_marketing_activities($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_marketing_activities')->result_array();
        }
        return $this->db->get('tblaccount_planning_marketing_activities')->result_array();
    }

    public function get_pmax_team($id = '')
    {
        $this->db->select('tblaccount_planning_team.id,rel_id,tblstaff.email,tblstaff.firstname,tblstaff.lastname,tblstaff.facebook,tblstaff.phonenumber,tblstaff.skype,tblstaff.linkedin,tblstaff.profile_image, tbldepartments.name as department, (select name from tblroles where tblroles.roleid = role) as title');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblaccount_planning_team.rel_id', 'left');
        $this->db->join('tblstaff_departments', 'tblstaff_departments.staffid = tblaccount_planning_team.rel_id', 'left');
        $this->db->join('tbldepartments','tbldepartments.departmentid = tblstaff_departments.departmentid', 'left');
        $this->db->where('rel_type', 'pmax_team');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_team')->result_array();
        }
        return $this->db->get('tblaccount_planning_team')->result_array();
    }
    public function get_client_team($id = '')
    {
        $this->db->select('*');
        $this->db->join('tblcontacts', 'tblcontacts.id = tblaccount_planning_team.rel_id', 'left');
        
        $this->db->where('rel_type', 'client_team');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_team')->result_array();
        }
        return $this->db->get('tblaccount_planning_team')->result_array();
    }

    public function get_objectives($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_objective')->result_array();
        }
        return $this->db->get('tblaccount_planning_objective')->result_array();
    }

    public function get_items($id = '')
    {
        $this->db->select('*');
        if (is_numeric($id)) {
            $this->db->where('account_planning_id', $id);
            return $this->db->get('tblaccount_planning_items')->result_array();
        }
        return $this->db->get('tblaccount_planning_items')->result_array();
    }

    public function add_objective($id ,$data)
    {
        if($data['objective_name'] != ''){
            $data['account_planning_id'] = $id;
            $data['name'] = $data['objective_name'];
            unset($data['objective_name']);
            $data['datecreated'] = date('Y-m-d H:i:s');
            $this->db->insert('tblaccount_planning_objective', $data);

            log_activity('Account Planning updated [ID: ' . $id . ']');
            return true;
        }else {
            return false; 
        }
    }
    public function add_item($id ,$data)
    {
        if($data['items_name'] != '' && $data['objective'] != ''){
            $data['account_planning_id'] = $id;
            $data['objective_id'] = $data['objective'];
            unset($data['objective']);
            $data['name'] = $data['items_name'];
            unset($data['items_name']);
            $data['datecreated'] = date('Y-m-d H:i:s');
            $this->db->insert('tblaccount_planning_items', $data);

            return true;
        }else{
            return false;
        }
    }
    public function add_task($id ,$data)
    {
        if($data['task_name'] != '' && $data['items_id'] != ''){
            $data['action_needed'] =  $data['task_name'];
            $data['deadline'] = to_sql_date($data['deadline']);
            unset($data['task_name']);

            $this->db->insert('tblaccount_planning_task', $data);
            return true;
        }else{
            return false;
        }
    }

    public function update_objective($id ,$data)
    {
        if($data['objective_name'] != ''){
            $data['name'] = $data['objective_name'];
            unset($data['objective_name']);
            $this->db->where('id', $id);
            $this->db->update('tblaccount_planning_objective', $data);

            log_activity('Objective updated [ID: ' . $id . ']');
            return true;
        }else {
            return false; 
        }
    }
    public function update_item($id ,$data)
    {
        if($data['items_name'] != '' && $data['objective'] != ''){
            $data['objective_id'] = $data['objective'];
            unset($data['objective']);
            $data['name'] = $data['items_name'];
            unset($data['items_name']);

            $this->db->where('id', $id);
            $this->db->update('tblaccount_planning_items', $data);

            return true;
        }else{
            return false;
        }
    }
    public function update_task($id ,$data)
    {
        if($data['task_name'] != '' && $data['items_id'] != ''){
            $data['action_needed'] =  $data['task_name'];
            $data['deadline'] = to_sql_date($data['deadline']);
            unset($data['task_name']);

            $this->db->where('id', $id);
            $this->db->update('tblaccount_planning_task', $data);
            return true;
        }else{
            return false;
        }
    }

    public function delete_objective($id = '', $account_planning_id = '')
    {
        if($id != '' || $account_planning_id != ''){
            if($account_planning_id != ''){
                $this->db->where('account_planning_id', $account_planning_id);
            }
            if($id != ''){
                $this->db->where('id', $id);
            }
            $this->db->delete('tblaccount_planning_objective');

            $this->delete_item('', $id);
            return true;
        }else{
            return false;
        }
    }
    public function delete_item($id = '', $objective_id = '')
    {
        if($id != '' || $objective_id != ''){
            if($objective_id != ''){
                $this->db->where('objective_id', $objective_id);
            }
            if($id != ''){
                $this->db->where('id', $id);
            }
            $this->db->delete('tblaccount_planning_items');

            $this->delete_task('', $id);

            return true;
        }else{
            return false;
        }

    }

    public function delete_task($id = '', $items_id = '')
    {   if($id != '' || $items_id != ''){
            if($items_id != ''){
                $this->db->where('items_id', $items_id);
            }
            if($id != ''){
                $this->db->where('id', $id);
            }
            $this->db->delete('tblaccount_planning_task');
                return true;
        }else{
            return false;
        }
    }
    public function get_todo_list($id)
    {


        $this->db->where('account_planning_id', $id);
        $todo_list = $this->db->get('tblaccount_planning_task')->result_array();
        if(count($todo_list) > 0){
            foreach ($todo_list as $key => $value) {
                $todo_list[$key]['deadline'] = _d($value['deadline']);
                if($value['convert_to_task'] == 0){
                    if (has_permission('account_planning', '', 'edit')) {
                    $todo_list[$key]['button'] =/*'<a href="#" class="btn btn-default btn-xs" onclick="convert_to_task('.$id.', '.$value['id'].'); return false;">'. _l('convert_to_task') .'</a> '*/icon_btn('#', 'external-link', 'btn-success', ['data-subject' => $value['item'],'data-description' => $value['action_needed'],'data-priority' => $value['prioritization'],'data-deadline' => _d($value['deadline']), 'data-pic' => $value['pic'], "data-toggle"=>"tooltip", "title"=>_l('convert_to_task'), "onclick" => "convert_to_task(this,".$id.", ".$value['id']."); return false;"]).' '.icon_btn('account_planning/delete_task/' . $value['id'].'/'.$id, 'remove', 'btn-danger _delete', ["data-toggle"=>"tooltip", "title"=>_l('delete_task')]);
                    }
                }else{
                    if (has_permission('account_planning', '', 'edit')) {
                    $todo_list[$key]['button'] =/*'<a href="'.admin_url('tasks/view/'.$value['convert_to_task']).'" onclick="init_task_modal(\''.$value['convert_to_task'].'\'); return false;" class="btn btn-default btn-xs">'. _l('view_task') .'</a> '*/icon_btn('#', 'eye', 'btn-success', ["data-toggle"=>"tooltip", "title"=>_l('view_task'), "onclick" => "init_task_modal('".$value['convert_to_task']."'); return false;"]).' '.icon_btn('account_planning/delete_task/' . $value['id'].'/'.$id, 'remove', 'btn-danger _delete', ["data-toggle"=>"tooltip", "title"=>_l('delete_task')]);
                    }
                }
            }
            return $todo_list;
        }else{
            $data_todo_list = [];
            $data_todo_list[0]['objective'] = '';
            $data_todo_list[0]['item'] = '';
            $data_todo_list[0]['action_needed'] = '';
            $data_todo_list[0]['prioritization'] = '';
            $data_todo_list[0]['pic'] = '';
            $data_todo_list[0]['deadline'] = '';
            $data_todo_list[0]['status'] = '';
            $data_todo_list[0]['id'] = '';
            $data_todo_list[0]['button'] = '';
            return $data_todo_list;
        }
    }

    function get_account_planning_tabs($id)
    {
        $account_planning_tabs = [
          [
            'name'    => 'due_diligence',
            'url'     => admin_url('account_planning/view/' . $id . '?group=due_diligence'),
            'icon'    => 'fa fa-user-circle',
            'lang'    => _l('due_diligence'),
            'visible' => true,
            'order'   => 1,
        ],
        [
            'name'    => 'team_information',
            'url'     => admin_url('account_planning/view/' . $id . '?group=team_information'),
            'icon'    => 'fa fa-users',
            'lang'    => _l('team_information'),
            'visible' => true,
            'order'   => 2,
        ],
          [
            'name'    => 'service_ability_offering',
            'url'     => admin_url('account_planning/view/' . $id . '?group=service_ability_offering'),
            'icon'    => 'fa fa-sticky-note-o',
            'lang'    => _l('service_ability_offering'),
            'visible' => true,
            'order'   => 3,
        ],
          [
            'name'    => 'planning',
            'url'     => admin_url('account_planning/view/' . $id . '?group=planning'),
            'icon'    => 'fa fa-area-chart',
            'lang'    => _l('planning'),
            'visible' => true,
            'order'   => 4,
        ]
      ];
        return $account_planning_tabs;
    }
    public function copy($account_planning_id, $data)
    {

        $account   = $this->get($account_planning_id);
        $_new_data = [];
        foreach ($account as $key => $value) {
           $_new_data[$key] = $value;
        }
        if(isset($_new_data['attachments'])){
            $attachments = $_new_data['attachments'];
            unset($_new_data['attachments']);
        }
        unset($_new_data['id']);
        unset($_new_data['company']);
        unset($_new_data['client_name']);
        $_new_data['client_id'] = $data['client_id'];
        $_new_data['date'] = to_sql_date($data['date']);
        $_new_data['subject'] = $data['subject'];
        $_new_data['latest_update'] = date('Y-m-d H:i:s');

        $this->db->insert('tblaccount_planning', $_new_data);
        $id = $this->db->insert_id();
        if ($id) {
            $financial = $this->get_financial($account_planning_id);
            if(isset($financial)){
                foreach ($financial as $value) {
                    unset($value['id']);
                    $value['account_planning_id'] = $id;
                    $this->db->insert('tblaccount_planning_financial', $value);
                }
            }
            $marketing_activities = $this->get_marketing_activities($account_planning_id);
            if(isset($marketing_activities)){
                foreach ($marketing_activities as $value) {
                    unset($value['id']);
                    $value['account_planning_id'] = $id;
                    $this->db->insert('tblaccount_planning_marketing_activities', $value);
                }
            }

            $client_team = $this->get_client_team($account_planning_id);
            if(isset($client_team)){
                foreach ($client_team as $value) {
                    $row = [];
                    $row['account_planning_id'] = $id;
                    $row['rel_id'] = $value['rel_id'];
                    $row['rel_type'] = 'client_team';
                    $this->db->insert('tblaccount_planning_team', $row);
                }
            }
            $pmax_team = $this->get_pmax_team($account_planning_id);
            if(isset($pmax_team)){
                foreach ($pmax_team as $value) {
                    $row = [];
                    $row['account_planning_id'] = $id;
                    $row['rel_id'] = $value['rel_id'];
                    $row['rel_type'] = 'pmax_team';
                    $this->db->insert('tblaccount_planning_team', $row);
                }
            }
            $todo_list = $this->get_todo_list($account_planning_id);
            if(isset($todo_list)){

                foreach ($todo_list as $key => $value) {
                    unset($value['id']);
                    $value['account_planning_id'] = $id;

                    /*unset($value['objective_id']);
                    unset($value['objective']);
                    unset($value['items']);*/
                    unset($value['deadline']);
                    unset($value['button']);
                    unset($value['convert_to_task']);

                    $this->db->insert('tblaccount_planning_task', $value);

                }
            }
            $service_ability_offering = $this->get_service_ability_offering($account_planning_id);
            if(isset($service_ability_offering)){
                foreach ($service_ability_offering as $value) {
                    unset($value['id']);
                    $value['account_planning_id'] = $id;
                    $this->db->insert('tblaccount_planning_service_ability_offering', $value);
                }
            }

            $current_service = $this->get_current_service($account_planning_id);
            if(isset($current_service)){
                foreach ($current_service as $value) {
                    unset($value['id']);
                    $value['account_planning_id'] = $id;
                    $this->db->insert('tblaccount_planning_current_service', $value);
                }
            }

            if(isset($attachments)){
                if (is_dir(get_upload_path_by_type('account_planning') . $account_planning_id)) {
                    xcopy(get_upload_path_by_type('account_planning') . $account_planning_id, get_upload_path_by_type('account_planning') . $id);
                }
                foreach ($attachments as $at) {
                    $at['rel_id'] = $id;
                    $at['dateadded'] = date('Y-m-d H:i:s');
                    $at['attachment_key'] = app_generate_hash();
                    unset($at['id']);
                    $this->db->insert('tblfiles', $at);
                }
            }

            return $id;
        }

        return false;
    }

    public function get_attachments($account_planning_id, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $account_planning_id);
        }
        $this->db->where('rel_type', 'account_planning');
        $result = $this->db->get('tblfiles');
        if (is_numeric($id)) {
            return $result->row();
        }
        return $result->result_array();
    }

    public function get_month()
    {
        $date = getdate();
        $date_1 = mktime(0, 0, 0, ($date['mon'] - 5), 1, $date['year']);
        $date_2 = mktime(0, 0, 0, ($date['mon'] - 4), 1, $date['year']);
        $date_3 = mktime(0, 0, 0, ($date['mon'] - 3), 1, $date['year']);
        $date_4 = mktime(0, 0, 0, ($date['mon'] - 2), 1, $date['year']);
        $date_5 = mktime(0, 0, 0, ($date['mon'] - 1), 1, $date['year']);
        $date_6 = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
        $date_7 = mktime(0, 0, 0, ($date['mon'] + 1), 1, $date['year']);
        $date_8 = mktime(0, 0, 0, ($date['mon'] + 2), 1, $date['year']);
        $date_9 = mktime(0, 0, 0, ($date['mon'] + 3), 1, $date['year']);
        $date_10 = mktime(0, 0, 0, ($date['mon'] + 4), 1, $date['year']);
        $date_11 = mktime(0, 0, 0, ($date['mon'] + 5), 1, $date['year']);
        $date_12 = mktime(0, 0, 0, ($date['mon'] + 6), 1, $date['year']);
        $month = [ '1' => ['id' => date('Y-m-d', $date_1), 'name' => date('F - Y', $date_1)],
                    '2' => ['id' => date('Y-m-d', $date_2), 'name' => date('F - Y', $date_2)],
                    '3' => ['id' => date('Y-m-d', $date_3), 'name' => date('F - Y', $date_3)],
                    '4' => ['id' => date('Y-m-d', $date_4), 'name' => date('F - Y', $date_4)],
                    '5' => ['id' => date('Y-m-d', $date_5), 'name' => date('F - Y', $date_5)],
                    '6' => ['id' => date('Y-m-d', $date_6), 'name' => date('F - Y', $date_6)],
                    '7' => ['id' => date('Y-m-d', $date_7), 'name' => date('F - Y', $date_7)],
                    '8' => ['id' => date('Y-m-d', $date_8), 'name' => date('F - Y', $date_8)],
                    '9' => ['id' => date('Y-m-d', $date_9), 'name' => date('F - Y', $date_9)],
                    '10' => ['id' => date('Y-m-d', $date_10), 'name' => date('F - Y', $date_10)],
                    '11' => ['id' => date('Y-m-d', $date_11), 'name' => date('F - Y', $date_11)],
                    '12' => ['id' => date('Y-m-d', $date_12), 'name' => date('F - Y', $date_12)],
            ];

        return $month;
    }

    /**
     *  Delete invoice attachment
     * @since  Version 1.0.4
     * @param   mixed $id  attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('account_planning') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Account Planning Attachment Deleted [Account Planning ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('account_planning') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('account_planning') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('account_planning') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }


    public function get_file($id, $rel_id = false)
    {
        $this->db->where('id', $id);
        $file = $this->db->get('tblfiles')->row();

        if ($file && $rel_id) {
            if ($file->rel_id != $rel_id) {
                return false;
            }
        }
        return $file;
    }
    public function get_pic_todolist($staffid = '')
    {
        $staff = $this->staff_model->get();
        $list_staff = [];
        foreach ($staff as $key => $value) {
            $note = [];
            $note['id'] = $value['staffid'];
            $note['label'] = trim($value['staffid'].' - '.$value['full_name']);
            $list_staff[] = $note;
        }
        return $list_staff;
    }

    public function _search_account_planning($q, $limit = 0)
    {
        $result = [
            'result'         => [],
            'type'           => 'account_planning',
            'search_heading' => _l('account_planning'),
        ];
		
		// Uncomment if you wish to restrict usage: if (has_permission('staff', '', 'view')) {
            // Staff
            $this->db->select();
            $this->db->from('tblaccount_planning');
            $this->db->join('tblclients', 'tblclients.userid = tblaccount_planning.client_id');
            $this->db->like('company', $q);
            $this->db->or_like('id', $q);
            $this->db->or_like('subject', $q);
            if ($limit != 0) {
                $this->db->limit($limit);
            }
            $this->db->order_by('subject', 'ASC');
            $result['result'] = $this->db->get()->result_array();
        // End of possible uncommenting: }
           
        return $result;
    }
}
