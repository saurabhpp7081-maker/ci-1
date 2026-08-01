<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="lead-wrapper<?= !empty($openEdit) ? ' open-edit' : ''; ?>">

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
        </button>

        <h4 class="modal-title">
            <i class="fa fa-user-plus"></i>
            <?= isset($lead) ? _l('edit') . ' ' . _l('lead') : _l('new_lead'); ?>
        </h4>
    </div>

    <?= form_open(isset($lead) ? admin_url('hotlist/lead/' . $lead->id) : admin_url('hotlist/lead'), ['id' => 'lead_form']); ?>
    <?= isset($lead) ? form_hidden('leadid', $lead->id) : ''; ?>

    <div class="modal-body">

        <div class="row">

            <!-- TOP -->
            <div class="col-md-4">
                <?php
                echo render_leads_status_select(
                    $statuses,
                    isset($lead) ? $lead->status : (isset($status_id) ? $status_id : ''),
                    'lead_add_edit_status'
                );
                ?>
            </div>

            <div class="col-md-4">
                <?= render_leads_source_select(
                    $sources,
                    isset($lead) ? $lead->source : get_option('leads_default_source'),
                    'lead_add_edit_source'
                ); ?>
            </div>

            <div class="col-md-4">
                <?= render_select(
                    'assigned',
                    $members,
                    ['staffid', ['firstname', 'lastname']],
                    'lead_add_edit_assigned',
                    isset($lead) ? $lead->assigned : get_staff_user_id()
                ); ?>
            </div>

            <div class="col-md-12">
                <hr class="mtop10 mbot15">
            </div>

            <!-- TAGS -->
            <div class="col-md-12">

                <div class="form-group">
                    <label for="tags"><?= _l('tags'); ?></label>

                    <input type="text"
                        class="tagsinput"
                        id="tags"
                        name="tags"
                        value="<?= isset($lead) ? prep_tags_input(get_tags_in($lead->id, 'lead')) : ''; ?>"
                        data-role="tagsinput">
                </div>

            </div>

            <!-- LEFT -->
            <div class="col-md-6">

                <?= render_input('name', 'lead_add_edit_name', isset($lead) ? $lead->name : ''); ?>

                <?= render_input('title', 'lead_title', isset($lead) ? $lead->title : ''); ?>

                <?= render_input('email', 'lead_add_edit_email', isset($lead) ? $lead->email : ''); ?>

                <?= render_input('website', 'lead_website', isset($lead) ? $lead->website : ''); ?>

                <?= render_input('phonenumber', 'lead_add_edit_phonenumber', isset($lead) ? $lead->phonenumber : ''); ?>

                <div class="form-group">

                    <label for="lead_value">
                        <?= _l('lead_value'); ?>
                    </label>

                    <div class="input-group">

                        <input type="number"
                            class="form-control"
                            name="lead_value"
                            value="<?= isset($lead) ? e($lead->lead_value) : ''; ?>">

                        <div class="input-group-addon">
                            <?= e($base_currency->symbol); ?>
                        </div>

                    </div>

                </div>

                <?= render_input(
                    'company_name',
                    'lead_company',
                    isset($lead_round['company_name']) && $lead_round['company_name'] !== ''
                        ? $lead_round['company_name']
                        : (isset($lead) ? $lead->company : '')
                ); ?>

            </div>

            <!-- RIGHT -->
            <div class="col-md-6">

                <?= render_textarea(
                    'address',
                    'lead_address',
                    isset($lead) ? $lead->address : '',
                    ['rows' => 2]
                ); ?>

                <?= render_input('city', 'lead_city', isset($lead) ? $lead->city : ''); ?>

                <?= render_input('state', 'lead_state', isset($lead) ? $lead->state : ''); ?>

                <?php
                $countries = get_all_countries();

                echo render_select(
                    'country',
                    $countries,
                    ['country_id', ['short_name']],
                    'lead_country',
                    isset($lead) ? $lead->country : get_option('customer_default_country'),
                    ['data-none-selected-text' => _l('dropdown_non_selected_tex')]
                );
                ?>

                <?= render_input('zip', 'lead_zip', isset($lead) ? $lead->zip : ''); ?>

                <?php if (!is_language_disabled()) { ?>

                    <div class="form-group">

                        <label for="default_language" class="control-label">
                            <?= _l('localization_default_language'); ?>
                        </label>

                        <select name="default_language"
                            id="default_language"
                            class="form-control selectpicker"
                            data-live-search="true"
                            data-none-selected-text="<?= _l('dropdown_non_selected_tex'); ?>">

                            <option value="">
                                <?= _l('system_default_string'); ?>
                            </option>

                            <?php foreach ($this->app->get_available_languages() as $availableLanguage) { ?>

                                <option value="<?= e($availableLanguage); ?>" <?= isset($lead) && $lead->default_language == $availableLanguage ? 'selected' : ''; ?>>
                                    <?= e(ucfirst($availableLanguage)); ?>
                                </option>

                            <?php } ?>

                        </select>

                    </div>

                <?php } ?>

                <!-- ROUND -->
                <div class="form-group mtop15">

                    <?php
                    echo render_select_with_input_group(
                        'round_id',
                        $rounds,
                        ['id', 'name'],
                        'hotlist_round',
                        isset($lead_round['round_id'])
    ? $lead_round['round_id']
    : '',
                        '<div class="input-group-btn">
                            <a href="#" class="btn btn-default" data-toggle="modal" data-target="#hotlist_round_modal">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>',
                        ['data-none-selected-text' => _l('dropdown_non_selected_tex')],
                        [],
                        '',
                        '',
                        true
                    );
                    ?>

                </div>

            </div>

            <!-- DESCRIPTION -->
            <div class="col-md-12">

                <?= render_textarea(
                    'description',
                    'lead_description',
                    isset($lead) ? $lead->description : ''
                ); ?>

            </div>

            <!-- CHECKBOX -->
            <div class="col-md-12 mtop10">

                <div class="checkbox checkbox-primary checkbox-inline">

                    <input type="checkbox"
                        name="is_public"
                        id="lead_public"
                        <?= isset($lead) && $lead->is_public == 1 ? 'checked' : ''; ?>>

                    <label for="lead_public">
                        <?= _l('lead_public'); ?>
                    </label>

                </div>

                <div class="checkbox checkbox-primary checkbox-inline">

                    <input type="checkbox"
                        name="contacted_today"
                        id="contacted_today"
                        <?= !isset($lead) || $lead->lastcontact == date('Y-m-d') ? 'checked' : ''; ?>>

                    <label for="contacted_today">
                        <?= _l('lead_add_edit_contacted_today'); ?>
                    </label>

                </div>

            </div>

        </div>

    </div>

    <div class="modal-footer">

        <button type="button"
            class="btn btn-default"
            data-dismiss="modal">

            <?= _l('close'); ?>

        </button>

        <button type="submit"
            class="btn btn-primary"
            id="lead-form-submit">

            <i class="fa fa-save"></i>
            <?= _l('submit'); ?>

        </button>

    </div>

    <?= form_close(); ?>

</div>
