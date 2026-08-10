<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
       <div class="panel_s">
    <div class="panel-body">

        <div class="clearfix">

            <h4 class="pull-left">
                <?= _l('students'); ?>
            </h4>

            <div class="pull-right">

                <a href="#"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#student_modal">

                    <i class="fa fa-plus"></i>
                    <?= _l('new_student'); ?>

                </a>

            </div>

        </div>

        <hr>

        <?php

        $table_data = [
            _l('id'),
            _l('admission_no'),
            _l('student_name'),
            _l('phone'),
            _l('course'),
            _l('status'),
            _l('options'),
        ];

        render_datatable(
            $table_data,
            'students',
            ['table-striped', 'table-hover']
        );

        ?>

    </div>
</div>
    </div>
</div>

<?php $this->load->view('student/student_modal'); ?>

<?php init_tail(); ?>

</body>

</html>