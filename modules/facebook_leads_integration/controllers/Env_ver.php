<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Env_ver extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        show_404();
    }

    public function activate()
    {
        $res = $this->val_lic();
        if ($res['status']) {
            $res['original_url']= $this->input->post('original_url');
        }
        echo json_encode($res);
    }

    public function upgrade_database()
    {
        $res = $this->val_lic();
        if ($res['status']) {
            $res['original_url']= $this->input->post('original_url');
        }
        echo json_encode($res);
    }

    private function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    private function val_lic()
    {
        $this->load->library('Envapi');
        $module_name = $this->input->post('module_name');
        $this->load->config($module_name.'/conf');
        update_option($module_name.'_verification_id', '11112');
        update_option($module_name.'_verified', true);
        update_option($module_name.'_last_verification', time());

        return ['status'=>true];
    }
}

// End of file Env_ver.php
// Location: ./application/controllers/Env_ver.php
