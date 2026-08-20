'use strict';

$(document).ready(function () {

    var $table = $('#ssx_tickets_table');

    /*
    | Check Table Exists
    */

    if (!$table.length) {
        return;
    }


    /*
    | Prevent Duplicate DataTable
    */

    if ($.fn.DataTable.isDataTable('#ssx_tickets_table')) {
        $table.DataTable().clear().destroy();
    }


    /*
    | Current Status Filter
    */

    var currentStatus = '';


    /*
    | Initialize Tickets DataTable
    */

    var table = $table.DataTable({

        processing: true,

        serverSide: false,

        ajax: {

            url: admin_url + 'smart_support/tickets_table',

            type: 'GET',

            data: function (d) {
                d.status = currentStatus;
            },

            dataSrc: function (json) {

                if (!json || !Array.isArray(json.data)) {
                    console.error('Invalid Smart Support response:', json);
                    return [];
                }

                return json.data;

            },

            error: function (xhr, status, error) {
                console.error('Smart Support AJAX Error:', status, error);
                console.error('Server Response:', xhr.responseText);
            }

        },


        /*
        | JSON → Table Column Mapping
        */

        columns: [
            { data: 'ticketid', defaultContent: '-' },
            { data: 'subject', defaultContent: '-' },
            { data: 'customer', defaultContent: '-' },
            { data: 'status', defaultContent: '-', orderable: false },
            { data: 'priority', defaultContent: '-', orderable: false },
            { data: 'assigned_to', defaultContent: 'Unassigned', orderable: false },
            { data: 'lastreply', defaultContent: '-' },
            { data: 'actions', defaultContent: '', orderable: false, searchable: false }
        ],


        /*
        | Default Sorting (latest ticket first)
        */

        order: [
            [0, 'desc']
        ],


        /*
        | Pagination
        */

        pageLength: 25,

        lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
        ],


        /*
        | Responsive
        */

        responsive: true,

        autoWidth: false,

        deferRender: true,


        /*
        | Language (Perfex fallback safe)
        */

        language: {

            processing: '<i class="fa fa-spinner fa-spin"></i>',

            search: '',

            searchPlaceholder:
                (typeof app !== 'undefined' && app.lang && app.lang.search)
                    ? app.lang.search
                    : 'Search...',

            lengthMenu: '_MENU_',

            emptyTable:
                (typeof app !== 'undefined' && app.lang && app.lang.no_data_found)
                    ? app.lang.no_data_found
                    : 'No data found',

            zeroRecords:
                (typeof app !== 'undefined' && app.lang && app.lang.no_data_found)
                    ? app.lang.no_data_found
                    : 'No matching tickets found',

            info: 'Showing _START_ to _END_ of _TOTAL_ entries',

            infoEmpty: 'Showing 0 to 0 of 0 entries',

            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            }

        },


        /*
        | Column Styling
        */

        columnDefs: [
            { targets: 0, className: 'text-nowrap' },
            { targets: 1, className: 'text-left' },
            { targets: [3, 4, 5], className: 'text-nowrap' },
            { targets: 7, className: 'text-right text-nowrap' }
        ]

    });


    /*
    | Status Tab Filter
    */

    $(document).on('click', '.ssx-status-tab', function (e) {

        e.preventDefault();

        var $this = $(this);

        $('.ssx-status-tab').removeClass('active');
        $this.addClass('active');

        currentStatus = $this.attr('data-status') || '';

        table.ajax.reload(null, true);

    });


    /*
    | Manual Reload Button
    */

    $(document).on('click', '#ssx_reload_tickets', function (e) {

        e.preventDefault();

        table.ajax.reload(null, false);

    });


    /*
    | Expose Table Globally
    */

    window.ssxTicketsTable = table;

});