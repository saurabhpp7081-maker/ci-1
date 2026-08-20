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

                        <?php if (!isset($ticket)) { ?>

                            <?php
                            echo form_open_multipart(
                                admin_url('smart_support/ticket_create'),
                                [
                                    'id' => 'smart-support-ticket-form',
                                    'autocomplete' => 'off',
                                ]
                            );
                            ?>

                            <div class="row">

                                <div class="col-md-6">

                                    <?php

                                    $customer_options = [];

                                    if (!empty($customers)) {

                                        foreach ($customers as $customer) {

                                            $customer_options[] = [
                                                'userid' => $customer->userid,
                                                'company' => $customer->company,
                                            ];
                                        }
                                    }

                                    echo render_select(
                                        'customer_id',
                                        $customer_options,
                                        ['userid', 'company'],
                                        _l('smart_support_customer'),
                                        '',
                                        [
                                            'data-none-selected-text' =>
                                            _l('smart_support_select_customer'),

                                            'id' => 'smart_support_customer',
                                        ]
                                    );

                                    ?>

                                </div>

                                <div class="col-md-6">

                                    <?php

                                    echo render_input(
                                        'email',
                                        _l('smart_support_email'),
                                        '',
                                        'email',
                                        [
                                            'id' =>
                                            'smart_support_email',

                                            'placeholder' =>
                                            _l(
                                                'smart_support_enter_email'
                                            ),

                                            'autocomplete' =>
                                            'off',
                                        ]
                                    );

                                    ?>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6">

                                    <?php

                                    echo render_input(
                                        'subject',
                                        _l('smart_support_subject'),
                                        '',
                                        'text',
                                        [
                                            'id' =>
                                            'smart_support_subject',

                                            'placeholder' =>
                                            _l(
                                                'smart_support_enter_subject'
                                            ),

                                            'autocomplete' =>
                                            'off',
                                        ]
                                    );

                                    ?>

                                </div>

                                <div class="col-md-6">

                                    <?php

                                    $category_options = [];

                                    if (!empty($categories)) {
                                        foreach ($categories as $category) {

                                            $category_options[] = [
                                                'id' => is_object($category)
                                                    ? $category->id
                                                    : $category['id'],

                                                'name' => is_object($category)
                                                    ? $category->name
                                                    : $category['name'],
                                            ];
                                        }
                                    }

                                    echo render_select(
                                        'category_id',
                                        $category_options,
                                        ['id', 'name'],
                                        _l('smart_support_category'),
                                        '',
                                        [
                                            'data-none-selected-text' =>
                                            _l(
                                                'smart_support_select_category'
                                            ),

                                            'id' =>
                                            'smart_support_category',
                                        ]
                                    );

                                    ?>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <?php

                                    $priority_options = [];

                                    if (!empty($priorities)) {
                                        foreach ($priorities as $priority) {

                                            $priority_options[] = [
                                                'id' => is_object($priority)
                                                    ? $priority->priorityid
                                                    : $priority['priorityid'],

                                                'name' => is_object($priority)
                                                    ? $priority->name
                                                    : $priority['name'],
                                            ];
                                        }
                                    }

                                    echo render_select(
                                        'priority',
                                        $priority_options,
                                        ['id', 'name'],
                                        _l('smart_support_priority'),
                                        1,
                                        [
                                            'data-none-selected-text' =>
                                            _l('smart_support_select_priority'),

                                            'id' => 'smart_support_priority',
                                        ]
                                    );

                                    ?>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <?php

                                    echo render_textarea(
                                        'description',
                                        _l('smart_support_description'),
                                        '',
                                        [
                                            'id' =>
                                            'smart_support_description',

                                            'rows' => 8,

                                            'placeholder' =>
                                            _l(
                                                'smart_support_describe_issue'
                                            ),
                                        ]
                                    );

                                    ?>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12">

                                    <div class="form-group">

                                        <label for="smart_support_attachments">

                                            <?php
                                            echo _l(
                                                'smart_support_attachments'
                                            );
                                            ?>

                                        </label>

                                        <div
                                            id="smart-support-dropzone"
                                            style="
                                                border:1px dashed #d8dde3;
                                                background:#f8f9fa;
                                                border-radius:4px;
                                                padding:30px;
                                                text-align:center;
                                                color:#777;
                                            ">

                                            <i
                                                class="fa fa-cloud-upload fa-2x"
                                                style="
                                                    margin-bottom:10px;
                                                "></i>

                                            <div>

                                                <?php

                                                echo _l(
                                                    'smart_support_drag_drop'
                                                );

                                                ?>

                                            </div>

                                            <input
                                                type="file"
                                                name="attachments[]"
                                                id="smart_support_attachments"
                                                multiple
                                                style="
                                                    display:block;
                                                    margin:15px auto 0;
                                                ">

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-12 text-right">

                                    <a
                                        href="<?php
                                                echo admin_url(
                                                    'smart_support/tickets'
                                                );
                                                ?>"
                                        class="btn btn-default">

                                        <?php
                                        echo _l('cancel');
                                        ?>

                                    </a>

                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i class="fa fa-plus"></i>

                                        <?php

                                        echo _l(
                                            'smart_support_create_ticket'
                                        );

                                        ?>

                                    </button>

                                </div>

                            </div>

                            <?php echo form_close(); ?>

                        <?php } else { ?>

                            <div class="row">

                                <div class="col-md-12">

                                    <h4>

                                        <?php

                                        echo html_escape(
                                            $ticket->subject
                                        );

                                        ?>

                                    </h4>

                                    <hr>

                                    <div class="row">

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_customer'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->customer_name)
                                                ? html_escape(
                                                    $ticket->customer_name
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_email'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->email)
                                                ? html_escape(
                                                    $ticket->email
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_category'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->category_name)
                                                ? html_escape(
                                                    $ticket->category_name
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                    </div>

                                    <hr>

                                    <div class="row">

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_priority'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->priority_name)
                                                ? html_escape(
                                                    $ticket->priority_name
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_status'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->status_name)
                                                ? html_escape(
                                                    $ticket->status_name
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                        <div class="col-md-4">

                                            <strong>

                                                <?php

                                                echo _l(
                                                    'smart_support_created_at'
                                                );

                                                ?>

                                            </strong>

                                            <br>

                                            <?php

                                            echo !empty($ticket->created_at)
                                                ? html_escape(
                                                    $ticket->created_at
                                                )
                                                : '-';

                                            ?>

                                        </div>

                                    </div>

                                    <hr>

                                    <div class="ticket-message">

                                        <?php

                                        echo !empty($ticket->description)
                                            ? $ticket->description
                                            : '-';

                                        ?>

                                    </div>

                                    <?php if (!empty($replies)) { ?>

                                        <hr>

                                        <?php foreach (
                                            $replies
                                            as $reply
                                        ) { ?>

                                            <div
                                                class="panel panel-default">

                                                <div
                                                    class="panel-heading">

                                                    <strong>

                                                        <?php

                                                        echo !empty($reply->staff_name)
                                                            ? html_escape(
                                                                $reply->staff_name
                                                            )
                                                            : _l('staff');

                                                        ?>

                                                    </strong>

                                                    <span
                                                        class="pull-right">

                                                        <?php

                                                        echo !empty($reply->created_at)
                                                            ? time_ago(
                                                                $reply->created_at
                                                            )
                                                            : '';

                                                        ?>

                                                    </span>

                                                </div>

                                                <div
                                                    class="panel-body">

                                                    <?php

                                                    echo !empty($reply->message)
                                                        ? $reply->message
                                                        : '';

                                                    ?>

                                                </div>

                                            </div>

                                        <?php } ?>

                                    <?php } ?>

                                    <hr>

                                    <?php echo form_open(
                                        admin_url('smart_support/ticket_reply/' . (int) $ticket->id),
                                        [
                                            'method' => 'post',
                                            'id'     => 'smart-support-reply-form',
                                        ]
                                    ); ?>

                                    <div class="form-group">
                                        <?php echo render_textarea(
                                            'message',
                                            _l('smart_support_reply'),
                                            '',
                                            [
                                                'rows'        => 6,
                                                'required'    => true,
                                                'placeholder' => _l('smart_support_message_placeholder'),
                                            ]
                                        ); ?>
                                    </div>

                                    <button
                                        type="submit"
                                        class="btn btn-primary">
                                        <i class="fa fa-reply"></i>
                                        <?php echo _l('smart_support_send_reply'); ?>
                                    </button>

                                    <a
                                        href="<?php echo admin_url('smart_support/ticket/' . (int) $ticket->id); ?>"
                                        class="btn btn-default">
                                        <?php echo _l('smart_support_back'); ?>
                                    </a>

                                    <?php echo form_close(); ?>
                                </div>

                            </div>

                        <?php } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script>
    $(function() {

        $('#smart-support-ticket-form').on('submit', function() {

            var email = $('#smart_support_email').val().trim();

            if (email === '') {

                alert(
                    '<?php echo _l('smart_support_email_required'); ?>'
                );

                $('#smart_support_email').focus();

                return false;
            }

            var emailPattern =
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailPattern.test(email)) {

                alert(
                    '<?php echo _l(
                            'smart_support_valid_email_required'
                        ); ?>'
                );

                $('#smart_support_email').focus();

                return false;
            }

            return true;
        });

    });
</script>