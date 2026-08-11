<?php

defined('BASEPATH') or exit ('No direct script access allowed');

/*
Module Name: Student
Discription: Student Module
Version:1.0.0
*/

define ('STUDENT_MODULE_NAME', 'student');
register_language_files(STUDENT_MODULE_NAME,['student']);
register_activation_hook(STUDENT_MODULE_NAME,'student_module_activate');


hooks()->add_action('admin_init', 'student_init_menu');
hooks()->add_action('app_admin_footer', 'student_footer');
hooks()->add_action('admin_navbar_start', 'student_top_nav');
  


function student_module_activate()
{
    $CI = &get_instance();

    require_once(__DIR__ . '/install.php');
}



function student_init_menu()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('student-files', [
        'name'     => 'Student ',
        'icon'     => 'fa fa-graduation-cap',
        'href'     => admin_url('student'),
        'position' => 70,
    ]);
}

function student_footer()
{
    echo '<script src="' .
        module_dir_url(
            STUDENT_MODULE_NAME,
            'assets/js/student.js'
        ) .
        '"></script>';
}


function student_top_nav()
{
    echo '<li>
        <a href="' . admin_url('student') . '">
            <i class="fa fa-graduation-cap"></i>
            <span>Student</span>
        </a>
    </li>';
}