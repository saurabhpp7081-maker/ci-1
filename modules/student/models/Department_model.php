<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Department_model extends App_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();

        // Student module ka apna department table
        $this->table = db_prefix() . 'student_departments';
    }

    public function get($id = null)
    {
        if ($id !== null) {

            return $this->db
                ->where('id', $id)
                ->get($this->table)
                ->row_array();
        }

        return $this->db
            ->order_by('name', 'ASC')
            ->get($this->table)
            ->result_array();
    }

    public function add($data)
    {
        $this->db->insert(
            $this->table,
            $data
        );

        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update(
                $this->table,
                $data
            );
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete($this->table);
    }

    public function exists(
        $name,
        $exclude_id = null
    ) {
        $this->db->where(
            'name',
            $name
        );

        if (
            $exclude_id !== null
            && $exclude_id !== ''
        ) {

            $this->db->where(
                'id !=',
                $exclude_id
            );
        }

        return $this->db
            ->count_all_results(
                $this->table
            ) > 0;
    }
}