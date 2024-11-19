<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Whatsapp_api_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->whatsapp_api_log_table = db_prefix() . 'whatsapp_api_debug_log';
    }

    public function get_template_data($id)
    {
        return $this->db->get_where(db_prefix() . 'whatsapp_templates', ['id' => $id])->row();
    }

    public function save_template_map_info($map_info)
    {
        return $this->db->insert(db_prefix() . 'whatsapp_templates_mapping', $map_info);
    }

    public function update_template_map_info($map_info, $where)
    {
        return $this->db->update(db_prefix() . 'whatsapp_templates_mapping', $map_info, $where);
    }

    public function delete_whatsapp_templates_mapping($id)
    {
        return $this->db->delete(db_prefix() . 'whatsapp_templates_mapping', $id);
    }

    public function get_mapping_data($where)
    {
        return $this->db
            ->select(db_prefix() . 'whatsapp_templates_mapping.*')
            ->select('wt.template_name, wt.language, wt.header_data_format, wt.header_data_text, wt.body_data, wt.footer_data, wt.buttons_data, wt.header_params_count, wt.body_params_count, wt.footer_params_count, whatsapp_templates_mapping.send_to')
            ->join(db_prefix() . 'whatsapp_templates wt', db_prefix() . 'whatsapp_templates_mapping.template_id = wt.id')
            ->get_where(db_prefix() . 'whatsapp_templates_mapping', $where)->result();
    }

    public function clear_webhook_log()
    {
        if ($this->db->truncate($this->whatsapp_api_log_table)) {
            return true;
        }

        return false;
    }

    public function get_whatsapp_api_log_info($id)
    {
        return $this->db->get_where(db_prefix() . 'whatsapp_api_debug_log', ['id' => $id])->row();
    }

    public function change_whatsapp_template_status($data, $where)
    {
        if ($this->db->update(db_prefix() . 'whatsapp_templates_mapping', $data, $where)) {
            return true;
        }

        return false;
    }

    public function add_request_log($data)
    {
        return $this->db->insert(db_prefix() . 'whatsapp_api_debug_log', $data);
    }
}
