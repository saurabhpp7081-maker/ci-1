<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student extends AdminController

{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('taxes_model');

        if (!is_admin()) {
            access_denied('Taxes');
        }

        $this->load->model('student/Student_model');
        $this->load->library('form_validation');
        $this->lang->load('student', 'english');
        // $this->load->helper('student');
    }
    public function index()
    {
        $data['title'] = _l('students');

        $this->load->view('manage', $data);
    }


    public function store()
    {
        // Validation
        $this->form_validation->set_rules('admission_no', 'Admission No', 'required|is_unique[tblstudents.admission_no]');
        $this->form_validation->set_rules('roll_no', 'Roll No', 'required');
        $this->form_validation->set_rules('full_name', 'Full Name', 'required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('father_name', 'Father Name', 'required');
        $this->form_validation->set_rules('mother_name', 'Mother Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('phone', 'Phone', 'required|numeric|exact_length[10]');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'required');
        $this->form_validation->set_rules('class', 'Class', 'required');
        $this->form_validation->set_rules('section', 'Section', 'required');
        $this->form_validation->set_rules('course', 'Course', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run() == FALSE) {

            echo validation_errors();

            exit;
        }

        $data = [
            'admission_no' => $this->input->post('admission_no', true),
            'roll_no'      => $this->input->post('roll_no', true),
            'full_name'    => $this->input->post('full_name', true),
            'father_name'  => $this->input->post('father_name', true),
            'mother_name'  => $this->input->post('mother_name', true),
            'email'        => $this->input->post('email', true),
            'phone'        => $this->input->post('phone', true),
            'gender'       => $this->input->post('gender', true),
            'dob'          => $this->input->post('dob', true),
            'class'        => $this->input->post('class', true),
            'section'      => $this->input->post('section', true),
            'course'       => $this->input->post('course', true),
            'address'      => $this->input->post('address', true),
            'status'       => $this->input->post('status', true),
            'created_at'   => date('Y-m-d H:i:s'),
        ];

        $insert_id = $this->Student_model->add($data);

        if ($insert_id) {
            set_alert('success', 'Student Added Successfully');
        } else {
            set_alert('danger', 'Something Went Wrong');
        }

        redirect(admin_url('student'));
    }

    public function delete($id)
    {
        if (!is_admin()) {
            access_denied('Delete Student');
        }

        $deleted = $this->Student_model->delete($id);

        if ($deleted) {
            set_alert('success', 'Student deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete student');
        }

        redirect(admin_url('student'));
    }
    public function table()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $aColumns = [
            'id',
            'admission_no',
            'full_name',
            'phone',
            'course',
            'status',
        ];

        $sIndexColumn = 'id';

        $sTable = db_prefix() . 'students';

        $join = [];

        $where = [];

        $additionalSelect = [];

        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            $additionalSelect
        );

        $output  = $result['output'];
        $rResult = $result['rResult'];

        foreach ($rResult as $student) {

            $row = [];

            $row[] = $student['id'];

            $row[] = $student['admission_no'];

            $row[] = $student['full_name'];

            $row[] = $student['phone'];

            $row[] = $student['course'];

            if ($student['status'] == 1) {

                $row[] = '<span class="label label-success">'
                    . _l('active')
                    . '</span>';
            } else {

                $row[] = '<span class="label label-danger">'
                    . _l('inactive')
                    . '</span>';
            }

            $actions = '';

            $actions .= '<a href="javascript:void(0);"
            onclick="editStudent(' . $student['id'] . ')"
            class="btn btn-default btn-icon"
            data-toggle="tooltip"
            data-title="' . _l('edit') . '">
            <i class="fa fa-pencil"></i>
        </a>';

            $actions .= ' ';

            $actions .= '<a href="' . admin_url('student/delete/' . $student['id']) . '"
            class="btn btn-default btn-icon _delete"
            data-toggle="tooltip"
            data-title="' . _l('delete') . '">
            <i class="fa fa-trash"></i>
        </a>';

            $row[] = $actions;

            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }
    public function get($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }

        $student = $this->Student_model->get($id);

        if (!$student) {
            echo json_encode([
                'success' => false,
                'message' => 'Student not found'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update($id)
    {
        if (!is_admin()) {
            access_denied('student');
        }

        if (!$id) {
            redirect(admin_url('student'));
        }

        if ($this->input->post()) {

            $data = [
                'admission_no' => $this->input->post('admission_no'),
                'roll_no'      => $this->input->post('roll_no'),
                'full_name'    => $this->input->post('full_name'),
                'father_name'  => $this->input->post('father_name'),
                'mother_name'  => $this->input->post('mother_name'),
                'email'        => $this->input->post('email'),
                'phone'        => $this->input->post('phone'),
                'gender'       => $this->input->post('gender'),
                'dob'          => $this->input->post('dob'),
                'class'        => $this->input->post('class'),
                'section'      => $this->input->post('section'),
                'course'       => $this->input->post('course'),
                'address'      => $this->input->post('address'),
                'status'       => $this->input->post('status'),
            ];

            $this->db->where('id', $id);

            if ($this->db->update('students', $data)) {

                set_alert('success', 'Student updated successfully.');
            } else {

                set_alert('danger', 'Failed to update student.');
            }

            redirect(admin_url('student'));
        }

        redirect(admin_url('student'));
    }
}
