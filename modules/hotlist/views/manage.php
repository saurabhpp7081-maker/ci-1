<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content" id="vueApp">
        <div class="row">
            <div class="col-md-12">
                <div class="leads-overview tw-mb-6<?= $isKanBan ? ' hide' : ''; ?>">
                    <h4 class="tw-my-0 tw-font-bold tw-text-xl tw-mb-2">
                        <?= _l('hotlist_manage'); ?>
                    </h4>
                    <div class="tw-grid tw-gap-2 sm:tw-grid-flow-col sm:tw-auto-cols-max tw-overflow-x-auto">
                        <?php foreach ($summary as $status) { ?>
                            <?php if (isset($status['junk']) || isset($status['lost'])) { ?>
                                <span class="label label-danger" data-toggle="tooltip">
                                    <?= $status['total']; ?>
                                    <?= e($status['name']); ?>
                                    -
                                    <?= $status['percent']; ?>%
                                </span>
                            <?php } else { ?>
                                <button type="button"
                                    @click="extra.leadsRules = <?= app\services\utilities\Js::from($table->findRule('status')->setValue([$status['id']])); ?>"
                                    class="tw-bg-transparent tw-border tw-border-solid tw-border-neutral-300 tw-shadow-sm tw-py-1 tw-px-2 tw-rounded-lg tw-text-sm hover:tw-bg-neutral-200/60 tw-text-neutral-600 hover:tw-text-neutral-600 focus:tw-text-neutral-600 text-left">
                                    <span class="tw-font-semibold tw-mr-1 rtl:tw-ml-1">
                                        <?= e($status['total']); ?>
                                    </span>
                                    <span class="tw-font-medium" style="color:<?= e($status['color']); ?>">
                                        <?= e($status['name']); ?>
                                    </span>
                                </button>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="_buttons tw-mb-2">
                    <div class="tw-flex tw-items-center tw-justify-between tw-space-x-2 rtl:tw-space-x-reverse">
                        <div class="tw-flex tw-items-center tw-space-x-1 rtl:tw-space-x-reverse">
                            <a href="#" onclick="init_hotlist_lead(); return false;" class="btn btn-primary">
                                <i class="fa-regular fa-plus"></i>
                                <?= _l('new_lead'); ?>
                            </a>
                            <a href="<?= admin_url('hotlist/switch_kanban/' . $switch_kanban); ?>"
                                class="btn btn-default hidden-xs !tw-px-3" data-toggle="tooltip" data-placement="top"
                                data-title="<?= $switch_kanban == 1 ? _l('leads_switch_to_kanban') : _l('switch_to_list_view'); ?>">
                                <?php if ($switch_kanban == 1) { ?>
                                    <i class="fa-solid fa-grip-vertical"></i>
                                <?php } else { ?>
                                    <i class="fa-solid fa-table-list"></i>
                                <?php } ?>
                            </a>
                            <?php if (is_admin() || get_option('allow_non_admin_members_to_import_leads') == '1') { ?>
                                <a href="<?= admin_url('hotlist/import'); ?>" class="hidden-xs btn btn-default">
                                    <i class="fa-solid fa-upload tw-mr-1"></i>
                                    <?= _l('import_leads'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div>
                            <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                                <div class="leads-search">
                                    <div data-toggle="tooltip" data-placement="top" data-title="<?= _l('search_by_tags'); ?>">
                                        <?= render_input('search', '', '', 'search', ['data-name' => 'search', 'onkeyup' => 'leads_kanban();', 'placeholder' => _l('leads_search')], [], 'no-margin') ?>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="tw-inline">
                                    <app-filters
                                        id="<?= $table->id(); ?>"
                                        view="<?= $table->viewName(); ?>"
                                        :rules="extra.leadsRules || <?= app\services\utilities\Js::from($this->input->get('status') ? $table->findRule('status')->setValue([$this->input->get('status')]) : []); ?>"
                                        :saved-filters="<?= $table->filtersJs(); ?>"
                                        :available-rules="<?= $table->rulesJs(); ?>">
                                    </app-filters>
                                </div>
                            <?php } ?>
                            <?= form_hidden('sort_type'); ?>
                            <?= form_hidden('sort', (get_option('default_leads_kanban_sort') != '' ? get_option('default_leads_kanban_sort_type') : '')); ?>
                        </div>
                    </div>
                </div>
                <div class="<?= $isKanBan ? '' : 'panel_s'; ?>">
                    <div class="<?= $isKanBan ? '' : 'panel-body'; ?>">
                        <div class="tab-content">
                            <?php if ($isKanBan) { ?>
                                <div class="active kan-ban-tab tw-mt-4" id="kan-ban-tab" style="overflow:auto;">
                                    <div class="kanban-leads-sort">
                                        <span class="bold"><?= _l('leads_sort_by'); ?>:</span>
                                        <a href="#" onclick="leads_kanban_sort('dateadded'); return false" class="dateadded">
                                            <?php if (get_option('default_leads_kanban_sort') == 'dateadded') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?= _l('leads_sort_by_datecreated'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('leadorder');return false;" class="leadorder">
                                            <?php if (get_option('default_leads_kanban_sort') == 'leadorder') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?= _l('leads_sort_by_kanban_order'); ?>
                                        </a>
                                        |
                                        <a href="#" onclick="leads_kanban_sort('lastcontact');return false;" class="lastcontact">
                                            <?php if (get_option('default_leads_kanban_sort') == 'lastcontact') {
                                                echo '<i class="kanban-sort-icon fa fa-sort-amount-' . strtolower(get_option('default_leads_kanban_sort_type')) . '"></i> ';
                                            } ?><?= _l('leads_sort_by_lastcontact'); ?>
                                        </a>
                                    </div>
                                    <div class="row">
                                        <div class="container-fluid leads-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="leads-table">
                                    <div class="col-md-12">
                                        <a href="#" data-toggle="modal" data-table=".table-hotlist-leads"
                                            data-target="#hotlist_leads_bulk_actions"
                                            class="hide bulk-actions-btn table-btn"><?= _l('bulk_actions'); ?></a>
                                        <div class="modal fade bulk_actions" id="hotlist_leads_bulk_actions" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title"><?= _l('bulk_actions'); ?></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php if (staff_can('delete', 'leads')) { ?>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" name="mass_delete" id="mass_delete">
                                                                <label for="mass_delete"><?= _l('mass_delete'); ?></label>
                                                            </div>
                                                            <hr class="mass_delete_separator" />
                                                        <?php } ?>
                                                        <div id="bulk_change">
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-primary checkbox-inline">
                                                                    <input type="checkbox" name="leads_bulk_mark_lost" id="leads_bulk_mark_lost" value="1">
                                                                    <label for="leads_bulk_mark_lost"><?= _l('lead_mark_as_lost'); ?></label>
                                                                </div>
                                                            </div>
                                                            <?= render_select('move_to_status_leads_bulk', $statuses, ['id', 'name'], 'ticket_single_change_status'); ?>
                                                            <?= render_select('move_to_source_leads_bulk', $sources, ['id', 'name'], 'lead_source'); ?>
                                                            <?= render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact'); ?>
                                                            <?= render_select('assign_to_leads_bulk', $staff, ['staffid', ['firstname', 'lastname']], 'leads_dt_assigned'); ?>
                                                            <div class="form-group">
                                                                <?= '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                                                <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
                                                            </div>
                                                            <hr />
                                                            <div class="form-group no-mbot">
                                                                <div class="radio radio-primary radio-inline">
                                                                    <input type="radio" name="leads_bulk_visibility" id="leads_bulk_public" value="public">
                                                                    <label for="leads_bulk_public"><?= _l('lead_public'); ?></label>
                                                                </div>
                                                                <div class="radio radio-primary radio-inline">
                                                                    <input type="radio" name="leads_bulk_visibility" id="leads_bulk_private" value="private">
                                                                    <label for="leads_bulk_private"><?= _l('private'); ?></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= _l('close'); ?></button>
                                                        <a href="#" class="btn btn-primary" onclick="hotlist_bulk_action(this); return false;"><?= _l('confirm'); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
            <?php
            $table_data = [];
            $_table_data = [
                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="hotlist-leads"><label></label></div>',
                [
                    'name'     => _l('the_number_sign'),
                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
                ],
                [
                    'name'     => _l('leads_dt_name'),
                    'th_attrs' => ['class' => 'toggleable', 'id' => 'th-name'],
                ],
            ];

            if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                $_table_data[] = [
                    'name'     => _l('gdpr_consent') . ' (' . _l('gdpr_short') . ')',
                    'th_attrs' => ['id' => 'th-consent', 'class' => 'not-export'],
                ];
            }

            $_table_data[] = [
                'name'     => _l('lead_company'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_email'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-email'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_phonenumber'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_lead_value'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-lead-value'],
            ];
            $_table_data[] = [
                'name'     => _l('tags'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-tags'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_assigned'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-assigned'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_status'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-status'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_source'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-source'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_last_contact'),
                'th_attrs' => ['class' => 'toggleable', 'id' => 'th-last-contact'],
            ];
            $_table_data[] = [
                'name'     => _l('leads_dt_datecreated'),
                'th_attrs' => ['class' => 'date-created toggleable', 'id' => 'th-date-created'],
            ];

            foreach ($_table_data as $_t) {
                array_push($table_data, $_t);
            }

            $custom_fields = get_custom_fields('leads', ['show_on_table' => 1]);

            foreach ($custom_fields as $field) {
                array_push($table_data, [
                    'name'     => $field['name'],
                    'th_attrs' => ['data-type' => $field['type'], 'data-custom-field' => 1],
                ]);
            }

            $table_data = hooks()->apply_filters('leads_table_columns', $table_data);
            ?>
            <div class="panel-table-full">
                <?php
                render_datatable(
                    $table_data,
                    'hotlist-leads',
                    ['customizable-table number-index-2'],
                    [
                        'id'                         => 'hotlist-leads',
                        'data-last-order-identifier' => 'hotlist-leads',
                        'data-default-order'         => get_table_last_order('leads'),
                    ]
                );
                ?>
            </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script id="hidden-columns-hotlist-leads" type="text/json">
    <?= get_staff_meta(get_staff_user_id(), 'hidden-columns-table-leads'); ?>
</script>
<style>
    #lead_form .hotlist-input-error,
    #lead_form .form-group.has-error input,
    #lead_form .form-group.has-error textarea,
    #lead_form .form-group.has-error select {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.15) !important;
    }

    #lead_form .hotlist-select-error>.dropdown-toggle {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 1px rgba(220, 53, 69, 0.15) !important;
    }

    #lead_form label.hotlist-inline-error {
        color: #dc3545 !important;
        font-size: 12px;
        font-weight: 400;
        margin-top: 6px;
        display: block;
    }

    #lead_form label.hotlist-inline-error-block {
        width: 100%;
        clear: both;
    }

    #lead_form .input-group+label.hotlist-inline-error-block,
    #lead_form .bootstrap-select+label.hotlist-inline-error-block {
        display: block;
    }
