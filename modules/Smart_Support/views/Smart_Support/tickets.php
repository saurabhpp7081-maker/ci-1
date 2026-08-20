<?php

defined('BASEPATH') or exit('No direct script access allowed');

init_head();
?>

<div id="wrapper">

    <div class="content">

        <div class="row">

            <div class="col-md-12">

                <div class="panel_s">

                    <div class="panel-body">

                        <!-- HEADER -->
                        <div class="row">

                            <div class="col-md-8">
                                <h4 class="no-margin">
                                    <?php echo _l('ssx_tickets'); ?>
                                </h4>
                            </div>

                            <div class="col-md-4 text-right">

                                <a
                                    href="<?php echo admin_url('smart_support/ticket'); ?>"
                                    class="btn btn-primary">
                                    <i class="fa fa-plus"></i>
                                    <?php echo _l('ssx_new_ticket'); ?>
                                </a>

                            </div>

                        </div>

                        <hr class="hr-panel-heading">

                        <!-- STATUS FILTERS -->
                        <div class="row">

                            <div class="col-md-12">

                                <div class="tw-flex tw-flex-wrap tw-gap-2">

                                    <a
                                        href="#"
                                        class="btn btn-default active ssx-status-tab"
                                        data-status="">
                                        <?php echo _l('ssx_all'); ?>
                                    </a>

                                    <a
                                        href="#"
                                        class="btn btn-default ssx-status-tab"
                                        data-status="1">
                                        <?php echo _l('ssx_open'); ?>
                                    </a>

                                    <a
                                        href="#"
                                        class="btn btn-default ssx-status-tab"
                                        data-status="2">
                                        <?php echo _l('ssx_in_progress'); ?>
                                    </a>

                                    <a
                                        href="#"
                                        class="btn btn-default ssx-status-tab"
                                        data-status="3">
                                        <?php echo _l('ssx_answered'); ?>
                                    </a>

                                    <a
                                        href="#"
                                        class="btn btn-default ssx-status-tab"
                                        data-status="4">
                                        <?php echo _l('ssx_on_hold'); ?>
                                    </a>

                                    <a
                                        href="#"
                                        class="btn btn-default ssx-status-tab"
                                        data-status="5">
                                        <?php echo _l('ssx_closed'); ?>
                                    </a>

                                </div>

                            </div>

                        </div>

                        <div class="clearfix"></div>

                        <!-- TICKETS TABLE -->
                        <div class="table-responsive mtop20">

                            <table
                                class="table table-striped"
                                id="ssx_tickets_table"
                                width="100%">

                                <thead>

                                    <tr>

                                        <th>
                                            <?php echo _l('ssx_ticket_id'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_subject'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_customer'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_status'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_priority'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_assigned_to'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_last_reply'); ?>
                                        </th>

                                        <th>
                                            <?php echo _l('ssx_actions'); ?>
                                        </th>

                                    </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php init_tail(); ?>