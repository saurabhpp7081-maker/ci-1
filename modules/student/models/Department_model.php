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

    public function get_all()
    {
        return $this->db->get(db_prefix() . 'student_departments')->result_array();
    }


    public function get_students_by_department()
{
    $students = db_prefix() . 'students';
    $departments = db_prefix() . 'student_departments';

    return $this->db
        ->select(
            $departments . '.name AS department_name,
            COUNT(' . $students . '.id) AS total'
        )
        ->from($departments)
        ->join(
            $students,
            $students . '.department_id = ' . $departments . '.id',
            'left'
        )
        ->group_by($departments . '.id')
        ->order_by('total', 'DESC')
        ->get()
        ->result_array();
}





}
