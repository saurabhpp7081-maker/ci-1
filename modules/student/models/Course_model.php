<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Course_model extends App_Model
{
    protected $table;

    public function __construct()
    {
        parent::__construct();

        $this->table = db_prefix() . 'student_courses';
    }


    /*
    |--------------------------------------------------------------------------
    | Get Course
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Add Course
    |--------------------------------------------------------------------------
    */

    public function add($data)
    {
        $insert_data = [
            'name'        => $data['name'],
            'course_code' => $data['course_code'],
            'duration'    => $data['duration'],
            'status'      => isset($data['status'])
                ? $data['status']
                : 1,
        ];

        $this->db->insert(
            $this->table,
            $insert_data
        );

        return $this->db->insert_id();
    }


    /*
    |--------------------------------------------------------------------------
    | Update Course
    |--------------------------------------------------------------------------
    */

    public function update($id, $data)
    {
        $update_data = [
            'name'        => $data['name'],
            'course_code' => $data['course_code'],
            'duration'    => $data['duration'],
            'status'      => isset($data['status'])
                ? $data['status']
                : 1,
        ];

        return $this->db
            ->where('id', $id)
            ->update(
                $this->table,
                $update_data
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Course
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete($this->table);
    }


    /*
    |--------------------------------------------------------------------------
    | Check Course Exists
    |--------------------------------------------------------------------------
    */

    public function exists($name, $exclude_id = null)
    {
        $this->db->where(
            'name',
            trim($name)
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
        return $this->db->get(db_prefix() . 'student_courses')->result_array();
    }

    


    public function code_exists($course_code, $exclude_id = null)
{
    $this->db->where(
        'course_code',
        trim($course_code)
    );

    if (
        $exclude_id !== null &&
        $exclude_id !== ''
    ) {
        $this->db->where(
            'id !=',
            (int) $exclude_id
        );
    }

    return $this->db
        ->count_all_results(
            $this->table
        ) > 0;
}





public function get_students_by_course()
{
    $students = db_prefix() . 'students';
    $courses = db_prefix() . 'student_courses';

    return $this->db
        ->select(
            $courses . '.name AS course_name,
            COUNT(' . $students . '.id) AS total'
        )
        ->from($courses)
        ->join(
            $students,
            $students . '.course_id = ' . $courses . '.id',
            'left'
        )
        ->group_by($courses . '.id')
        ->order_by('total', 'DESC')
        ->get()
        ->result_array();
}


}
