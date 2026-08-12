$(function () {

    appValidateForm($('#student-form'), {
        admission_no: 'required',
        roll_no: 'required',
        full_name: 'required',
        father_name: 'required',
        mother_name: 'required',
        email: {
            required: true,
            email: true
        },
        phone: 'required',
        gender: 'required',
        dob: 'required',
        class: 'required',
        section: 'required',
        course: 'required',
        address: 'required',
        
        
    });

    $('#student_modal').on('hidden.bs.modal', function () {

        $('#student-form')[0].reset();

        $('#student_id').val('');

        $('#student-form').attr(
            'action',
            admin_url + 'student/store'
        );

        $('#student_modal .modal-title').text('New Student');

        if ($('#student-form').data('validator')) {
            $('#student-form').validate().resetForm();
        }

        $('#student-form')
            .find('.form-group')
            .removeClass('has-error');

    });

    initDataTable(
        '.table-students',
        admin_url + 'student/table',
        undefined,
        undefined,
        undefined,
        []
    );

     initDataTable(
        '.table-departments',
        admin_url + 'student/departments_table',
        [2],
        [2]
    );

});


function editStudent(id)
{
    $.ajax({
        url: admin_url + 'student/get/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (response) {

            if (!response.success) {
                alert(response.message);
                return;
            }

            var student = response.data;

            $('#student_id').val(student.id);
            $('#admission_no').val(student.admission_no);
            $('#roll_no').val(student.roll_no);
            $('#full_name').val(student.full_name);
            $('#father_name').val(student.father_name);
            $('#mother_name').val(student.mother_name);
            $('#email').val(student.email);
            $('#phone').val(student.phone);
            $('#gender').val(student.gender);
            $('#dob').val(student.dob);
            $('#class').val(student.class);
            $('#section').val(student.section);
            $('#course').val(student.course);
            $('#address').val(student.address);
            $('#status').val(student.status);

            $('#student-form').attr(
                'action',
                admin_url + 'student/update/' + student.id
            );

            $('#student_modal .modal-title').text('Edit Student');

            $('#student_modal').modal('show');
        },

        error: function () {
            alert('Unable to load student data.');
        }
    });
}


function deleteStudent(id)
{
    if (confirm('Are you sure you want to delete this student?')) {
        window.location.href = admin_url + 'student/delete/' + id;
    }
}


$(function () {

    /*
    |--------------------------------------------------------------------------
    | Department Form Validation
    |--------------------------------------------------------------------------
    */

    appValidateForm(
        $('#department-form'),
        {
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            },

            
        },
        function (form) {

            var $form = $(form);

            var id = $('#department_id').val();

            var url;

            /*
            |--------------------------------------------------------------------------
            | Add / Update URL
            |--------------------------------------------------------------------------
            */

            if (id && id > 0) {

                url = admin_url
                    + 'student/update_department/'
                    + id;

            } else {

                url = admin_url
                    + 'student/add_department';
            }


            /*
            |--------------------------------------------------------------------------
            | Submit Button
            |--------------------------------------------------------------------------
            */

            var $button = $form.find(
                'button[type="submit"]'
            );

            var originalText = $button.html();

            $button.prop(
                'disabled',
                true
            );

            $button.html(
                '<i class="fa fa-spinner fa-spin"></i> '
                + 'Saving...'
            );


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: url,

                type: 'POST',

                data: $form.serialize(),

                dataType: 'json',

                success: function (response) {

                    if (response.success) {

                        alert_float(
                            'success',
                            response.message
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Close Modal
                        |--------------------------------------------------------------------------
                        */

                        $('#department_modal')
                            .modal('hide');


                        /*
                        |--------------------------------------------------------------------------
                        | Reset Form
                        |--------------------------------------------------------------------------
                        */

                        $form[0].reset();

                        $('#department_id')
                            .val('');


                        /*
                        |--------------------------------------------------------------------------
                        | Reset Modal Title
                        |--------------------------------------------------------------------------
                        */

                        $('#department_modal .modal-title')
                            .text(
                                'New Department'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Reload Department DataTable
                        |--------------------------------------------------------------------------
                        */

                        $('.table-departments')
                            .DataTable()
                            .ajax.reload(
                                null,
                                false
                            );

                    } else {

                        alert_float(
                            'danger',
                            response.message
                        );
                    }
                },


                error: function (xhr) {

                    console.log(
                        'Department AJAX Error:',
                        xhr.responseText
                    );

                    alert_float(
                        'danger',
                        'Something went wrong. Please try again.'
                    );
                },


                complete: function () {

                    $button
                        .prop(
                            'disabled',
                            false
                        )
                        .html(
                            originalText
                        );
                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Department Modal Reset
    |--------------------------------------------------------------------------
    */

    $('#department_modal').on(
        'hidden.bs.modal',
        function () {

            $('#department-form')[0]
                .reset();

            $('#department_id')
                .val('');

            $('#status')
                .val('1');

            $('#department_modal .modal-title')
                .text(
                    'New Department'
                );


            /*
            |--------------------------------------------------------------------------
            | Reset Validation
            |--------------------------------------------------------------------------
            */

            if (
                $('#department-form')
                    .data('validator')
            ) {

                $('#department-form')
                    .validate()
                    .resetForm();
            }


            $('#department-form')
                .find('.form-group')
                .removeClass(
                    'has-error'
                );

        }
    );

});


/*
|--------------------------------------------------------------------------
| Edit Department
|--------------------------------------------------------------------------
*/

function editDepartment(id)
{
    $.ajax({
        url: admin_url + 'student/get_department/' + id,
        type: 'GET',
        dataType: 'json',

        success: function (response) {

            console.log('EDIT RESPONSE:', response);

            if (!response.success) {

                alert_float(
                    'danger',
                    response.message
                );

                return;
            }

            var department = response.data;

            console.log('EDIT DATA:', department);

            /*
            |--------------------------------------------------------------------------
            | Hidden ID
            |--------------------------------------------------------------------------
            */

            $('#department-form')
                .find('[name="id"]')
                .val(department.id);


            /*
            |--------------------------------------------------------------------------
            | Department Name
            |--------------------------------------------------------------------------
            */

            $('#department-form')
                .find('[name="name"]')
                .val(department.name);


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $('#department-form')
                .find('[name="status"]')
                .val(String(department.status))
                .trigger('change');


            /*
            |--------------------------------------------------------------------------
            | Modal Title
            |--------------------------------------------------------------------------
            */

            $('#department_modal .modal-title')
                .text('Edit Department');


            /*
            |--------------------------------------------------------------------------
            | Open Modal
            |--------------------------------------------------------------------------
            */

            $('#department_modal')
                .modal('show');
        },

        error: function (xhr) {

            console.log(
                'GET DEPARTMENT ERROR:',
                xhr.responseText
            );

            alert_float(
                'danger',
                'Unable to load department data.'
            );
        }
    });
}