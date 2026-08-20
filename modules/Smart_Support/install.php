<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

$prefix = db_prefix();
$now    = date('Y-m-d H:i:s');

/*
 * Smart Support Categories
 */
if (!$CI->db->table_exists($prefix . 'ssx_categories')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_categories` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `description` TEXT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `sort_order` INT NOT NULL DEFAULT 0,
            `created_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_categories_status` (`status`),
            KEY `ssx_categories_sort_order` (`sort_order`),
            KEY `ssx_categories_created_by` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Predefined Replies
 */
if (!$CI->db->table_exists($prefix . 'ssx_predefined_replies')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_predefined_replies` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `message` TEXT NOT NULL,
            `category_id` INT UNSIGNED NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `sort_order` INT NOT NULL DEFAULT 0,
            `created_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_predefined_replies_category_id` (`category_id`),
            KEY `ssx_predefined_replies_status` (`status`),
            KEY `ssx_predefined_replies_created_by` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Ticket Meta
 *
 * Perfex's existing ticket table remains the main ticket table.
 * This table stores only Smart Support specific ticket information.
 */
if (!$CI->db->table_exists($prefix . 'ssx_ticket_meta')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_ticket_meta` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ticket_id` INT UNSIGNED NOT NULL,
            `category_id` INT UNSIGNED NULL,
            `project_id` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ssx_ticket_meta_ticket_id` (`ticket_id`),
            KEY `ssx_ticket_meta_category_id` (`category_id`),
            KEY `ssx_ticket_meta_project_id` (`project_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Customer Requests
 */
if (!$CI->db->table_exists($prefix . 'ssx_customer_requests')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_customer_requests` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `client_id` INT UNSIGNED NOT NULL,
            `contact_id` INT UNSIGNED NULL,
            `project_id` INT UNSIGNED NULL,
            `subject` VARCHAR(191) NOT NULL,
            `description` TEXT NULL,
            `category_id` INT UNSIGNED NULL,
            `priority` VARCHAR(50) NOT NULL DEFAULT 'medium',
            `status` VARCHAR(50) NOT NULL DEFAULT 'new',
            `converted_ticket_id` INT UNSIGNED NULL,
            `assigned_to` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_customer_requests_client_id` (`client_id`),
            KEY `ssx_customer_requests_contact_id` (`contact_id`),
            KEY `ssx_customer_requests_project_id` (`project_id`),
            KEY `ssx_customer_requests_category_id` (`category_id`),
            KEY `ssx_customer_requests_status` (`status`),
            KEY `ssx_customer_requests_assigned_to` (`assigned_to`),
            KEY `ssx_customer_requests_ticket_id` (`converted_ticket_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Estimate Requests
 */
if (!$CI->db->table_exists($prefix . 'ssx_estimate_requests')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_estimate_requests` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `request_number` VARCHAR(50) NOT NULL,
            `client_id` INT UNSIGNED NOT NULL,
            `contact_id` INT UNSIGNED NULL,
            `project_id` INT UNSIGNED NULL,
            `service` VARCHAR(191) NOT NULL,
            `requirement` VARCHAR(255) NULL,
            `description` TEXT NULL,
            `budget` DECIMAL(15,2) NULL,
            `deadline` DATE NULL,
            `status` VARCHAR(50) NOT NULL DEFAULT 'new',
            `assigned_to` INT UNSIGNED NULL,
            `estimate_id` INT UNSIGNED NULL,
            `admin_notes` TEXT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ssx_estimate_requests_number` (`request_number`),
            KEY `ssx_estimate_requests_client_id` (`client_id`),
            KEY `ssx_estimate_requests_contact_id` (`contact_id`),
            KEY `ssx_estimate_requests_project_id` (`project_id`),
            KEY `ssx_estimate_requests_status` (`status`),
            KEY `ssx_estimate_requests_assigned_to` (`assigned_to`),
            KEY `ssx_estimate_requests_estimate_id` (`estimate_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Documents
 */
if (!$CI->db->table_exists($prefix . 'ssx_documents')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_documents` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `client_id` INT UNSIGNED NULL,
            `contact_id` INT UNSIGNED NULL,
            `project_id` INT UNSIGNED NULL,
            `ticket_id` INT UNSIGNED NULL,
            `customer_request_id` INT UNSIGNED NULL,
            `estimate_request_id` INT UNSIGNED NULL,
            `name` VARCHAR(191) NOT NULL,
            `file_name` VARCHAR(255) NOT NULL,
            `file_path` VARCHAR(500) NOT NULL,
            `file_type` VARCHAR(100) NULL,
            `file_size` BIGINT UNSIGNED NULL,
            `visibility` VARCHAR(50) NOT NULL DEFAULT 'private',
            `uploaded_by` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_documents_client_id` (`client_id`),
            KEY `ssx_documents_contact_id` (`contact_id`),
            KEY `ssx_documents_project_id` (`project_id`),
            KEY `ssx_documents_ticket_id` (`ticket_id`),
            KEY `ssx_documents_customer_request_id` (`customer_request_id`),
            KEY `ssx_documents_estimate_request_id` (`estimate_request_id`),
            KEY `ssx_documents_visibility` (`visibility`),
            KEY `ssx_documents_uploaded_by` (`uploaded_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Notifications
 */
if (!$CI->db->table_exists($prefix . 'ssx_notifications')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_notifications` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_type` VARCHAR(50) NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `ticket_id` INT UNSIGNED NULL,
            `customer_request_id` INT UNSIGNED NULL,
            `estimate_request_id` INT UNSIGNED NULL,
            `type` VARCHAR(100) NOT NULL,
            `title` VARCHAR(191) NOT NULL,
            `message` TEXT NULL,
            `is_read` TINYINT(1) NOT NULL DEFAULT 0,
            `read_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_notifications_user` (`user_type`, `user_id`),
            KEY `ssx_notifications_ticket_id` (`ticket_id`),
            KEY `ssx_notifications_customer_request_id` (`customer_request_id`),
            KEY `ssx_notifications_estimate_request_id` (`estimate_request_id`),
            KEY `ssx_notifications_type` (`type`),
            KEY `ssx_notifications_is_read` (`is_read`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Activity Logs
 */
if (!$CI->db->table_exists($prefix . 'ssx_activity_logs')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_activity_logs` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_type` VARCHAR(50) NULL,
            `user_id` INT UNSIGNED NULL,
            `ticket_id` INT UNSIGNED NULL,
            `customer_request_id` INT UNSIGNED NULL,
            `estimate_request_id` INT UNSIGNED NULL,
            `action` VARCHAR(100) NOT NULL,
            `description` TEXT NULL,
            `ip_address` VARCHAR(45) NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `ssx_activity_logs_user` (`user_type`, `user_id`),
            KEY `ssx_activity_logs_ticket_id` (`ticket_id`),
            KEY `ssx_activity_logs_customer_request_id` (`customer_request_id`),
            KEY `ssx_activity_logs_estimate_request_id` (`estimate_request_id`),
            KEY `ssx_activity_logs_action` (`action`),
            KEY `ssx_activity_logs_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Settings
 */
if (!$CI->db->table_exists($prefix . 'ssx_settings')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_settings` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `value` TEXT NULL,
            `autoload` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ssx_settings_name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Smart Support Staff Permissions
 */
if (!$CI->db->table_exists($prefix . 'ssx_staff_permissions')) {
    $CI->db->query("
        CREATE TABLE `{$prefix}ssx_staff_permissions` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `staff_id` INT UNSIGNED NOT NULL,
            `permission` VARCHAR(100) NOT NULL,
            `permission_value` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ssx_staff_permissions_unique` (`staff_id`, `permission`),
            KEY `ssx_staff_permissions_permission` (`permission`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}

/*
 * Default Smart Support Settings
 */
$defaultSettings = [
    'ticket_prefix' => 'SS-',
    'estimate_request_prefix' => 'ER-',
    'default_ticket_priority' => 'medium',
    'default_ticket_status' => 'new',
    'allow_customer_ticket_attachment' => '1',
    'allow_customer_document_upload' => '1',
    'notify_customer_ticket_created' => '1',
    'notify_customer_staff_reply' => '1',
    'notify_customer_status_changed' => '1',
    'notify_customer_ticket_resolved' => '1',
];

foreach ($defaultSettings as $name => $value) {
    $exists = $CI->db
        ->where('name', $name)
        ->get($prefix . 'ssx_settings')
        ->row();

    if (!$exists) {
        $CI->db->insert($prefix . 'ssx_settings', [
            'name'       => $name,
            'value'      => $value,
            'autoload'   => 1,
            'created_at' => $now,
        ]);
    }
}

/*
 * Default Support Categories
 */
$defaultCategories = [
    'Technical Support',
    'Billing',
    'Payment',
    'Account',
    'Website',
    'Server',
    'General Query',
    'Project Related',
    'Other',
];

foreach ($defaultCategories as $category) {
    $exists = $CI->db
        ->where('name', $category)
        ->get($prefix . 'ssx_categories')
        ->row();

    if (!$exists) {
        $CI->db->insert($prefix . 'ssx_categories', [
            'name'       => $category,
            'status'     => 1,
            'sort_order' => 0,
            'created_at' => $now,
        ]);
    }
}

/*
 * Default Predefined Reply
 */
$replyExists = $CI->db
    ->where('name', 'Ticket Received')
    ->get($prefix . 'ssx_predefined_replies')
    ->row();

if (!$replyExists) {
    $CI->db->insert($prefix . 'ssx_predefined_replies', [
        'name'       => 'Ticket Received',
        'message'    => 'We have received your support request. Our team is currently reviewing your issue.',
        'status'     => 1,
        'sort_order' => 0,
        'created_at' => $now,
    ]);
}

