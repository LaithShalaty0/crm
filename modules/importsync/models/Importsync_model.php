<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Importsync_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addMappedCsv($data)
    {
        $this->db->insert(db_prefix() . 'importsync_csv_mapped', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    public function getMappedCsv($id)
    {
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'importsync_csv_mapped')->row();
    }

    public function updateMappedCsv($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'importsync_csv_mapped', $data);

        return $this->db->affected_rows() > 0;
    }

    public function deleteMappedCsv($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'importsync_csv_mapped');

        $directory = FCPATH . 'modules/importsync/uploads/mapped_csv/' . $id . '/';

        if (is_dir($directory)) {
            delete_dir($directory);
        }

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

}
