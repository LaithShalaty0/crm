<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_staff extends App_import
{
    protected $notImportableFields = ['staffid','datecreated','profile_image','last_ip','two_factor_auth_enabled','last_login','last_activity','last_password_change','new_pass_key','new_pass_key_requested','admin','default_language','direction','media_path_slug','is_not_staff','two_factor_auth_enable','two_factor_auth_code','two_factor_auth_code_requested','google_auth_secret'];

    protected $requiredFields = ['firstname', 'lastname', 'email', 'password'];

    public function __construct()
    {
        $this->addItemsGuidelines();

        parent::__construct();
    }

    public function perform()
    {
        $this->initialize();

        $databaseFields      = $this->getImportableDatabaseFields();
        $totalDatabaseFields = count($databaseFields);

        foreach ($this->getRows() as $rowNumber => $row) {
            $insert = [];
            $duplicate = false;
            for ($i = 0; $i < $totalDatabaseFields; $i++) {
                $row[$i] = $this->checkNullValueAddedByUser($row[$i]);

                if ($databaseFields[$i] == 'firstname' && $row[$i] == '') {
                    $row[$i] = '/';
                } elseif ($databaseFields[$i] == 'lastname' && $row[$i] == '') {
                    $row[$i] = '/';
                } elseif ($databaseFields[$i] == 'email' && $row[$i] == '') {
                    $row[$i] = '/';
                    $duplicate = $this->isDuplicateStaffEmail($row[$i]);
                } elseif ($databaseFields[$i] == 'password') {
                    $row[$i] = $this->passwordValue($row[$i]);
                }

                $insert[$databaseFields[$i]] = $row[$i];
            }

            if ($duplicate) {
                continue;
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                $this->incrementImported();
                $id = null;

                if (!$this->isSimulation()) {

                    if (!isset($insert['datecreated'])) {
                        $insert['datecreated'] = date('Y-m-d H:i:s');
                    }

                    $this->ci->db->insert(db_prefix().'staff', $insert);
                    $id = $this->ci->db->insert_id();
                } else {
                    $this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
                }

                $this->handleCustomFieldsInsert($id, $row, $i, $rowNumber, 'staff');
            }

            if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
                break;
            }
        }
    }

    public function formatFieldNameForHeading($field)
    {
        return parent::formatFieldNameForHeading($field);
    }

    protected function failureRedirectURL()
    {
        return admin_url('importsync/import_staff');
    }

    private function addItemsGuidelines()
    {
        $this->addImportGuidelinesInfo('Duplicate email rows won\'t be imported.', true);
        $this->addImportGuidelinesInfo('In the role field you should add ID of the role on your CRM.');
        $this->addImportGuidelinesInfo('Active field accepts 0 or 1 which means 0 not active and 1 active.');
    }

    private function formatValuesForSimulation($values)
    {
        return $values;
    }

    private function isDuplicateStaffEmail($email)
    {
        return total_rows(db_prefix() . 'staff', ['email' => $email]);
    }

    private function passwordValue($value)
    {
        if ($value != '') {
            $value = app_hash_password($value);
        } else {
            $value = '123';
        }

        return $value;
    }
}