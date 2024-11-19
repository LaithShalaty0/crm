<?php 
defined('BASEPATH') or exit('No direct script access allowed');

function handle_account_planning($account_planning_id, $index_name = 'attachments')
{
    $path = get_upload_path_by_type('account_planning') . $account_planning_id . '/';
    $file_uploaded = false;
    $CI   = & get_instance();

    if (isset($_FILES[$index_name])) {
        _file_attachments_index_fix($index_name);
        for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
            // Get the temp file path
            $tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
            // Make sure we have a filepath

            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $file_uploaded = true;
                    $attachment    = [];
                    $attachment[]  = [
                    'file_name' => $filename,
                    'filetype'  => $_FILES['attachments']['type'][$i],
                    ];
                    $CI->misc_model->add_attachment_to_database($account_planning_id, 'account_planning', $attachment);
                }

            }
        }
    }

    if ($file_uploaded == true) {
        return true;
    }
    return false;
}

function reformat_currency($value)
{
    return str_replace(',','', $value);
}