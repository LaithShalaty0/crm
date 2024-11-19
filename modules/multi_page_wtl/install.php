<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$CI = &get_instance();

if (!$CI->db->field_exists("is_mpwtl", db_prefix() . 'web_to_lead')) {
	$CI->db->query("ALTER TABLE " . db_prefix() . 'web_to_lead' . " ADD is_mpwtl int(1) DEFAULT 0");
}

if (!$CI->db->field_exists("form_color", db_prefix() . 'web_to_lead')) {
	$CI->db->query("ALTER TABLE " . db_prefix() . 'web_to_lead' . " ADD form_color varchar(15) DEFAULT NULL");
}

if (!$CI->db->field_exists("form_bg_color", db_prefix() . 'web_to_lead')) {
	$CI->db->query("ALTER TABLE " . db_prefix() . 'web_to_lead' . " ADD form_bg_color varchar(15) DEFAULT NULL");
}

if (!$CI->db->field_exists("form_theme", db_prefix() . 'web_to_lead')) {
	$CI->db->query("ALTER TABLE " . db_prefix() . 'web_to_lead' . " ADD form_theme varchar(15) DEFAULT 'elegant'");
}

/**
 * Modify core files
 * @return [type] [description]
 */
function mpwtl_modFile($fname, $searchF, $replaceW) {
	$fhandle = fopen($fname, "r");
	$content = fread($fhandle, filesize($fname));
	if (strstr($content, $searchF)) {
		$content = str_replace($searchF, $replaceW, $content);
		$fhandle = fopen($fname, "w");
		fwrite($fhandle, $content);
	}
	fclose($fhandle);
	return true;
}
$search = "\$result  = data_tables_init(\$aColumns, \$sIndexColumn, \$sTable, [], [], ['form_key', 'id']);";
$replace = "\$result = data_tables_init(\$aColumns, \$sIndexColumn, \$sTable, [], [' AND is_mpwtl = 0'], ['form_key', 'id']);";
$file_path = APPPATH . 'views/admin/tables/web_to_lead.php';
mpwtl_modFile($file_path, $search, $replace);
