<?php
/* REPAIR POST VARIABLES - to handle arrays for checkboxes/radio buttons
-------------------------------------------------------------------------------------------------- */
function repairPost($data) {
   // combine rawpost and $_POST ($data) to rebuild broken arrays in $_POST
	$rawpost = "&".file_get_contents("php://input");
	while(list($key,$value)= each($data)) {
		$pos = preg_match_all("/&".$key."=([^&]*)/i",$rawpost, $regs, PREG_PATTERN_ORDER);       
		if((!is_array($value)) && ($pos > 1)) {
			$qform[$key] = array();
			for($i = 0; $i < $pos; $i++) {
				$qform[$key][$i] = urldecode($regs[1][$i]);
			}
		}
		else {
			$qform[$key] = $value;
		}
	}
	return $qform;
}
//if(isset($_POST) && !empty($_POST)) $_POST = repairPost($_POST);

/* CHECK MEDIA TYPES
-------------------------------------------------------------------------------------------------- */
function checkInputType($type) {
	global $g_forminputArray;
	if(!in_array($type,$g_forminputArray)) $type = 'input';	
	return formatText($type,'url');
}

/* CREATE OPTIONS - takes array, default
-------------------------------------------------------------------------------------------------- */
function createOptions($array,$defaultText,$id,$class='',$disabled='') {
	if($disabled=='true') $disabled = ' disabled="disabled"';
	$return = '<select'.addAttributes('',$id,@$class,'','','','','',$id).$disabled.'>'."\n";
	$return .= '<option value="0">'.$defaultText.'</option>'."\n";
	
	$optgroup_check_array = array(); 
	$optgroup_details_array = array(); 
	foreach($array as $key => $optionArray) {
		
		if(!empty($optionArray['optgroup'])) {
			$optgroup_safe = url_encode($optionArray['optgroup']);
			if(!in_array($optgroup_safe,$optgroup_check_array)) {
				$optgroup_check_array[] = $optgroup_safe;
				$optgroup_details_array[] = array(
					'label' => $optgroup_safe,
					'text' => $optionArray['optgroup']
				);
			}
			// we're not using the normal option/value, we need optgroup around, see below
			continue;
		}
		
		$selected = '';
		if(isset($optionArray['selected'])) $selected = ' selected="selected"';
		$return .= '<option value="'.formatText($optionArray['value']).'"'.$selected.'>'.formatText($optionArray['text']).'</option>'."\n";
	}
	
	// optgroup added in ... :s
	if(!empty($optgroup_check_array)) {
		asort($optgroup_check_array);
		foreach($optgroup_check_array as $check_key => $check_value) {
			//echo '<pre>'; print_r($optgroup_details_array[$check_key]); echo '</pre><br /><br />';
			$return .= '<optgroup label="'.formatText($optgroup_details_array[$check_key]['text']).'">'."\n";
			foreach($array as $key => $optionArray) {
				if($optionArray['optgroup']==$optgroup_details_array[$check_key]['text']) {
					$selected = '';
					if(isset($optionArray['selected'])) $selected = ' selected="selected"';
					$return .= '<option label="'.formatText($optionArray['optgroup']).' - '.formatText($optionArray['text']).'" value="'.formatText($optionArray['value']).'"'.$selected.'>'.formatText($optionArray['text']).'</option>'."\n";
				}
				else continue;
			}
			$return .= '</optgroup>'."\n";
		}
	}
	
	return $return.'</select>';
}

