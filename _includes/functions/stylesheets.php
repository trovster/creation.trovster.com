<?php
function discard(&$checkArray, $key, $mediaArray) {
	if(!in_array($checkArray,$mediaArray)) $checkArray = false;
}
function checkStylesheetMedia($media) {
	global $g_cssmediaArray;
	
	$media = strtolower($media);
	if(empty($media)) $media = 'screen';
	$checkArray = explode(',',$media);

	array_walk($checkArray,'discard',$g_cssmediaArray);
	$checkArray = cleanArray($checkArray);
	
	if(!empty($checkArray)) $media = implode(',',$checkArray);
	else $media = 'all';
	
	if($media=='screen') $media .= ',projection';
	elseif($media=='projection') $media .= ',screen';
	
	return $media;
}

function createInlineStyle($array) {
	if(empty($array) || !is_array($array)) return '';
	
	$script_css_array = array(); $print_css_array = array(); $other_css_array = array();
	$general_css_path = '/css/';
	foreach($array as $stylesheet_array) {
		if(empty($stylesheet_array['file'])) continue;
		elseif(!empty($array['media']) && strtolower($stylesheet_array['media'])=='print') $print_css_array[] = $stylesheet_array;
		elseif(preg_match('#^scripts/#',$stylesheet_array['file'])) $script_css_array[] = $stylesheet_array;
		else $other_css_array[] = $stylesheet_array;
	}
	$array = array_merge($script_css_array,$other_css_array);
	$array = array_merge($array,$print_css_array);
	
	$styleblock = ''; $import_styleblock = '';
	$check_array = array();
	foreach($array as $array) {
		$path = $general_css_path; $checkPath = $_SERVER['DOCUMENT_ROOT'];

		$title = '';
		$media = '';
		$stylesheet = '';
		$rel = 'stylesheet';
		$accesskey = '';
		
		if(in_array($array['file'],$check_array)) continue;
		else $check_array[] = $array['file'];
		
		if(!empty($array['title'])) 	$title = ' title="'.formatText($array['title']).'"';
		if(!empty($array['file']))		$stylesheet = $array['file'];
		if(!empty($array['media']))		$media = $array['media'];
		if(!empty($array['rel']) && $array['rel']=='alternate') $rel = $array['rel'].' '.$rel;
		if(!empty($array['accesskey'])) $accesskey = ' accesskey="'.$array['accesskey'].'"';
		$accesskey = '';

		if(substr($stylesheet,0,7)=='http://') {
			$path = ''; $checkPath = '';
		}
		else {
			$stylesheet = formatText($stylesheet,'url');
		}
		if(substr($stylesheet,-4,4)!='.css') {
			$stylesheet .= '.css';
		}
		$media = checkStylesheetMedia($media);

		if(is_file($checkPath.$path.htmlentities($stylesheet))) {
			$styleblock_temp = file_get_contents($checkPath.$path.htmlentities($stylesheet));
			preg_match_all('#@import url\(([^)]+)\)\;#',$styleblock_temp,$at_import_files_array);
			if(!empty($at_import_files_array) && is_array($at_import_files_array)) {
				foreach($at_import_files_array[1] as $at_import_file) {
					$import_styleblock_temp = file_get_contents($_SERVER['DOCUMENT_ROOT'].$general_css_path.$at_import_file);
					$styleblock .= $import_styleblock_temp;
				}
			}
			$styleblock .= $styleblock_temp;
		}
	}
	return css_strip_whitespace($styleblock);
}


function css_strip_whitespace($css) {
  $replace = array(
    "#/\*.*?\*/#s" => "",  // Strip C style comments.
    "#\s\s+#"      => " ", // Strip excess whitespace.
  );
  $search = array_keys($replace);
  $css = preg_replace($search, $replace, $css);

  $replace = array(
    ": "  => ":",
    "; "  => ";",
    " {"  => "{",
    " }"  => "}",
    ", "  => ",",
    "{ "  => "{",
    ";}"  => "}", // Strip optional semicolons.
    ",\n" => ",", // Don't wrap multiple selectors.
    "\n}" => "}", // Don't wrap closing braces.
    "} "  => "}\n", // Put each rule on it's own line.
  );
  $search = array_keys($replace);
  $css = str_replace($search, $replace, $css);

  return trim($css);
}
?>