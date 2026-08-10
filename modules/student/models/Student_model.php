<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function get_all()
    {
        return $this->db
            ->get(db_prefix() . 'students')
            ->result_array();
    }


    public function add(array $data)
    {
        $insert = $this->db->insert(
            db_prefix() . 'students',
            $data
        );

        if (!$insert) {
            echo '<pre>';
            print_r($this->db->error());
            echo '</pre>';

            echo $this->db->last_query();

            exit;
        }

        return $this->db->insert_id();
    }


    public function delete($id)
    {
        $this->db->where('id', $id);

        return $this->db->delete(
            db_prefix() . 'students'
        );
    }


    /**
     * Get students for DataTable
     */
    public function get_table_data()
    {
        $this->db->select([
            'id',
            'admission_no',
            'full_name',
            'phone',
            'course',
            'status',
        ]);

        $this->db->from(db_prefix() . 'students');

        return $this->db->get()->result_array();
    }


    public function get($id)
{
    return $this->db
        ->where('id', $id)
        ->get(db_prefix() . 'students')
        ->row_array();
}
}