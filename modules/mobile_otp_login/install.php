<?php

defined('BASEPATH') or exit('No direct script access allowed');

function mobile_otp_login_module_activate()
{
    $CI = &get_instance();

    mobile_otp_login_create_mobile_column($CI);
    mobile_otp_login_create_otp_table($CI);
    mobile_otp_login_seed_options();
    mobile_otp_login_register_custom_routes();
}

function mobile_otp_login_create_mobile_column($CI)
{
    $table = db_prefix() . 'staff';

    if (!$CI->db->field_exists('mobile', $table)) {
        $CI->db->query("ALTER TABLE `{$table}` ADD `mobile` VARCHAR(15) NULL AFTER `phonenumber`");
    }

    $indexName = 'mobile';
    $result    = $CI->db->query("SHOW INDEX FROM `{$table}` WHERE Key_name = " . $CI->db->escape($indexName))->row();

    if (!$result) {
        try {
            $CI->db->query("ALTER TABLE `{$table}` ADD UNIQUE KEY `{$indexName}` (`mobile`)");
        } catch (Throwable $e) {
            log_activity('Mobile OTP Login: unable to add unique index on staff.mobile - ' . $e->getMessage());
        }
    }
}

function mobile_otp_login_create_otp_table($CI)
{
    $table = db_prefix() . 'otp_login';

    $CI->db->query(
        "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `mobile` VARCHAR(15) NOT NULL,
            `otp` VARCHAR(6) NOT NULL,
            `expires_at` DATETIME NOT NULL,
            `is_used` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `mobile_created_at` (`mobile`, `created_at`),
            KEY `otp_lookup` (`mobile`, `otp`, `is_used`, `expires_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set
    );
}

function mobile_otp_login_seed_options()
{
    $defaults = [
        'mobile_otp_login_test_mode'             => '1',
        'mobile_otp_login_sms_provider'          => 'fast2sms',
        'mobile_otp_login_fast2sms_api_key'      => '',
        'mobile_otp_login_fast2sms_route'        => 'otp',
        'mobile_otp_login_fast2sms_flash'        => '0',
        'mobile_otp_login_msg91_authkey'          => '',
        'mobile_otp_login_msg91_flow_id'          => '',
        'mobile_otp_login_msg91_sender'           => '',
        'mobile_otp_login_msg91_route'            => '4',
        'mobile_otp_login_msg91_otp_variable'     => 'OTP',
        'mobile_otp_login_default_country_code'   => '91',
        'mobile_otp_login_expiry_seconds'         => '120',
        'mobile_otp_login_resend_after_seconds'   => '60',
        'mobile_otp_login_send_limit_window'      => '600',
        'mobile_otp_login_max_sends_per_window'   => '3',
        'mobile_otp_login_max_verify_attempts'    => '5',
        'mobile_otp_login_verify_block_seconds'   => '600',
    ];

    foreach ($defaults as $name => $value) {
        if (get_option($name) === false) {
            add_option($name, $value);
        }
    }
}

function mobile_otp_login_register_custom_routes()
{
    $routesPath = APPPATH . 'config/my_routes.php';
    $markerA    = "// MOBILE OTP LOGIN ROUTES START";
    $markerB    = "// MOBILE OTP LOGIN ROUTES END";
    $block      = implode(PHP_EOL, [
        $markerA,
        "\$route['admin/mobile-login'] = 'mobile_otp_login/mobile_otp_login/index';",
        "\$route['admin/mobile-login/send-otp'] = 'mobile_otp_login/mobile_otp_login/send_otp';",
        "\$route['admin/mobile-login/verify'] = 'mobile_otp_login/mobile_otp_login/verify_otp';",
        "\$route['admin/mobile-login/resend'] = 'mobile_otp_login/mobile_otp_login/resend_otp';",
        "\$route['mobile-login'] = 'mobile_otp_login/mobile_otp_login/index';",
        "\$route['mobile-login/send-otp'] = 'mobile_otp_login/mobile_otp_login/send_otp';",
        "\$route['mobile-login/verify'] = 'mobile_otp_login/mobile_otp_login/verify_otp';",
        "\$route['mobile-login/resend'] = 'mobile_otp_login/mobile_otp_login/resend_otp';",
        $markerB,
    ]);

    if (!file_exists($routesPath)) {
        file_put_contents(
            $routesPath,
            "<?php" . PHP_EOL . PHP_EOL . "defined('BASEPATH') or exit('No direct script access allowed');" . PHP_EOL . PHP_EOL . $block . PHP_EOL
        );

        return;
    }

    $contents = file_get_contents($routesPath);
    if (strpos($contents, $markerA) !== false) {
        return;
    }

    $contents = rtrim($contents) . PHP_EOL . PHP_EOL . $block . PHP_EOL;
    file_put_contents($routesPath, $contents);
}
