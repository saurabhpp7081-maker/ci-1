<?php

defined('BASEPATH') or exit('No direct script access allowed');

// MOBILE OTP LOGIN ROUTES START
$route['admin/mobile-login'] = 'mobile_otp_login/mobile_otp_login/index';
$route['admin/mobile-login/send-otp'] = 'mobile_otp_login/mobile_otp_login/send_otp';
$route['admin/mobile-login/verify'] = 'mobile_otp_login/mobile_otp_login/verify_otp';
$route['admin/mobile-login/resend'] = 'mobile_otp_login/mobile_otp_login/resend_otp';
$route['mobile-login'] = 'mobile_otp_login/mobile_otp_login/index';
$route['mobile-login/send-otp'] = 'mobile_otp_login/mobile_otp_login/send_otp';
$route['mobile-login/verify'] = 'mobile_otp_login/mobile_otp_login/verify_otp';
$route['mobile-login/resend'] = 'mobile_otp_login/mobile_otp_login/resend_otp';
// MOBILE OTP LOGIN ROUTES END
