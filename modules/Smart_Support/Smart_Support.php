<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Smart Support
Description: Smart Support module for Perfex CRM
Version: 1.0.0
Requires at least: 2.3.*
Author: Saurabh Patel
*/

define('SMART_SUPPORT_MODULE_NAME', 'smart_support');
register_activation_hook(
    SMART_SUPPORT_MODULE_NAME,
    'smart_support_activate'
);

function smart_support_activate()
{
    $install_file = module_dir_path(
        SMART_SUPPORT_MODULE_NAME,
        'install.php'
    );

    if (!file_exists($install_file)) {
        die('Smart Support install.php not found: ' . $install_file);
    }

    require_once $install_file;

    
}

register_language_files(
    SMART_SUPPORT_MODULE_NAME,
    ['Smart_Support']
);

hooks()->add_action(
    'admin_init',
    'smart_support_init_menu_items'
);

function smart_support_init_menu_items()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('smart_support', [
        'name'     => _l('smart_support'),
        'href'     => admin_url('smart_support'),
        'collapse' => true,
        'position' => 30,
        'icon'     => 'fa fa-life-ring',
    ]);

    $CI->app_menu->add_sidebar_children_item('smart_support', [
        'slug'     => 'smart_support_tickets',
        'name'     => _l('smart_support_tickets'),
        'href'     => admin_url('smart_support/tickets'),
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('smart_support', [
        'slug'     => 'smart_support_categories',
        'name'     => _l('smart_support_categories'),
        'href'     => admin_url('smart_support/categories'),
        'position' => 2,
    ]);

    $CI->app_menu->add_sidebar_children_item('smart_support', [
        'slug'     => 'smart_support_predefined_replies',
        'name'     => _l('smart_support_predefined_replies'),
        'href'     => admin_url('smart_support/predefined_replies'),
        'position' => 3,
    ]);

    $CI->app_menu->add_sidebar_children_item('smart_support', [
        'slug'     => 'smart_support_estimate_requests',
        'name'     => _l('smart_support_estimate_requests'),
        'href'     => admin_url('smart_support/estimate_requests'),
        'position' => 4,
    ]);
}

hooks()->add_action('app_admin_head', 'smart_support_add_css');
hooks()->add_action('app_admin_footer', 'smart_support_add_scripts');

function smart_support_add_css()
{
    if (is_admin()) {
        echo '<link href="' . base_url('modules/smart_support/assets/css/Smart_Support.css') . '" rel="stylesheet" type="text/css" />';
    }
}

function smart_support_add_scripts()
{
    if (is_admin()) {
        echo '<script src="' . base_url('modules/smart_support/assets/js/Smart_Support.js') . '"></script>';
    }
}