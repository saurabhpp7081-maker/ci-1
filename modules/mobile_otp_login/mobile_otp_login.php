<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Mobile OTP Login
Description: Staff login using mobile number and OTP without modifying Perfex core authentication files.
Version: 1.0.0
Requires at least: 2.3.*
*/

define('MOBILE_OTP_LOGIN_MODULE_NAME', 'mobile_otp_login');

require_once __DIR__ . '/install.php';

register_language_files(MOBILE_OTP_LOGIN_MODULE_NAME, ['mobile_otp_login']);
register_activation_hook(MOBILE_OTP_LOGIN_MODULE_NAME, 'mobile_otp_login_module_activate');

hooks()->add_action('admin_auth_init', 'mobile_otp_login_maybe_redirect_default_auth');
hooks()->add_action('app_admin_authentication_head', 'mobile_otp_login_inject_auth_switcher');
hooks()->add_filter('module_' . MOBILE_OTP_LOGIN_MODULE_NAME . '_action_links', 'mobile_otp_login_module_action_links');

function mobile_otp_login_maybe_redirect_default_auth()
{
    $CI = &get_instance();

    $useClassicCookie = $CI->input->cookie('mobile_otp_login_classic', true);
    if ($useClassicCookie === '1') {
        delete_cookie('mobile_otp_login_classic');
    }

    if (is_staff_logged_in() || $CI->input->post('email') || $CI->input->get('classic') === '1' || $useClassicCookie === '1') {
        return;
    }

    if (strtolower($CI->router->fetch_class()) !== 'authentication') {
        return;
    }

    $method = strtolower($CI->router->fetch_method());
    if (!in_array($method, ['index', 'admin'], true)) {
        return;
    }

    redirect(admin_url('mobile-login'));
}

function mobile_otp_login_module_action_links($actions)
{
    $actions[] = '<a href="' . admin_url('mobile-login') . '">' . _l('mobile_otp_login_open_login') . '</a>';
    $actions[] = '<a href="' . admin_url('authentication') . '">' . _l('mobile_otp_login_open_classic_login') . '</a>';

    return $actions;
}

function mobile_otp_login_inject_auth_switcher()
{
    $mobileLoginUrl = admin_url('mobile-login');
    $classicLoginUrl = admin_url('authentication');
    ?>
    <style>
        .mobile-otp-auth-switcher {
            margin-top: 14px;
            text-align: center;
        }

        .mobile-otp-auth-switcher a {
            color: #64748b;
            font-size: 13px;
            text-decoration: none;
        }

        .mobile-otp-auth-switcher a:hover,
        .mobile-otp-auth-switcher a:focus {
            color: #2563eb;
            text-decoration: none;
        }
    </style>
    <script>
        $(function () {
            var currentUrl = window.location.pathname.toLowerCase();
            var isAuthPage = currentUrl.indexOf('/authentication') !== -1;
            var isMobileOtpPage = currentUrl.indexOf('/mobile-login') !== -1;

            if (!isAuthPage && !isMobileOtpPage) {
                return;
            }

            var switcherId = 'mobile-otp-auth-switcher';
            if ($('#' + switcherId).length) {
                return;
            }

            var linkHtml = '';

            if (isAuthPage) {
                linkHtml = '<div id="' + switcherId + '" class="mobile-otp-auth-switcher">' +
                    '<a href="<?= e($mobileLoginUrl); ?>"><?= addslashes('Use mobile OTP instead'); ?></a>' +
                '</div>';
            } else if (isMobileOtpPage) {
                linkHtml = '<div id="' + switcherId + '" class="mobile-otp-auth-switcher">' +
                    '<a href="<?= e($classicLoginUrl); ?>" id="mobile-otp-use-classic"><?= addslashes('Use email and password instead'); ?></a>' +
                '</div>';
            }

            if (!linkHtml) {
                return;
            }

            var $visibleForm = $('form:visible').first();
            var $primaryButton = $visibleForm.find('.btn.btn-primary').last();

            if ($primaryButton.length) {
                $(linkHtml).insertAfter($primaryButton);
            }

            $(document).on('click', '#mobile-otp-use-classic', function () {
                document.cookie = 'mobile_otp_login_classic=1; path=/';
            });
        });
    </script>
    <?php
}
