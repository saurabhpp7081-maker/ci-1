<?php

defined('BASEPATH') or exit('No direct script access allowed');

$lang['mobile_otp_login_page_title']           = 'Mobile OTP Login';
$lang['mobile_otp_login_badge']                = 'Secure staff access';
$lang['mobile_otp_login_heading']              = 'Sign in with mobile OTP';
$lang['mobile_otp_login_subheading']           = 'Use your registered staff mobile number to receive a one-time password and access the Perfex admin area.';
$lang['mobile_otp_login_mobile_label']         = 'Mobile number';
$lang['mobile_otp_login_mobile_placeholder']   = 'Enter your mobile number';
$lang['mobile_otp_login_send_button']          = 'Send OTP';
$lang['mobile_otp_login_otp_label']            = 'One-time password';
$lang['mobile_otp_login_otp_help']             = 'Enter the 6-digit OTP sent to your registered mobile number.';
$lang['mobile_otp_login_verify_button']        = 'Verify and login';
$lang['mobile_otp_login_change_mobile']        = 'Change mobile';
$lang['mobile_otp_login_resend_button']        = 'Resend OTP';
$lang['mobile_otp_login_resend_in']            = 'Resend available in %s seconds';
$lang['mobile_otp_login_classic_login_link']   = 'Use email and password instead';
$lang['mobile_otp_login_open_login']           = 'Open mobile login';
$lang['mobile_otp_login_open_classic_login']   = 'Open classic login';
$lang['mobile_otp_login_invalid_mobile']       = 'Please enter a valid mobile number.';
$lang['mobile_otp_login_mobile_not_found']     = 'No staff account was found for this mobile number.';
$lang['mobile_otp_login_inactive_staff']       = 'This staff account is inactive.';
$lang['mobile_otp_login_otp_sent']             = 'OTP sent successfully.';
$lang['mobile_otp_login_otp_generated_test']   = 'OTP generated in test mode. Check the browser console.';
$lang['mobile_otp_login_sent_to']              = 'OTP sent to';
$lang['mobile_otp_login_invalid_otp']          = 'Please enter a valid 6-digit OTP.';
$lang['mobile_otp_login_invalid_or_expired_otp'] = 'The OTP is invalid, expired, or already used.';
$lang['mobile_otp_login_success']              = 'Login successful. Redirecting...';
$lang['mobile_otp_login_wait_before_resend']   = 'Please wait %s seconds before requesting another OTP.';
$lang['mobile_otp_login_send_rate_limited']    = 'Too many OTP requests. Please try again later.';
$lang['mobile_otp_login_too_many_attempts']    = 'Too many failed verification attempts. Please wait and request a new OTP.';
$lang['mobile_otp_login_fast2sms_not_configured'] = 'Fast2SMS is not configured yet. Please add the API key before sending OTPs.';
$lang['mobile_otp_login_msg91_not_configured'] = 'MSG91 is not configured yet. Please set the module options before sending OTPs.';
$lang['mobile_otp_login_sms_failed']           = 'OTP could not be sent at the moment. Please try again later.';
