<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Importsync extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('importsync_model');
        hooks()->do_action('importsync_init');
    }

    public function index()
    {
        show_404();
    }

    public function manage_mappings()
    {
        if (!has_permission('importsync', '', 'view')) {
            access_denied('importsync');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('importsync', 'tables/mappings'));
        }

        $data['title'] = _l('importsync') . ' - ' . _l('importsync_sync_csv');
        $this->load->view('manage', $data);
    }

    public function csv_mappings()
    {
        if (!has_permission('importsync_sync_csv', '', 'create')) {
            access_denied('importsync');
        }

        $data['title'] = _l('importsync') . ' - ' . _l('importsync_create_mapping');
        $this->load->view('sync_csv', $data);
    }

    public function get_csv_columns()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_FILES['csvFile']['error'] === UPLOAD_ERR_OK) {
                $csvFilePath = $_FILES['csvFile']['tmp_name'];
                $csvData = array_map('str_getcsv', file($csvFilePath));
                $columns = isset($csvData[0]) ? $csvData[0] : [];

                echo json_encode($columns);
            } else {
                echo json_encode([]);
            }
        }
    }

    public function map_csv()
    {
        // Define a mapping array for column synchronization
        $mappings = isset($_POST['mappings']) ? $_POST['mappings'] : [];
        $csvType = $_POST['csv_type'];

        // Define a mapping array for column synchronization
        $columnMapping = [];

        // Update the mapping based on the received data
        $mappings = json_decode($mappings);
        foreach ($mappings as $mainColumn => $csvColumn) {
            if (!empty($mainColumn) && !empty($csvColumn)) {
                $columnMapping[$mainColumn] = $csvColumn;
            }
        }

        $sourceCsvFilePath = $_FILES['csvFile']['tmp_name']; // Path to the second CSV file
        $sourceCsvData = array_map('str_getcsv', file($sourceCsvFilePath));

        // Load the target CSV file
        $targetCsvFilePath = FCPATH . 'modules/importsync/uploads/standard_csv/' . $csvType . '_import_file.csv';
        $targetCsvData = array_map('str_getcsv', file($targetCsvFilePath));

        // Create an array to hold the extracted data based on mapping
        $extractedData = [];

        // Extract data from the source CSV based on the mapping
        foreach ($sourceCsvData as $sourceRow) {
            $extractedRow = [];
            foreach ($columnMapping as $targetColumn => $sourceColumn) {
                $sourceColumnIndex = array_search($sourceColumn, $sourceCsvData[0]);
                $extractedRow[$targetColumn] = $sourceRow[$sourceColumnIndex];
            }
            $extractedData[] = $extractedRow;
        }

        unset($extractedData[0]); //Remove unnecessary columns

        // Match extracted data to the target CSV based on index
        foreach ($extractedData as $index => $extractedRow) {
            if (isset($targetCsvData[$index + 1])) {
                foreach ($columnMapping as $targetColumn => $sourceColumn) {
                    if (isset($extractedRow[$targetColumn])) {
                        $targetCsvData[$index + 1][array_search($targetColumn, $targetCsvData[0])] = $extractedRow[$targetColumn];
                    }
                }
            } else {
                // If there's no corresponding row, create a new row with extracted data
                $newRow = array_fill(0, count($targetCsvData[0]), ''); // Create an empty row with the same number of columns
                foreach ($columnMapping as $targetColumn => $sourceColumn) {
                    if (isset($extractedRow[$targetColumn])) {
                        $newRow[array_search($targetColumn, $targetCsvData[0])] = $extractedRow[$targetColumn];
                    }
                }
                $targetCsvData[] = $newRow;
            }
        }

        // Write the updated data back to the target CSV
        $combinedCsvContent = implode("\n", array_map(function ($row) {
            return implode(",", $row);
        }, $targetCsvData));

        $fileName = $csvType.'-Mapped-CSV.csv';
        $csvData = [
            'mapped_by' => get_staff_user_id(),
            'csv_type' => $csvType,
            'csv_filename' => $fileName,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $mappedCsvID = $this->importsync_model->addMappedCsv($csvData);

        $path = FCPATH . 'modules/importsync/uploads/mapped_csv/' . $mappedCsvID . '/';
        _maybe_create_upload_path($path);

        file_put_contents($path . $fileName, $combinedCsvContent);
        $fileUrl = substr(module_dir_url('importsync/uploads/mapped_csv/' . $mappedCsvID . '/' .$fileName), 0, -1);

        $redirectUrl = '';
        switch ($csvType) {
            case 'leads':
                $redirectUrl = admin_url('leads/import');
                break;
            case 'customers':
                $redirectUrl = admin_url('clients/import');
                break;
            case 'expenses':
                $redirectUrl = admin_url('expenses/import');
                break;
            case 'items':
                $redirectUrl = admin_url('invoice_items/import');
                break;
            case 'staff':
                $redirectUrl = admin_url('importsync/import_staff');
                break;
        }

        echo json_encode([
            'status' => true,
            'mapped_csv_url' => $fileUrl,
            'redirect_url' => $redirectUrl
        ]);
        die;
    }

    public function delete_mapping($id='')
    {
        if (!has_permission('importsync', '', 'delete')) {
            access_denied('importsync');
        }

        if (!$id) {
            redirect(admin_url('importsync/manage_mappings'));
        }

        $response = $this->importsync_model->deleteMappedCsv($id);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('importsync')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('importsync')));
        }

        redirect(admin_url('importsync/manage_mappings'));
    }

    public function import_staff()
    {
        if (!staff_can('import_staff', 'importsync')) {
            access_denied('importsync');
        }

        $staffImporter = new Import_staff();

        $staffImporter->setDatabaseFields($this->db->list_fields(db_prefix() . 'staff'))
            ->setCustomFields(get_custom_fields('staff'));

        if ($this->input->post('download_sample') === 'true') {
            $staffImporter->downloadSample();
        }

        if (
            $this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''
        ) {
            $staffImporter->setSimulation($this->input->post('simulate'))
                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                ->setFilename($_FILES['file_csv']['name'])
                ->perform();

            $data['total_rows_post'] = $staffImporter->totalRows();

            if (!$staffImporter->isSimulation()) {
                set_alert('success', _l('import_total_imported', $staffImporter->totalImported()));
            }
        }

        $data['title'] = _l('importsync_import_staff');
        $data['importInstance'] = $staffImporter;
        $this->load->view('import_staff', $data);
    }
}
