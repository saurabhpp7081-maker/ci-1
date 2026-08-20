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
public function update($id, array $data)
{
    $id = (int) $id;

    if ($id <= 0) {
        return false;
    }

    $this->db->where('id', $id);

    $updated = $this->db->update(
        db_prefix() . 'students',
        $data
    );

    if (!$updated) {
        log_message(
            'error',
            'Student update failed: ' .
            json_encode($this->db->error())
        );

        return false;
    }

    return $this->db->affected_rows() >= 0;
}

// overview dasboard 

public function get_total_students()
{
    return $this->db
        ->count_all(db_prefix() . 'students');
}

public function get_active_students()
{
    return $this->db
        ->where('status', 1)
        ->count_all_results(db_prefix() . 'students');
}

public function get_inactive_students()
{
    return $this->db
        ->where('status', 0)
        ->count_all_results(db_prefix() . 'students');
}

public function get_this_month_students()
{
    return $this->db
        ->where(
            'created_at >=',
            date('Y-m-01 00:00:00')
        )
        ->where(
            'created_at <=',
            date('Y-m-t 23:59:59')
        )
        ->count_all_results(db_prefix() . 'students');
}

public function get_students_by_course()
{
    $students = db_prefix() . 'students';
    $courses  = db_prefix() . 'student_courses';

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


public function get_students_by_department()
{
    $students    = db_prefix() . 'students';
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