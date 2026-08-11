<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

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
            `address` TEXT DEFAULT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Inactive',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `admission_no` (`admission_no`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
}