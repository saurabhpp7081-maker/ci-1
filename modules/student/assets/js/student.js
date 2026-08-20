$(function () {

    // Student Form Validation & Submission Handler
    appValidateForm(
        $('#student-form'),
        {
            admission_no: 'required',
            roll_no: 'required',
            full_name: 'required',
            email: {
                required: true,
                email: true
            },
            phone: 'required',
            gender: 'required',
            dob: 'required',
            department_id: 'required',
            course_id: 'required',
            status: 'required'
        },
        function (form) {
            var $form = $(form);
            var url = $form.attr('action');

            if (!url) {
                var studentId = $('#student_id').val();
                if (studentId && studentId > 0) {
                    url = admin_url + 'student/update/' + studentId;
                } else {
                    url = admin_url + 'student/store';
                }
            }

            var $button = $form.find('button[type="submit"]');
            var originalText = $button.html();

            // Disable button and show spinner
            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize(),
                dataType: 'text', // Read as raw text to prevent parse errors if HTML/redirect is returned
                success: function (rawResponse) {
                    console.log('Student Submit Raw Response:', rawResponse);

                    var response = null;
                    if (typeof rawResponse === 'object') {
                        response = rawResponse;
                    } else if (typeof rawResponse === 'string') {
                        try {
                            response = JSON.parse(rawResponse);
                        } catch (e) {
                            console.warn('Response is not valid JSON string, handling as fallback HTML/Text response.');
                        }
                    }

                    // Check if JSON response explicitly states success or failure
                    if (response && typeof response.success !== 'undefined') {
                        if (response.success) {
                            var successMsg = response.message || 'Student saved successfully.';
                            alert_float('success', successMsg);
                            resetAndCloseStudentForm($form);
                        } else {
                            var errorMsg = response.message || 'Failed to save student.';
                            alert_float('danger', errorMsg);
                        }
                    } else {
                        // Fallback handling if backend sent HTML/redirect response on successful insert/update
                        alert_float('success', 'Student saved successfully.');
                        resetAndCloseStudentForm($form);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Student Submit AJAX Error Status:', status);
                    console.error('Student Submit AJAX Error Detail:', error);
                    console.error('Student Submit Response Text:', xhr.responseText);

                    var errorMsg = 'Something went wrong while saving student.';

                    if (xhr.responseText) {
                        try {
                            var errJson = JSON.parse(xhr.responseText);
                            if (errJson && errJson.message) {
                                errorMsg = errJson.message;
                            }
                        } catch (e) {
                            // Response is not JSON
                        }
                    }

                    alert_float('danger', errorMsg);
                },
                complete: function () {
                    $button.prop('disabled', false);
                    $button.html(originalText);
                }
            });

            return false;
        }
    );

    // Helper function to reset and close student form
    function resetAndCloseStudentForm($form) {
        // Close modal
        $('#student_modal').modal('hide');

        // Reset form
        $form[0].reset();
        $('#student_id').val('');

        // Reset selectpicker
        $form.find('.selectpicker').val('').selectpicker('refresh');

        // Reset Validation error states
        if ($form.data('validator')) {
            $form.validate().resetForm();
        }
        $form.find('.form-group').removeClass('has-error');

        // Reload DataTables
        if ($.fn.DataTable.isDataTable('.table-students')) {
            $('.table-students').DataTable().ajax.reload(null, false);
        }

        // Reset form action & modal title back to default (Store / New Student)
        $form.attr('action', admin_url + 'student/store');
        $('#student_modal .modal-title').text('New Student');
        $('#student_modal h4').text('New Student');
    }


    // Student Modal Reset on Hidden
    $('#student_modal').on('hidden.bs.modal', function () {
        var $form = $('#student-form');

        $form[0].reset();
        $('#student_id').val('');

        $form.attr('action', admin_url + 'student/store');

        $('#student_modal .modal-title').text('New Student');
        $('#student_modal h4').text('New Student');

        // Reset Selectpicker
        $form.find('.selectpicker').val('').selectpicker('refresh');

        // Reset Validation
        if ($form.data('validator')) {
            $form.validate().resetForm();
        }

        $form.find('.form-group').removeClass('has-error');
    });


    // Students DataTable
    initDataTable(
        '.table-students',
        admin_url + 'student/table',
        undefined,
        undefined,
        undefined,
        []
    );


    // Departments DataTable
    initDataTable(
        '.table-departments',
        admin_url + 'student/departments_table',
        [2],
        [2]
    );


    // Courses DataTable
    initDataTable(
        '.table-courses',
        admin_url + 'student/courses_table',
        [2],
        [2]
    );


    // Department Form Validation
    appValidateForm(
        $('#department-form'),
        {
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            }
        },
        function (form) {
            var $form = $(form);
            var id = $form.find('[name="id"]').val();
            var url;

            // Add / Update URL
            if (id && id > 0) {
                url = admin_url + 'student/update_department/' + id;
            } else {
                url = admin_url + 'student/add_department';
            }

            // Submit Button
            var $button = $form.find('button[type="submit"]');
            var originalText = $button.html();

            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            // AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);

                        // Close Modal
                        $('#department_modal').modal('hide');

                        // Reset Form
                        $form[0].reset();
                        $form.find('[name="id"]').val('');

                        // Reset Status
                        $form.find('[name="status"]').val('1').selectpicker('refresh');

                        // Reset Modal Title
                        $('#department_modal .modal-title').text('New Department');

                        // Reload Department DataTable
                        if ($.fn.DataTable.isDataTable('.table-departments')) {
                            $('.table-departments').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function (xhr) {
                    console.log('Department AJAX Error:', xhr.responseText);
                    alert_float('danger', 'Something went wrong. Please try again.');
                },
                complete: function () {
                    $button.prop('disabled', false);
                    $button.html(originalText);
                }
            });
        }
    );


    // Department Modal Reset
    $('#department_modal').on('hidden.bs.modal', function () {
        var $form = $('#department-form');

        $form[0].reset();
        $form.find('[name="id"]').val('');

        // Reset Status
        $form.find('[name="status"]').val('1').selectpicker('refresh');

        // Reset Modal Title
        $('#department_modal .modal-title').text('New Department');

        // Reset Validation
        if ($form.data('validator')) {
            $form.validate().resetForm();
        }

        $form.find('.form-group').removeClass('has-error');
    });


    // Course Form Validation
    appValidateForm(
        $('#course-form'),
        {
            name: {
                required: true,
                minlength: 2,
                maxlength: 100
            }
        },
        function (form) {
            var $form = $(form);
            var id = $form.find('[name="id"]').val();
            var url;

            // Add / Update URL
            if (id && id > 0) {
                url = admin_url + 'student/update_course/' + id;
            } else {
                url = admin_url + 'student/add_course';
            }

            // Submit Button
            var $button = $form.find('button[type="submit"]');
            var originalText = $button.html();

            $button.prop('disabled', true);
            $button.html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            // AJAX
            $.ajax({
                url: url,
                type: 'POST',
                data: $form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert_float('success', response.message);

                        // Close Modal
                        $('#course_modal').modal('hide');

                        // Reset Form
                        $form[0].reset();
                        $form.find('[name="id"]').val('');

                        // Reset Status
                        $form.find('[name="status"]').val('1').selectpicker('refresh');

                        // Reset Modal Title
                        $('#course_modal .modal-title').text('New Course');

                        // Reload Course DataTable
                        if ($.fn.DataTable.isDataTable('.table-courses')) {
                            $('.table-courses').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function (xhr) {
                    console.log('Course AJAX Error:', xhr.responseText);
                    alert_float('danger', 'Something went wrong. Please try again.');
                },
                complete: function () {
                    $button.prop('disabled', false);
                    $button.html(originalText);
                }
            });
        }
    );


    // Course Modal Reset
    $('#course_modal').on('hidden.bs.modal', function () {
        var $form = $('#course-form');

        $form[0].reset();
        $form.find('[name="id"]').val('');

        // Reset Status
        $form.find('[name="status"]').val('1').selectpicker('refresh');

        // Reset Modal Title
        $('#course_modal .modal-title').text('New Course');

        // Reset Validation
        if ($form.data('validator')) {
            $form.validate().resetForm();
        }

        $form.find('.form-group').removeClass('has-error');
    });

});


