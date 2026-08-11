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

