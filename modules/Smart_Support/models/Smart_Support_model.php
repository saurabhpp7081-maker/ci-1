<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Smart_Support_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_customers()
    {
        return $this->db
            ->select([
                'userid',
                'company',
            ])
            ->from(db_prefix() . 'clients')
            ->where('active', 1)
            ->order_by('company', 'ASC')
            ->get()
            ->result();
    }

    public function get_customer($customer_id)
    {
        return $this->db
            ->select([
                'userid',
                'company',
            ])
            ->where(
                'userid',
                (int) $customer_id
            )
            ->get(
                db_prefix() . 'clients'
            )
            ->row();
    }

    public function get_contacts($customer_id)
    {
        return $this->db
            ->select([
                'id',
                'userid',
                'firstname',
                'lastname',
                'email',
            ])
            ->from(db_prefix() . 'contacts')
            ->where(
                'userid',
                (int) $customer_id
            )
            ->where('active', 1)
            ->order_by('firstname', 'ASC')
            ->get()
            ->result();
    }

    public function get_contact($contact_id, $customer_id = null)
    {
        $this->db
            ->select([
                'id',
                'userid',
                'firstname',
                'lastname',
                'email',
            ])
            ->from(db_prefix() . 'contacts')
            ->where(
                'id',
                (int) $contact_id
            );

        if ($customer_id !== null) {
            $this->db->where(
                'userid',
                (int) $customer_id
            );
        }

        return $this->db
            ->get()
            ->row();
    }

    public function get_categories()
    {
        return $this->db
            ->select([
                'id',
                'name',
                'description',
                'status',
                'sort_order',
            ])
            ->from(
                db_prefix() . 'ssx_categories'
            )
            ->where('status', 1)
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'DESC')
            ->get()
            ->result();
    }

    public function get_category($id)
    {
        return $this->db
            ->where(
                'id',
                (int) $id
            )
            ->get(
                db_prefix() . 'ssx_categories'
            )
            ->row();
    }

    public function add_category($data)
    {
        $this->db->insert(
            db_prefix() . 'ssx_categories',
            $data
        );

        return $this->db->insert_id();
    }

    public function update_category($id, $data)
    {
        return $this->db
            ->where(
                'id',
                (int) $id
            )
            ->update(
                db_prefix() . 'ssx_categories',
                $data
            );
    }

    public function delete_category($id)
    {
        return $this->db
            ->where(
                'id',
                (int) $id
            )
            ->delete(
                db_prefix() . 'ssx_categories'
            );
    }

    public function get_priorities()
    {
        return [
            (object) [
                'priorityid' => 1,
                'name'       => 'Low',
            ],
            (object) [
                'priorityid' => 2,
                'name'       => 'Medium',
            ],
            (object) [
                'priorityid' => 3,
                'name'       => 'High',
            ],
            (object) [
                'priorityid' => 4,
                'name'       => 'Urgent',
            ],
        ];
    }

    public function get_staff()
    {
        return $this->db
            ->select([
                'staffid',
                'firstname',
                'lastname',
                'email',
            ])
            ->from(db_prefix() . 'staff')
            ->where('active', 1)
            ->order_by('firstname', 'ASC')
            ->get()
            ->result();
    }

    public function get_departments()
    {
        return $this->db
            ->select([
                'departmentid',
                'name',
            ])
            ->from(
                db_prefix() . 'departments'
            )
            ->order_by('name', 'ASC')
            ->get()
            ->result();
    }

    public function create_ticket($data)
    {
        $this->db->insert(
            db_prefix() . 'ssx_tickets',
            $data
        );

        if (!$this->db->affected_rows()) {
            log_message(
                'error',
                'Smart Support ticket insert failed: ' .
                $this->db->last_query()
            );

            return false;
        }

        return $this->db->insert_id();
    }

    public function get_ticket($id)
    {
        $ticket = $this->db
            ->select([
                't.*',
                'c.company AS customer_name',
                'cat.name AS category_name',
            ])
            ->from(
                db_prefix() . 'ssx_tickets AS t'
            )
            ->join(
                db_prefix() . 'clients AS c',
                'c.userid = t.customer_id',
                'left'
            )
            ->join(
                db_prefix() . 'ssx_ticket_meta AS tm',
                'tm.ticket_id = t.id',
                'left'
            )
            ->join(
                db_prefix() . 'ssx_categories AS cat',
                'cat.id = tm.category_id',
                'left'
            )
            ->where(
                't.id',
                (int) $id
            )
            ->get()
            ->row();

        if (!$ticket) {
            return null;
        }

        $ticket->priority_name =
            $this->get_priority_name(
                $ticket->priority
            );

        $ticket->status_name =
            $this->get_status_name(
                $ticket->status
            );

        return $ticket;
    }

