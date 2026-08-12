<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();


/*
|--------------------------------------------------------------------------
| Students Table
|--------------------------------------------------------------------------
*/

if (!$CI->db->table_exists(db_prefix() . 'students')) {

    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "students` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `admission_no` VARCHAR(30) NOT NULL,
            `roll_no` VARCHAR(30) DEFAULT NULL,
            `full_name` VARCHAR(100) NOT NULL,
            `father_name` VARCHAR(100) DEFAULT NULL,
            `mother_name` VARCHAR(100) DEFAULT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `gender` ENUM('Male','Female','Other') DEFAULT NULL,
            `dob` DATE DEFAULT NULL,
            `class` VARCHAR(50) DEFAULT NULL,
            `section` VARCHAR(50) DEFAULT NULL,
            `course` VARCHAR(100) DEFAULT NULL,
            `department_id` INT(11) DEFAULT NULL,
            `address` TEXT DEFAULT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            PRIMARY KEY (`id`),
            UNIQUE KEY `admission_no` (`admission_no`),
            KEY `department_id` (`department_id`)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}


/*
|--------------------------------------------------------------------------
| Add department_id if Students table already exists
|--------------------------------------------------------------------------
*/

if (
    $CI->db->table_exists(db_prefix() . 'students')
    && !$CI->db->field_exists(
        'department_id',
        db_prefix() . 'students'
    )
) {

    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "students`
        ADD `department_id` INT(11) DEFAULT NULL
        AFTER `course`
    ");

}


/*
|--------------------------------------------------------------------------
| Add department_id index if needed
|--------------------------------------------------------------------------
*/

if ($CI->db->table_exists(db_prefix() . 'students')) {

    $fields = $CI->db->list_fields(
        db_prefix() . 'students'
    );

    if (
        in_array('department_id', $fields)
        && !$CI->db->query(
            "SHOW INDEX FROM `" . db_prefix() . "students`
             WHERE Key_name = 'department_id'"
        )->num_rows()
    ) {

        $CI->db->query("
            ALTER TABLE `" . db_prefix() . "students`
            ADD INDEX `department_id` (`department_id`)
        ");

    }
}


/*
|--------------------------------------------------------------------------
| Departments Table
|--------------------------------------------------------------------------
*/

if (!$CI->db->table_exists(db_prefix() . 'departments')) {

    $CI->db->query("
        CREATE TABLE `" . db_prefix() . "departments` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(150) NOT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            PRIMARY KEY (`id`),
            UNIQUE KEY `department_name` (`name`)

        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}


/*
|--------------------------------------------------------------------------
| Add status if Departments table already exists
|--------------------------------------------------------------------------
*/

if (
    $CI->db->table_exists(db_prefix() . 'departments')
    && !$CI->db->field_exists(
        'status',
        db_prefix() . 'departments'
    )
) {

    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "departments`
        ADD `status` TINYINT(1) NOT NULL DEFAULT 1
        AFTER `name`
    ");
}


/*
|--------------------------------------------------------------------------
| Add created_at if Departments table already exists
|--------------------------------------------------------------------------
*/

if (
    $CI->db->table_exists(db_prefix() . 'departments')
    && !$CI->db->field_exists(
        'created_at',
        db_prefix() . 'departments'
    )
) {

    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "departments`
        ADD `created_at` DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
    ");
}


/*
|--------------------------------------------------------------------------
| Add updated_at if Departments table already exists
|--------------------------------------------------------------------------
*/

if (
    $CI->db->table_exists(db_prefix() . 'departments')
    && !$CI->db->field_exists(
        'updated_at',
        db_prefix() . 'departments'
    )
) {

    $CI->db->query("
        ALTER TABLE `" . db_prefix() . "departments`
        ADD `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
    ");
}