<?php

function importsync_supported_csvs()
{
    return [
        [
            'value' => 'leads',
            'name' => _l('leads')
        ],
        [
            'value' => 'customers',
            'name' => _l('customers')
        ],
        [
            'value' => 'items',
            'name' => _l('items')
        ],
        [
            'value' => 'expenses',
            'name' => _l('expenses')
        ],
        [
            'value' => 'staff',
            'name' => _l('staff')
        ]
    ];
}

function supportedCsvImports()
{
    return [
        'leads',
        'customers',
        'items',
        'expenses',
        'staff'
    ];
}

function getCsvLibraryData($csv_type)
{

    $CI = &get_instance();

    if ($csv_type === 'customers') {
        $dbFields = $CI->db->list_fields(db_prefix() . 'contacts');
        foreach ($dbFields as $key => $contactField) {
            if ($contactField == 'phonenumber') {
                $dbFields[$key] = 'contact_phonenumber';
            }
        }

        $dbFields = array_merge($dbFields, $CI->db->list_fields(db_prefix() . 'clients'));
        $CI->load->library('import/import_customers', [], 'customers_import');
        $CI->customers_import->setDatabaseFields($dbFields);

        return [
            'sample_table_html' => $CI->customers_import->createSampleTableHtml(),
            'import_guidelines' => $CI->customers_import->importGuidelinesInfoHtml(),
        ];
    }

    if ($csv_type === 'leads') {
        $dbFields = $CI->db->list_fields(db_prefix() . 'leads');
        array_push($dbFields, 'tags');

        $CI->load->library('import/import_leads', [], 'leads_import');
        $CI->leads_import->setDatabaseFields($dbFields);

        return [
            'sample_table_html' => $CI->leads_import->createSampleTableHtml(),
            'import_guidelines' => $CI->leads_import->importGuidelinesInfoHtml(),
        ];
    }

    if ($csv_type === 'items') {
        $CI->load->library('import/import_items', [], 'items_import');
        $CI->items_import->setDatabaseFields($CI->db->list_fields(db_prefix() . 'items'));

        return [
            'sample_table_html' => $CI->items_import->createSampleTableHtml(),
            'import_guidelines' => $CI->items_import->importGuidelinesInfoHtml(),
        ];
    }

    if ($csv_type === 'expenses') {
        $CI->load->library('import/import_expenses', [], 'expenses_import');
        $CI->expenses_import->setDatabaseFields($CI->db->list_fields(db_prefix() . 'expenses'));

        return [
            'sample_table_html' => $CI->expenses_import->createSampleTableHtml(),
            'import_guidelines' => $CI->expenses_import->importGuidelinesInfoHtml(),
        ];
    }

    if ($csv_type === 'staff') {
        $staffImporter = new Import_staff();
        $staffImporter->setDatabaseFields($CI->db->list_fields(db_prefix() . 'staff'));

        return [
            'sample_table_html' => $staffImporter->createSampleTableHtml(),
            'import_guidelines' => $staffImporter->importGuidelinesInfoHtml(),
        ];
    }
}

function renderCsvTypeColumns($type = '')
{
    if ($type === 'leads') {

        $getCsvLibraryData = getCsvLibraryData('leads');
        $importFields = [
            'Name',
            'Position',
            'Company',
            'Description',
            'Country',
            'Zip',
            'City',
            'State',
            'Address',
            'Status',
            'Source',
            'Email',
            'Website',
            'Phonenumber',
            'Lead value',
            'Tags'
        ];

        return [
                'import_fields' => $importFields
            ] + $getCsvLibraryData;
    }

    if ($type === 'customers') {

        $getCsvLibraryData = getCsvLibraryData('customers');
        $importFields = [
            'Firstname',
            'Lastname',
            'Email',
            'Contact phonenumber',
            'Position',
            'Company',
            'Vat',
            'Phonenumber',
            'Country',
            'City',
            'Zip',
            'State',
            'Address',
            'Website',
            'Billing street',
            'Billing city',
            'Billing state',
            'Billing zip',
            'Billing country',
            'Shipping street',
            'Shipping city',
            'Shipping state',
            'Shipping zip',
            'Shipping country',
            'Longitude',
            'Latitude',
            'Stripe id'
        ];

        return [
                'import_fields' => $importFields
            ] + $getCsvLibraryData;
    }

    if ($type === 'items') {

        $getCsvLibraryData = getCsvLibraryData('items');
        $importFields = [
            'Description',
            'Long description',
            'Rate - USD',
            'Tax',
            'Tax2',
            'Unit',
            'Group'
        ];

        return [
                'import_fields' => $importFields
            ] + $getCsvLibraryData;
    }

    if ($type === 'expenses') {

        $getCsvLibraryData = getCsvLibraryData('expenses');
        $importFields = [
            'Category',
            'Amount',
            'Tax',
            'Tax2',
            'Reference no',
            'Note',
            'Expense name',
            'Customer',
            'Billable',
            'Payment Mode',
            'Date',
        ];

        return [
                'import_fields' => $importFields
            ] + $getCsvLibraryData;
    }

    if ($type === 'staff') {

        $getCsvLibraryData = getCsvLibraryData('staff');
        $importFields = [
            'Email',
            'Firstname',
            'Lastname',
            'Facebook',
            'Linkedin',
            'Phonenumber',
            'Skype',
            'Password',
            'Role',
            'Active',
            'Hourly rate',
            'Email signature',
        ];

        return [
                'import_fields' => $importFields
            ] + $getCsvLibraryData;
    }
}