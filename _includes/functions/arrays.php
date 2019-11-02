<?php
function array_search_recursive($needle, $haystack, $key_lookin="") {
	$path = NULL;
	if (!empty($key_lookin) && array_key_exists($key_lookin, $haystack) && $needle === $haystack[$key_lookin]) {
		$path[] = $key_lookin;
	}
	else {
		foreach($haystack as $key => $val) {
			if(is_scalar($val) && $val === $needle && empty($key_lookin)) {
				$path[] = $key;
				break;
			}       
			elseif(is_array($val) && $path = array_search_recursive($needle, $val, $key_lookin)) {
				array_unshift($path, $key);
				break;
			}
		}
	}
	return $path;
}
function multi_array_search($search_value, $the_array) {
	if(is_array($the_array)) {
		foreach ($the_array as $key => $value) {
			$result = multi_array_search($search_value, $value);
			if (is_array($result)) {
				$return = $result;
				array_unshift($return, $key);
				return $return;
			}
			elseif ($result == true) {
				$return[] = $key;
				return $return;
			}
		}
		return false;
	}
	else {
		if($search_value == $the_array) {
			return true;
		}
		else {
			return false;
		}
	}
}
function cleanArray($array) {
   foreach ($array as $index => $value) {
       if(empty($value)) unset($array[$index]);
   }
   return $array;
}
?>