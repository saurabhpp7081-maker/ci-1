<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: My Module
Description: my module
Version: 1.0
*/

if(!defined('MY_MODULE')) {
   define('MY_MODULE', basename(__DIR__));
}

hooks()->add_action('admin_init', 'my_module_sidebar_menu');
register_activation_hook(MY_MODULE, 'my_module_activation_hook');

register_language_files(MY_MODULE, [MY_MODULE]);

function my_module_sidebar_menu()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('my-module-menu', [
        'name'     => 'My Module',
        'href'     => admin_url('my_module'),
        'icon'     => 'fa fa-folder',
        'position' => 60,
    ]);
}

$CI = &get_instance();          

// $CI->load->helper('my_module_helper');
require_once(__DIR__ . '/helpers/my_module_helper.php');




register_staff_capabilities('my_module', [
    'capabilities' => [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ],
]);

