<?php

header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends AdminController {
	public function __construct() {
		parent::__construct();
		$this->load->model('leads_model');
	}

	public function save_form_data() {
		$data = $this->input->post();

		// form data should be always sent to the request and never should be empty
		// this code is added to prevent losing the old form in case any errors
		if (!isset($data['formData']) || isset($data['formData']) && !$data['formData']) {
			echo json_encode([
				'success' => false,
			]);
			die;
		}

		$data['formData'] = str_replace('\\"', "'", $data['formData']);
		$data['formData'] = str_replace("\\'", "'", $data['formData']);

		log_message('error', $data['formData']);

		// If user paste with styling eq from some editor word and the Codeigniter XSS feature remove and apply xss=remove, may break the json.
		$data['formData'] = preg_replace('/=\\\\/m', "=''", $data['formData']);

		$this->db->where('id', $data['id']);
		$this->db->update(db_prefix() . 'web_to_lead', [
			'form_data' => $data['formData'],
		]);
		if ($this->db->affected_rows() > 0) {
			echo json_encode([
				'success' => true,
				'message' => _l('updated_successfully', _l('web_to_lead_form')),
			]);
		} else {
			echo json_encode([
				'success' => false,
			]);
		}
	}

	public function form($id = '') {
		if (!is_admin()) {
			access_denied('Web To Lead Access');
		}
		if ($this->input->post()) {
			if ($id == '') {
				$data = $this->input->post();
				$data['is_mpwtl'] = 1;
				$data['form_data'] = json_encode([[]]);
				$id = $this->leads_model->add_form($data);
				if ($id) {
					set_alert('success', _l('added_successfully', _l('web_to_lead_form')));
					redirect(admin_url(MPWTL_MODULE_NAME . '/leads/form/' . $id));
				}
			} else {
				$success = $this->leads_model->update_form($id, $this->input->post());
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('web_to_lead_form')));
				}
				redirect(admin_url(MPWTL_MODULE_NAME . '/leads/form/' . $id));
			}
		}

		$data['formData'] = [];
		$custom_fields = get_custom_fields('leads', 'type != "link"');

		$cfields = format_external_form_custom_fields($custom_fields);
		$data['title'] = _l('web_to_lead');

		if ($id != '') {
			$data['form'] = $this->leads_model->get_form([
				'id' => $id,
			]);
			$data['title'] = $data['form']->name . ' - ' . _l('web_to_lead_form');
			if (!empty($data['form']->form_data) && !is_null($data['form']->form_data)) {
				$data['formData'] = $data['form']->form_data;
			}
		}

		$this->load->model('roles_model');
		$data['roles'] = $this->roles_model->get();
		$data['sources'] = $this->leads_model->get_source();
		$data['statuses'] = $this->leads_model->get_status();

		$data['members'] = $this->staff_model->get('', [
			'active' => 1,
			'is_not_staff' => 0,
		]);

		$data['languages'] = $this->app->get_available_languages();
		$data['cfields'] = $cfields;

		$db_fields = [];
		$fields = [
			'html_block',
			'name',
			'title',
			'email',
			'phonenumber',
			'lead_value',
			'company',
			'address',
			'city',
			'state',
			'country',
			'zip',
			'description',
			'website',
		];

		$fields = hooks()->apply_filters('lead_form_available_database_fields', $fields);

		$className = 'form-control';

		foreach ($fields as $f) {
			$_field_object = new stdClass();
			$type = 'text';
			$subtype = '';
			if ($f == 'email') {
				$subtype = 'email';
			} elseif ($f == 'description' || $f == 'address') {
				$type = 'textarea';
			} elseif ($f == 'country') {
				$type = 'select';
			} elseif ($f == 'html_block') {
				$type = 'paragraph';
				$className = '';
				$subtype = 'html_block';
			}

			if ($f == 'html_block') {
				$label = _l('lead_html_block');
			} elseif ($f == 'name') {
				$label = _l('lead_add_edit_name');
			} elseif ($f == 'email') {
				$label = _l('lead_add_edit_email');
			} elseif ($f == 'phonenumber') {
				$label = _l('lead_add_edit_phonenumber');
			} elseif ($f == 'lead_value') {
				$label = _l('lead_add_edit_lead_value');
				$type = 'number';
			} else {
				$label = _l('lead_' . $f);
			}

			$field_array = [
				'subtype' => $subtype,
				'type' => $type,
				'label' => $label,
				'className' => $className,
				'name' => $f,
			];

			if ($f == 'country') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];

				$countries = get_all_countries();
				foreach ($countries as $country) {
					$selected = false;
					if (get_option('customer_default_country') == $country['country_id']) {
						$selected = true;
					}
					array_push($field_array['values'], [
						'label' => $country['short_name'],
						'value' => (int) $country['country_id'],
						'selected' => $selected,
					]);
				}
			}

			if ($f == 'name') {
				$field_array['required'] = true;
			}

			$_field_object->label = $label;
			$_field_object->name = $f;
			$_field_object->fields = [];
			$_field_object->fields[] = $field_array;
			$db_fields[] = $_field_object;
		}
		$data['bodyclass'] = 'web-to-lead-form';
		$data['db_fields'] = $db_fields;

		$this->load->view('admin/leads/formbuilder', $data);
	}

	public function forms($id = '') {
		if (!is_admin()) {
			access_denied('Web To Lead Access');
		}

		if ($this->input->is_ajax_request()) {
			$this->app->get_table_data(module_views_path(MPWTL_MODULE_NAME, 'admin/tables/web_to_lead'));
		}

		$data['title'] = _l(MPWTL_MODULE_NAME);
		$this->load->view('admin/leads/forms', $data);
	}

	public function delete_form($id) {
		if (!is_admin()) {
			access_denied('Web To Lead Access');
		}

		$success = $this->leads_model->delete_form($id);
		if ($success) {
			set_alert('success', _l('deleted', _l('web_to_lead_form')));
		}

		redirect(admin_url('multi_page_wtl/leads/forms'));
	}

	// Sources
	/* Manage leads sources */
	public function sources() {
		if (!is_admin()) {
			access_denied('Leads Sources');
		}
		$data['sources'] = $this->leads_model->get_source();
		$data['title'] = 'Leads sources';
		$this->load->view('admin/leads/manage_sources', $data);
	}

	/* Add or update leads sources */
	public function source() {
		if (!is_admin() && get_option('staff_members_create_inline_lead_source') == '0') {
			access_denied('Leads Sources');
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$inline = isset($data['inline']);
				if (isset($data['inline'])) {
					unset($data['inline']);
				}

				$id = $this->leads_model->add_source($data);

				if (!$inline) {
					if ($id) {
						set_alert('success', _l('added_successfully', _l('lead_source')));
					}
				} else {
					echo json_encode(['success' => $id ? true : fales, 'id' => $id]);
				}
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->leads_model->update_source($data, $id);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('lead_source')));
				}
			}
		}
	}

	/* Delete leads source */
	public function delete_source($id) {
		if (!is_admin()) {
			access_denied('Delete Lead Source');
		}
		if (!$id) {
			redirect(admin_url('leads/sources'));
		}
		$response = $this->leads_model->delete_source($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('lead_source_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('lead_source')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('lead_source_lowercase')));
		}
		redirect(admin_url('leads/sources'));
	}

	// Statuses
	/* View leads statuses */
	public function statuses() {
		if (!is_admin()) {
			access_denied('Leads Statuses');
		}
		$data['statuses'] = $this->leads_model->get_status();
		$data['title'] = 'Leads statuses';
		$this->load->view('admin/leads/manage_statuses', $data);
	}

	/* Add or update leads status */
	public function status() {
		if (!is_admin() && get_option('staff_members_create_inline_lead_status') == '0') {
			access_denied('Leads Statuses');
		}
		if ($this->input->post()) {
			$data = $this->input->post();
			if (!$this->input->post('id')) {
				$inline = isset($data['inline']);
				if (isset($data['inline'])) {
					unset($data['inline']);
				}
				$id = $this->leads_model->add_status($data);
				if (!$inline) {
					if ($id) {
						set_alert('success', _l('added_successfully', _l('lead_status')));
					}
				} else {
					echo json_encode(['success' => $id ? true : fales, 'id' => $id]);
				}
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->leads_model->update_status($data, $id);
				if ($success) {
					set_alert('success', _l('updated_successfully', _l('lead_status')));
				}
			}
		}
	}

	/* Delete leads status from databae */
	public function delete_status($id) {
		if (!is_admin()) {
			access_denied('Leads Statuses');
		}
		if (!$id) {
			redirect(admin_url('leads/statuses'));
		}
		$response = $this->leads_model->delete_status($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', _l('is_referenced', _l('lead_status_lowercase')));
		} elseif ($response == true) {
			set_alert('success', _l('deleted', _l('lead_status')));
		} else {
			set_alert('warning', _l('problem_deleting', _l('lead_status_lowercase')));
		}
		redirect(admin_url('leads/statuses'));
	}

	/* Add new lead note */
	public function add_note($rel_id) {
		if (!is_staff_member() || !$this->leads_model->staff_can_access_lead($rel_id)) {
			ajax_access_denied();
		}

		if ($this->input->post()) {
			$data = $this->input->post();

			if ($data['contacted_indicator'] == 'yes') {
				$contacted_date = to_sql_date($data['custom_contact_date'], true);
				$data['date_contacted'] = $contacted_date;
			}

			unset($data['contacted_indicator']);
			unset($data['custom_contact_date']);

			// Causing issues with duplicate ID or if my prefixed file for lead.php is used
			$data['description'] = isset($data['lead_note_description']) ? $data['lead_note_description'] : $data['description'];

			if (isset($data['lead_note_description'])) {
				unset($data['lead_note_description']);
			}

			$note_id = $this->misc_model->add_note($data, 'lead', $rel_id);

			if ($note_id) {
				if (isset($contacted_date)) {
					$this->db->where('id', $rel_id);
					$this->db->update(db_prefix() . 'leads', [
						'lastcontact' => $contacted_date,
					]);
					if ($this->db->affected_rows() > 0) {
						$this->leads_model->log_lead_activity($rel_id, 'not_lead_activity_contacted', false, serialize([
							get_staff_full_name(get_staff_user_id()),
							_dt($contacted_date),
						]));
					}
				}
			}
		}
		echo json_encode(['leadView' => $this->_get_lead_data($rel_id), 'id' => $rel_id]);
	}

	public function validate_unique_field() {
		if ($this->input->post()) {

			// First we need to check if the field is the same
			$lead_id = $this->input->post('lead_id');
			$field = $this->input->post('field');
			$value = $this->input->post($field);

			if ($lead_id != '') {
				$this->db->select($field);
				$this->db->where('id', $lead_id);
				$row = $this->db->get(db_prefix() . 'leads')->row();
				if ($row->{$field} == $value) {
					echo json_encode(true);
					die();
				}
			}

			echo total_rows(db_prefix() . 'leads', [$field => $value]) > 0 ? 'false' : 'true';
		}
	}

}