// Edit Student
function editStudent(id) {
    $.ajax({
        url: admin_url + 'student/get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (!response.success) {
                alert_float('danger', response.message);
                return;
            }

            var student = response.data;

            // Student ID
            $('#student_id').val(student.id);

            // Basic Information
            $('#admission_no').val(student.admission_no);
            $('#roll_no').val(student.roll_no);
            $('#full_name').val(student.full_name);
            $('#father_name').val(student.father_name);
            $('#mother_name').val(student.mother_name);
            $('#email').val(student.email);
            $('#phone').val(student.phone);
            $('#dob').val(student.dob);
            $('#address').val(student.address);

            // Gender
            $('#student-form')
                .find('[name="gender"]')
                .val(String(student.gender))
                .selectpicker('refresh');

            // Department
            $('#student-form')
                .find('[name="department_id"]')
                .val(String(student.department_id))
                .selectpicker('refresh');

            // Course
            $('#student-form')
                .find('[name="course_id"]')
                .val(String(student.course_id))
                .selectpicker('refresh');

            // Status
            $('#student-form')
                .find('[name="status"]')
                .val(String(student.status))
                .selectpicker('refresh');

            // Update URL
            $('#student-form').attr(
                'action',
                admin_url + 'student/update/' + student.id
            );

            // Modal heading
            $('#student_modal .modal-title').text('Edit Student');
            $('#student_modal h4').text('Edit Student');

            // Show modal
            $('#student_modal').modal('show');
        },
        error: function (xhr) {
            console.log('GET STUDENT ERROR:', xhr.responseText);
            alert_float('danger', 'Unable to load student data.');
        }
    });
}


