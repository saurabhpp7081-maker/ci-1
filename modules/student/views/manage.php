<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">

        <div class="panel_s">

            <div class="panel-body">

                <div class="clearfix">

                    <h4 class="pull-left">
                        Students
                    </h4>

                    <div class="pull-right">

                        <a href="#" class="btn btn-primary"
                            data-toggle="modal"
                            data-target="#student_modal">

                            <i class="fa fa-plus"></i>
                            Add Student

                        </a>

                    </div>

                </div>

                <hr>

                <table class="table table-students">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Admission No</th>

                            <th>Name</th>

                            <th>Phone</th>

                            <th>Course</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (!empty($students)) { ?>

                            <?php foreach ($students as $student) { ?>

                                <tr>

                                    <td><?= $student['id']; ?></td>

                                    <td><?= $student['admission_no']; ?></td>

                                    <td><?= $student['full_name']; ?></td>

                                    <td><?= $student['phone']; ?></td>

                                    <td><?= $student['course']; ?></td>

                                    <td>

                                        <?php if ($student['status'] == 1) { ?>

                                            <span class="label label-success">
                                                Active
                                            </span>

                                        <?php } else { ?>

                                            <span class="label label-danger">
                                                Inactive
                                            </span>

                                        <?php } ?>

                                    </td>

                                    <td>

                                        <a href="javascript:void(0);"
                                            onclick="editStudent(<?= $student['id']; ?>)"
                                            class="btn btn-default btn-icon">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <a href="javascript:void(0)"
                                            onclick="deleteStudent(<?= $student['id']; ?>)">
                                            Delete
                                        </a>

                                    </td>

                                </tr>

                            <?php } ?>

                        <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
</div>

<?php $this->load->view('student/student_modal'); ?>

<?php init_tail(); ?>

</body>

</html>