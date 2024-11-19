<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

add_option('staff_members_create_inline_call_direction', 1);
add_option('staff_members_create_inline_cl_types', 1);
add_option('staff_members_daily_calls_target', 0);
add_option('staff_members_monthly_calls_target', 0);

/* Generate the table in the database. */
if (!$CI->db->table_exists(db_prefix() . 'call_logs')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "call_logs` (
      `id` int(11) NOT NULL,
      `call_purpose` varchar(255) DEFAULT NULL,
      `userphone` varchar(255) DEFAULT NULL,
      `call_summary` text,
      `call_start_time` datetime NOT NULL,
      `call_end_time` datetime NOT NULL,
      `call_duration` varchar(255) DEFAULT NULL,
      `has_follow_up` tinyint(4) DEFAULT '0',
      `follow_up_schedule` datetime NULL DEFAULT NULL,
      `is_important` tinyint(4) DEFAULT '0',
      `is_completed` tinyint(4) DEFAULT '0',
      `staffid` int(11) DEFAULT '0',
      `call_with_staffid` int(11) DEFAULT '0',
      `call_direction` int(11) DEFAULT '0',
      `notified` tinyint(4) DEFAULT '0',
      
      `customer_type` varchar(255) DEFAULT NULL,
      `clientid` int(11) DEFAULT '0',
      
      `rel_type` INT(11) DEFAULT '0',
      `rel_id` int(11) DEFAULT '0',
      `dateadded` datetime DEFAULT NULL,
      `dateaupdated` datetime DEFAULT NULL,
      `datestart` datetime DEFAULT NULL
      
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'call_logs`
      ADD PRIMARY KEY (`id`),ADD KEY `clientid` (`clientid`),
      ADD KEY `staffid` (`staffid`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'call_logs`
        MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}

$cl_relTypeTable = db_prefix() . 'call_logs_rel_types';
$cl_callDirectionsTable = db_prefix() . 'call_logs_directions';

if (!$CI->db->table_exists($cl_relTypeTable)) {
    $CI->db->query('CREATE TABLE `' . $cl_relTypeTable . '` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `key` varchar(100) NOT NULL,
      `is_default` tinyint(4) DEFAULT "0",
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

    $cl_rel_types = get_related_to_types();
    foreach ($cl_rel_types as $item) {
        $CI->db->insert($cl_relTypeTable, array(
            'name' => $item['lang_key'],
            'key' => $item['key'],
            'is_default' => $item['is_default']
        ));
    }
}

if (!$CI->db->table_exists($cl_callDirectionsTable)) {
    $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $cl_callDirectionsTable . '` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `is_default` tinyint(4) DEFAULT "0",
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

    $cl_call_directions = get_call_directions();
    foreach ($cl_call_directions as $item) {
        $CI->db->insert($cl_callDirectionsTable, array(
            'name' => $item['label'],
            'is_default' => 1
        ));
    }
}

$cl_goalsNotificationsTable = db_prefix() . 'call_logs_goals_notified';
if (!$CI->db->table_exists($cl_goalsNotificationsTable)) {
    $CI->db->query('CREATE TABLE `'. $cl_goalsNotificationsTable .'` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staffid` int(11) DEFAULT "0",
      `notify_date` datetime DEFAULT NULL,
      `goal_type` varchar(255) DEFAULT "daily",
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');
}