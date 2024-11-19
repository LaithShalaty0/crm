<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'importsync_csv_mapped')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . "importsync_csv_mapped` (
  `id` int(11) NOT NULL,
  `mapped_by` int,
  `csv_type` text,
  `csv_filename` text,
  `created_at` datetime
) ENGINE=InnoDB DEFAULT CHARSET=" . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'importsync_csv_mapped`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'importsync_csv_mapped`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1');
}