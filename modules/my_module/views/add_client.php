<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

    <div class="content">

         <div class="md:tw-flex md:tw-gap-6">
            <?php if (isset($client)) { ?>
            <div class="md:tw-max-w-64 tw-w-full">
                <?php $this->load->view('admin/clients/tabs'); ?>
            </div>
            <?php } ?>
            <div
                class="tw-mt-12 md:tw-mt-0 tw-w-full <?= isset($client) ? 'tw-max-w-6xl' : 'tw-mx-auto tw-max-w-4xl'; ?>">

                <?php if (! isset($client)) {?>
                <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                    <?= _l('add_new_customer'); ?>
                </h4>
                <?php } ?>

                <div class="panel_s">
                    <div class="panel-body">
                        <?php if (isset($client)) { ?>
                        <?= form_hidden('isedit'); ?>
                        <?= form_hidden('userid', $client->userid); ?>
                        <div class="clearfix"></div>
                        <?php } ?>
                        <div>
                            <div class="tab-content">
                                <?php $this->load->view((isset($tab) ? $tab['view'] : 'admin/clients/groups/profile')); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel-footer text-right tw-space-x-1" id="profile-save-section">
                        <?php if (! isset($client)) { ?>
                        <button class="btn btn-default save-and-add-contact customer-form-submiter">
                            <?= _l('save_customer_and_add_contact'); ?>
                        </button>
                        <?php } ?>
                        <button class="btn btn-primary only-save customer-form-submiter">
                            <?= _l('submit'); ?>
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
</div>

<?php init_tail(); ?>

<script>
    
    var vRules = {};
    if (app.options.company_is_required == 1) {
        vRules = {
            company: 'required',
        }
    }

    appValidateForm($('.client-form'), vRules);

    if (typeof(customer_id) == 'undefined') {
        $('#company').on('blur', function() {
            var company = $(this).val();
            var $companyExistsDiv = $('#company_exists_info');

            if (company == '') {
                $companyExistsDiv.addClass('hide');
                return;
            }

            $.post(admin_url + 'clients/check_duplicate_customer_name', {
                    company: company
                })
                .done(function(response) {
                    if (response) {
                        response = JSON.parse(response);
                        if (response.exists == true) {
                            $companyExistsDiv.removeClass('hide');
                            $companyExistsDiv.html('<div class="alert alert-info">' + response
                                .message + '</div>');
                        } else {
                            $companyExistsDiv.addClass('hide');
                        }
                    }
                });
        });
    }

    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]')
            .selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]')
            .selectpicker('val'));
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.client-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });



function delete_contact_profile_image(contact_id) {
    requestGet('clients/delete_contact_profile_image/' + contact_id).done(function() {
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src',
            '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
    });
}

function customerGoogleDriveSave(pickData) {
    saveCustomerProfileExternalFile(pickData, 'gdrive');
}

function saveCustomerProfileExternalFile(files, externalType) {
    $.post(admin_url + 'clients/add_external_attachment', {
        files: files,
        clientid: customer_id,
        external: externalType
    }).done(function() {
        window.location.reload();
    });
}

function validate_contact_form() {
    appValidateForm('#contact-form', {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {

                    var $sentSetPassword = $('input[name="send_set_password_email"]');

                    if ($('#contact input[name="contactid"]').val() == '' && $sentSetPassword.prop(
                            'checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
            <?php if (hooks()->apply_filters('contact_email_required', 'true') === 'true') { ?>
            required: true,
            <?php } ?>
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            <?php if (hooks()->apply_filters('contact_email_unique', 'true') === 'true') { ?>
            remote: {
                url: admin_url + "misc/contact_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#contact input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="contactid"]').val();
                    }
                }
            }
            <?php } ?>
        }
    }, contactFormHandler);
}

function contactFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);

    $("#contact input[type=file]").each(function() {
        if ($(this).val() === "") {
            $(this).prop('disabled', true);
        }
    });

    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
        
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
            if (typeof(response.is_individual) != 'undefined' && response.is_individual) {
                $('.new-contact').addClass('disabled');
                if (!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                    $('.new-contact-wrapper').attr('data-toggle', 'tooltip');
                }
            }
        }

        if ($.fn.DataTable.isDataTable('.table-contacts')) {
            $('.table-contacts').DataTable().ajax.reload(null, false);
        } else if ($.fn.DataTable.isDataTable('.table-all-contacts')) {
            $('.table-all-contacts').DataTable().ajax.reload(null, false);
        }

        if (response.proposal_warning && response.proposal_warning != false) {
            $('body').find('#contact_proposal_warning').removeClass('hide');
            $('body').find('#contact_update_proposals_emails').attr('data-original-email', response
                .original_email);
            $('#contact').animate({
                scrollTop: 0
            }, 800);
        } else {
            $('#contact').modal('hide');
        }
    }).fail(function(error) {
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    requestGet('clients/form_contact/' + client_id + '/' + contact_id).done(function(response) {
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal', '#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_contact_form();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}


function update_all_proposal_emails_linked_to_contact(contact_id) {
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post(admin_url + 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(
        response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }
        contacts_shared_ids.selectpicker('refresh');
        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post(admin_url + 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function save_longitude_and_latitude(clientid) {
    var data = {};
    data.latitude = $('#latitude').val();
    data.longitude = $('#longitude').val();
    $.post(admin_url + 'clients/save_longitude_and_latitude/' + clientid, data).done(function(response) {
        if (response == 'success') {
            alert_float('success', "<?php echo e(_l('updated_successfully', _l('client'))); ?>");
        }
        setTimeout(function() {
            window.location.reload();
        }, 1200);
    }).fail(function(error) {
        alert_float('danger', error.responseText);
    });
}

function fetch_lat_long_from_google_cprofile() {
    var data = {};
    data.address = $('#long_lat_wrapper').data('address');
    data.city = $('#long_lat_wrapper').data('city');
    data.country = $('#long_lat_wrapper').data('country');
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post(admin_url + 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>");
            } else {
                alert_float('danger', data.response.status + ' - ' + data.response.error_message);
            }
        }
    });
}

</script>

<script>
        $('.customer-form-submiter').on('click', function() {
        var form = $('.client-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });
</script>


















