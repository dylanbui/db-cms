<?php

namespace App\Helper;

class AdFunc {

	public static function loadInputSupportType($name ,$selected = "" ,$status = false ,$params = '')
	{
		if (empty($selected)) {
			$selected = "";
		}
		$type = array (
				'input'=>'Input',
				'checkbox'=>'Check Box',
				'radio'=>'Radio',
				'selectbox'=>'Select Box',
				'textarea'=>'Textarea',
                'date_picker'=>'Date Picker',
				'choose_file'=>'Choose File',
				'mce_basic'=>'TextEditor Basic',			
				'mce_simple'=>'TextEditor Simple',
	// 			'mce_simple_1'=>'TextEditor Simple 1',
	// 			'mce_simple_2'=>'TextEditor Simple 2',
	// 			'mce_simple_3'=>'TextEditor Simple 3',			
				'mce_advance'=>'TextEditor Advance',
				'reference_to_table'=>'Reference To Database Table',
				'reference_to_page'=>'Reference To Page Object',
		);

		if($status)
			$type['status'] = 'As status field';

		$str = '<select name="'.$name.'" '.($params?$params:'').' class="input-block-level">';
		foreach($type as $key => $val)
			$str .= '<option value="'.$key.'" '.($key == $selected?'selected':'').'>'.$val.'</option>';
		$str .= '</select>';
		return $str;
	}

	public static function loadSelectFormatType($name ,$selected = "" ,$status = false ,$params = '')
	{
		if (empty($selected)) {
			$selected = "";
		}
		
	// 	required: "This field is required.",
	// 	remote: "Please fix this field.",
	// 	email: "Please enter a valid email address.",
	// 	url: "Please enter a valid URL.",
	// 	date: "Please enter a valid date.",
	// 	dateISO: "Please enter a valid date (ISO).",
	// 	number: "Please enter a valid number.",
	// 	digits: "Please enter only digits.",
	// 	creditcard: "Please enter a valid credit card number.",
	// 	equalTo: "Please enter the same value again.",
	// 	maxlength: $.validator.format("Please enter no more than {0} characters."),
	// 	minlength: $.validator.format("Please enter at least {0} characters."),
	// 	rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
	// 	range: $.validator.format("Please enter a value between {0} and {1}."),
	// 	max: $.validator.format("Please enter a value less than or equal to {0}."),
	// 	min: $.validator.format("Please enter a value greater than or equal to {0}.")
		
		$type = array (
				'none'=>'-- none --',
				'email'=>'email',
				'date'=>'date',
				'dateISO'=>'dateISO',
				'number'=>'number',
				'digits'=>'digits',
				'creditcard'=>'creditcard',
				'equalTo'=>'equalTo',
				'maxlength'=>'maxlength',
				'minlength'=>'minlength',
				'rangelength'=>'rangelength',
				'max'=>'max',
				'min'=>'min',
				'range'=>'range'
		);

		if($status)
			$type['status'] = 'As status field';

		$str = '<select name="'.$name.'" '.($params?$params:'').' class="input-block-level">';
		foreach($type as $key => $val)
			$str .= '<option value="'.$key.'" '.($key == $selected?'selected':'').'>'.$val.'</option>';
		$str .= '</select>';
		return $str;
	}

	public static function checkOrSelectInput(&$value)
	{
		if (empty($value)) {
			return "";
		} else if($value == 0) {
			return "";
		} else if($value == 1) {
			return "checked";
		}
		return "";
	}