</style>
<script>
    var leadUniqueValidationFields = <?= json_encode(get_option('lead_unique_validation')); ?>;
    var leadAttachmentsDropzone;
</script>
<?php include_once APPPATH . 'views/admin/leads/status.php'; ?>
<?php init_tail(); ?>
<script>
    $(function() {
        var $hotlistTable = $('table.table-hotlist-leads');

        if ($hotlistTable.length) {
            var hotlistConsentHeading = $hotlistTable.find('#th-consent');
            var hotlistTableNotSortable = [0];
            var hotlistTableNotSearchable = [0, $hotlistTable.find('#th-assigned').index()];

            if (hotlistConsentHeading.length > 0) {
                hotlistTableNotSortable.push(hotlistConsentHeading.index());
                hotlistTableNotSearchable.push(hotlistConsentHeading.index());
            }

            window.table_hotlist_leads = initDataTable(
                $hotlistTable,
                admin_url + 'hotlist/table',
                hotlistTableNotSearchable,
                hotlistTableNotSortable,
                typeof LeadsServerParams !== 'undefined' ? LeadsServerParams : {},
                [$hotlistTable.find('th.date-created').index(), 'desc']
            );
            table_leads = window.table_hotlist_leads;
        }

        window.leads_kanban = function(search) {
            init_kanban(
                'hotlist/kanban',
                leads_kanban_update,
                '.leads-status',
                290,
                360,
                init_leads_status_sortable
            );
        };

        window.hotlist_bulk_action = function(event) {
            if (!confirm_delete()) {
                return;
            }

            var massDelete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};

            if (massDelete === false || typeof massDelete === 'undefined') {
                data.lost = $('#leads_bulk_mark_lost').prop('checked');
                data.status = $('#move_to_status_leads_bulk').val();
                data.assigned = $('#assign_to_leads_bulk').val();
                data.source = $('#move_to_source_leads_bulk').val();
                data.last_contact = $('#leads_bulk_last_contact').val();
                data.tags = $('#tags_bulk').tagit('assignedTags');
                data.visibility = $('input[name="leads_bulk_visibility"]:checked').val();
                data.assigned = typeof data.assigned === 'undefined' ? '' : data.assigned;
                data.visibility = typeof data.visibility === 'undefined' ? '' : data.visibility;

                if (data.status === '' &&
                    data.lost === false &&
                    data.assigned === '' &&
                    data.source === '' &&
                    data.last_contact === '' &&
                    data.tags.length === 0 &&
                    data.visibility === '') {
                    return;
                }
            } else {
                data.mass_delete = true;
            }

            var rows = table_hotlist_leads.find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') === true) {
                    ids.push(checkbox.val());
                }
            });

            data.ids = ids;
            $(event).addClass('disabled');

            setTimeout(function() {
                $.post(admin_url + 'hotlist/bulk_action', data)
                    .done(function() {
                        window.location.reload();
                    })
                    .fail(function(xhr) {
                        $('#lead-modal').modal('hide');
                        alert_float('danger', xhr.responseText);
                    });
            }, 200);
        };

        function hotlistLeadAction() {
            var leadId = $('#lead-modal').find('input[name="leadid"]').val();
            var action = admin_url + 'hotlist/lead';

            if (leadId) {
                action += '/' + leadId;
            }

            return action;
        }

        function hotlistLeadFormHandler(form) {
            form = $(form);

            var validator = form.data('validator');
            var data = form.serialize();
            var $saveButtons = $('#lead-modal').find('.lead-save-btn, #lead-form-submit');

            $saveButtons.prop('disabled', true).addClass('disabled');

            $.post(form.attr('action'), data)
                .done(function(response) {
                    response = typeof response === 'string' ? JSON.parse(response) : response;

                    if (!response || response.success === false) {
                        if (typeof alert_float === 'function') {
                            alert_float('danger', (response && response.message) ? response.message : 'Lead save failed');
                        }
                        return;
                    }

                    if (response.message !== '' && typeof alert_float === 'function') {
                        alert_float('success', response.message);
                    }

                    if (response.proposal_warning && response.proposal_warning != false) {
                        $('#lead_proposal_warning').removeClass('hide');
                        $('#lead-modal').animate({
                            scrollTop: 0
                        }, 800);
                    } else if (typeof _lead_init_data === 'function') {
                        _lead_init_data(response, response.id);
                    }

                    hotlistReloadLeads();
                })
                .fail(function(xhr) {
                    var response = xhr.responseJSON;

                    if (!response && xhr.responseText) {
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response = null;
                        }
                    }

                    if (response && response.errors && validator) {
                        validator.showErrors(response.errors);
                        hotlistRenderInlineErrors(form, response.errors);
                        if (typeof validator.focusInvalid === 'function') {
                            validator.focusInvalid();
                        }
                    } else if (response && response.errors) {
                        hotlistRenderInlineErrors(form, response.errors);
                    } else if (typeof alert_float === 'function') {
                        alert_float('danger', (response && response.message) ? response.message : (xhr.responseText || 'Lead save failed'));
                    }
                })
                .always(function() {
                    $saveButtons.prop('disabled', false).removeClass('disabled');
                });

            return false;
        }

        function hotlistClearInlineErrors($form) {
            $form.find('.form-group').removeClass('has-error');
            $form.find('label.hotlist-inline-error').remove();
            $form.find('.bootstrap-select').removeClass('hotlist-select-error');
            $form.find('input, textarea, select').removeClass('hotlist-input-error');
        }

        function hotlistRenderInlineErrors($form, errors) {
            if (!$form.length || !errors) {
                return;
            }

            hotlistClearInlineErrors($form);

            $.each(errors, function(fieldName, message) {
                var $field = $form.find('[name="' + fieldName + '"]');
                if (!$field.length) {
                    return;
                }

                var $group = $field.closest('.form-group');
                $group.addClass('has-error');
                $field.addClass('hotlist-input-error');

                if ($field.hasClass('selectpicker')) {
                    var $inputGroup = $field.closest('.input-group');
                    var $bootstrapSelect = $group.find('.bootstrap-select').first();

                    $bootstrapSelect.addClass('hotlist-select-error');

                    if ($inputGroup.length) {
                        $('<label class="error hotlist-inline-error hotlist-inline-error-block">' + message + '</label>').insertAfter($inputGroup);
                    } else if ($bootstrapSelect.length) {
                        $('<label class="error hotlist-inline-error hotlist-inline-error-block">' + message + '</label>').insertAfter($bootstrapSelect);
                    } else {
                        $('<label class="error hotlist-inline-error hotlist-inline-error-block">' + message + '</label>').insertAfter($field);
                    }
                } else {
                    $('<label class="error hotlist-inline-error">' + message + '</label>').insertAfter($field);
                }
            });
        }

        function hotlistCollectClientErrors($form) {
            var errors = {};

            if ($.trim($form.find('[name="name"]').val()) === '') {
                errors.name = 'This field is required.';
            }

            if ($.trim($form.find('[name="source"]').val()) === '') {
                errors.source = 'This field is required.';
            }

            if ($.trim($form.find('[name="status"]').val()) === '') {
                errors.status = 'This field is required.';
            }

            var email = $.trim($form.find('[name="email"]').val());
            if (email !== '') {
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    errors.email = 'Please enter a valid email address.';
                }
            }

            return errors;
        }

        function hotlistMarkRequiredLabels($form) {
            if (!$form.length) {
                return;
            }

            $.each(['name', 'source', 'status'], function(_, fieldName) {
                var $field = $form.find('[name="' + fieldName + '"]');
                var $label = $field.closest('.form-group').find('label.control-label').first();

                if ($label.length && $label.find('.req').length === 0) {
                    $label.prepend('<small class="req text-danger">* </small>');
                }
            });
        }

        function hotlistPrepareLeadForm() {
            var $form = $('#lead_form');

            if (!$form.length) {
                return $form;
            }

            $form.attr('action', hotlistLeadAction());

            if (typeof validate_lead_form === 'function') {
                try {
                    if ($form.data('validator')) {
                        if (typeof $form.data('validator').destroy === 'function') {
                            $form.data('validator').destroy();
                        }
                        $form.removeData('validator');
                    }
                    validate_lead_form();
                } catch (e) {
                    console.warn('Hotlist lead validation fallback:', e);
                }
            }

            if ($form.data('validator')) {
                $form.data('validator').settings.submitHandler = hotlistLeadFormHandler;
            }

            hotlistMarkRequiredLabels($form);

            return $form;
        }

        function hotlistReloadLeads() {
            if ($.fn.DataTable.isDataTable('.table-hotlist-leads') && typeof table_hotlist_leads !== 'undefined') {
                table_hotlist_leads.ajax.reload(null, false);
            } else if ($('body').hasClass('kan-ban-body')) {
                leads_kanban();
            }
        }

      window.init_hotlist_lead = function(id = '')  {
            $.get(admin_url + 'hotlist/lead/' + id, function(response) {
                response = JSON.parse(response);
                _lead_init_data(response, id);
            });
        }
        window.init_hotlist_lead_edit = function(id) {
            if (!id) {
                return;
            }

           $.get(admin_url + 'hotlist/lead/' + id + '?edit=true', function(response) {
                response = JSON.parse(response);
                _lead_init_data(response, id);
            });
            
        }
        

        
        leads_kanban();

        $(document).off('shown.bs.modal.hotlist', '#lead-modal');
        $(document).on('shown.bs.modal.hotlist', '#lead-modal', function() {
            hotlistPrepareLeadForm();
        });

        $(document).off('submit.hotlist', '#lead_form');
        $(document).on('submit.hotlist', '#lead_form', function(e) {
            e.preventDefault();

            var $form = hotlistPrepareLeadForm();
            var validator = $form.data('validator');
            var clientErrors = hotlistCollectClientErrors($form);

            if (Object.keys(clientErrors).length) {
                hotlistRenderInlineErrors($form, clientErrors);
                if (typeof alert_float === 'function') {
                    alert_float('warning', 'Please fix the highlighted errors and try again.');
                }
                return false;
            }

            if (validator) {
                if (!$form.valid()) {
                    var validatorErrors = validator.errorMap || {};
                    hotlistRenderInlineErrors($form, validatorErrors);
                    return false;
                }

                return hotlistLeadFormHandler(this);
            }

            return hotlistLeadFormHandler(this);
        });

        $(document).off('click.hotlist', '.lead-save-btn, #lead-form-submit');
        $(document).on('click.hotlist', '.lead-save-btn, #lead-form-submit', function(e) {
            e.preventDefault();
            $('#lead_form').trigger('submit');
            return false;
        });

        $(document).off('click.hotlist', '#lead-modal [lead-edit]');
        $(document).on('click.hotlist', '#lead-modal [lead-edit]', function(e) {
            var leadId = $('#lead-modal').find('input[name="leadid"]').val();

            e.preventDefault();
            e.stopImmediatePropagation();

            if (leadId && !$('#lead-modal').find('input[name="company_name"]').length) {
                window.init_hotlist_lead_edit(leadId);
                return false;
            }

            setTimeout(hotlistPrepareLeadForm, 50);
            return false;
        });

        $(document).off('input.hotlist change.hotlist', '#lead_form input, #lead_form textarea, #lead_form select');
        $(document).on('input.hotlist change.hotlist', '#lead_form input, #lead_form textarea, #lead_form select', function() {
            var $form = $('#lead_form');
            hotlistClearInlineErrors($form);
        });

        $(document).off('ajaxComplete.hotlist');
        $(document).on('ajaxComplete.hotlist', function(event, xhr, settings) {
            if (settings && settings.url && (settings.url.indexOf('leads/lead') !== -1 || settings.url.indexOf('hotlist/lead') !== -1)) {
                setTimeout(hotlistPrepareLeadForm, 50);
            }
        });

        $('#leads_bulk_mark_lost').on('change', function() {
            $('#move_to_status_leads_bulk').prop('disabled', $(this).prop('checked') == true);
            $('#move_to_status_leads_bulk').selectpicker('refresh');
        });
        $('#move_to_status_leads_bulk').on('change', function() {
            if ($(this).selectpicker('val') != '') {
                $('#leads_bulk_mark_lost').prop('disabled', true);
                $('#leads_bulk_mark_lost').prop('checked', false);
            } else {
                $('#leads_bulk_mark_lost').prop('disabled', false);
            }
        });
    });
</script>


</body>

</html>
