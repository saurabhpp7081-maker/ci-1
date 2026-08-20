<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

    <div class="content">

        <div class="panel_s">

            <div class="panel-body">

                <!-- Header -->
                <div class="clearfix">

                    <h4 class="pull-left">
                        <?= _l('courses'); ?>
                    </h4>

                    <div class="pull-right">

                        <a href="#"
                           class="btn btn-primary"
                           data-toggle="modal"
                           data-target="#course_modal">

                            <i class="fa fa-plus"></i>
                            <?= _l('new_course'); ?>

                        </a>

                    </div>

                </div>

                <hr>

                <?php

                // -------------------------------------------------
                // Courses DataTable Columns
                // -------------------------------------------------

                $table_data = [
                    _l('course_name'),
                    _l('course_code'),
                    _l('duration'),
                    _l('status'),
                    _l('options'),
                ];

                render_datatable(
                    $table_data,
                    'courses',
                    ['table-striped', 'table-hover']
                );

                ?>

            </div>

        </div>

    </div>

</div>


<?php

// ---------------------------------------------------------
// Course Add/Edit Modal
// ---------------------------------------------------------

$this->load->view(
    'student/courses/course_modal'
);

?>


<?php init_tail(); ?>