<?php

defined('BASEPATH') or exit('No direct script access allowed');
add_option('account_planning_enabled', 1);
if (!$CI->db->table_exists(db_prefix() . 'account_planning')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `client_id` INT(11) NOT NULL,
  `vision` VARCHAR(255) NULL,
  `mission` VARCHAR(255) NULL,
  `lead_generation` VARCHAR(45) NULL,
  `current_service_know_pmax` VARCHAR(45) NULL,
  `current_service_facebook` VARCHAR(45) NULL,
  `current_service_sem` VARCHAR(45) NULL,
  `objectives` VARCHAR(255) NULL,
  `threat` VARCHAR(255) NULL,
  `opportunity` VARCHAR(255) NULL,
  `criteria_to_success` VARCHAR(255) NULL,
  `constraints` VARCHAR(255) NULL,
  `data_tree` LONGTEXT NULL,
  `latest_update` DATE NULL,
  `new_update` DATE NULL,
  `product` VARCHAR(255) NULL,
  `sale_channel_online` VARCHAR(255) NULL,
  `sale_channel_offline` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('revenue_next_year', 'account_planning')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'account_planning` 
ADD COLUMN `revenue_next_year` VARCHAR(255) NULL AFTER `sale_channel_offline`,
ADD COLUMN `wallet_share` VARCHAR(255) NULL AFTER `revenue_next_year`,
ADD COLUMN `client_status` VARCHAR(255) NULL AFTER `wallet_share`,
ADD COLUMN `bcg_model` VARCHAR(255) NULL AFTER `client_status`,
ADD COLUMN `margin` VARCHAR(255) NULL AFTER `bcg_model`;');            
}

if (!$CI->db->field_exists('subject', 'account_planning')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'account_planning` 
ADD COLUMN `subject` VARCHAR(255) NULL AFTER `margin`,
ADD COLUMN `date` DATE NULL AFTER `subject`;');            
}




if (!$CI->db->table_exists(db_prefix() . 'account_planning_marketing_activities')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_marketing_activities` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `item` VARCHAR(255) NULL,
  `reference` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}


if (!$CI->db->table_exists(db_prefix() . 'account_planning_service_ability_offering')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_service_ability_offering` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `service` VARCHAR(255) NULL,
  `potential` VARCHAR(255) NULL,
  `scale` VARCHAR(255) NULL,
  `convert` VARCHAR(255) NULL,
  `prioritization` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_current_service')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_current_service` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NULL,
  `potential` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_objective')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_objective` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `datecreated` DATE NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_items')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `objective_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `datecreated` DATE NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_task')) {
    $CI->db->query('CREATE TABLE `'.db_prefix() . 'account_planning_task` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `items_id` INT(11) NOT NULL,
  `action_needed` VARCHAR(255) NOT NULL,
  `prioritization` VARCHAR(255) NULL,
  `pic` VARCHAR(255) NULL,
  `deadline` DATE NULL,
  `status` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->field_exists('account_planning_id', 'account_planning_task')) {
    $CI->db->query('ALTER TABLE `'.db_prefix() . 'account_planning_task` 
ADD COLUMN `account_planning_id` INT(11) NULL AFTER `items_id`,
ADD COLUMN `objective` VARCHAR(255) NULL AFTER `status`,
ADD COLUMN `item` VARCHAR(255) NULL AFTER `objective`,
ADD COLUMN `convert_to_task` VARCHAR(255) NULL AFTER `item`;');            
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_financial')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'account_planning_financial` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `year` VARCHAR(45) NULL,
  `revenue` VARCHAR(255) NULL,
  `sales_spent` VARCHAR(255) NULL,
  `traffic` VARCHAR(255) NULL,
  `loss` VARCHAR(255) NULL,
  PRIMARY KEY (`id`));');
}

if (!$CI->db->table_exists(db_prefix() . 'account_planning_team')) {
    $CI->db->query('CREATE TABLE `'.db_prefix().'account_planning_team` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `account_planning_id` INT(11) NOT NULL,
  `rel_id` VARCHAR(45) NOT NULL,
  `rel_type` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`));');
}