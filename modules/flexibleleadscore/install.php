<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'flexiblels_criteria')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'flexiblels_criteria` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `flexibleleadscore_criteria` mediumtext NOT NULL,
    `flexibleleadscore_criteria_operator` mediumtext NOT NULL,
    `flexibleleadscore_criteria_value` mediumtext NOT NULL,
    `flexibleleadscore_add_substract` mediumtext NOT NULL,
    `flexibleleadscore_points` int(11) NOT NULL,
    `date_added` datetime NOT NULL,
    `date_updated` datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexiblels_criteria`
    ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexiblels_criteria`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

if (!$CI->db->table_exists(db_prefix() . 'flexiblels_lead_scores')) {
    //lead_id is a foreign key to leads table
$CI->db->query('CREATE TABLE `' . db_prefix() . 'flexiblels_lead_scores` (
    `id` int(11) NOT NULL,
    `lead_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `score` int(11) NOT NULL,
    `date_added` datetime NOT NULL,
    `date_updated` datetime NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexiblels_lead_scores`
    ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexiblels_lead_scores`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'flexiblels_lead_scores` ADD FOREIGN KEY (lead_id) REFERENCES ' . db_prefix() . 'leads(id) ON DELETE CASCADE;');
}

//add option to store lead scoring status
if (empty(get_option(FLEXIBLELEADSCORE_STATUS_OPTION))) {
  add_option(FLEXIBLELEADSCORE_STATUS_OPTION, FLEXIBLELEADSCORE_INACTIVE_STATUS);
}

//add option to store cron status
if (empty(get_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION))) {
  add_option(FLEXIBLELEADSCORE_CRON_STATUS_OPTION, FLEXIBLELEADSCORE_CRON_STATUS_INACTIVE);
}