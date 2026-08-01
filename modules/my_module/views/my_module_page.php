<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<script src="https://cdn.tailwindcss.com"></script>
<div id="wrapper">
    <?php

    $where_summary = '';

    if (staff_cant('view', 'customers')) {

        $where_summary = ' AND userid IN (
        SELECT customer_id 
        FROM ' . db_prefix() . 'customer_admins 
        WHERE staff_id=' . get_staff_user_id() . '
    )';
    }

    $this->db->where('active', 1);

    if (staff_cant('view', 'customers')) {

        $this->db->where_in(
            'userid',
            'SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id=' . get_staff_user_id(),
            false
        );
    }

    $total = $this->db->count_all_results(db_prefix() . 'clients');

    echo $total;


    ?>
    <div class="content">
        <div class="row mb-4">
            <div class="col-md-12">
                <?php if (staff_cant('view', 'customers')) {
                    if (!have_assigned_customers() && staff_cant('create', 'customers')) {
                        access_denied('customers');
                    }
                } ?>
                <div class="mb-4">
                    <h1 class="text-2xl font-bold"><?= _l('customers'); ?></h1>
                    <a href="#" class="text-blue-500 text-sm"><?= _l('contacts'); ?> →</a>
                </div>


                <div class="tw-grid tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-6 tw-gap-2">

                    <!-- TOTAL -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(db_prefix() . 'clients'); ?>
                        </span>

                        <span class="text-dark tw-truncate">
                            Total Records
                        </span>

                    </div>



                    <!-- ACTIVE -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(db_prefix() . 'clients', ['active' => 1]); ?>
                        </span>

                        <span class="text-success tw-truncate">
                            Active Records
                        </span>

                    </div>



                    <!-- INACTIVE -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(db_prefix() . 'clients', ['active' => 0]); ?>
                        </span>

                        <span class="text-danger tw-truncate">
                            Inactive Records
                        </span>

                    </div>



                    <!-- TODAY -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(
                                db_prefix() . 'clients',
                                ['datecreated LIKE' => date('Y-m-d') . '%']
                            ); ?> </span>

                        <span class="text-info tw-truncate">
                            Today Added
                        </span>

                    </div>



                    <!-- PENDING -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(
                                db_prefix() . 'clients',
                                ['leadid !=' => 0]
                            ); ?> </span>

                        <span class="text-warning tw-truncate">
                            Pending
                        </span>

                    </div>



                    <!-- COMPLETED -->
                    <div class="tw-border-neutral-300/80 tw-shadow-sm tw-text-sm tw-border tw-border-solid tw-rounded-lg tw-px-4 tw-py-3 tw-flex tw-items-center tw-font-medium tw-bg-white">

                        <span class="tw-font-semibold tw-mr-1">
                            <?= total_rows(
                                db_prefix() . 'clients',
                                ['registration_confirmed' => 1]
                            ); ?> </span>

                        <span class="text-success tw-truncate">
                            Completed
                        </span>

                    </div>

                </div>
            </div>
        </div>


      <div class="tw-flex tw-justify-between tw-items-center tw-gap-x-6 tw-mb-4">

    <div class="tw-flex tw-justify-between tw-items-center tw-gap-x-1">

        <?php if (staff_can('create', 'customers')) { ?>

            <a href="<?= admin_url('my_module/add_client_page'); ?>"
               class="btn btn-primary">

                <i class="fa-regular fa-plus tw-mr-1"></i>

                <?= _l('new_client'); ?>

            </a>

        <?php } ?>


        <?php if (staff_can('create', 'customers')) { ?>

            <a href="<?= admin_url('clients/import'); ?>"
               class="hidden-xs btn btn-default">

                <i class="fa-solid fa-upload tw-mr-1"></i>

                <?= _l('import_customers'); ?>

            </a>

        <?php } ?>

    </div>


    <div id="vueApp" class="tw-inline">

        <app-filters
            id="clients"
            view="clients"
            :saved-filters="[]"
            :available-rules="[]">

        </app-filters>

    </div>

</div>

<?php

$table_data = [];

$_table_data = [

    '<span class="hide"> - </span>
    <div class="checkbox mass_select_all_wrap">
        <input type="checkbox" id="mass_select_all" data-to-table="clients">
        <label></label>
    </div>',

    [
        'name'     => _l('the_number_sign'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-number'],
    ],

    [
        'name'     => _l('clients_list_company'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-company'],
    ],

    [
        'name'     => _l('contact_primary'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact'],
    ],

    [
        'name'     => _l('company_primary_email'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-primary-contact-email'],
    ],

    [
        'name'     => _l('clients_list_phone'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-phone'],
    ],

    [
        'name'     => _l('customer_active'),
        'th_attrs' => ['class' => 'text-center toggleable', 'id' => 'th-active'],
    ],

    [
        'name'     => _l('customer_groups'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-groups'],
    ],

    [
        'name'     => _l('date_created'),
        'th_attrs' => ['class' => 'toggleable', 'id' => 'th-date-created'],
    ],
];

foreach ($_table_data as $_t) {
    $table_data[] = $_t;
}

render_datatable($table_data, 'clients');

?>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        var tAPI = initDataTable('.table-clients', admin_url + 'clients/table', [0], [0], {},
            <?= hooks()->apply_filters('customers_table_default_order', json_encode([2, 'asc'])); ?>
        );
    });

    function customers_bulk_action(event) {
        var r = confirm(app.lang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if (mass_delete == false || typeof(mass_delete) == 'undefined') {
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('#addBtn').click(function() {
            window.location.href = admin_url + 'my_module/add_client';
        });
    });
</script>

<script>
$(function () {

    initDataTable(
        '.table-clients',
        admin_url + 'my_module/table'
    );

});
</script>



<script>
function customers_bulk_action(event) {

    var r = confirm(app.lang.confirm_action_prompt);

    if (r == false) {
        return false;
    }

    var ids = [];

    $('.table-clients tbody tr').each(function () {

        var checkbox = $(this).find('td:eq(0) input');

        if (checkbox.prop('checked')) {
            ids.push(checkbox.val());
        }
    });

    $.post(
        admin_url + 'my_module/bulk_action',
        { ids: ids }
    ).done(function () {

        window.location.reload();

    });
}
</script>
</body>

</html>
</body>

</html>