/* CREATE FORM - form array, method (defaults post), action (default ./), ID, Class & upload type
-------------------------------------------------------------------------------------------------- */
function createForm($array,$method='post',$action='./',$formID='',$formClass='',$upload='') {
	if(!is_array($array)) return false;	
	
	if(empty($method) || ($method!='post' && $method!='get')) $method = 'post';
	if(empty($action)) $action = './';
	if(!empty($upload) && $upload=='upload') $upload = ' enctype="multipart/form-data"';
	
	$formStart = '<form method="'.formatText($method,'url').'" action="'.formatText($action).'"'.addAttributes('',$formID,$formClass).$upload.'>'."\n";
	$formEnd = '</form>'."\n\n";
	
	$form = '';
	$formArray = array();
	foreach($array as $fieldset_type => $fieldset_array) {
		if(empty($fieldset_type)) continue;
		$fieldset_type = strtolower($fieldset_type);	
		
		// setup the classes
		if(!empty($fieldset_array['class'])) {
			if(!is_array($fieldset_array['class'])) $fieldset_array['class'] = array($fieldset_array['class']);
		}
		elseif(empty($fieldset_array['class'])) $fieldset_array['class'] = array();
		
		if($fieldset_type=='submit') $fieldset_array['class'][] = 'submit-fieldset';
		if($fieldset_type=='checkbox') $fieldset_array['class'][] = 'checkbox';
		if($fieldset_type=='radio') $fieldset_array['class'][] = 'radio';
		
		if(empty($fieldset_array['elements'])) continue;
		if(!empty($fieldset_array['id']) && substr($fieldset_array['id'],-9)!='-fieldset') $fieldset_array['id'] .= '-fieldset';
		
		$form .= "\t".'<fieldset'.addAttributes('',@$fieldset_array['id'],@$fieldset_array['class']).'>'."\n";
		$form .= "\t\t".'<legend>'.formatText(@$fieldset_array['fieldset']).'</legend>'."\n";
		
		//$form_array = array(); 
		foreach($fieldset_array['elements'] as $input_type => $input_array) {
			if($fieldset_array!='submit' && (!empty($input_array['type']) && $input_array['type']=='checkbox' || $input_array['type']=='radio')) {
				// not inside submit array AND the input type is a checkbox or radio
				
				$listClass = array();
				if(!empty($input_array['class'])) {
					if(!is_array($input_array['class'])) $input_array['class'] = array($input_array['class']);
				}
				else $input_array['class'] = array();
				
				$listClass = array_merge($listClass,@$input_array['class']);
				if(!empty($input_array['radio']) && $input_array['type']=='radio') {
					$array = $input_array['radio'];
					$listClass[] = 'radio';
				}
				elseif(!empty($input_array['checkbox']) && $input_array['type']=='checkbox') {
					$array = $input_array['checkbox'];
					$listClass[] = 'checkbox';
				}
				else continue;

				$radioList = array(); $r_i=0;
				foreach($array as $key => $radio_array) {
					if(empty($input_array['name']) || empty($input_array['type']) || !validToken($input_array['name']) || !validToken($radio_array['id'])) continue;
			
					$type = formatText($input_array['type'],'url');
					$name = formatText($input_array['name'],'url');
					$id = $name.'-'.formatText($radio_array['id'],'url');
					
					$input_value = ''; $selected = '';
					if(isset($radio_array['selected']) || isset($radio_array['checked'])) $selected = ' checked="checked"';
					if(!empty($radio_array['value'])) $radio_value = $radio_array['value'];
					
					$inputAttributes = addAttributes(@$radio_array['title'],$id,'','','','','',@$input_array['type'],@$input_array['name'],'','','','',@$input_array['metadata']);
					$input = '<input'.$inputAttributes.$selected.' value="'.$radio_value.'" />';
					
					// create the label
					$label = '';
					if(!empty($radio_array['label'])) {
						$label = '<label for="'.$id.'"'.addAttributes(@$radio_array['title']).'>';
						$label .= formatText($radio_array['label']);
						$label .= '</label>';
					}
					$radioList[$r_i]['text'] = $input.$label;
					$r_i++;
				}
				
				// if the name has 'required' then append a * to the label.
				$required_start = ''; $required_end = '';
				$label_error_text = '';
				if(strpos($name,'required')>0) {
					$required_start = '<strong>';
					$required_end = ' <abbr title="Required" class="required-abbr">*</abbr></strong>';
					$listClass[] = 'required';
				}
				if(!empty($input_array['error'])) {
					$label_error_text = ' <em class="error">'.formatText($input_array['error']).'</em>'; // append the error message after the </label>
					$listClass[] = 'error';
				}
				
				if(isset($input_array['label'])) {
					if(!empty($formArray) && is_array($formArray)) $formArray_count = count($formArray);
					else $formArray_count = 0;
					
					$formArray[$formArray_count]['text'] = $required_start.$input_array['label'].$required_end.$label_error_text;
					$formArray[$formArray_count]['definition'] = "\n".createList($radioList);
					$formArray[$formArray_count]['class'] = $listClass;
				}
				
			}
			else {
				// main type of inputs...
				if(!isset($input_array['name']) || !isset($input_array['type']) || !validToken($input_array['name'])) continue;
				$type = formatText($input_array['type'],'url');
				$name = formatText($input_array['name'],'url');
								
				$id = $name; // set the ID to the name, if the ID exists, then use that for the 'for' instead
				if(isset($input_array['id']) && validToken($input_array['id'])) $id = formatText($input_array['id'],'url');
				
				// classes go on the container element, checking whether it's an array
				if(isset($input_array['class'])) {
					if(!is_array($input_array['class'])) $input_array['class'] = array($input_array['class']);
				}
				else $input_array['class'] = array();
				
				$disabled = ''; // if disabled is true
				if(isset($input_array['disabled']) && $input_array['disabled']=='true') {
					$disabled = ' disabled="disabled"';
					$input_array['class'][] = 'disabled';
				}
				
				// if the name has 'required' then append a * to the label.
				$required_start = ''; $required_end = '';
				if(strpos($name,'required')>0) {
					$required_start = '<strong>';
					$required_end = ' <abbr title="Required" class="required-abbr">*</abbr></strong>';
					$input_array['class'][] = 'required';
				}
								
				// create the label
				$label = '';
				if(isset($input_array['label'])) {
					$label = '<label for="'.$id.'"'.addAttributes(@$input_array['title'],'','','','',@$input_array['accesskey']).'>';
					$label .= $required_start.formatText($input_array['label']).':'.$required_end;
					$label .= '</label>';
					if(!empty($input_array['error'])) {
						$label .= ' <em class="error">'.formatText($input_array['error']).'</em>'; // append the error message after the </label>
						$input_array['class'][] = 'error';
					}
				}
				
				$inputClass = '';
				$input_value = '';
				$inputID = $id;
				if(!empty($input_array['value'])) $input_value = $input_array['value'];
				
				if($fieldset_type=='submit') {
					if($input_array['type']=='submit') {
						if(empty($input_array['class'])) $input_array['class'][] = formatText($input_value,'url');
						$input_array['class'][] = 'submit';
						$input_array['title'] = formatText($input_array['value']);
					}
					//$inputID = $id.'-'.formatText($input_value,'url');
					$id = $id.'-'.formatText($input_value,'url');
					$id = '';
					
				}
				$inputClass = $input_array['class'];

				// input attributes for the different types of inputs
				if($input_array['type']==='text' || $input_array['type']==='password' || $input_array['type']==='submit' || $input_array['type']==='hidden' || $input_array['type']==='file') {
					
					if($input_array['type']==='submit' && $input_array['name']==='submit') {
						$input_array['name'] .= '_form';
					}
					$input_array['class'][] = checkInputType($input_array['type']);
					$inputClass[] = checkInputType($input_array['type']);
					$inputAttributes = addAttributes(@$input_array['title'],$id,$inputClass,'','',@$input_array['accesskey'],@$input_array['tabindex'],@$input_array['type'],@$input_array['name'],'','','','',@$input_array['metadata']);
					
					$element = '<input'.$inputAttributes.$disabled.' value="'.$input_value.'" />';
					
					if(strstr($type,'email')!==FALSE ) $input_array['class'][] = 'email';
					elseif(strstr($type,'url')!==FALSE || strstr($type,'website')!==FALSE) $input_array['class'][] = 'url';
					elseif(strstr($type,'postcode')!==FALSE) $input_array['class'][] = 'postcode';
					elseif(strstr($type,'telephone')!==FALSE) $input_array['class'][] = 'telephone';
				}
				elseif($input_array['type']==='textarea') {
					$input_array['class'][] = 'textarea';
					$inputClass[] = 'textarea';
					$inputAttributes = addAttributes(@$input_array['title'],$inputID,$inputClass,'','',@$input_array['accesskey'],@$input_array['tabindex'],'',@$input_array['name']);
					$element = '<textarea'.$inputAttributes.$disabled.' cols="50" rows="10">'.$input_value.'</textarea>';
					
				}
				elseif($input_array['type']==='select') {
					$input_array['class'][] = 'select';
					$element = $input_value;
				}			
				
				if(!empty($formArray) && is_array($formArray)) $i = count($formArray);
				else $i = 0;
				
				//echo $fieldset_type;
				if($fieldset_type=='submit') {
					// we don't need a definition list, just a list of submit buttons and hidden inputs
					if(!empty($submitArray) && is_array($submitArray)) $s = count($submitArray);
					else $s = 0;
					$submitArray[$s]['text'] = $element;
					$submitArray[$s]['type'] = $input_array['type'];
					$submitArray[$s]['class'] = $input_array['class'];
				}
				else {
					// definition list required, we're in the fieldset, we now need to loop through the inputs
					$formArray[$i]['text'] = $label;
					if(!empty($formArray[$i]['definition']) && !is_array($formArray[$i]['definition'])) $formArray[$i]['definition'] = array($formArray[$i]['definition']);
					$formArray[$i]['definition'][] = $element;
					$formArray[$i]['class'] = $input_array['class'];
					//echo $label;
					
					// notes appear after the input, can be just one or an array
					if(isset($input_array['note'])) {
						if(!is_array($input_array['note'])) $input_array['note'] = array($input_array['note']);
						foreach($input_array['note'] as $note) {
							$formArray[$i]['definition'][] = formatText($note);
						}
					}
				}
			}
		}
		
		if($fieldset_type=='submit') {
			if(empty($submitArray)) continue;
			foreach($submitArray as $i => $els) {
				if(isset($els['type']) && $els['type']!='submit') {
					$form .= $els['text']."\n";
					unset($submitArray[$i]); // remove non-submit buttons from the form array
				}
			}
			$submitArray = array_values($submitArray);
			$form .= createList($submitArray); // create a list of the SUBMIT buttons only
			unset($submitArray);
		}
		elseif(isset($formArray)) {
			//echo '<pre>'; print_r($formArray); echo '</pre><br />';
			$form .= createDefinitionList($formArray); // the main inputs are in a definition list
			unset($formArray);
		}

		$form .= "\t".'</fieldset>'."\n";
	}
	if(!empty($form)) return $formStart.$form.$formEnd;
}


