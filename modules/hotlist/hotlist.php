<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Hotlist
Description: Hotlist Module
Version: 1.0.0
*/

define('HOTLIST_MODULE_NAME', 'hotlist');

register_language_files(HOTLIST_MODULE_NAME, ['custom']);
register_activation_hook(HOTLIST_MODULE_NAME, 'hotlist_module_activate');

hooks()->add_action('admin_init', 'hotlist_init_menu');
hooks()->add_action('app_init', 'hotlist_bootstrap_helpers');
hooks()->add_action('app_admin_footer', 'hotlist_lead_custom_field');


function hotlist_module_activate()
{
    $CI = &get_instance();

    if (!$CI->db->table_exists(db_prefix() . 'hotlist_rounds')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'hotlist_rounds` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `active` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }

    if (!$CI->db->table_exists(db_prefix() . 'hotlist_lead_rounds')) {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'hotlist_lead_rounds` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `lead_id` INT UNSIGNED NOT NULL,
            `round_id` INT UNSIGNED NULL,
            `company_name` VARCHAR(255) NULL,
            PRIMARY KEY (`id`),
            KEY `lead_id` (`lead_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
    }

    $CI->db->from(db_prefix() . 'hotlist_rounds');
    if ((int) $CI->db->count_all_results() === 0) {
        $CI->db->insert_batch(db_prefix() . 'hotlist_rounds', [
            ['name' => 'L1 Round', 'active' => 1],
            ['name' => 'L2 Round', 'active' => 1],
            ['name' => 'L3 Round', 'active' => 1],
        ]);
    }
}

function hotlist_init_menu()
{
    $CI = &get_instance();

    $CI->app_menu->add_sidebar_menu_item('hotlist', [
        'name'     => 'Hotlist',
        'icon'     => 'fa fa-list',
        'href'     => admin_url('hotlist'),
        'position' => 20,
    ]);
}

function hotlist_bootstrap_helpers()
{
    if (function_exists('leads_public_url')) {
        return;
    }

    $helperPath = APPPATH . 'helpers/leads_helper.php';
    if (file_exists($helperPath)) {
        require_once $helperPath;
    }
}
function hotlist_lead_custom_field()
{
    $CI = &get_instance();
    if (strtolower($CI->router->fetch_class()) !== 'hotlist') {
        return;
    }
    $CI->load->model('hotlist/hotlist_model');
    $rounds = $CI->hotlist_model->get_round(); ?> <script>
        $(document).on('shown.bs.modal', '#lead-modal', function() {
            setTimeout(function() {
                if ($('#hotlist_extra_fields_row').length) {
                    return;
                }
                
                $('#lead-modal .selectpicker').selectpicker('refresh');
            }, 200);
        });
    </script> <?php }