public function get_tickets($status = '')
{
    $this->db->select('
        t.*,
        c.company as customer_name,
        CONCAT(s.firstname," ",s.lastname) as assigned_name
    ');

    $this->db->from(db_prefix().'ssx_tickets t');

    $this->db->join(
        db_prefix().'clients c',
        'c.userid=t.customer_id',
        'left'
    );

    $this->db->join(
        db_prefix().'staff s',
        's.staffid=t.assigned_to',
        'left'
    );

    if ($status != '') {
        $this->db->where('t.status', $status);
    }

    $this->db->order_by('t.id','DESC');

    return $this->db->get()->result();
}

    public function create_ticket_meta($data)
    {
        $table =
            db_prefix() .
            'ssx_ticket_meta';

        $ticket_id = (int) $data['ticket_id'];

        $existing = $this->db
            ->where(
                'ticket_id',
                $ticket_id
            )
            ->get($table)
            ->row();

        if ($existing) {
            $update_data = [
                'category_id' =>
                    isset($data['category_id'])
                        ? $data['category_id']
                        : null,

                'project_id' =>
                    isset($data['project_id'])
                        ? $data['project_id']
                        : null,

                'updated_at' =>
                    date('Y-m-d H:i:s'),
            ];

            $this->db
                ->where(
                    'ticket_id',
                    $ticket_id
                )
                ->update(
                    $table,
                    $update_data
                );

            return $existing->id;
        }

        $this->db->insert(
            $table,
            $data
        );

        if (!$this->db->affected_rows()) {
            return false;
        }

        return $this->db->insert_id();
    }

    public function get_ticket_meta($ticket_id)
    {
        return $this->db
            ->where(
                'ticket_id',
                (int) $ticket_id
            )
            ->get(
                db_prefix() . 'ssx_ticket_meta'
            )
            ->row();
    }

    public function update_ticket_meta(
        $ticket_id,
        $data
    ) {
        return $this->db
            ->where(
                'ticket_id',
                (int) $ticket_id
            )
            ->update(
                db_prefix() . 'ssx_ticket_meta',
                $data
            );
    }

    public function delete_ticket($id)
    {
        $ticket_id = (int) $id;

        $this->db
            ->where(
                'ticket_id',
                $ticket_id
            )
            ->delete(
                db_prefix() . 'ssx_ticket_meta'
            );

        if (
            $this->db->table_exists(
                db_prefix() . 'ssx_ticket_replies'
            )
        ) {
            $this->db
                ->where(
                    'ticket_id',
                    $ticket_id
                )
                ->delete(
                    db_prefix() . 'ssx_ticket_replies'
                );
        }

        return $this->db
            ->where(
                'id',
                $ticket_id
            )
            ->delete(
                db_prefix() . 'ssx_tickets'
            );
    }

    public function get_priority_name($priority)
    {
        switch ((int) $priority) {
            case 1:
                return 'Low';

            case 2:
                return 'Medium';

            case 3:
                return 'High';

            case 4:
                return 'Urgent';

            default:
                return 'Low';
        }
    }

    public function get_status_name($status)
    {
        switch ((int) $status) {
            case 1:
                return 'Open';

            case 2:
                return 'In Progress';

            case 3:
                return 'Answered';

            case 4:
                return 'On Hold';

            case 5:
                return 'Closed';

            default:
                return 'Open';
        }
    }

    public function get_ticket_replies($ticket_id)
    {
        $table =
            db_prefix() .
            'ssx_ticket_replies';

        if (
            !$this->db->table_exists(
                $table
            )
        ) {
            return [];
        }

        return $this->db
            ->select([
                'r.*',
                'CONCAT(
                    COALESCE(s.firstname, ""),
                    " ",
                    COALESCE(s.lastname, "")
                ) AS staff_name',
            ])
            ->from(
                $table . ' AS r'
            )
            ->join(
                db_prefix() . 'staff AS s',
                's.staffid = r.staff_id',
                'left'
            )
            ->where(
                'r.ticket_id',
                (int) $ticket_id
            )
            ->order_by(
                'r.id',
                'ASC'
            )
            ->get()
            ->result();
    }

    public function create_ticket_reply(
        $ticket_id,
        $message
    ) {
        $table =
            db_prefix() .
            'ssx_ticket_replies';

        if (
            !$this->db->table_exists(
                $table
            )
        ) {
            log_message(
                'error',
                'Smart Support reply table does not exist: ' .
                $table
            );

            return false;
        }

        $message = trim($message);

        if ($message === '') {
            return false;
        }

        $ticket = $this->get_ticket(
            $ticket_id
        );

        if (!$ticket) {
            return false;
        }

        $data = [
            'ticket_id' =>
                (int) $ticket_id,

            'staff_id' =>
                (int) get_staff_user_id(),

            'message' =>
                $message,

            'created_at' =>
                date('Y-m-d H:i:s'),
        ];

        $this->db->insert(
            $table,
            $data
        );

        if (!$this->db->affected_rows()) {
            log_message(
                'error',
                'Smart Support reply insert failed: ' .
                $this->db->last_query()
            );

            return false;
        }

        return $this->db->insert_id();
    }

    public function update_ticket_reply_data(
        $ticket_id
    ) {
        return $this->db
            ->where(
                'id',
                (int) $ticket_id
            )
            ->update(
                db_prefix() . 'ssx_tickets',
                [
                    'updated_at' =>
                        date('Y-m-d H:i:s'),
                ]
            );
    }
}