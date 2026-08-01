<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('leads_public_url')) {
    $helperPath = APPPATH . 'helpers/leads_helper.php';

    if (file_exists($helperPath)) {
        require_once $helperPath;
    }
}
