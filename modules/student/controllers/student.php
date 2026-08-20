<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied('Students');
        }

        $this->load->model('student/Student_model');
        $this->load->model('student/Department_model');
        $this->load->model('student/Course_model');

        $this->load->library('form_validation');
        $this->lang->load('student', 'english');
    }


    /*
    |--------------------------------------------------------------------------
    | Students
    |--------------------------------------------------------------------------
    */

    public function index()
{
    $data['title'] = _l('students');

    $data['departments'] = $this->Department_model->get_all();
    $data['courses'] = $this->Course_model->get_all();

    $this->load->view('manage', $data);
}


    /*
    |--------------------------------------------------------------------------
    | Store Student
    |--------------------------------------------------------------------------
    */

    public function store()
    {

    // echo '<pre>';
    // print_r($this->input->post());
    // exit;
        $this->form_validation->set_rules(
            'admission_no',
            'Admission No',
            'required|is_unique[' . db_prefix() . 'students.admission_no]'
        );

        $this->form_validation->set_rules(
            'roll_no',
            'Roll No',
            'required'
        );

        $this->form_validation->set_rules(
            'full_name',
            'Full Name',
            'required|min_length[3]|max_length[100]'
        );

        // $this->form_validation->set_rules(
        //     'father_name',
        //     'Father Name',
        //     'required'
        // );

        // $this->form_validation->set_rules(
        //     'mother_name',
        //     'Mother Name',
        //     'required'
        // );

        $this->form_validation->set_rules(
            'email',
            'Email',
            'required|valid_email'
        );

        $this->form_validation->set_rules(
            'phone',
            'Phone',
            'required|numeric|exact_length[10]'
        );

        $this->form_validation->set_rules(
            'gender',
            'Gender',
            'required'
        );

        $this->form_validation->set_rules(
            'dob',
            'Date of Birth',
            'required'
        );

        $this->form_validation->set_rules(
            'department_id',
            'Department',
            'required'
        );

        $this->form_validation->set_rules(
            'course_id',
            'Course',
            'required'
        );

        $this->form_validation->set_rules(
            'address',
            'Address',
            'required'
        );

        $this->form_validation->set_rules(
            'status',
            'Status',
            'required'
        );


        if ($this->form_validation->run() === false) {

            set_alert(
                'danger',
                strip_tags(validation_errors())
            );

            redirect(
                admin_url('student')
            );

            return;
        }


        $data = [
            'admission_no' => $this->input->post(
                'admission_no',
                true
            ),

            'roll_no' => $this->input->post(
                'roll_no',
                true
            ),

            'full_name' => $this->input->post(
                'full_name',
                true
            ),

            'father_name' => $this->input->post(
                'father_name',
                true
            ),

            'mother_name' => $this->input->post(
                'mother_name',
                true
            ),

            'email' => $this->input->post(
                'email',
                true
            ),

            'phone' => $this->input->post(
                'phone',
                true
            ),

            'gender' => $this->input->post(
                'gender',
                true
            ),

            'dob' => $this->input->post(
                'dob',
                true
            ),

            'department_id' => (int) $this->input->post(
                'department_id'
            ),

            'course_id' => (int) $this->input->post(
                'course_id'
            ),

            'address' => $this->input->post(
                'address',
                true
            ),

            'status' => (int) $this->input->post(
                'status'
            ),
        ];


        $insert_id = $this->Student_model->add($data);


        if ($insert_id) {

            set_alert(
                'success',
                'Student Added Successfully'
            );

        } else {

            set_alert(
                'danger',
                'Something Went Wrong'
            );
        }


        redirect(
            admin_url('student')
        );
    }


    
    


    /*
    |--------------------------------------------------------------------------
    | Delete Student
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $id = (int) $id;

        if (!$id) {

            set_alert(
                'danger',
                'Invalid Student ID'
            );

            redirect(
                admin_url('student')
            );

            return;
        }


        $deleted = $this->Student_model->delete($id);


        if ($deleted) {

            set_alert(
                'success',
                'Student deleted successfully'
            );

        } else {

            set_alert(
                'danger',
                'Failed to delete student'
            );
        }


        redirect(
            admin_url('student')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Students DataTable
    |--------------------------------------------------------------------------
    */


