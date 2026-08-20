<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="department_modal" tabindex="-1" role="dialog" aria-labelledby="departmentModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <?= form_open(admin_url('student/add_department'), [
                'id' => 'department-form',
                'method' => 'post'
            ]); ?>

            <div class="modal-header">
                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="departmentModalLabel">
                    <?= _l('new_department'); ?>
                </h4>
            </div>

            <div class="modal-body">

                <?= render_input(
                    'name',
                    'department_name',
                    '',
                    'text',
                    [
                        'id' => 'department_name',
                        'autocomplete' => 'off'
                    ]
                ); ?>

                <?= render_select(
                    'status',
                    [
                        ['id' => '1',  'name' => _l('active')],
                        ['id' => '2', 'name' => _l('inactive')],
                    ],
                    ['id', 'name'],
                    'status',
                    '',
                ); ?>

                <input type="hidden" name="id" id="department_id">

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-default"
                    data-dismiss="modal">
                    <?= _l('close'); ?>
                </button>

                <button type="submit"
                    class="btn btn-primary">
                    <?= _l('submit'); ?>
                </button>

            </div>

            <?= form_close(); ?>

        </div>
    </div>
</div>