<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade"
     id="course_modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="courseModalLabel">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <?= form_open(admin_url('student/add_course'), [
                'id' => 'course-form',
                'method' => 'post'
            ]); ?>


            <!-- Modal Header -->
            <div class="modal-header">

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

                <h4 class="modal-title" id="courseModalLabel">
                    <?= _l('new_course'); ?>
                </h4>

            </div>


            <!-- Modal Body -->
            <div class="modal-body">

                <!-- Course Name -->
                <?= render_input(
                    'name',
                    'course_name',
                    '',
                    'text',
                    [
                        'id' => 'course_name',
                        'autocomplete' => 'off'
                    ]
                ); ?>


                <!-- Course Code -->
                <?= render_input(
                    'course_code',
                    'course_code',
                    '',
                    'text',
                    [
                        'id' => 'course_code',
                        'autocomplete' => 'off'
                    ]
                ); ?>


                <!-- Duration -->
                <?= render_input(
                    'duration',
                    'duration',
                    '',
                    'text',
                    [
                        'id' => 'duration',
                        'placeholder' => 'e.g. 3 Years',
                        'autocomplete' => 'off'
                    ]
                ); ?>


                <!-- Status -->
                <?= render_select(
                    'status',
                    [
                        [
                            'id' => '1',
                            'name' => _l('active')
                        ],
                        [
                            'id' => '0',
                            'name' => _l('inactive')
                        ],
                    ],
                    ['id', 'name'],
                    'status',
                    '1'
                ); ?>


                <!-- Course ID -->
                <input type="hidden"
                       name="id"
                       id="course_id">

            </div>


            <!-- Modal Footer -->
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