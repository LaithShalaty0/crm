<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('mpwtl_render_form_builder_field')) {
	/**
	 * Used for customer forms eq. leads form, builded from the form builder plugin
	 * @param  object $field field from database
	 * @return mixed
	 */
	function mpwtl_render_form_builder_field($field) {
		$type = $field->type;
		$classNameCol = 'col-md-12';
		if (isset($field->className)) {
			if (strpos($field->className, 'form-col') !== false) {
				$classNames = explode(' ', $field->className);
				if (is_array($classNames)) {
					$classNameColArray = array_filter($classNames, function ($class) {
						return startsWith($class, 'form-col');
					});

					$classNameCol = implode(' ', $classNameColArray);
					$classNameCol = trim($classNameCol);

					$classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
					$classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
					$classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
					$classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

					// Default col-md-X
					$classNameCol = str_replace('form-col', 'col-md', $classNameCol);
				}
			}
		}

		echo '<div class="' . $classNameCol . '">';
		if ($type == 'header' || $type == 'paragraph') {
			echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . html_entity_decode(check_for_links(nl2br($field->label))) . '</' . $field->subtype . '>';
		} else {
			echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
			echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ' : '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
			if (isset($field->subtype) && $field->subtype == 'color') {
				echo '<div class="input-group colorpicker-input">
         <input' . (isset($field->required) ? ' required="true"' : '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
             <span class="input-group-addon"><i></i></span>
         </div>';
			} elseif ($type == 'file' || $type == 'text' || $type == 'number') {
				$ftype = isset($field->subtype) ? $field->subtype : $type;
				echo '<input' . (isset($field->required) ? ' required="true"' : '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize="' . file_upload_max_size() . '"' : '') . '>';
			} elseif ($type == 'textarea') {
				echo '<textarea' . (isset($field->required) ? ' required="true"' : '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '">' . (isset($field->value) ? $field->value : '') . '</textarea>';
			} elseif ($type == 'date') {
				echo '<input' . (isset($field->required) ? ' required="true"' : '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _d($field->value) : '') . '">';
			} elseif ($type == 'datetime-local') {
				echo '<input' . (isset($field->required) ? ' required="true"' : '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
			} elseif ($type == 'select') {
				echo '<select' . (isset($field->required) ? ' required="true"' : '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"' : '') . 'data-none-selected-text="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
				$values = [];
				if (isset($field->values) && count($field->values) > 0) {
					foreach ($field->values as $option) {
						echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
					}
				}
				echo '</select>';
			} elseif ($type == 'checkbox-group') {
				$values = [];
				if (isset($field->values) && count($field->values) > 0) {
					$i = 0;
					echo '<div class="chk">';
					foreach ($field->values as $checkbox) {
						echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
						echo '<input' . (isset($field->required) ? ' required="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
						echo '<label for="chk_' . $field->name . '_' . $i . '">';
						echo $checkbox->label;
						echo '</label>';
						echo '</div>';
						$i++;
					}
					echo '</div>';
				}
			}
			echo '</div>';
		}
		echo '</div>';
	}
}