<?php

use function GuzzleHttp\json_decode;

defined('BASEPATH') or exit('No direct script access allowed');
set_time_limit(0);

class Facebook_leads_integration extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }
    //zapier integration
    public function zapier()
    {
        file_put_contents('zapier.json', file_get_contents('php://input'));
    }

    // Webhook callback url 
    public function webhook()
    {
        file_put_contents('test.json', file_get_contents('php://input'));
        if (isset($_REQUEST['hub_challenge'])) {
            $challenge = $_REQUEST['hub_challenge'];
            $verify_token = $_REQUEST['hub_verify_token'];
            if ($verify_token == get_option('verifytoken')) {
                echo htmlspecialchars($challenge);
            }
        } else {
            $this->session->set_userdata('fb_lead', file_get_contents('php://input'));
            redirect('facebook_leads_integration/getLeadGenID');
        }
    }

    // returns the table of Facebook pages
    public function getTable()
    {
		
        $pages = $_POST['pages'];

         update_option('facebook_pages',json_encode($pages));
        $html = '<table class="table table-striped" id="pageTable">
        <thead>
            <tr>
                <th>' . _l('page_name') . '</th>
                <th>' . _l('action') . '</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($pages as $page) {
            if (!in_array($page['id'] . get_option('appId'), json_decode(get_option('subscribed_pages')))) {
               
                $html .= '<tr> <td>' . $page["name"] . '</td> <td><input type="button" value="' . _l('fbleadssubscribe') . '" id="' . $page['id'] . '" onclick="subscribe (' . $page['id'] . ',\'' . $page["access_token"] . '\');" class="btn btn-info"></td> </tr>';
                
            }else{
                $html .= '<tr> <td>' . $page["name"] . '</td> <td><input type="button" value="' . _l('fbleadsunsubscribe') . '" id="' . $page['id'] . '" onclick="unsubscribeApps (' . $page['id'] . ',\'' . $page["access_token"] . '\');" class="btn btn-danger"></td> </tr>';
            } 

        }
       
        $html .= '</tbody>
        </table>';
		\modules\facebook_leads_integration\core\Apiinit::parse_module_url('facebook_leads_integration');
		\modules\facebook_leads_integration\core\Apiinit::check_url('facebook_leads_integration');
        print_r($html);
    }
    // save Facebook leads data
    public function saveData($data, $status)
    {
        if ($status == true) {
            $lead = $data;
        } else {
            $lead =  file_get_contents(APP_MODULES_PATH . 'facebook_leads_integration/lead_data.json', TRUE);
        }
        $json = json_decode($lead);
        $fields = array();
        array_push($fields, 'name');
        array_push($fields, 'address');
        array_push($fields, 'title');
        array_push($fields, 'city');
        array_push($fields, 'email');
        array_push($fields, 'state');
        array_push($fields, 'website');
        array_push($fields, 'country');
        array_push($fields, 'phonenumber');
        array_push($fields, 'zip');
        array_push($fields, 'company');
        array_push($fields, 'default_language');
        array_push($fields, 'description');
        array_push($fields, 'assigned');
        array_push($fields, 'source');
        array_push($fields, 'status');

        $custom_fields = array();
        foreach (get_custom_fields('leads') as $field) {
            $custom_fields[$field['id']] = $field['slug'];
        }

        $custom_fields_with_values = array();
        $custom_fields_with_values['leads'] = array();
        $data = array();
        foreach ($json->field_data as $field) {
            if (in_array($field->name, $fields)) {
                $data[$field->name] = $field->values[0];
            } elseif (in_array($field->name, $custom_fields)) {
                $id = array_search($field->name, $custom_fields);
                $custom_fields_with_values['leads'][$id] = $field->values[0];
            }
        }

        $data['assigned'] = get_option("facebook_lead_assigned");
        $data['source'] = get_option("facebook_lead_source");
        $data['status'] = get_option("facebook_lead_status");

        $data['is_public'] = 0;


        if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
            $data['country'] = 0;
        }

        if (isset($data['custom_contact_date'])) {
            unset($data['custom_contact_date']);
        }

        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']   = get_staff_user_id();
        $this->db->insert(db_prefix() . 'leads', $data);
        $insert_id = $this->db->insert_id();


        // Save custom fields
        if (isset($custom_fields)) {
            handle_custom_fields_post($insert_id, $custom_fields_with_values);
        }
    }
    // Store pages id which are subscribed 
    public function pageSubscribed()
    {
		\modules\facebook_leads_integration\core\Apiinit::parse_module_url('facebook_leads_integration');
		\modules\facebook_leads_integration\core\Apiinit::check_url('facebook_leads_integration');
        $pages = json_decode(get_option('subscribed_pages'));
        $page_id = $_POST['id'];
        $app_id = get_option('appId');
        // $page=$pages[]
        array_push($pages, $page_id . $app_id);
        update_option('subscribed_pages', json_encode($pages));
        print_r(json_encode($pages));
    }
    // Exclude pages id which are Unsubscribed 
    public function pageUnSubscribed()
    {

        $pages = json_decode(get_option('subscribed_pages'));
        $page_id = $_POST['id'];
        $app_id = get_option('appId');


        $pages = array_diff($pages, [$page_id . $app_id]);
        $new_pages = array();
        foreach ($pages as $page) {

            if ($page != $page_id . $app_id) {
                array_push($new_pages, $page);
            }
        }


        update_option('subscribed_pages', json_encode($new_pages));
        print_r(json_encode($new_pages));
    }
    // Save and update Long live Facebook access token
    public function saveToken()
    {
        update_option('longLifeAccessToken', $_POST['data']);
    }
    // Returns the leadgen_id of a facebook lead
    public function getLeadGenID()
    {

        $data = json_decode($this->session->userdata('fb_lead'));
        $id = $data->entry[0]->changes[0]->value->leadgen_id;
        if ($id == "444444444444") {
            echo htmlspecialchars($this->saveData($id, false));
        } else {
            echo htmlspecialchars($this->get_lead_data($id));
        }
        $this->session->unset_userdata('fb_lead');
    }
    // returns facebook lead data
    public function get_lead_data($id = 407118556715098)
    {

        require_once APP_MODULES_PATH . 'facebook_leads_integration/src/Facebook/autoload.php';


        $fb = new Facebook\Facebook([
            'app_id' => get_option('appId'),
            'app_secret' => get_option('appSecret'),
            'default_graph_version' => 'v5.0',

        ]);

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get(
                '/' . $id,
                get_option('longLifeAccessToken')

            );
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo htmlspecialchars('Graph returned an error: ' . $e->getMessage());
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo htmlspecialchars('Facebook SDK returned an error: ' . $e->getMessage());
            exit;
        }
        $graphNode = $response;

        echo htmlspecialchars($this->saveData($graphNode->getBody(), true));
    }
    // Save dummy data against test_lead
    public function dumyData()
    {
        redirect('facebook_leads_integration/saveData');
    }

    public function updateFields()
    {
        $id = $_POST['id'];
        $view_assigned = $_POST['view_assigned'];
        $view_source = $_POST['view_source'];
        $view_status = $_POST['view_status'];

        if ($id == 1) {
            update_option('facebook_lead_assigned', $view_assigned);
        } elseif ($id == 2) {
            update_option('facebook_lead_source', $view_source);
        } else {
            update_option('facebook_lead_status', $view_status);
        }
    }
}
