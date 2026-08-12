<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">

    <div class="content">

        <div class="panel_s">

            <div class="panel-body">

                <div class="clearfix">

                    <h4 class="pull-left">
                        Departments
                    </h4>

                    <div class="pull-right">

                        <a href="#"
                           class="btn btn-primary"
                           data-toggle="modal"
                           data-target="#department_modal">

                            <i class="fa fa-plus"></i>
                            New Department

                        </a>

                    </div>

                </div>

                <hr>

                <?php

                $table_data = [
                    'Name',
                    'Status',
                    'Options',
                ];

                render_datatable(
                    $table_data,
                    'departments',
                    ['table-striped', 'table-hover']
                );

                ?>

            </div>

        </div>

    </div>

</div>


<?php
$this->load->view(
    'student/departments/department_modal'
);
?>


<?php init_tail(); ?>
