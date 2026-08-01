<?php

defined('BASEPATH') or exit('No direct script access allowed');

class My_module extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {


        if (staff_cant('view', 'my_module')) {

            access_denied('my_module');
        }

        $this->load->model('my_module/custom_menu_model');

        $data['clients'] = $this->custom_menu_model->get_clients();

        $this->load->view('my_module/my_module_page', $data);
    }

    public function add_client_page()
    {


        $data['title'] = _l('add_client');
        // $this->load->view('my_module/add_client', $data);

        $this->load->model('clients_model');
        $this->load->model('currencies_model');
        $this->load->model('staff_model');

        $data['title'] = 'Add Client';
        $data['client'] = null;


        $data['groups'] = $this->clients_model->get_groups();
        $data['userid'] = $this->input->get('userid');

        $data['currencies'] = $this->currencies_model->get();

        $data['staff'] = $this->staff_model->get();

        $data['customer_admins'] = [];

        $data['customer_groups'] = [];

        $this->load->view(
            'my_module/add_client',
            $data
        );
    }

    public function db()
    {
        $this->load->model('custom_menu_model');

        $data['clients'] = $this->custom_menu_model->get_clients();

        $data['title'] = 'Client List';

        $this->load->view('my_module/my_module_page', $data);
    }

    public function add_client()
    {
        if ($this->input->post()) {

            $data = $this->input->post();


            if (empty($data['company']) || strlen($data['company']) < 3) {
                echo json_encode([
                    'success' => false,
                    'message' => 'This field is required '
                ]);
                return;
            }


            $insert = [
                'company'            => $data['company'],
                'vat'                => $data['vat'] ?? '',
                'phonenumber'        => $data['phone'] ?? '',
                'website'            => $data['website'] ?? '',
                'address'            => $data['address'] ?? '',
                'city'               => $data['city'] ?? '',
                'zip'                => $data['zip'] ?? '',
                'state'              => $data['state'] ?? '',
                'country'            => $data['country'] ?? 0,
                'default_language'   => $data['default_language'] ?? '',
                'default_currency'   => $data['default_currency'] ?? 0,
                'datecreated'        => date('Y-m-d H:i:s'),
                'active'             => 1
            ];


           
        }

         $this->db->insert(db_prefix() . 'clients', $insert);

            $client_id = $this->db->insert_id();

            if (!empty($data['groups_in'])) {
                foreach ($data['groups_in'] as $group_id) {

                    $this->db->insert(db_prefix() . 'customer_groups', [
                        'customer_id' => $client_id,
                        'groupid'     => $group_id
                    ]);
                }
            }

            set_alert('success', 'Client Added Successfully');

            redirect(admin_url('clients/client/' . $client_id));
    }
}
