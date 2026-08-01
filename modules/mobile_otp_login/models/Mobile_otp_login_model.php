<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mobile_otp_login_model extends App_Model
{
    public function issue_otp($mobile)
    {
        $normalizedMobile = $this->normalize_mobile($mobile);

        if ($normalizedMobile === '') {
            return $this->failure(_l('mobile_otp_login_invalid_mobile'));
        }

        $user = $this->find_staff_by_mobile($normalizedMobile);
        if (!$user) {
            return $this->failure(_l('mobile_otp_login_mobile_not_found'));
        }

        if ((int) $user->active !== 1) {
            return $this->failure(_l('mobile_otp_login_inactive_staff'));
        }

        $rateLimited = $this->validate_send_rate_limit($normalizedMobile);
        if (!$rateLimited['success']) {
            return $rateLimited;
        }

        $otp            = (string) random_int(100000, 999999);
        $expiresSeconds = max(60, (int) get_option('mobile_otp_login_expiry_seconds'));
        $expiresAt      = date('Y-m-d H:i:s', time() + $expiresSeconds);

        $this->invalidate_unused_otps($normalizedMobile);

        $this->db->insert(db_prefix() . 'otp_login', [
            'mobile'     => $normalizedMobile,
            'otp'        => $otp,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
        ]);

        if ($this->is_test_mode_enabled()) {
            log_activity('Mobile OTP Login test mode generated OTP [' . $otp . '] for mobile [' . $normalizedMobile . ']');

            return [
                'success'               => true,
                'message'               => _l('mobile_otp_login_otp_generated_test'),
                'otp'                   => $otp,
                'resend_after_seconds'  => max(1, (int) get_option('mobile_otp_login_resend_after_seconds')),
                'expires_after_seconds' => $expiresSeconds,
            ];
        }

        $otpId = $this->db->insert_id();

        $smsResponse = $this->send_otp_via_provider($normalizedMobile, $otp);
        if (!$smsResponse['success']) {
            $this->db->where('id', $otpId);
            $this->db->delete(db_prefix() . 'otp_login');

            return $smsResponse;
        }

        return [
            'success'              => true,
            'message'              => _l('mobile_otp_login_otp_sent'),
            'resend_after_seconds' => max(1, (int) get_option('mobile_otp_login_resend_after_seconds')),
            'expires_after_seconds'=> $expiresSeconds,
        ];
    }

    public function verify_otp($mobile, $otp)
    {
        $normalizedOtp = preg_replace('/\D+/', '', (string) $otp);

        if (strlen($normalizedOtp) !== 6) {
            return $this->failure(_l('mobile_otp_login_invalid_otp'));
        }

        $user = $this->find_staff_by_mobile($mobile);
        if (!$user) {
            return $this->failure(_l('mobile_otp_login_mobile_not_found'));
        }

        $this->db->where('mobile', $mobile);
        $this->db->where('otp', $normalizedOtp);
        $this->db->where('is_used', 0);
        $this->db->where('expires_at >=', date('Y-m-d H:i:s'));
        $this->db->order_by('id', 'DESC');
        $record = $this->db->get(db_prefix() . 'otp_login')->row();

        if (!$record) {
            return $this->failure(_l('mobile_otp_login_invalid_or_expired_otp'));
        }

        $this->db->where('id', $record->id);
        $this->db->update(db_prefix() . 'otp_login', ['is_used' => 1]);

        return [
            'success' => true,
            'user'    => $user,
        ];
    }

    public function normalize_mobile($mobile)
    {
        $mobile = preg_replace('/\D+/', '', (string) $mobile);

        if (strlen($mobile) < 10 || strlen($mobile) > 15) {
            return '';
        }

        return $mobile;
    }

    public function find_staff_by_mobile($mobile)
    {
        $candidates = $this->mobile_candidates($mobile);
        $quoted     = array_map([$this->db, 'escape'], $candidates);
        $cleanExpr  = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(`mobile`, ' ', ''), '-', ''), '+', ''), '(', ''), ')', '')";

        $this->db->where_in('active', [0, 1]);
        $this->db->where($cleanExpr . ' IN (' . implode(',', $quoted) . ')', null, false);
        $this->db->order_by('staffid', 'ASC');

        return $this->db->get(db_prefix() . 'staff')->row();
    }

    private function validate_send_rate_limit($mobile)
    {
        $resendAfter = max(1, (int) get_option('mobile_otp_login_resend_after_seconds'));
        $window      = max(60, (int) get_option('mobile_otp_login_send_limit_window'));
        $maxSends    = max(1, (int) get_option('mobile_otp_login_max_sends_per_window'));

        $this->db->where('mobile', $mobile);
        $this->db->order_by('id', 'DESC');
        $latest = $this->db->get(db_prefix() . 'otp_login')->row();

        if ($latest) {
            $elapsed = time() - strtotime($latest->created_at);
            if ($elapsed < $resendAfter) {
                return $this->failure(
                    sprintf(_l('mobile_otp_login_wait_before_resend'), $resendAfter - $elapsed),
                    429
                );
            }
        }

        $this->db->where('mobile', $mobile);
        $this->db->where('created_at >=', date('Y-m-d H:i:s', time() - $window));
        $recentCount = $this->db->count_all_results(db_prefix() . 'otp_login');

        if ($recentCount >= $maxSends) {
            return $this->failure(_l('mobile_otp_login_send_rate_limited'), 429);
        }

        return ['success' => true];
    }

    private function invalidate_unused_otps($mobile)
    {
        $this->db->where('mobile', $mobile);
        $this->db->where('is_used', 0);
        $this->db->update(db_prefix() . 'otp_login', ['is_used' => 1]);
    }

    private function mobile_candidates($mobile)
    {
        $countryCode = preg_replace('/\D+/', '', (string) get_option('mobile_otp_login_default_country_code'));
        $candidates  = [$mobile];

        if ($countryCode !== '' && strpos($mobile, $countryCode) === 0 && strlen($mobile) > 10) {
            $candidates[] = substr($mobile, strlen($countryCode));
        }

        if ($countryCode !== '' && strlen($mobile) === 10) {
            $candidates[] = $countryCode . $mobile;
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function send_otp_via_provider($mobile, $otp)
    {
        $provider = strtolower(trim((string) get_option('mobile_otp_login_sms_provider')));

        if ($provider === 'msg91') {
            return $this->send_msg91_otp($mobile, $otp);
        }

        return $this->send_fast2sms_otp($mobile, $otp);
    }

    private function is_test_mode_enabled()
    {
        return (string) get_option('mobile_otp_login_test_mode') === '1';
    }

    private function send_fast2sms_otp($mobile, $otp)
    {
        $apiKey = trim((string) get_option('mobile_otp_login_fast2sms_api_key'));
        $route  = trim((string) get_option('mobile_otp_login_fast2sms_route'));
        $flash  = trim((string) get_option('mobile_otp_login_fast2sms_flash'));

        if ($apiKey === '') {
            return $this->failure(_l('mobile_otp_login_fast2sms_not_configured'));
        }

        if ($route === '') {
            $route = 'otp';
        }

        $payload = [
            'variables_values' => $otp,
            'route'            => $route,
            'numbers'          => $mobile,
            'flash'            => ($flash === '1' ? '1' : '0'),
        ];

        $apiResponse = $this->post_fast2sms($apiKey, $payload);

        if (!$apiResponse['success']) {
            $decoded = $apiResponse['decoded'];

            // If OTP API is blocked until KYC/website verification, try Quick SMS fallback.
            if (is_array($decoded) && isset($decoded['status_code']) && (int) $decoded['status_code'] === 996) {
                $quickResponse = $this->post_fast2sms($apiKey, [
                    'message'  => 'Your OTP is ' . $otp . ' for Perfex login. Do not share it.',
                    'language' => 'english',
                    'route'    => 'q',
                    'numbers'  => $mobile,
                    'flash'    => ($flash === '1' ? '1' : '0'),
                ]);

                if ($quickResponse['success']) {
                    return ['success' => true];
                }

                $quickMessage = $this->extract_fast2sms_error_message($quickResponse['decoded']);
                log_activity('Mobile OTP Login Fast2SMS quick route failed response: ' . $quickResponse['response']);

                return $this->failure($quickMessage ?: _l('mobile_otp_login_sms_failed'));
            }

            $providerMessage = $this->extract_fast2sms_error_message($decoded);
            log_activity('Mobile OTP Login Fast2SMS failed response: ' . $apiResponse['response']);

            return $this->failure($providerMessage ?: _l('mobile_otp_login_sms_failed'));
        }

        return ['success' => true];
    }

    private function post_fast2sms($apiKey, array $payload)
    {
        $ch = curl_init('https://www.fast2sms.com/dev/bulkV2');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => [
                'authorization: ' . $apiKey,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_POSTFIELDS     => http_build_query($payload),
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            log_activity('Mobile OTP Login Fast2SMS error: ' . $error);

            return [
                'success'  => false,
                'response' => '',
                'decoded'  => null,
            ];
        }

        $decoded = json_decode($response, true);
        $success = $httpCode >= 200
            && $httpCode < 300
            && is_array($decoded)
            && isset($decoded['return'])
            && (bool) $decoded['return'] === true;

        return [
            'success'  => $success,
            'response' => $response,
            'decoded'  => $decoded,
        ];
    }

    private function extract_fast2sms_error_message($decoded)
    {
        if (!is_array($decoded)) {
            return null;
        }

        if (!empty($decoded['message']) && is_string($decoded['message'])) {
            return $decoded['message'];
        }

        if (!empty($decoded['message']) && is_array($decoded['message'])) {
            return implode(' ', $decoded['message']);
        }

        return null;
    }

    private function send_msg91_otp($mobile, $otp)
    {
        $authKey   = trim((string) get_option('mobile_otp_login_msg91_authkey'));
        $flowId    = trim((string) get_option('mobile_otp_login_msg91_flow_id'));
        $sender    = trim((string) get_option('mobile_otp_login_msg91_sender'));
        $route     = trim((string) get_option('mobile_otp_login_msg91_route'));
        $otpVarKey = trim((string) get_option('mobile_otp_login_msg91_otp_variable'));

        if ($authKey === '' || $flowId === '') {
            return $this->failure(_l('mobile_otp_login_msg91_not_configured'));
        }

        if ($otpVarKey === '') {
            $otpVarKey = 'OTP';
        }

        $recipient = [
            'mobiles' => $mobile,
        ];
        $recipient[$otpVarKey] = $otp;

        $payload = [
            'flow_id'    => $flowId,
            'recipients' => [$recipient],
        ];

        if ($sender !== '') {
            $payload['sender'] = $sender;
        }

        if ($route !== '') {
            $payload['route'] = $route;
        }

        $ch = curl_init('https://api.msg91.com/api/v5/flow/');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => [
                'authkey: ' . $authKey,
                'content-type: application/json',
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            log_activity('Mobile OTP Login SMS error: ' . $error);
            return $this->failure(_l('mobile_otp_login_sms_failed'));
        }

        $decoded = json_decode($response, true);
        $success = $httpCode >= 200 && $httpCode < 300 && isset($decoded['type']) && strtolower((string) $decoded['type']) === 'success';

        if (!$success) {
            log_activity('Mobile OTP Login SMS failed response: ' . $response);
            return $this->failure(_l('mobile_otp_login_sms_failed'));
        }

        return ['success' => true];
    }

    private function failure($message, $statusCode = 422)
    {
        return [
            'success'     => false,
            'message'     => $message,
            'status_code' => $statusCode,
        ];
    }
}
