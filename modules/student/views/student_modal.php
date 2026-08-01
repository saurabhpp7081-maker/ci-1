<div class="modal fade" id="student_modal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal">

                    &times;

                </button>

                <h4>Add Student</h4>

            </div>

            <div class="modal-body">

                <?= form_open(admin_url('student/store'), ['id' => 'student-form']); ?>
                <?= render_input('admission_no', 'Admission No'); ?>

                <?= render_input('roll_no', 'Roll No'); ?>

                <?= render_input('full_name', 'Full Name'); ?>

                <?= render_input('father_name', 'Father Name'); ?>

                <?= render_input('mother_name', 'Mother Name'); ?>

                <?= render_input('email', 'Email', '', 'email'); ?>

                <?= render_input('phone', 'Phone', '', 'tel'); ?>

                <?= render_select(
                    'gender',
                    [
                        ['id' => 'Male', 'name' => 'Male'],
                        ['id' => 'Female', 'name' => 'Female'],
                        ['id' => 'Other', 'name' => 'Other']
                    ],
                    ['id', 'name'],
                    'Gender'
                ); ?>

                <?= render_date_input('dob', 'Date of Birth'); ?>

                <?= render_input('class', 'Class'); ?>

                <?= render_input('section', 'Section'); ?>

                <?= render_input('course', 'Course'); ?>

                <?= render_textarea('address', 'Address'); ?>

                <?= render_select(
                    'status',
                    [
                        ['id' => 1, 'name' => 'Active'],
                        ['id' => 0, 'name' => 'Inactive']
                    ],
                    ['id', 'name'],
                    'Status'
                ); ?>

                <button class="btn btn-primary">
                    Save Student
                </button>

                <?= form_close(); ?>
            </div>

        </div>

    </div>

</div>