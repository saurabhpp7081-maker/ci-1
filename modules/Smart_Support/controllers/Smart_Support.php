<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Smart_Support extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Smart_Support_model');
    }

    public function index()
    {
        $data['title'] = _l('smart_support');

        $this->load->view('Smart_Support/manage', $data);
    }

    public function tickets()
    {
        $data['title'] = _l('smart_support_tickets');

        $this->load->view('Smart_Support/tickets', $data);
    }

public function tickets_table()
{
    $status = $this->input->get('status');

    $tickets = $this->Smart_Support_model->get_tickets($status);

    $data = [];

    $statusText = [
        1 => '<span class="label label-success">Open</span>',
        2 => '<span class="label label-info">In Progress</span>',
        3 => '<span class="label label-primary">Answered</span>',
        4 => '<span class="label label-warning">On Hold</span>',
        5 => '<span class="label label-default">Closed</span>',
    ];

    $priorityText = [
        1 => 'Low',
        2 => 'Medium',
        3 => 'High',
        4 => 'Urgent',
    ];

    foreach ($tickets as $t) {

        $data[] = [
            'ticketid' => (int) $t->id,

            'subject' => html_escape($t->subject),

            'customer' => html_escape(
                $t->customer_name ?: '-'
            ),

            'status' => $statusText[(int) $t->status] ?? '-',

            'priority' => $priorityText[(int) $t->priority] ?? '-',

            'assigned_to' => html_escape(
                $t->assigned_name ?: 'Unassigned'
            ),

            'lastreply' => !empty($t->updated_at)
                ? _dt($t->updated_at)
                : '-',

            'actions' => '
                <a href="' .
                admin_url(
                    'smart_support/ticket/' . (int) $t->id
                ) .
                '" class="btn btn-default btn-icon">
                    <i class="fa fa-eye"></i>
                </a>
            ',
        ];
    }

    echo json_encode([
        'draw' => (int) $this->input->get('draw'),
        'recordsTotal' => count($data),
        'recordsFiltered' => count($data),
        'data' => $data,
    ]);

    exit;
}

    public function ticket($id = null)
    {
        if ($id === null) {

            $data['title'] = _l('smart_support_new_ticket');

            $data['customers'] =
                $this->Smart_Support_model->get_customers();

            $data['categories'] =
                $this->Smart_Support_model->get_categories();

            $data['priorities'] =
                $this->Smart_Support_model->get_priorities();

            $this->load->view(
                'Smart_Support/ticket',
                $data
            );

            return;
        }

        $ticket =
            $this->Smart_Support_model->get_ticket($id);

        if (!$ticket) {
            show_404();
        }

        $data['title'] =
            _l('smart_support_ticket') .
            ' #' .
            $ticket->id;

        $data['ticket'] = $ticket;

        $data['replies'] =
            $this->Smart_Support_model->get_ticket_replies($id);

        $this->load->view(
            'Smart_Support/ticket',
            $data
        );
    }

