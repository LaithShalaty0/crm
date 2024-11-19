<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Flexibleleadscore_leadscore_model extends App_Model
{
    protected $table = 'flexiblels_lead_scores';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $conditions
     * @return array|array[]
     * get all models
     */
    public function all($conditions = [])
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table);
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * @param $conditions
     * @return array
     * get model by id
     */
    public function get($conditions)
    {
        $this->db->select('*');
        $this->db->from(db_prefix() . $this->table);
        if (!empty($conditions)) {
            $this->db->where($conditions);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * @param $data
     * @return bool
     * add model
     */
    public function add($data)
    {
        $this->db->insert(db_prefix() . $this->table, $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }
        return false;
    }

    public function delete_where($conditions)
    {
        $this->db->where($conditions);
        $this->db->delete(db_prefix() . $this->table);
    }

    public function delete_all()
    {
        $this->db->empty_table(db_prefix() . $this->table);
    }

    public function scores_report()
    {
        $this->db->select('score, COUNT(score) as howmany');
        $this->db->from(db_prefix() . $this->table);
        $this->db->group_by('score');
        $this->db->order_by('howmany', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function scores_report_data()
    {
        $scores = $this->scores_report();
        $chart = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => flexiblels_lang('score'),
                    'backgroundColor' => 'rgba(124, 179, 66, 0.5)',
                    'borderColor'     => '#7cb342',
                    'data' => [],
                ],
            ],
        ];
        foreach ($scores as $score) {
            array_push($chart['labels'], $score['howmany'] . ' ' . flexiblels_lang('leads'));
            array_push($chart['datasets'][0]['data'], $score['score']);
        }

        return $chart;
    }
}
