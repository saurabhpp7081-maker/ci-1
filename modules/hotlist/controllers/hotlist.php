<?php

use app\services\LeadProfileBadges;
use app\services\leads\LeadsKanban;

defined('BASEPATH') or exit('No direct script access allowed');

class hotlist extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('leads_public_url')) {
            $helperPath = APPPATH . 'helpers/leads_helper.php';
            if (file_exists($helperPath)) {
                require_once $helperPath;
            }
        }


       
        $this->load->model('hotlist/hotlist_model');
        $this->load->model('leads_model');
    }

    public function index()
    {
        close_setup_menu();

        if (!is_staff_member()) {
            access_denied('Hotlist');
        }

        $table = App_table::new('hotlist_leads', module_views_path(HOTLIST_MODULE_NAME, 'table/leads'));
        App_table::register($table);

        $data['switch_kanban'] = true;

        if ($this->session->userdata('leads_kanban_view') == 'true') {
            $data['switch_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }

        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
            $this->load->model('gdpr_model');
            $data['consent_purposes'] = $this->gdpr_model->get_consent_purposes();
        }

        $data['summary']  = get_leads_summary();
        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['title']    = _l('hotlist_manage');
        $data['table']    = App_table::find('hotlist_leads');
        $data['leadid']   = '';
        $data['isKanBan'] = $this->session->has_userdata('leads_kanban_view')
            && $this->session->userdata('leads_kanban_view') == 'true';

        $this->load->view('hotlist/manage', $data);
    }

    public function table()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $table = App_table::new('hotlist_leads', module_views_path(HOTLIST_MODULE_NAME, 'table/leads'));
        App_table::register($table);
        $table->output();
    }

    public function kanban()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $data['statuses']      = $this->leads_model->get_status();
        $data['base_currency'] = get_base_currency();
        $data['summary']       = get_leads_summary();

        echo $this->load->view('hotlist/kan-ban', $data, true);
    }

    public function leads_kanban_load_more()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $this->db->where('id', $status);
        $status = $this->db->get(db_prefix() . 'leads_status')->row_array();

        $leads = (new LeadsKanban($status['id']))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)
            ->get();

        foreach ($leads as $lead) {
            $this->load->view('admin/leads/_kan_ban_card', [
                'lead'   => $lead,
                'status' => $status,
            ]);
        }
    }

    public function switch_kanban($set = 0)
    {
        $this->session->set_userdata([
            'leads_kanban_view' => $set == 1 ? 'true' : 'false',
        ]);

        redirect(admin_url('hotlist'));
    }

    /* Add or update lead */
    public function lead($id = '')
    {
        if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $validationErrors = $this->validate_hotlist_lead_form();
            if (!empty($validationErrors)) {
                $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => _l('hotlist_fix_validation_errors'),
                        'errors'  => $validationErrors,
                    ]));

                return;
            }

            if ($id == '') {
                $id      = $this->leads_model->add($this->input->post());
                $message = $id ? _l('added_successfully', _l('lead')) : _l('problem_adding', _l('lead_lowercase'));

                if ($id) {
                    $this->db->insert(db_prefix() . 'hotlist_lead_rounds', [
                        'lead_id'      => $id,
                        'round_id'     => $this->input->post('round_id'),
                        'company_name' => $this->input->post('company_name'),
                    ]);

                    $this->sync_hotlist_company($id, $this->input->post('company_name'));
                }

                echo json_encode([
                    'success'  => $id ? true : false,
                    'id'       => $id,
                    'message'  => $message,
                    'leadView' => $id ? $this->_get_lead_data($id) : [],
                ]);
            } else {
                $emailOriginal   = $this->db->select('email')->where('id', $id)->get(db_prefix() . 'leads')->row()->email;
                $proposalWarning = false;
                $message         = _l('problem_updating', _l('lead_lowercase'));
                $success         = $this->leads_model->update($this->input->post(), $id);




                $save_data = [
    'lead_id'      => $id,
    'round_id'     => $this->input->post('round_id'),
    'company_name' => $this->input->post('company_name'),
];

$existing = $this->db
    ->where('lead_id', $id)
    ->get(db_prefix() . 'hotlist_lead_rounds')
    ->row();

if ($existing) {

    $this->db->where('lead_id', $id);

    $this->db->update(
        db_prefix() . 'hotlist_lead_rounds',
        $save_data
    );

} else {

    $this->db->insert(
        db_prefix() . 'hotlist_lead_rounds',
        $save_data
    );
}

                $this->sync_hotlist_company($id, $this->input->post('company_name'));

                if ($success) {
                    $emailNow = $this->db->select('email')->where('id', $id)->get(db_prefix() . 'leads')->row()->email;

                    $proposalWarning = (total_rows(db_prefix() . 'proposals', [
                        'rel_type' => 'lead',
                        'rel_id'   => $id, ]) > 0 && ($emailOriginal != $emailNow) && $emailNow != '') ? true : false;

                    $message = _l('updated_successfully', _l('lead'));
                }
                echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
                    'proposal_warning' => $proposalWarning,
                    'leadView'         => $this->_get_lead_data($id),
                ]);
            }
            die;
        }

        echo json_encode([
            'leadView' => $this->_get_lead_data($id),
        ]);
    }

    private function validate_hotlist_lead_form()
    {
        $errors = [];
        $requiredMessage = 'This field is required.';

        if (trim((string) $this->input->post('name', false)) === '') {
            $errors['name'] = $requiredMessage;
        }

        if (trim((string) $this->input->post('source', false)) === '') {
            $errors['source'] = $requiredMessage;
        }

        $status = $this->input->post('status', false);
        if ($status === null || $status === '') {
            $errors['status'] = $requiredMessage;
        }

        $email = trim((string) $this->input->post('email', false));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = _l('client_invalid_email');
        }

        $leadValue = $this->input->post('lead_value', false);
        if ($leadValue !== null && $leadValue !== '' && !is_numeric($leadValue)) {
            $errors['lead_value'] = 'Lead value must be a valid number.';
        }

        return $errors;
    }

    private function sync_hotlist_company($leadId, $companyName)
    {
        if (!$leadId) {
            return;
        }

        $this->db->where('id', $leadId);
        $this->db->update(db_prefix() . 'leads', [
            'company' => trim((string) $companyName),
        ]);
    }

    private function _get_lead_data($id = '')
    {
        $reminder_data         = '';
        $data['lead_locked']   = false;
        $data['openEdit']      = $this->input->get('edit') ? true : false;
        $data['members']       = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['status_id']     = $this->input->get('status_id') ? $this->input->get('status_id') : get_option('leads_default_status');
        $data['base_currency'] = get_base_currency();

        if (is_numeric($id)) {
            $leadWhere = (staff_can('view', 'leads') ? [] : '(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');

            $lead = $this->leads_model->get($id, $leadWhere);

            if (!$lead) {
                header('HTTP/1.0 404 Not Found');
                echo _l('lead_not_found');
                die;
            }

            if (total_rows(db_prefix() . 'clients', ['leadid' => $id]) > 0) {
                $data['lead_locked'] = (!is_admin() && get_option('lead_lock_after_convert_to_customer') == 1);
            }

            $reminder_data = $this->load->view('admin/includes/modals/reminder', [
                'id'             => $lead->id,
                'name'           => 'lead',
                'members'        => $data['members'],
                'reminder_title' => _l('lead_set_reminder_title'),
            ], true);

            $data['lead']          = $lead;
            $data['mail_activity'] = $this->leads_model->get_mail_activity($id);
            $data['notes']         = $this->misc_model->get_notes($id, 'lead');
            $data['activity_log']  = $this->leads_model->get_lead_activity_log($id);

            if (is_gdpr() && get_option('gdpr_enable_consent_for_leads') == '1') {
                $this->load->model('gdpr_model');
                $data['purposes'] = $this->gdpr_model->get_consent_purposes($lead->id, 'lead');
                $data['consents'] = $this->gdpr_model->get_consents(['lead_id' => $lead->id]);
            }

            $leadProfileBadges         = new LeadProfileBadges($id);
            $data['total_reminders']   = $leadProfileBadges->getCount('reminders');
            $data['total_notes']       = $leadProfileBadges->getCount('notes');
            $data['total_attachments'] = $leadProfileBadges->getCount('attachments');
            $data['total_tasks']       = $leadProfileBadges->getCount('tasks');
            $data['total_proposals']   = $leadProfileBadges->getCount('proposals');
        }

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['rounds'] = $this->hotlist_model->get_round();

$data['lead_round'] = [];

if ($id != '') {

    $data['lead_round'] = $this->db
        ->where('lead_id', $id)
        ->get(db_prefix() . 'hotlist_lead_rounds')
        ->row_array();

    if (isset($data['lead']) && empty($data['lead_round']['company_name']) && !empty($data['lead']->company)) {
        $data['lead_round']['company_name'] = $data['lead']->company;
    }
}

if (isset($data['lead']) && !empty($data['lead_round']['company_name'])) {
    $data['lead']->company = $data['lead_round']['company_name'];
}

        $data = hooks()->apply_filters('lead_view_data', $data);

        return [
            'data'          => $this->load->view('hotlist/lead', $data, true),
            'reminder_data' => $reminder_data,
        ];
    }
    public function lead_round()
    {
        $data['rounds'] = $this->hotlist_model->get_round();
        $this->load->view('hotlist/lead_round', $data);
    }

    public function bulk_action()
    {
        if (!is_staff_member()) {
            ajax_access_denied();
        }

        hooks()->do_action('before_do_bulk_action_for_leads');
        $total_deleted = 0;

        if ($this->input->post()) {
            $ids                   = $this->input->post('ids');
            $status                = $this->input->post('status');
            $source                = $this->input->post('source');
            $assigned              = $this->input->post('assigned');
            $visibility            = $this->input->post('visibility');
            $tags                  = $this->input->post('tags');
            $last_contact          = $this->input->post('last_contact');
            $lost                  = $this->input->post('lost');
            $has_permission_delete = staff_can('delete', 'leads');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($has_permission_delete && $this->leads_model->delete($id)) {
                            $this->db->where('lead_id', $id);
                            $this->db->delete(db_prefix() . 'hotlist_lead_rounds');
                            $total_deleted++;
                        }
                    } else {
                        if ($status || $source || $assigned || $last_contact || $visibility) {
                            $update = [];

                            if ($status) {
                                $this->leads_model->update_lead_status([
                                    'status' => $status,
                                    'leadid' => $id,
                                ]);
                            }

                            if ($source) {
                                $update['source'] = $source;
                            }

                            if ($assigned) {
                                $update['assigned'] = $assigned;
                            }

                            if ($last_contact) {
                                $update['lastcontact'] = to_sql_date($last_contact, true);
                            }

                            if ($visibility) {
                                $update['is_public'] = $visibility === 'public' ? 1 : 0;
                            }

                            if (count($update) > 0) {
                                $this->db->where('id', $id);
                                $this->db->update(db_prefix() . 'leads', $update);
                            }
                        }

                        if ($tags) {
                            handle_tags_save($tags, $id, 'lead');
                        }

                        if ($lost == 'true') {
                            $this->leads_model->mark_as_lost($id);
                        }
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_leads_deleted', $total_deleted));
        }
    }

    public function import()
    {
        if (!is_admin() && get_option('allow_non_admin_members_to_import_leads') != '1') {
            access_denied('Hotlist Import');
        }

        $dbFields = $this->db->list_fields(db_prefix() . 'leads');
        array_push($dbFields, 'tags');

        $this->load->library('import/import_leads', [], 'import');
        $this->import->setDatabaseFields($dbFields)
            ->setCustomFields(get_custom_fields('leads'));

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if ($this->input->post() && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }

        $data['statuses'] = $this->leads_model->get_status();
        $data['sources']  = $this->leads_model->get_source();
        $data['members']  = $this->staff_model->get('', ['is_not_staff' => 0, 'active' => 1]);
        $data['title']    = _l('import_leads');

        $this->load->view('hotlist/import', $data);
    }

    public function delete($id)
    {
        if (!is_staff_member() || !$id) {
            access_denied('hotlist');
        }

        if (!staff_can('delete', 'leads')) {
            access_denied('leads');
        }

        $success = $this->leads_model->delete($id);

        if ($success) {
            $this->db->where('lead_id', $id);
            $this->db->delete(db_prefix() . 'hotlist_lead_rounds');
            set_alert('success', _l('deleted', _l('lead')));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }

        redirect(admin_url('hotlist'));
    }
}
