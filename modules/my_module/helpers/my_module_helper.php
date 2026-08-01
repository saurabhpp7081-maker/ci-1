<?php

defined('BASEPATH') or exit('No direct script access allowed');

function have_assigned_my_tasks()
{
    $CI = &get_instance();

    $CI->db->where('assigned', get_staff_user_id());

    return $CI->db->count_all_results(db_prefix() . 'my_tasks') > 0;
}