	public static function loadDefineInput($arrConf ,$name ,$value = "" ,$css = "")
	{
		// 	$value = (empty($value) ? "" : $value);
		//<input type="text" data-msg-email="Please enter a valid email address" data-rule-email="true" data-msg-required="Please enter your email" data-rule-required="true" value="" name="email" id="email" class="input-xxlarge">
		
		$required = "";
		$required_text = "";
		if (df($arrConf['required'],0) == 1)
		{
			$required = " data-rule-required='true' ";
			$required_text = "";//"<span class='red'> (*) </span>";		
		}
		
		$format_type = "";
		if (df($arrConf['format_type'],'none') != 'none')
		{
			$format_type = " data-rule-{$arrConf['format_type']}='true' ";
			$required_text .= "<span class='help-inline'>&nbsp;Format : ".ucfirst($arrConf['format_type'])."  </span>";
		}	
		
		$type = $arrConf['type'];
		
		$html = "";
		switch ($type) 
		{
			case "input":
				$html = "<input type='text' class='input-xlarge' id='{$name}' name='{$name}' value='{$value}' {$required} {$format_type}>";
				break;
			case "checkbox":
				$arrData = self::evalInputData($arrConf['type_value']);
				$c_required = (df($arrConf['required'],0) == 1) ? "required" : "";			
				foreach ($arrData as $t_key => $t_value)
				{
					$checked = "";
					if (in_array($t_key, explode(",",$value)))
						$checked = "checked";
					$html .= "<label class='checkbox'><input type='checkbox' name='{$name}' value='{$t_key}' {$checked} {$c_required}>{$t_value}</label>";
					$c_required = '';
				}
				break;
			case "radio":
				$arrData = self::evalInputData($arrConf['type_value']);
				$c_required = (df($arrConf['required'],0) == 1) ? "required" : "";			
				foreach ($arrData as $t_key => $t_value)
				{
					$checked = "";
					if ($t_key == $value)
						$checked = "checked = 'true'";				
					$html .= "<label class='radio inline'><input type='radio' value='$t_key' name='{$name}' {$c_required}> {$t_value}</label>";
					$c_required = '';
				}
				break;
			case "selectbox": // array(key => value, key => value, key => value);
				$c_required = (df($arrConf['required'],0) == 1) ? "required> <option value=''>--- Chon ---</option>" : ">";			
				$html = "<select class='input-xlarge' id='{$name}' name='{$name}' {$c_required}";
				$arrData = self::evalInputData($arrConf['type_value']);
				foreach ($arrData as $t_key => $t_value)
				{
					$checked = "";
					if ($t_key == $value)
						$checked = "selected='true'";				
					$html .= "<option value='{$t_key}' {$checked}>{$t_value}</option>";
				}
				$html .= "</select>";
				break;
			case "textarea":
				$html = "<textarea rows='3' class='input-block-level' id='{$name}' name='{$name}' {$required} {$format_type}>{$value}</textarea>";
				break;

            case "date_picker":
                $now = date('d-m-Y');
                $format_value = df($arrConf['format_value'],'dd/mm/yyyy');
                $uni_name = str_replace(array('[',']'), '_', $name);
                $html = "<div data-date-viewmode='years' data-date-format='dd-mm-yyyy' data-date='{$now}' class='input-append date simple_date_picker_{$uni_name}'>";
                $html .= "<input type='text' id='{$name}' name='{$name}' {$required} value='{$value}' size='16' class='simple_date_picker_{$uni_name}'>";
                $html .= "<span class='add-on'><i class='icon-calendar'></i></span>";
                $html .= "</div>";
                $html .= "<script type='text/javascript'>";
                $html .= "	$('.simple_date_picker_{$uni_name}').datepicker({";
                $html .= "		format: '{$format_value}'";
                $html .= "	});";
                $html .= "</script>";
                break;

			case "choose_file":
				$input_id = str_replace("[", "_", $name);
				$input_id = str_replace("]", "_", $input_id);
				$window_link = __JS_URL . "ckeditor/kcfinder/browse.php?type=images";
				$html = "<input type='text' class='input-xxlarge' id='{$input_id}' name='{$name}' value='{$value}' {$required} {$format_type}>&nbsp;";
				$html .= "<a class='btn btn-small' href=# onclick='openChooseFileWindow(\"{$input_id}\",\"{$window_link}\")' type='button'><i class='icon-folder-open'></i></a>";
				break;
			case "mce_advance":
				$html = "<textarea rows='2' class='input-block-level' id='{$name}' name='{$name}' {$required}>{$value}</textarea>";
				$html .= "<script type='text/javascript'>";
				$html .= "	jQuery(document).ready(function ($) {";
				$html .= "		CKEDITOR.replace( '{$name}');";
				$html .= "	});";
				$html .= "</script>";
				break;
			case "mce_simple_1":
			case "mce_simple_2":
			case "mce_basic":
				$html = "<textarea rows='2' class='input-block-level' id='{$name}' name='{$name}' {$required}>{$value}</textarea>";
				$html .= "<script type='text/javascript'>";
				$html .= "	jQuery(document).ready(function ($) {";
				$html .= "		CKEDITOR.replace( '{$name}',{toolbar : 'Basic'});";
				$html .= "	});";
				$html .= "</script>";
				break;
			case "mce_simple":
				$html = "<textarea rows='2' class='input-block-level' id='{$name}' name='{$name}' {$required}>{$value}</textarea>";
				$html .= "<script type='text/javascript'>";
				$html .= "	jQuery(document).ready(function ($) {";
				$html .= "		CKEDITOR.replace( '{$name}',{toolbar : 'Basic_1'});";
				$html .= "	});";
				$html .= "</script>";
				break;
			case "reference_to_table":
				
	// 			array('ref_object'=>'member table', 'ref_id' => 'key_col', 'ref_display' => 'display col', 'ref_where' => 'condition');
				
				$c_required = (df($arrConf['required'],0) == 1) ? "required> <option value=''>--- Chon ---</option>" : ">";
				$html = "<select class='input-xlarge' id='{$name}' name='{$name}' {$c_required}";
					
				$arrData = self::evalInputData($arrConf['type_value']);
				
				$table = $arrData['ref_object'];
				$id = $arrData['ref_id'];
				$display_name = $arrData['ref_display'];
				$where_condition = df($arrData['ref_where'], NULL);
				
				$sql = "SELECT {$id} as display_id , {$display_name} as display_value FROM "._TB_PREFIX.$table;
				if (!is_null($where_condition))
					$sql .= " WHERE $where_condition ";
							
	            $rs = \App\Lib\Core\DbConnection::getInstance()->query($sql);

				foreach ($rs as $row)
				{
					$checked = "";
					if ($row['display_id'] == $value)
						$checked = "selected='true'";
					$html .= "<option value='{$row['display_id']}' {$checked}>{$row['display_value']}</option>";
				}
				$html .= "</select>";
				break;
			case "reference_to_page":
				
	// 			array('ref_object'=>'member table', 'ref_id' => 'key_col', 'ref_display' => 'display col', 'ref_where' => 'condition');
							
				$c_required = (df($arrConf['required'],0) == 1) ? "required> <option value=''>--- Chon ---</option>" : ">";
				$html = "<select class='input-xlarge' id='{$name}' name='{$name}' {$c_required}";
				
				$arrData = self::evalInputData($arrConf['type_value']);
					
				$table = $arrData['ref_object'];
				$id = $arrData['ref_id'];
				$display_name = $arrData['ref_display'];
				$where_condition = df($arrData['ref_where'], NULL);
					
				// Create configure object
				$config = \App\Lib\Core\Config::getInstance();
				$defaut_lang = $config->config_values['application']['language'];
				
				$sql = "SELECT p_cten_ln.{$id} as display_id , p_cten_ln.{$display_name} as display_value ";
				$sql .= " FROM `".TB_PAGE_CONTENT."`as p_cten ";
				$sql .= " 	INNER JOIN `".TB_PAGE_CONFIGURE."` as p_cfg ON p_cten.page_id = p_cfg.id ";
				$sql .= " 	INNER JOIN `".TB_PAGE_CONTENT_LN."`as p_cten_ln ON p_cten.id = p_cten_ln.id ";
				$sql .= " WHERE p_cfg.code = '{$table}' AND ln = '{$defaut_lang}'";
				
				if (!is_null($where_condition))
					$sql .= " AND $where_condition ";
					
	            $rs = \App\Lib\Core\DbConnection::getInstance()->query($sql);
					
				foreach ($rs as $row)
				{
					$checked = "";
					if ($row['display_id'] == $value)
						$checked = "selected='true'";
					$html .= "<option value='{$row['display_id']}' {$checked}>{$row['display_value']}</option>";
				}
				$html .= "</select>";
				break;			
		}
		return $html.$required_text;
	}