/* CUSTOM FORM ERRORS - takes two arrays 1) the form 2) the errors
-------------------------------------------------------------------------------------------------- */
function createFormErrors($formArray,$errorArray) {
	foreach($errorArray as $field => $error) {
		$searchArray = array_search_recursive($field,$formArray);
		$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['error'] = $error;
		//$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['class'] = 'error';
		if(strpos(strtolower($error),'invalid')!==false) {
			$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['class'][] = 'invalid';
		}
	}

	foreach($_POST as $field => $value) {
		$searchArray = array_search_recursive($field,$formArray);
		if(!empty($searchArray)) {
			if($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']!='password'
			&& $formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']!='select'
			&& $formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']!='checkbox'
			&& $formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']!='radio') {
				$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['value'] = $value;
			}
			if($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']=='checkbox'
			|| $formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']=='radio') {
				$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]][$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']][$value]['checked'] = 'checked';
			}
			if($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['type']=='select'
			&& !empty($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['options'])
			&& !empty($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['text'])
			&& !empty($value)) {
				$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['options'][$value]['selected'] = 'selected';
				$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['value'] = createOptions(
					$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['options'],
					$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['text'],
					$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['id']
				);
			}
			if(!isset($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['error']) &&
			   !empty($formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['value']) &&
			   $formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['value']!=='http://') {
				//$formArray[$searchArray[0]][$searchArray[1]][$searchArray[2]]['class'][] = 'correct';
			}
		}
	}
	return $formArray;
}
?>