<?php

class Form extends AbstractPlugin
{

	private $DB = null;
	private $_formEnctype = null;
	private $_formName = null;
	private $_formMethod = null;
	private $_formAction = null;
	private $_formSubmitButtonTitle = null;
	private $_fields = array();
	private $_fieldsNames = array();
	private $_errors = array();

	public function __construct($name, $action, $method, $submitButtonTitle)
	{
		$this->_formName = $name;
		$this->_formAction = $action;
		$this->_formMethod = $method;
		$this->_formSubmitButtonTitle = $submitButtonTitle;
		$this->DB = DB::singleton();
	}

	public function createField($name)
	{
		$this->_fields[] = array();
		$id_field = count($this->_fields) - 1;
		$this->_fieldsNames[$id_field] = $name;

		return $id_field;
	}

	public function addInputText($name, $label, $defaultValue, &$idField, $secure = false)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => ($secure ? "password" : "text"), "label" => $label, "defaultValue" => $defaultValue);
		}
	}

	public function addInputHidden($name, $defaultValue, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "hidden", "defaultValue" => $defaultValue);
		}
	}

	public function addInputRadio($name, $label, $defaultValue, $arrayLabels, $arrayValues, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "radio", "label" => $label, "defaultValue" => $defaultValue, "arrayLabels" => $arrayLabels, "arrayValues" => $arrayValues);
		}
	}

	public function addInputCheckbox($name, $label, $defaultValues, $arrayLabels, $arrayValues, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "checkbox", "label" => $label, "defaultValue" => $defaultValues, "arrayLabels" => $arrayLabels, "arrayValues" => $arrayValues);
		}
	}

	public function addInputSelect($name, $label, $defaultValue, $arrayLabels, $arrayValues, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "select", "label" => $label, "defaultValue" => $defaultValue, "arrayLabels" => $arrayLabels, "arrayValues" => $arrayValues);
		}
	}

	public function addTextarea($name, $label, $defaultValue, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "textarea", "label" => $label, "defaultValue" => $defaultValue);
		}
	}

	public function addCustomText($name, $label, $defaultValue, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_fields[$idField][$name] = array("type" => "custom_text", "label" => $label, "defaultValue" => $defaultValue);
		}
	}

	public function addUploadFile($name, $label, &$idField)
	{
		if ($this->fieldExists($idField))
		{
			$this->_formEnctype = 'multipart/form-data';
			$this->_fields[$idField][$name] = array("type" => "file", "label" => $label, "defaultValue" => '');
		}
	}

	public function display()
	{
		$return = '';
		$return .= '<form ' . ($this->_formEnctype != null ? 'enctype="' . $this->_formEnctype . '"' : '') . ' name="' . $this->_formName . '" id="' . $this->_formName . '" action="' . $this->_formAction . '" method="' . $this->_formMethod . '" class="' . Core::config('form', 'form_class') . '">';

		foreach ($this->_fields as $field_key => $field_input)
		{
			$return .= '<fieldset>';
			$return .= '<legend>' . $this->_fieldsNames[$field_key] . '</legend>';

			foreach ($field_input as $input_id => $input_values)
			{
				switch ($input_values['type'])
				{
					case 'text' :
					case 'password' :
						$return .= '<label for="' . $input_id . '" class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';
						$return .= '<input type="' . $input_values['type'] . '" name="' . $input_id . '" value="' . $input_values['defaultValue'] . '" id="' . $input_id . '" class="' . Core::config('form', 'input_text_class') . '" />';
						break;

					case 'hidden' :
						$return .= '<input type="' . $input_values['type'] . '" name="' . $input_id . '" value="' . $input_values['defaultValue'] . '" id="' . $input_id . '" class="' . Core::config('form', 'input_hidden_class') . '" />';
						break;

					case 'custom_text' :
						$return .= '<label for="' . $input_id . '" class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';
						$return .= '<span id="' . $input_id . '" class="' . Core::config('form', 'input_custom_text_class') . '">' . $input_values['defaultValue'] . '</span>';
						break;

					case 'radio' :
						$return .= '<label class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';

						$i = 0;

						foreach ($input_values['arrayLabels'] as $input_array_key => $input_array_labels)
						{
							$return .= '<label for="' . $input_id . '_' . $i . '" class="' . Core::config('form', 'label_radio_class') . '">' . $input_array_labels . '</label>';
							$return .= '<input ' . ($input_values['defaultValue'] == $input_values['arrayValues'][$input_array_key] ? 'checked="checked"' : '') . ' type="radio" name="' . $input_id . '" id="' . $input_id . '_' . $i . '" value="' . $input_values['arrayValues'][$input_array_key] . '" class="' . Core::config('form', 'input_radio_class') . '" />';
							$i++;
						}
						break;

					case 'checkbox' :
						$return .= '<label class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';

						$i = 0;

						foreach ($input_values['arrayLabels'] as $input_array_key => $input_array_labels)
						{
							$return .= '<label for="' . $input_id . '_' . $i . '" class="' . Core::config('form', 'label_checkbox_class') . '">' . $input_array_labels . '</label>';
							$return .= '<input ' . (in_array($input_values['arrayValues'][$input_array_key], $input_values['defaultValue']) ? 'checked="checked"' : '') . ' type="checkbox" name="' . $input_id . '" id="' . $input_id . '_' . $i . '" value="' . $input_values['arrayValues'][$input_array_key] . '" class="' . Core::config('form', 'input_checkbox_class') . '" />';
							$i++;
						}
						break;

					case 'select' :
						$return .= '<label for="' . $input_id . '" class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';

						$return .= '<select name="' . $input_id . '" id="' . $input_id . '">';

						foreach ($input_values['arrayLabels'] as $input_array_key => $input_array_labels)
						{
							$return .= '<option ' . ($input_values['defaultValue'] == $input_values['arrayValues'][$input_array_key] ? 'selected="selected"' : '') . '  value="' . $input_values['arrayValues'][$input_array_key] . '" class="' . Core::config('form', 'input_select_class') . '">' . $input_array_labels . '</option>';
						}

						$return .= '</select>';

						break;

					case 'textarea' :
						$return .= '<label for="' . $input_id . '" class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';
						$return .= '<textarea name="' . $input_id . '" id="' . $input_id . '" class="' . Core::config('form', 'textarea') . '">' . $input_values['defaultValue'] . '</textarea>';
						break;

					case 'file' :
						$return .= '<label for="' . $input_id . '" class="' . Core::config('form', 'label_class') . '">' . $input_values['label'] . '</label>';
						$return .= '<input type="' . $input_values['type'] . '" name="' . $input_id . '" value="' . $input_values['defaultValue'] . '" id="' . $input_id . '" class="' . Core::config('form', 'input_file_class') . '" />';
						break;
				}

				$return .= '<div class="' . Core::config('form', 'return') . '"></div>';
			}
			$return .= '</fieldset>';
		}

		$return .= '<input type="submit" name="submit" class="' . Core::config('form', 'input_submit_class') . '" value="' . $this->_formSubmitButtonTitle . '" />';
		$return .= '</form>';

		return $return;
	}

	public function verifContent($inputName, $value, $regex, $message)
	{
		if (!preg_match($regex, $value))
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value);
		}
	}

	public function verifMatch($inputName, $value1, $value2, $message)
	{
		if ($value1 != $value2)
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value1);
		}
	}

	public function verifNotExist($inputName, $value, $table, $champ, $message)
	{
		$result = $this->DB->query("SELECT * FROM " . $table . " WHERE " . $champ . " LIKE '" . strtolower($value) . "'");


		if (count($result) != 0)
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value);
		}
	}

	public function verifExist($inputName, $value, $table, $champ, $message)
	{
		$result = $this->DB->query("SELECT * FROM " . $table . " WHERE " . $champ . " LIKE '" . strtolower($value) . "'");

		if (count($result) == 0)
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value);
		}
	}

	public function verifLength($inputName, $value, $min_length = 0, $max_length = 99999, $message)
	{
		if (strlen($value) > $max_length || strlen($value) < $min_length)
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value);
		}
	}

	public function verifHigherValue($inputName, $higherValue, $lowerValue, $message)
	{
		if ($higherValue <= $lowerValue)
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $higherValue);
		}
	}

	public function verifNotEmpty($inputName, $value, $message)
	{
		if (empty($value))
		{
			if ($message != '')
				$this->_errors[] = $message;

			$this->setDefaultValue($inputName, '');
		}
		else
		{
			$this->setDefaultValue($inputName, $value);
		}
	}

	public function verifFileUploaded($inputName, $value, $extensions, $size, $message)
	{
		if (!isset($value))
		{
			if ($message != '')
				$this->_errors[] = $message;
		}
		else
		{
			$file = pathinfo($value['name']);

			if (!in_array($file['extension'], $extensions))
			{
				if ($message != '')
					$this->_errors[] = $message;
			}
			else
			{

				if (filesize($value['tmp_name']) > $size)
				{
					if ($message != '')
						$this->_errors[] = $message;
				}
			}
		}
	}

	public function addCustomError($message)
	{
		if ($message != '')
			$this->_errors[] = $message;
	}

	public function report()
	{
		return (count($this->_errors) == 0 ? true : false);
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	private function getField($inputName)
	{
		foreach ($this->_fields as $k => $field)
		{
			foreach ($field as $l => $f)
			{
				if ($inputName == $l)
				{
					return $k;
					exit;
				}
			}
		}
	}

	private function fieldExists($idField)
	{
		return (isset($this->_fields[$idField]) ? true : false);
	}

	private function setDefaultValue($inputName, $value)
	{
		$field = $this->getField($inputName);

		if ($this->_fields[$field][$inputName]['type'] == 'checkbox')
			$this->_fields[$field][$inputName]['defaultValue'] = array($value);
		else
			$this->_fields[$field][$inputName]['defaultValue'] = $value;
	}

}

?>