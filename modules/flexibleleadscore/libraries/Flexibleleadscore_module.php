<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flexibleleadscore_module
{
    private $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
    }

   public function update_old_records()
    {
        $this->ci->load->model('flexibleleadscore/flexibleleadscore_leadscore_model');
        $this->ci->load->model('leads_model');
        //delete all the old records of leads score
        $this->ci->flexibleleadscore_leadscore_model->delete_all();
        //get all the leads
        $this->ci->load->model('flexibleleadscore/flexleads_model');
        $leads = $this->ci->flexleads_model->all();
        //get all the criteria
        $criterias = flexiblels_get_criteria();
        //loop through all the leads and calculate score based on criteria
        foreach ($leads as $lead) {
            $score = $this->calculate_score($lead,$criterias);
            $this->ci->flexibleleadscore_leadscore_model->add([
                'lead_id' => $lead['id'],
                'score' => $score,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function update_single_lead_score($lead_id){
        $this->ci->load->model('flexibleleadscore/flexibleleadscore_leadscore_model');
        $this->ci->load->model('flexibleleadscore/flexleads_model');
        //get all the criteria
        $criterias = flexiblels_get_criteria();
        //get the lead
        $lead = $this->ci->flexleads_model->get(['id'=>$lead_id]);
        //calculate score
        $score = $this->calculate_score($lead,$criterias);
        //delete the old score
        $this->ci->flexibleleadscore_leadscore_model->delete_where([
            'lead_id' => $lead_id
        ]);
        //add the new score
        $this->ci->flexibleleadscore_leadscore_model->add([
            'lead_id' => $lead_id,
            'score' => $score,
            'date_added' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s')
        ]);
    }

    private function calculate_score($lead, $criterias)
    {
        $score = 0;
        foreach ($criterias as $criteria) {
            $score += $this->calculate_score_based_on_criteria($lead, $criteria);
        }
        return $score;
    }

    public function calculate_score_based_on_criteria($lead, $criteria)
    {
        $operator = $criteria['flexibleleadscore_criteria_operator'];
        $criteria_value = @unserialize($criteria['flexibleleadscore_criteria_value']);
        $source = $criteria['flexibleleadscore_criteria'];
        $action = $criteria['flexibleleadscore_add_substract'];
        $points = $criteria['flexibleleadscore_points'];
        $score = 0;
        switch ($source) {
            case 'lead-source':
                //let us compare the lead source with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['source'],$criteria_value);
                break;

            case 'lead-status':
                //let us compare the lead status with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['status'],$criteria_value);
                break;

            case 'lead-form':
                //let us compare the lead form with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['form_id'],$criteria_value);
                break;
            case 'name':
                //let us compare the lead name with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['name'],$criteria_value);
                break;
            case 'email':
                //let us compare the lead email with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['email'],$criteria_value);
                break;
            case 'title':
                //let us compare the lead title with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['title'],$criteria_value);
                break;

            case 'company':
                //let us compare the lead company with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['company'],$criteria_value);
                break;
            case 'phonenumber':
                //let us compare the lead phonenumber with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['phonenumber'],$criteria_value);
                break;
            case 'address':
                //let us compare the lead address with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['address'],$criteria_value);
                break;
            case 'city':
                //let us compare the lead city with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['city'],$criteria_value);
                break;
            case 'state':
                //let us compare the lead state with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['state'],$criteria_value);
                break;
            case 'country':
                //let us compare the lead country with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['country'],$criteria_value);
                break;
            case 'zip':
                //let us compare the lead zip with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['zip'],$criteria_value);
                break;
            case 'description':
                //let us compare the lead description with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['description'],$criteria_value);
                break;
            case 'lead-value':
                //let us compare the lead value with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['lead_value'],$criteria_value);
                break;

            case 'website':
                //let us compare the lead website with the criteria value based on the operator
                $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$lead['website'],$criteria_value);
                break;
            default:
                //this is custom fields
                if(is_numeric($source)){
                    //custom fields are numeric
                    $custom_field   = get_custom_fields('leads', 'id ='.$source);
                    if($custom_field){
                        $custom_field_value = get_custom_field_value($lead['id'],$source,'leads'); //this could be an array
                        if($custom_field[0]['type'] =="multiselect"){
                            //this is probably an array, split the value to be an array
                            $custom_field_value = explode(',',$custom_field_value);
                        }
                        //$criteria_value = is_array($criteria_value) ? implode(',')$criteria_value[0] : $criteria_value;
                        //criteria value could be an array and custom field value is already comma separated, how do we compare?
                        $score = $this->calculate_score_based_on_action_and_operator($operator,$action,$score,$points,$custom_field_value,$criteria_value);
                    }
                }
                break;
        }
        return $score;
    }

    private function calculate_score_based_on_action_and_operator($operator, $action, $score, $points, $lead_value, $criteria_value)
    {
        if(is_array($lead_value)){
            sort($lead_value);
            sort($criteria_value);
            $lead_value = array_map('trim', $lead_value);
            $criteria_value = array_map('trim', $criteria_value);
        }else{
            $lead_value = strtolower(trim($lead_value));
            $criteria_value = strtolower(trim($criteria_value));
        }
        switch ($operator) {
            case FLEXIBLELEADSCORE_IS_CRITERIA_OPERATOR:
            case FLEXIBLELEADSCORE_EQUAL_TO_CRITERIA_OPERATOR:
                //if the lead value is an array
                if($lead_value == $criteria_value){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;
            case FLEXIBLELEADSCORE_IS_NOT_CRITERIA_OPERATOR:
            case FLEXIBLELEADSCORE_NOT_EQUAL_TO_CRITERIA_OPERATOR:
                if($lead_value != $criteria_value){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;

            case FLEXIBLELEADSCORE_GREATER_THAN_CRITERIA_OPERATOR:
                if(is_array($lead_value) && is_array($criteria_value)) {
                    if (count($lead_value) > count($criteria_value)) {
                        if ($action == 'add') {
                            $score += $points;
                        } else {
                            $score -= $points;
                        }
                    }
                }else{
                    if($lead_value > $criteria_value){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_LESS_THAN_CRITERIA_OPERATOR:
                if(is_array($lead_value) && is_array($criteria_value)) {
                    if (count($lead_value) < count($criteria_value)) {
                        if ($action == 'add') {
                            $score += $points;
                        } else {
                            $score -= $points;
                        }
                    }
                }else{
                    if($lead_value < $criteria_value){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_GREATER_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR:
                if(is_array($lead_value) && is_array($criteria_value)) {
                    if (count($lead_value) >= count($criteria_value)) {
                        if ($action == 'add') {
                            $score += $points;
                        } else {
                            $score -= $points;
                        }
                    }
                }else{
                    if($lead_value >= $criteria_value){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_LESS_THAN_OR_EQUAL_TO_CRITERIA_OPERATOR:
                if(is_array($lead_value) && is_array($criteria_value)) {
                    if (count($lead_value) <= count($criteria_value)) {
                        if ($action == 'add') {
                            $score += $points;
                        } else {
                            $score -= $points;
                        }
                    }
                }else {
                    if ($lead_value <= $criteria_value) {
                        if ($action == 'add') {
                            $score += $points;
                        } else {
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_CONTAINS_CRITERIA_OPERATOR:
                //if the lead value and criteria value is an array
                if(is_array($lead_value) && is_array($criteria_value)){
                    if(array_intersect($lead_value,$criteria_value)){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }else{
                    if(strpos($lead_value,$criteria_value) !== false){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_DOES_NOT_CONTAINS_CRITERIA_OPERATOR:
                //if the lead value and criteria value is an array
                if(is_array($lead_value) && is_array($criteria_value)){
                    if(!array_intersect($lead_value,$criteria_value)){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }else{
                    if(strpos($lead_value,$criteria_value) === false){
                        if($action == 'add'){
                            $score += $points;
                        }else{
                            $score -= $points;
                        }
                    }
                }
                break;

            case FLEXIBLELEADSCORE_STARTS_WITH_CRITERIA_OPERATOR:
                if(strpos($lead_value,$criteria_value) === 0){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;

            case FLEXIBLELEADSCORE_ENDS_WITH_CRITERIA_OPERATOR:
                if(substr($lead_value, -strlen($criteria_value)) === $criteria_value){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;

           case FLEXIBLELEADSCORE_IS_EMPTY_CRITERIA_OPERATOR:
                if(empty($lead_value)){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;
           case FLEXIBLELEADSCORE_IS_NOT_EMPTY_CRITERIA_OPERATOR:
                if(!empty($lead_value)){
                    if($action == 'add'){
                        $score += $points;
                    }else{
                        $score -= $points;
                    }
                }
                break;

            default:
                break;
        }
        return $score;
    }


}
