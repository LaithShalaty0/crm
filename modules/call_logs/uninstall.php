<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

delete_option('staff_members_create_inline_call_direction');
delete_option('staff_members_create_inline_cl_types');
delete_option('staff_members_daily_calls_target');
delete_option('staff_members_monthly_calls_target');

$CI->db->query('DROP TABLE `' . db_prefix() . 'call_logs`');
$CI->db->query('DROP TABLE `' . db_prefix() . 'call_logs_rel_types`');
$CI->db->query('DROP TABLE `' . db_prefix() . 'call_logs_directions`');
$CI->db->query('DROP TABLE `' . db_prefix() . 'call_logs_goals_notified`');
