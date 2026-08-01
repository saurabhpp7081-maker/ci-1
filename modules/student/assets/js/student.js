

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
        status: 'required'
    });
$('#student_modal').on('hidden.bs.modal', function () {

    $('#student-form')[0].reset();

    if ($('#student-form').data('validator')) {
        $('#student-form').validate().resetForm();
    }

    $('#student-form')
        .find('.form-group')
        .removeClass('has-error');

});
});



function deleteStudent(id)
{
    alert(id);
}
