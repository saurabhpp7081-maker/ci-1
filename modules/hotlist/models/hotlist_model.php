<?php

defined('BASEPATH') or exit('No direct script access allowed');

class hotlist_model extends App_Model
{

    public function __construct()
    {
        parent::__construct();
        


    }
    public function get_all()
    {
        return $this->db->get(db_prefix() . 'leads')->result_array();

    }


    public function add_round($name)
    {
        $name = trim($name);

        $existing = $this->db
            ->where('name', $name)
            ->get(db_prefix() . 'hotlist_rounds')
            ->row_array();

        if ($existing) {
            return $existing;
        }

        $this->db->insert(db_prefix() . 'hotlist_rounds', [
            'name'   => $name,
            'active' => 1,
        ]);

        return [
            'id'   => $this->db->insert_id(),
            'name' => $name,
        ];
    }

    public function get_round($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'hotlist_rounds')->row();
        }

        $this->db->order_by('name', 'asc');
        return $this->db->get(db_prefix() . 'hotlist_rounds')->result_array();
    }
    public function update_round($id, $name)
    {
        $name = trim($name);

        $existing = $this->db
            ->where('name', $name)
            ->where('id !=', $id)
            ->get(db_prefix() . 'hotlist_rounds')
            ->row_array();

        if ($existing) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'hotlist_rounds', [
            'name' => $name,
        ]);

        return true;    
    }

    public function _get_lead_data($id)
    {
        $this->db->select('id, name, email, phone, company, country');
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'leads')->row_array();
    }
}