public function ticket_create()
{
    if (!$this->input->post()) {
        redirect(
            admin_url('smart_support/ticket')
        );
    }

    $customer_id = (int) $this->input->post('customer_id');

    $email = trim(
        $this->input->post('email', true)
    );

    $category_id = (int) $this->input->post('category_id');

    $priority = (int) $this->input->post('priority');

    $subject = trim(
        $this->input->post('subject', true)
    );

    $description = trim(
        $this->input->post('description')
    );

    if (!$customer_id) {
        set_alert(
            'danger',
            _l('smart_support_customer_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if ($email === '') {
        set_alert(
            'danger',
            _l('smart_support_email_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_alert(
            'danger',
            _l('smart_support_valid_email_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if (!$category_id) {
        set_alert(
            'danger',
            _l('smart_support_category_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if (!$subject) {
        set_alert(
            'danger',
            _l('smart_support_subject_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if (!$description) {
        set_alert(
            'danger',
            _l('smart_support_message_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    if (!$priority) {
        $priority = 1;
    }

    $customer = $this->Smart_Support_model->get_customer(
        $customer_id
    );

    if (!$customer) {
        set_alert(
            'danger',
            _l('smart_support_customer_required')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    $ticket_data = [
        'customer_id' => $customer_id,
        'email'       => $email,
        'subject'     => $subject,
        'priority'    => $priority,
        'description' => $description,
        'status'      => 1,
        'created_at'  => date('Y-m-d H:i:s'),
        'updated_at'  => date('Y-m-d H:i:s'),
    ];

    $ticket_id =
        $this->Smart_Support_model->create_ticket(
            $ticket_data
        );

    if (!$ticket_id) {
        set_alert(
            'danger',
            _l('smart_support_ticket_create_failed')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    $meta_data = [
        'ticket_id'  => $ticket_id,
        'category_id' => $category_id,
        'project_id' => null,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ];

    $meta_id =
        $this->Smart_Support_model->create_ticket_meta(
            $meta_data
        );

    if (!$meta_id) {
        $this->Smart_Support_model->delete_ticket(
            $ticket_id
        );

        set_alert(
            'danger',
            _l('smart_support_ticket_create_failed')
        );

        redirect(
            admin_url('smart_support/ticket')
        );
    }

    set_alert(
        'success',
        _l('smart_support_ticket_created')
    );

    redirect(
        admin_url(
            'smart_support/ticket/' . $ticket_id
        )
    );
}

public function ticket_reply($id)
{
    $id = (int) $id;

    if (!$id) {
        show_404();
    }

    if (!$this->input->post()) {
        redirect(
            admin_url('smart_support/ticket/' . $id)
        );
    }

    $message = trim(
        $this->input->post('message', false)
    );

    if ($message === '') {
        set_alert(
            'danger',
            _l('smart_support_message_required')
        );

        redirect(
            admin_url('smart_support/ticket/' . $id)
        );
    }

    $ticket = $this->Smart_Support_model->get_ticket($id);

    if (!$ticket) {
        show_404();
    }

    $reply_id = $this->Smart_Support_model
        ->create_ticket_reply(
            $id,
            $message
        );

    if (!$reply_id) {
        set_alert(
            'danger',
            _l('smart_support_reply_failed')
        );

        redirect(
            admin_url('smart_support/ticket/' . $id)
        );
    }

    $this->Smart_Support_model
        ->update_ticket_reply_data($id);

    set_alert(
        'success',
        _l('smart_support_reply_added')
    );

    redirect(
        admin_url('smart_support/ticket/' . $id)
    );
}


public function update_ticket_reply_data($ticket_id)
{
    return $this->db
        ->where('id', (int) $ticket_id)
        ->update(
            db_prefix() . 'ssx_tickets',
            [
                'last_reply_at' => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        );
}


public function get_ticket_replies($ticket_id)
{
    return $this->db
        ->select([
            'r.*',
            'CONCAT(
                COALESCE(s.firstname, ""),
                " ",
                COALESCE(s.lastname, "")
            ) AS staff_name',
        ])
        ->from(db_prefix() . 'ssx_ticket_replies AS r')
        ->join(
            db_prefix() . 'staff AS s',
            's.staffid = r.staff_id',
            'left'
        )
        ->where(
            'r.ticket_id',
            (int) $ticket_id
        )
        ->order_by('r.id', 'ASC')
        ->get()
        ->result();
}

    public function categories()
    {
        $data['title'] = _l('smart_support_categories');

        $data['categories'] =
            $this->Smart_Support_model->get_categories();

        $this->load->view(
            'Smart_Support/categories',
            $data
        );
    }

    public function category_save()
    {
        $id = $this->input->post('id');

        $name = trim(
            $this->input->post('name', true)
        );

        $description = $this->input->post(
            'description',
            true
        );

        $status = $this->input->post('status');

        $sort_order = $this->input->post(
            'sort_order'
        );

        if ($name === '') {
            set_alert(
                'danger',
                _l('smart_support_category_name_required')
            );

            redirect(
                admin_url('smart_support/categories')
            );
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'status' => $status !== null
                ? (int) $status
                : 1,
            'sort_order' => $sort_order !== null
                ? (int) $sort_order
                : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $this->Smart_Support_model->update_category(
                $id,
                $data
            );

            set_alert(
                'success',
                _l('smart_support_category_updated')
            );
        } else {
            $data['created_by'] =
                get_staff_user_id();

            $data['created_at'] =
                date('Y-m-d H:i:s');

            $this->Smart_Support_model->add_category(
                $data
            );

            set_alert(
                'success',
                _l('smart_support_category_added')
            );
        }

        redirect(
            admin_url('smart_support/categories')
        );
    }

    public function category_delete($id)
    {
        if (!$id) {
            redirect(
                admin_url('smart_support/categories')
            );
        }

        $this->Smart_Support_model->delete_category(
            $id
        );

        set_alert(
            'success',
            _l('smart_support_category_deleted')
        );

        redirect(
            admin_url('smart_support/categories')
        );
    }

    public function predefined_replies()
    {
        $data['title'] = _l(
            'smart_support_predefined_replies'
        );

        $this->load->view(
            'Smart_Support/predefined_replies',
            $data
        );
    }

    public function estimate_requests()
    {
        $data['title'] = _l(
            'smart_support_estimate_requests'
        );

        $this->load->view(
            'Smart_Support/estimate_requests',
            $data
        );
    }
}
