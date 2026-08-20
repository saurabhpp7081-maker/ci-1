<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Student
Discription: Student Module
Version:1.0.0
*/

define('STUDENT_MODULE_NAME', 'student');
register_language_files(STUDENT_MODULE_NAME, ['student']);
register_activation_hook(STUDENT_MODULE_NAME, 'student_module_activate');
// register_uninstall_hook(STUDENT_MODULE_NAME, 'student_uninstall');

hooks()->add_action('admin_init', 'student_init_menu');
hooks()->add_action('app_admin_footer', 'student_footer');
hooks()->add_action('admin_navbar_start', 'student_top_nav');
// hooks()->add_action(
//     'after_dashboard',
//     'student_dashboard_overview'
// );

hooks()->add_filter('get_dashboard_widgets', 'student_dashboard_widgets');



// setup sidebar menu hook 
hooks()->add_filter('setup_menu_items', 'student_setup_menu_items', 999);





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

// stup menu section add student menu item
function student_setup_menu_items($items)
{
    $items['students'] = [
        'name'     => 'Student',
        'slug'     => 'student',
        'icon'     => '....',
        'href'     => '#',
        'position' => 7,
        'collapse' => true,
        'children' => [
            [
                'name'     => 'Departments',
                'slug'     => 'student-departments',
                'href'     => admin_url('student/departments'),
                'position' => 1,
                'icon'     => '.....',
            ],
            [
                'name'     => 'Courses',
                'slug'     => 'student-courses',
                'href'     => admin_url('student/courses'),
                'position' => 2,
                'icon'     => '.....',
            ],
        ],
    ];

    return $items;
}



// function student_uninstall()
// {
//     $CI = &get_instance();

//     log_message('error', '=== STUDENT UNINSTALL STARTED ===');

//     $students_table = db_prefix() . 'students';
//     $departments_table = db_prefix() . 'departments';

//     log_message('error', 'Checking table: ' . $students_table);

//     if ($CI->db->table_exists($students_table)) {

//         log_message('error', 'students table found, attempting drop...');

//         $CI->db->query('DROP TABLE `' . $students_table . '`');

//         $db_error = $CI->db->error();
//         if (!empty($db_error['message'])) {
//             log_message('error', 'DROP students ERROR: ' . print_r($db_error, true));
//         } else {
//             log_message('error', 'STUDENTS TABLE DROPPED: ' . $students_table);
//         }

//     } else {
//         log_message('error', 'students table NOT FOUND — nothing to drop');
//     }

//     if ($CI->db->table_exists($departments_table)) {

//         log_message('error', 'departments table found, attempting drop...');

//         $CI->db->query('DROP TABLE `' . $departments_table . '`');

//         $db_error = $CI->db->error();
//         if (!empty($db_error['message'])) {
//             log_message('error', 'DROP departments ERROR: ' . print_r($db_error, true));
//         } else {
//             log_message('error', 'DEPARTMENTS TABLE DROPPED: ' . $departments_table);
//         }

//     } else {
//         log_message('error', 'departments table NOT FOUND — nothing to drop');
//     }

//     log_message('error', '=== STUDENT UNINSTALL FINISHED ===');
// }









/**
 * Register Student Dashboard Widget
 */
function student_dashboard_widgets($widgets)
{
    $widgets[] = [
        'path'      => 'student/dashboard/overview',
        'container' => 'left-8',
    ];

    return $widgets;
}
