<?php

defined('BASEPATH') or exit('No direct script access allowed');


if (!function_exists('student_status')) {
    function student_status($status)
    {
        if ($status == 1) {
            return '<span class="label label-success">'
                . _l('active')
                . '</span>';
        }

        return '<span class="label label-danger">'
            . _l('inactive')
            . '</span>';
    }
}

if (!function_exists('get_student_name')) {
    function get_student_name($student_id)
    {
        $CI = &get_instance();

        $student = $CI->db
            ->where('id', $student_id)
            ->get(db_prefix() . 'students')
            ->row();

        return $student ? $student->name : '';
    }
}


function render_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
{
    $_additional_classes = '';
    $_table_attributes   = ' ';
    if (count($additional_classes) > 0) {
        $_additional_classes = ' ' . implode(' ', $additional_classes);
    }
    $CI      = &get_instance();
    $browser = $CI->agent->browser();
    $IEfix   = '';
    if ($browser == 'Internet Explorer') {
        $IEfix = 'ie-dt-fix';
    }

    foreach ($table_attributes as $key => $val) {
        $_table_attributes .= $key . '="' . $val . '" ';
    }

    $table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
    $table .= '<thead>';
    $table .= '<tr>';

    foreach ($headings as $heading) {
        if (! is_array($heading)) {
            $table .= '<th>' . $heading . '</th>';
        } else {
            $th_attrs = '';
            if (isset($heading['th_attrs'])) {
                foreach ($heading['th_attrs'] as $key => $val) {
                    $th_attrs .= $key . '="' . $val . '" ';
                }
            }
            $th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
            $table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
        }
    }
    $table .= '</tr>';
    $table .= '</thead>';
    $table .= '<tbody></tbody>';
    $table .= '</table></div>';
    echo $table;
}

/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 *
 * @return array
 */

