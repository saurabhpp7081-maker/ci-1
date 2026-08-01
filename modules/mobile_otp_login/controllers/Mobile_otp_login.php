<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property-read Authentication_model    $Authentication_model
 * @property-read Announcements_model     $announcements_model
 * @property-read Mobile_otp_login_model  $mobile_otp_login_model
 */
class Mobile_otp_login extends App_Controller
{
    public function __construct()
    {
        parent::__construct();

        load_admin_language();
        $this->lang->load('mobile_otp_login', 'english');

        $this->load->model('Authentication_model');
        $this->load->model('announcements_model');
        $this->load->model('mobile_otp_login/mobile_otp_login_model');
    }

    public function index()
    {
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }

        if (function_exists('init_admin_auth_assets')) {
            init_admin_auth_assets();
        }

        $data = [
            'title'                    => _l('mobile_otp_login_page_title'),
            'send_otp_url'             => admin_url('mobile-login/send-otp'),
            'verify_otp_url'           => admin_url('mobile-login/verify'),
            'resend_otp_url'           => admin_url('mobile-login/resend'),
            'classic_login_url'        => admin_url('authentication'),
            'otp_expiry_seconds'       => (int) get_option('mobile_otp_login_expiry_seconds'),
            'resend_after_seconds'     => (int) get_option('mobile_otp_login_resend_after_seconds'),
            'csrf_token_name'          => $this->security->get_csrf_token_name(),
            'csrf_token_hash'          => $this->security->get_csrf_hash(),
        ];

        $this->load->view('mobile_otp_login/auth/login', $data);
    }

    public function send_otp()
    {
        $this->json_only();

        $mobile = $this->input->post('mobile', true);
        $result = $this->mobile_otp_login_model->issue_otp($mobile);

        $status = 200;
        if (!$result['success']) {
            $status = isset($result['status_code']) ? (int) $result['status_code'] : 422;
        }

        $this->json_response($result, $status);
    }

    public function resend_otp()
    {
        $this->send_otp();
    }

    public function verify_otp()
    {
        $this->json_only();

        $mobile          = $this->input->post('mobile', true);
        $otp             = $this->input->post('otp', true);
        $normalizedMobile = $this->mobile_otp_login_model->normalize_mobile($mobile);

        if ($normalizedMobile === '') {
            $this->json_response([
                'success' => false,
                'message' => _l('mobile_otp_login_invalid_mobile'),
            ], 422);
        }

        if ($this->is_verify_temporarily_blocked($normalizedMobile)) {
            $this->json_response([
                'success' => false,
                'message' => _l('mobile_otp_login_too_many_attempts'),
            ], 429);
        }

        $result = $this->mobile_otp_login_model->verify_otp($normalizedMobile, $otp);

        if (!$result['success']) {
            $this->track_failed_verify_attempt($normalizedMobile);
            $status = isset($result['status_code']) ? (int) $result['status_code'] : 422;
            $this->json_response($result, $status);
        }

        $this->clear_verify_attempts($normalizedMobile);
        $user = $result['user'];

        $this->Authentication_model->two_factor_auth_login($user);
        $this->announcements_model->set_announcements_as_read_except_last_one(get_staff_user_id(), true);

        hooks()->do_action('after_staff_login');

        $this->json_response([
            'success'      => true,
            'message'      => _l('mobile_otp_login_success'),
            'redirect_url' => $this->resolve_redirect_url_after_login(),
        ]);
    }

    private function json_only()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
    }

    private function json_response($payload, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }

    private function is_verify_temporarily_blocked($mobile)
    {
        $key      = $this->attempts_key($mobile);
        $attempts = $this->session->userdata($key);

        if (!is_array($attempts)) {
            return false;
        }

        if ((int) $attempts['blocked_until'] < time()) {
            $this->session->unset_userdata($key);
            return false;
        }

        return true;
    }

    private function track_failed_verify_attempt($mobile)
    {
        $key            = $this->attempts_key($mobile);
        $maxAttempts    = max(1, (int) get_option('mobile_otp_login_max_verify_attempts'));
        $blockSeconds   = max(60, (int) get_option('mobile_otp_login_verify_block_seconds'));
        $current        = $this->session->userdata($key);

        if (!is_array($current)) {
            $current = ['count' => 0, 'blocked_until' => 0];
        }

        $current['count']++;

        if ($current['count'] >= $maxAttempts) {
            $current['blocked_until'] = time() + $blockSeconds;
        }

        $this->session->set_userdata($key, $current);
    }

    private function clear_verify_attempts($mobile)
    {
        $this->session->unset_userdata($this->attempts_key($mobile));
    }

    private function attempts_key($mobile)
    {
        return 'mobile_otp_login_verify_' . md5($mobile);
    }

    private function resolve_redirect_url_after_login()
    {
        if ($this->session->has_userdata('red_url')) {
            $redirectUrl = $this->session->userdata('red_url');
            $this->session->unset_userdata('red_url');

            if (strpos($redirectUrl, 'clients') === false) {
                return $redirectUrl;
            }
        }

        return admin_url();
    }
}