	// function load_define_input($type ,$name ,$value = "" ,$css = "")
	// {
	// // 	'input'=>'Input',
	// // 	'checkbox'=>'Check Box',
	// // 	'radio'=>'Radio',
	// // 	'selectbox'=>'Select Box',
	// // 	'textarea'=>'Textarea',
	// // 	'tinymce'=>'TextEditor Advance',
	// // 	'simplemce'=>'TextEditor Simple',
	// // 	'simplemce1'=>'TextEditor Simple 1',
	// // 	'simplemce2'=>'TextEditor Simple 2',
	// // 	'simplemce3'=>'TextEditor Simple 3',

	// // 	$value = (empty($value) ? "" : $value);
		
	// 	$html = "";
	// 	switch ($type) {
	// 		case "input":
	// 			$html = "<input type='text' class='input-xlarge' id='{$name}' name='{$name}' value='{$value}'>";
	// 			break;
	// 		case "checkbox":
	// 			$html = "Dang dinh nghia";
	// 			break;
	// 		case "radio":
	// 			$html = "Dang dinh nghia";
	// 			break;
	// 		case "selectbox":
	// 			$html = "Dang dinh nghia";
	// 			break;
	// 		case "textarea":
	// 			$html = "<textarea rows='3' class='input-block-level' id='{$name}' name='{$name}'>{$value}</textarea>";
	// 			break;
	// 		case "choose_file":
	// 			$input_id = str_replace("[", "_", $name);
	// 			$input_id = str_replace("]", "_", $input_id);
	// 			$window_link = __JS_URL . "/ckeditor/kcfinder/browse.php?type=images";
	// 			$html = "<input type='text' class='input-xxlarge' id='{$input_id}' name='{$name}' value='{$value}'>&nbsp;";
	// 			$html .= "<a class='btn btn-small' href=# onclick='openChooseFileWindow(\"{$input_id}\",\"{$window_link}\")' type='button'><i class='icon-folder-open'></i></a>";			
	// 			break;			
	// 		case "mce_advance":
	// 			$html = "<textarea rows='2' class='input-block-level' id='{$name}' name='{$name}'>{$value}</textarea>";
	// 			$html .= "<script type='text/javascript'>";								
	//     		$html .= "	jQuery(document).ready(function ($) {";
	//     		$html .= "		CKEDITOR.replace( '{$name}');";
	//     		$html .= "	});";
	//     		$html .= "</script>";
	// 			break;
	// 		case "mce_simple_1":
	// 		case "mce_simple_2":
	// 		case "mce_simple_3":
	// 		case "mce_simple":
	// 			$html = "<textarea rows='2' class='input-block-level' id='{$name}' name='{$name}'>{$value}</textarea>";
	// 			$html .= "<script type='text/javascript'>";
	// 			$html .= "	jQuery(document).ready(function ($) {";
	// 			$html .= "		CKEDITOR.replace( '{$name}',{toolbar : 'Basic_1'});";
	// 			$html .= "	});";
	// 			$html .= "</script>";
	// 			break;						
	// 	}
	// 	return $html;
	// }


	public static function evalInputData($key_value)
	{
		$key_value = htmlspecialchars_decode($key_value);
		$data = NULL;
		eval("\$data = array({$key_value});");
		return $data;
	}

	public static function showMessage($title = NULL ,$alter = NULL ,$data = NULL)
	{
		// alter : success ,info ,error
		if (is_null($data))
			return "";
		
		if (!empty($alter))
			$alter = '-'.$alter;
		
		$html = "<div class='alert alert{$alter} fade in'>";
		$html .= "	<button data-dismiss='alert' class='close'>×</button>";

		if (is_string($data)) 
		{
			$html .= "		<strong>{$title} !</strong> {$data}.";
		} else 
		{
			if (!empty($title))
				$html .= "		<h4>{$title} !</h4>";		

			$html .= "		<ul>";
			foreach ($data as $row)
				$html .= "	<li>{$row}.</li>";			
			$html .= "		</ul>";
		}
			
		$html .= "</div>";		
		
		return $html;
	}

} 

