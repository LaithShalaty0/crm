<?php

defined('BASEPATH') || exit('No direct script access allowed');

add_option('whatsapp_api_enabled', 1);

//get codeigniter instance
$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix().'whatsapp_templates')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'whatsapp_templates` (
 	`id` INT NOT NULL AUTO_INCREMENT ,
	`template_id` BIGINT UNSIGNED NOT NULL COMMENT "id from api" ,
	`template_name` VARCHAR(255) NOT NULL ,
	`language` VARCHAR(50) NOT NULL ,
	`status` VARCHAR(50) NOT NULL ,
	`category` VARCHAR(100) NOT NULL ,
	`header_data_format` VARCHAR(10) NOT NULL ,
	`header_data_text` TEXT ,
	`header_params_count` INT NOT NULL ,
	`body_data` TEXT NOT NULL ,
	`body_params_count` INT NOT NULL ,
	`footer_data` TEXT,
	`footer_params_count` INT NOT NULL ,
	`buttons_data` VARCHAR(255) NOT NULL ,
	PRIMARY KEY (`id`),
	UNIQUE KEY `template_id` (`template_id`)
)ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';');
}

if (!$CI->db->table_exists(db_prefix().'whatsapp_templates_mapping')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'whatsapp_templates_mapping` (
 	`id` INT NOT NULL AUTO_INCREMENT ,
	`template_id` INT(11) NOT NULL,
	`category` VARCHAR(100) NOT NULL ,
	`send_to` VARCHAR(50) NOT NULL ,
	`header_params` VARCHAR(255) NOT NULL ,
	`body_params` VARCHAR(255) NOT NULL ,
	`footer_params` VARCHAR(255) NOT NULL ,
	`active` TINYINT NOT NULL DEFAULT "1",
	`debug_mode` TINYINT NOT NULL DEFAULT "0",
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';');
}

if (!$CI->db->table_exists(db_prefix().'whatsapp_api_debug_log')) {
    $CI->db->query(
        'CREATE TABLE `'.db_prefix().'whatsapp_api_debug_log` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`api_endpoint` varchar(255) NULL DEFAULT NULL,
		`phone_number_id` varchar(255) NULL DEFAULT NULL,
		`access_token` TEXT NULL DEFAULT NULL,
		`business_account_id` varchar(255) NULL DEFAULT NULL,
		`response_code` varchar(4) NOT NULL,
		`response_data` text NOT NULL,
		`send_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`send_json`)),
		`message_category` varchar(50) NOT NULL,
		`category_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`category_params`)),
		`merge_field_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`merge_field_data`)),
		`recorded_at` datetime NOT NULL DEFAULT current_timestamp(),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET='.$CI->db->char_set.';'
    );
}
/*End of file install.php */
