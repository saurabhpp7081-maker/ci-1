<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student extends AdminController

{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('student/Student_model');
        $this->load->library('form_validation');
    }
    public function index()
    {
        $this->load->model('student/Student_model');

        $data['title'] = 'Students';

        $data['students'] = $this->Student_model->get_all();

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
    echo $id;
}




}