// Delete Student
function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        window.location.href = admin_url + 'student/delete/' + id;
    }
}


// Edit Department
function editDepartment(id) {
    $.ajax({
        url: admin_url + 'student/get_department/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log('EDIT DEPARTMENT RESPONSE:', response);

            if (!response.success) {
                alert_float('danger', response.message);
                return;
            }

            var department = response.data;
            console.log('EDIT DEPARTMENT DATA:', department);

            var $form = $('#department-form');

            // Department ID
            $form.find('[name="id"]').val(department.id);

            // Department Name
            $form.find('[name="name"]').val(department.name);

            // Status
            $form
                .find('[name="status"]')
                .val(String(department.status))
                .selectpicker('refresh');

            // Modal Title
            $('#department_modal .modal-title').text('Edit Department');

            // Open Modal
            $('#department_modal').modal('show');
        },
        error: function (xhr) {
            console.log('GET DEPARTMENT ERROR:', xhr.responseText);
            alert_float('danger', 'Unable to load department data.');
        }
    });
}


// Edit Course
function editCourse(id) {
    $.ajax({
        url: admin_url + 'student/get_course/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            console.log('EDIT COURSE RESPONSE:', response);

            if (!response.success) {
                alert_float('danger', response.message);
                return;
            }

            var course = response.data;
            console.log('EDIT COURSE DATA:', course);

            var $form = $('#course-form');

            // Course ID
            $form.find('[name="id"]').val(course.id);

            // Course Name
            $form.find('[name="name"]').val(course.name);

            // Status
            $form
                .find('[name="status"]')
                .val(String(course.status))
                .selectpicker('refresh');

            // Modal Title
            $('#course_modal .modal-title').text('Edit Course');

            // Open Modal
            $('#course_modal').modal('show');
        },
        error: function (xhr) {
            console.log('GET COURSE ERROR:', xhr.responseText);
            alert_float('danger', 'Unable to load course data.');
        }
    });
}