public function table()
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    $students = db_prefix() . 'students';
    $courses  = db_prefix() . 'student_courses';

    $aColumns = [
        $students . '.id',
        $students . '.admission_no',
        $students . '.full_name',
        $students . '.phone',
        $students . '.course_id',
        $students . '.status',
    ];

    $sIndexColumn = $students . '.id';

    $sTable = $students;

    $join = [
        'LEFT JOIN ' . $courses . '
            ON ' . $courses . '.id = ' . $students . '.course_id'
    ];

    $where = [];

    $additionalSelect = [
        $courses . '.name AS course_name'
    ];

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

        // ID
        $row[] = $student[$students . '.id'];

        // Admission No
        $row[] = e(
            $student[$students . '.admission_no']
        );

        // Full Name
        $row[] = e(
            $student[$students . '.full_name']
        );

        // Phone
        $row[] = e(
            $student[$students . '.phone']
        );

        // Course
        $row[] = e(
            $student['course_name'] ?? ''
        );

        // Status
        if (
            isset($student[$students . '.status']) &&
            (int) $student[$students . '.status'] === 1
        ) {

            $row[] = '<span class="label label-success">'
                . _l('active')
                . '</span>';

        } else {

            $row[] = '<span class="label label-danger">'
                . _l('inactive')
                . '</span>';
        }

        // Actions
        $student_id = (int) $student[$students . '.id'];

        $actions = '';

        // Edit
        $actions .= '<a href="javascript:void(0);"
            onclick="editStudent(' . $student_id . ')"
            class="btn btn-default btn-icon"
            data-toggle="tooltip"
            data-title="' . _l('edit') . '">
            <i class="fa fa-pencil"></i>
        </a>';

        $actions .= ' ';

        // Delete
        $actions .= '<a href="'
            . admin_url(
                'student/delete/' . $student_id
            )
            . '"
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




    /*
    |--------------------------------------------------------------------------
    | Get Student
    |--------------------------------------------------------------------------
    */

    public function get($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $id = (int) $id;


        if (!$id) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid Student ID'
            ]);

            return;
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


    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

   public function update($id)
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    $id = (int) $id;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Student ID.'
        ]);
        return;
    }

    $student = $this->Student_model->get($id);

    if (!$student) {
        echo json_encode([
            'success' => false,
            'message' => 'Student not found.'
        ]);
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    $this->form_validation->set_rules(
        'admission_no',
        'Admission No',
        'required'
    );

    $this->form_validation->set_rules(
        'roll_no',
        'Roll No',
        'required'
    );

    $this->form_validation->set_rules(
        'full_name',
        'Full Name',
        'required|min_length[3]|max_length[100]'
    );

    $this->form_validation->set_rules(
        'father_name',
        'Father Name',
        'required'
    );

    $this->form_validation->set_rules(
        'mother_name',
        'Mother Name',
        'required'
    );

    $this->form_validation->set_rules(
        'email',
        'Email',
        'required|valid_email'
    );

    $this->form_validation->set_rules(
        'phone',
        'Phone',
        'required|numeric|exact_length[10]'
    );

    $this->form_validation->set_rules(
        'gender',
        'Gender',
        'required'
    );

    $this->form_validation->set_rules(
        'dob',
        'Date of Birth',
        'required'
    );

    $this->form_validation->set_rules(
        'department_id',
        'Department',
        'required'
    );

    $this->form_validation->set_rules(
        'course_id',
        'Course',
        'required'
    );

    $this->form_validation->set_rules(
        'address',
        'Address',
        'required'
    );

    $this->form_validation->set_rules(
        'status',
        'Status',
        'required'
    );

    if ($this->form_validation->run() === false) {

        echo json_encode([
            'success' => false,
            'message' => strip_tags(
                validation_errors()
            )
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Data
    |--------------------------------------------------------------------------
    */

    $data = [
        'admission_no' => $this->input->post(
            'admission_no',
            true
        ),

        'roll_no' => $this->input->post(
            'roll_no',
            true
        ),

        'full_name' => $this->input->post(
            'full_name',
            true
        ),

        'father_name' => $this->input->post(
            'father_name',
            true
        ),

        'mother_name' => $this->input->post(
            'mother_name',
            true
        ),

        'email' => $this->input->post(
            'email',
            true
        ),

        'phone' => $this->input->post(
            'phone',
            true
        ),

        'gender' => $this->input->post(
            'gender',
            true
        ),

        'dob' => $this->input->post(
            'dob',
            true
        ),

        'department_id' => (int) $this->input->post(
            'department_id'
        ),

        'course_id' => (int) $this->input->post(
            'course_id'
        ),

        'address' => $this->input->post(
            'address',
            true
        ),

        'status' => (int) $this->input->post(
            'status'
        ),
    ];

    /*
    |--------------------------------------------------------------------------
    | Update Student
    |--------------------------------------------------------------------------
    */

    $updated = $this->Student_model->update(
        $id,
        $data
    );

    /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

    if ($updated) {

        echo json_encode([
            'success' => true,
            'message' => 'Student updated successfully.'
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Failed to update student.'
        ]);
    }

    exit;
}


    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    public function departments()
    {
        $data['title'] = 'Departments';

        $this->load->view(
            'student/departments/manage',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Departments DataTable
    |--------------------------------------------------------------------------
    */

    public function departments_table()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $aColumns = [
            'name',
            'status',
        ];


        $sIndexColumn = 'id';

        $sTable = db_prefix() . 'student_departments';

        $join = [];

        $where = [];


        $additionalSelect = [
            'id'
        ];


        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            $additionalSelect
        );


        $output = $result['output'];
        $rResult = $result['rResult'];


        foreach ($rResult as $department) {

            $row = [];


            $row[] = e(
                $department['name']
            );


            if (
                (int) $department['status'] === 1
            ) {

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
                onclick="editDepartment('
                . $department['id']
                . ')"
                class="btn btn-default btn-icon"
                data-toggle="tooltip"
                data-title="'
                . _l('edit')
                . '">
                <i class="fa fa-pencil"></i>
            </a>';


            $actions .= ' ';


            $actions .= '<a href="'
                . admin_url(
                    'student/delete_department/'
                    . $department['id']
                )
                . '"
                class="btn btn-default btn-icon _delete"
                data-toggle="tooltip"
                data-title="'
                . _l('delete')
                . '">
                <i class="fa fa-trash"></i>
            </a>';


            $row[] = $actions;


            $output['aaData'][] = $row;
        }


        echo json_encode($output);
    }


    /*
    |--------------------------------------------------------------------------
    | Add Department
    |--------------------------------------------------------------------------
    */

    public function add_department()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $this->form_validation->set_rules(
            'name',
            'Department Name',
            'required|trim|min_length[2]|max_length[150]'
        );


        if (
            $this->form_validation->run() === false
        ) {

            echo json_encode([
                'success' => false,
                'message' => strip_tags(
                    validation_errors()
                )
            ]);

            return;
        }


        $name = trim(
            $this->input->post(
                'name',
                true
            )
        );


        $status = (int) $this->input->post(
            'status'
        );


        if (
            $this->Department_model->exists(
                $name
            )
        ) {

            echo json_encode([
                'success' => false,
                'message' => 'Department name already exists.'
            ]);

            return;
        }


        $data = [
            'name' => $name,
            'status' => $status
        ];


        $insert_id = $this->Department_model->add(
            $data
        );


        if ($insert_id) {

            echo json_encode([
                'success' => true,
                'message' => 'Department added successfully.',
                'id' => $insert_id
            ]);

        } else {

            echo json_encode([
                'success' => false,
                'message' => 'Failed to add department.'
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Get Department
    |--------------------------------------------------------------------------
    */

    public function get_department($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $id = (int) $id;


        if (!$id) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid department ID.'
            ]);

            return;
        }


        $department = $this->Department_model->get(
            $id
        );


        if (!$department) {

            echo json_encode([
                'success' => false,
                'message' => 'Department not found.'
            ]);

            return;
        }


        echo json_encode([
            'success' => true,
            'data' => $department
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Department
    |--------------------------------------------------------------------------
    */

    public function update_department($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $id = (int) $id;


        if (!$id) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid department ID.'
            ]);

            return;
        }


        $department = $this->Department_model->get(
            $id
        );


        if (!$department) {

            echo json_encode([
                'success' => false,
                'message' => 'Department not found.'
            ]);

            return;
        }


        $this->form_validation->set_rules(
            'name',
            'Department Name',
            'required|trim|min_length[2]|max_length[150]'
        );


        if (
            $this->form_validation->run() === false
        ) {

            echo json_encode([
                'success' => false,
                'message' => strip_tags(
                    validation_errors()
                )
            ]);

            return;
        }


        $name = trim(
            $this->input->post(
                'name',
                true
            )
        );


        $status = (int) $this->input->post(
            'status'
        );


        if (
            $this->Department_model->exists(
                $name,
                $id
            )
        ) {

            echo json_encode([
                'success' => false,
                'message' => 'Department name already exists.'
            ]);

            return;
        }


        $data = [
            'name' => $name,
            'status' => $status
        ];


        $updated = $this->Department_model->update(
            $id,
            $data
        );


        if ($updated) {

            echo json_encode([
                'success' => true,
                'message' => 'Department updated successfully.'
            ]);

        } else {

            echo json_encode([
                'success' => false,
                'message' => 'Failed to update department.'
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Department
    |--------------------------------------------------------------------------
    */

    public function delete_department($id)
    {
        $id = (int) $id;


        if (!$id) {

            set_alert(
                'danger',
                'Invalid department ID.'
            );

            redirect(
                admin_url('student/departments')
            );

            return;
        }


        $department = $this->Department_model->get(
            $id
        );


        if (!$department) {

            set_alert(
                'danger',
                'Department not found.'
            );

            redirect(
                admin_url('student/departments')
            );

            return;
        }


        $deleted = $this->Department_model->delete(
            $id
        );


        if ($deleted) {

            set_alert(
                'success',
                'Department deleted successfully.'
            );

        } else {

            set_alert(
                'danger',
                'Failed to delete department.'
            );
        }


        redirect(
            admin_url('student/departments')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    */

    public function courses()
    {
        $data['title'] = 'Courses';

        $this->load->view(
            'student/courses/manage',
            $data
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Courses DataTable
    |--------------------------------------------------------------------------
    */

    public function courses_table()
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $aColumns = [
            'name',
            'course_code',
            'duration',
            'status',
        ];


        $sIndexColumn = 'id';

        $sTable = db_prefix() . 'student_courses';

        $join = [];

        $where = [];


        $additionalSelect = [
            'id'
        ];


        $result = data_tables_init(
            $aColumns,
            $sIndexColumn,
            $sTable,
            $join,
            $where,
            $additionalSelect
        );


        $output = $result['output'];
        $rResult = $result['rResult'];


        foreach ($rResult as $course) {

            $row = [];


            // Course Name
            $row[] = e(
                $course['name']
            );


            // Course Code
            $row[] = e(
                $course['course_code'] ?? ''
            );


            // Duration
            $row[] = e(
                $course['duration'] ?? ''
            );


            // Status
            if (
                (int) $course['status'] === 1
            ) {

                $row[] = '<span class="label label-success">'
                    . _l('active')
                    . '</span>';

            } else {

                $row[] = '<span class="label label-danger">'
                    . _l('inactive')
                    . '</span>';
            }


            // Actions
            $actions = '';


            $actions .= '<a href="javascript:void(0);"
                onclick="editCourse('
                . $course['id']
                . ')"
                class="btn btn-default btn-icon"
                data-toggle="tooltip"
                data-title="'
                . _l('edit')
                . '">
                <i class="fa fa-pencil"></i>
            </a>';


            $actions .= ' ';


            $actions .= '<a href="'
                . admin_url(
                    'student/delete_course/'
                    . $course['id']
                )
                . '"
                class="btn btn-default btn-icon _delete"
                data-toggle="tooltip"
                data-title="'
                . _l('delete')
                . '">
                <i class="fa fa-trash"></i>
            </a>';


            $row[] = $actions;


            $output['aaData'][] = $row;
        }


        echo json_encode($output);
    }


    /*
    |--------------------------------------------------------------------------
    | Add Course
    |--------------------------------------------------------------------------
    */

  public function add_course()
{
    if (!is_admin()) {
        ajax_access_denied();
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    $this->form_validation->set_rules(
        'name',
        'Course Name',
        'required|trim|min_length[2]|max_length[150]'
    );

    $this->form_validation->set_rules(
        'course_code',
        'Course Code',
        'required|trim|max_length[50]'
    );

    $this->form_validation->set_rules(
        'duration',
        'Duration',
        'required|trim|max_length[50]'
    );

    $this->form_validation->set_rules(
        'status',
        'Status',
        'required'
    );

    if ($this->form_validation->run() === false) {

        echo json_encode([
            'success' => false,
            'message' => strip_tags(
                validation_errors()
            )
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Get POST Data
    |--------------------------------------------------------------------------
    */

    $name = trim(
        $this->input->post(
            'name',
            true
        )
    );

    $course_code = trim(
        $this->input->post(
            'course_code',
            true
        )
    );

    $duration = trim(
        $this->input->post(
            'duration',
            true
        )
    );

    $status = (int) $this->input->post(
        'status'
    );

    /*
    |--------------------------------------------------------------------------
    | Course Name Duplicate Check
    |--------------------------------------------------------------------------
    */

    if ($this->Course_model->exists($name)) {

        echo json_encode([
            'success' => false,
            'message' => 'Course name already exists.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Course Code Duplicate Check
    |--------------------------------------------------------------------------
    */

    if ($this->Course_model->code_exists($course_code)) {

        echo json_encode([
            'success' => false,
            'message' => 'Course code already exists.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Insert Data
    |--------------------------------------------------------------------------
    */

    $data = [
        'name'        => $name,
        'course_code' => $course_code,
        'duration'    => $duration,
        'status'      => $status,
    ];

    /*
    |--------------------------------------------------------------------------
    | Add Course
    |--------------------------------------------------------------------------
    */

    $insert_id = $this->Course_model->add($data);

    if ($insert_id) {

        echo json_encode([
            'success' => true,
            'message' => 'Course added successfully.',
            'id'      => $insert_id,
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Failed to add course.',
        ]);
    }

    exit;
}


    /*
    |--------------------------------------------------------------------------
    | Get Course
    |--------------------------------------------------------------------------
    */

    public function get_course($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $id = (int) $id;


        if (!$id) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid course ID.'
            ]);

            return;
        }


        $course = $this->Course_model->get(
            $id
        );


        if (!$course) {

            echo json_encode([
                'success' => false,
                'message' => 'Course not found.'
            ]);

            return;
        }


        echo json_encode([
            'success' => true,
            'data' => $course
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Update Course
    |--------------------------------------------------------------------------
    */

    public function update_course($id)
    {
        if (!is_admin()) {
            ajax_access_denied();
        }


        $id = (int) $id;


        if (!$id) {

            echo json_encode([
                'success' => false,
                'message' => 'Invalid course ID.'
            ]);

            return;
        }


        $course = $this->Course_model->get(
            $id
        );


        if (!$course) {

            echo json_encode([
                'success' => false,
                'message' => 'Course not found.'
            ]);

            return;
        }


        $this->form_validation->set_rules(
            'name',
            'Course Name',
            'required|trim|min_length[2]|max_length[150]'
        );


        $this->form_validation->set_rules(
            'course_code',
            'Course Code',
            'required|trim|max_length[50]'
        );


        $this->form_validation->set_rules(
            'duration',
            'Duration',
            'required|trim|max_length[50]'
        );


        $this->form_validation->set_rules(
            'status',
            'Status',
            'required'
        );


        if (
            $this->form_validation->run() === false
        ) {

            echo json_encode([
                'success' => false,
                'message' => strip_tags(
                    validation_errors()
                )
            ]);

            return;
        }


        $name = trim(
            $this->input->post(
                'name',
                true
            )
        );


        $course_code = trim(
            $this->input->post(
                'course_code',
                true
            )
        );


        $duration = trim(
            $this->input->post(
                'duration',
                true
            )
        );


        $status = (int) $this->input->post(
            'status'
        );


        // Duplicate Name
        if (
            $this->Course_model->exists(
                $name,
                $id
            )
        ) {

            echo json_encode([
                'success' => false,
                'message' => 'Course name already exists.'
            ]);

            return;
        }


        // Duplicate Code
        if (
            $this->Course_model->code_exists(
                $course_code,
                $id
            )
        ) {

            echo json_encode([
                'success' => false,
                'message' => 'Course code already exists.'
            ]);

            return;
        }


        $data = [
            'name' => $name,
            'course_code' => $course_code,
            'duration' => $duration,
            'status' => $status
        ];


        $updated = $this->Course_model->update(
            $id,
            $data
        );


        if ($updated) {

            echo json_encode([
                'success' => true,
                'message' => 'Course updated successfully.'
            ]);

        } else {

            echo json_encode([
                'success' => false,
                'message' => 'Failed to update course.'
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Course
    |--------------------------------------------------------------------------
    */

    public function delete_course($id)
    {
        $id = (int) $id;


        if (!$id) {

            set_alert(
                'danger',
                'Invalid course ID.'
            );

            redirect(
                admin_url('student/courses')
            );

            return;
        }


        $course = $this->Course_model->get(
            $id
        );


        if (!$course) {

            set_alert(
                'danger',
                'Course not found.'
            );

            redirect(
                admin_url('student/courses')
            );

            return;
        }


        $deleted = $this->Course_model->delete(
            $id
        );


        if ($deleted) {

            set_alert(
                'success',
                'Course deleted successfully.'
            );

        } else {

            set_alert(
                'danger',
                'Failed to delete course.'
            );
        }


        redirect(
            admin_url('student/courses')
        );
    }
}