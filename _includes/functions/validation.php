<?php
function validate($var,$type) {
	$var = trim(str_replace(' ','',$var));
	$type = strtolower($type);
	global $g_validationArray;
	
	if(strstr($type,'email')!==FALSE ) {
		if(!preg_match($g_validationArray['email'],$var)) {
			return NULL;
		}
	}
	elseif(strstr($type,'url')!==FALSE || strstr($type,'website')!==FALSE) {
		if(substr($var,0,7)!='http://') $var = 'http://'.$var;
		if(!preg_match($g_validationArray['website'], $var)) {
			return NULL;
		}
	}
	elseif(strstr($type,'postcode')!==FALSE) {
		if(!preg_match($g_validationArray['postcode'], $var)) {
			return NULL;
		}
	}
	elseif(strstr($type,'telephone')!==FALSE || strstr($type,'fax')!==FALSE) {
		$var = preg_replace('/ /','',$var);
		if(!preg_match($g_validationArray['telephone'], $var)) {
			return NULL;
		}
	}
	if(strstr($type,'postcode')!==FALSE) $var = strtoupper($var);
	return $var;
}

function validateError($var,$type) {
	$var = strip_tags(trim($var));
	$type = strtolower($type);
	
	if(strstr($type,'postcode')!==FALSE) {
		return 'Invalid postcode';
	}
	elseif(strstr($type,'email')!==FALSE) {
		return 'Invalid email address';
	}
	elseif(strstr($type,'telephone')!==FALSE || strstr($type,'fax')!==FALSE) {
		return 'Invalid number';
	}
	elseif(strstr($type,'url')!==FALSE || strstr($type,'website')!==FALSE) {
		return 'Invalid website address';
	}
	return FALSE;
}

function checkRequired($array) {
	if(!is_array($array)) return FALSE;
	$array = stripTags($array);
	foreach($array as $key => $value) {
		if(strstr($key,'required') && empty($value)) {
			$requiredArray[$key] = 'Missing';
		}
		if(!empty($value) && $value!='http://' && !validate($value,$key)) {
			$requiredArray[$key] = validateError($value,$key);
		}
	}
	if(!empty($requiredArray)) return $requiredArray;
	else return FALSE;
}
function check_form_required_elements($form) {
	$exclude_element_type_array = array('hidden','submit');
	$array = array();
	foreach($form as $fieldset) {
		if(!empty($fieldset['elements'])) {
			foreach($fieldset['elements'] as $key => $elements) {
				if(!empty($elements['type']) && !in_array($elements['type'],$exclude_element_type_array) && empty($_POST[$key])) {
					$array[$key] = @$elements['value'];
				}
			}
		}
	}
	return checkRequired($array);
}
function stripTags($array,$type='',$admin=false) {
	if(!is_array($array)) return FALSE;
	$trimmedArray = array();
	$allowed_html = '';
	if($admin==true) {
		$allowed_html = '<del><ins>';
	}
	foreach($array as $key => $value) {
		//if(is_array($value)) continue;
		//$value_output = $value;
		if(!is_array($value)) $value_array = array($value);
		else $value_array = $value;
		foreach($value_array as $value_output) {
			$value_output = trim($value_output);
			if($type=='db') $value_output = mysql_real_escape_string($value_output);
			if(is_array($value)) $key_output = $key.'-'.$value_output;
			else $key_output = $key;
			$trimmedArray[$key_output] = strip_tags(trim($value_output),$allowed_html);
		}
	}
	return $trimmedArray;
}

function authorise() {
	if(!empty($_SESSION['login']) && $_SESSION['login']['session']==session_id() && is_numeric($_SESSION['login']['id'])) {
		return TRUE;
	}
	else {
		if(!empty($_POST['username-required']) && !empty($_POST['password-required'])) {
			$username = formatText($_POST['username-required'],'db');
			$password = md5($_POST['password-required']);
			$sql = "SELECT ID, Username
					FROM author_details
					WHERE Password = '".$password."'
					AND (Username = '".$username ."' OR Email = '".$username."')
					AND Active = '1'";
			$query = mysqli_query($connect_admin, $sql);
			if(mysql_num_rows($query)===1) {
				$array = mysqli_fetch_array($query);
				$_SESSION['login']['session'] = session_id();
				$_SESSION['login']['id'] = $array['ID'];
				
				$person_update_sql = "UPDATE author_details SET Last_Login = NOW() WHERE ID = '".mysql_real_escape_string($array['ID'])."'";
				mysqli_query($connect_admin, $person_update_sql);
				return TRUE;
			}
		}
	}
	return FALSE;
}
?>