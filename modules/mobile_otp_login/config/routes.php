<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['mobile_otp_login']           = 'mobile_otp_login/index';
$route['mobile_otp_login/send_otp']  = 'mobile_otp_login/send_otp';
$route['mobile_otp_login/verify_otp'] = 'mobile_otp_login/verify_otp';
$route['mobile_otp_login/resend_otp'] = 'mobile_otp_login/resend_otp';
