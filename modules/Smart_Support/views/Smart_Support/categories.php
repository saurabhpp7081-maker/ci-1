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

                        <div class="row">

                            <div class="col-md-8">

                                <h4 class="no-margin">
                                    <?php echo _l('smart_support_categories'); ?>
                                </h4>

                            </div>

                            <div class="col-md-4 text-right">

                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    data-toggle="modal"
                                    data-target="#categoryModal"
                                >
                                    <i class="fa fa-plus"></i>
                                    <?php echo _l('smart_support_add_category'); ?>
                                </button>

                            </div>

                        </div>

                        <hr class="hr-panel-heading">

                        <div class="table-responsive">

                            <table class="table table-striped dt-table">

                                <thead>

                                    <tr>
                                        <th><?php echo _l('id'); ?></th>
                                        <th><?php echo _l('smart_support_category_name'); ?></th>
                                        <th><?php echo _l('smart_support_description'); ?></th>
                                        <th><?php echo _l('smart_support_status'); ?></th>
                                        <th><?php echo _l('smart_support_sort_order'); ?></th>
                                        <th><?php echo _l('smart_support_actions'); ?></th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($categories as $category) { ?>

                                        <tr>

                                            <td>
                                                <?php echo (int) $category->id; ?>
                                            </td>

                                            <td>
                                                <?php echo html_escape($category->name); ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo $category->description
                                                    ? html_escape($category->description)
                                                    : '-';
                                                ?>
                                            </td>

                                            <td>

                                                <?php if ((int) $category->status === 1) { ?>

                                                    <span class="label label-success">
                                                        <?php echo _l('smart_support_active'); ?>
                                                    </span>

                                                <?php } else { ?>

                                                    <span class="label label-default">
                                                        <?php echo _l('smart_support_inactive'); ?>
                                                    </span>

                                                <?php } ?>

                                            </td>

                                            <td>
                                                <?php echo (int) $category->sort_order; ?>
                                            </td>

                                            <td>

                                                <button
                                                    type="button"
                                                    class="btn btn-default btn-sm"
                                                    onclick="editCategory(
                                                        <?php echo (int) $category->id; ?>,
                                                        <?php echo html_escape(json_encode($category->name)); ?>,
                                                        <?php echo html_escape(json_encode($category->description)); ?>,
                                                        <?php echo (int) $category->status; ?>,
                                                        <?php echo (int) $category->sort_order; ?>
                                                    )"
                                                >
                                                    <i class="fa fa-pencil"></i>
                                                </button>

                                                <a
                                                    href="<?php echo admin_url(
                                                        'smart_support/category_delete/' . $category->id
                                                    ); ?>"
                                                    class="btn btn-danger btn-sm _delete"
                                                >
                                                    <i class="fa fa-trash"></i>
                                                </a>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<div
    class="modal fade"
    id="categoryModal"
    tabindex="-1"
    role="dialog"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <?php echo form_open(
                admin_url('smart_support/category_save')
            ); ?>

                <div class="modal-header">

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                    >
                        &times;
                    </button>

                    <h4 class="modal-title">
                        <?php echo _l('smart_support_category'); ?>
                    </h4>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="id"
                        id="category_id"
                        value=""
                    >

                    <div class="form-group">

                        <label for="category_name">
                            <?php echo _l('smart_support_category_name'); ?>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="category_name"
                            class="form-control"
                            required
                        >

                    </div>

                    <div class="form-group">

                        <label for="category_description">
                            <?php echo _l('smart_support_description'); ?>
                        </label>

                        <textarea
                            name="description"
                            id="category_description"
                            class="form-control"
                            rows="4"
                        ></textarea>

                    </div>

                    <div class="form-group">

                        <label for="category_status">
                            <?php echo _l('smart_support_status'); ?>
                        </label>

                        <select
                            name="status"
                            id="category_status"
                            class="form-control"
                        >

                            <option value="1">
                                <?php echo _l('smart_support_active'); ?>
                            </option>

                            <option value="0">
                                <?php echo _l('smart_support_inactive'); ?>
                            </option>

                        </select>

                    </div>

                    <div class="form-group">

                        <label for="category_sort_order">
                            <?php echo _l('smart_support_sort_order'); ?>
                        </label>

                        <input
                            type="number"
                            name="sort_order"
                            id="category_sort_order"
                            class="form-control"
                            value="0"
                            min="0"
                        >

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        <?php echo _l('cancel'); ?>
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <?php echo _l('save'); ?>
                    </button>

                </div>

            <?php echo form_close(); ?>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script>
function editCategory(
    id,
    name,
    description,
    status,
    sortOrder
) {
    $('#category_id').val(id);

    $('#category_name').val(name);

    $('#category_description').val(description);

    $('#category_status').val(status);

    $('#category_sort_order').val(sortOrder);

    $('#categoryModal').modal('show');
}
</script>

</body>
</html>