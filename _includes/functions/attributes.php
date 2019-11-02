<?php
/* VALID TOKEN -- checks for valid id/name/class - http://www.w3.org/TR/html401/types.html#type-name
-------------------------------------------------------------------------------------------------- */
function validToken($var) {
	//global $g_naughty_words;
	//foreach($g_naughty_words as $n_word) $var = str_highlight($var, $n_word, '', '');

	if(!empty($var) && preg_match('/^[a-zA-Z][a-zA-Z0-9\-\_\:\.]+$/',$var)) {
		return formatText($var,'url');
	}
	else {
		return false;
	}
}

/* ADD CLASS -- taking either an array or string
-------------------------------------------------------------------------------------------------- */
function addClass($var) {
	if(is_array($var) && !empty($var)) {
		$class = ''; $className_array = array();
		foreach($var as $className) {
			if(!validToken($className)) continue;
			if(!in_array($className,$className_array)) $className_array[] = $className;
			else continue;
			$class .= ' '.validToken($className);
		}
		return trim($class);
	}
	else return validToken($var);
}

/* ADD ATTRIBUTES -- title, ids, classes, xfn data and rel types
-------------------------------------------------------------------------------------------------- */
function addAttributes($title='',$id='',$class='',$xfn='',$rel='',$accesskey='',$tabindex='',$type='',$name='',$mime='',$lang='',$hreflang='',$charset='',$metadata=array()) {
	
	$returnTitle=''; $returnID=''; $returnRel=''; $returnClasses=''; $rels='';
	$returnAccesskey=''; $returnTabindex=''; $returnType=''; $returnName='';

	if(validToken($id)) {
		$returnID = ' id="'.validToken($id).'"';
	}
	if(validToken($name)) {
		$nameArray = '';
		if(strtolower($type)=='checkbox') $nameArray = '[]';
		$returnName = ' name="'.validToken($name).$nameArray.'"';
	}
	if(!empty($type) && checkInputType($type)) {
		$returnType = ' type="'.checkInputType($type).'"';
	}
	elseif(!empty($mime)) {
		$returnMime = ' type="'.formatText($mime,'url').'"';
	}
	
	if(!empty($lang)) {
		$returnLang = ' lang="'.formatText($lang,'url').'"';
	}
	if(!empty($hreflang)) {
		$returnHreflang = ' hreflang="'.formatText($hreflang,'url').'"';
	}
	if(!empty($charset)) {
		$returnCharset = ' charset="'.formatText($charset,'url').'"';
	}
	$metadata_string = '';
	if(!empty($metadata) && is_array($metadata)) {
		$metadata_string .= ' {';
		foreach($metadata as $meta_key => $meta_value) {
			$metadata_string .= "'".$meta_key."':'".$meta_value."', ";
		}
		$metadata_string = rtrim(rtrim($metadata_string),',');
		$metadata_string .= '}';
		//echo $metadata_string.'<br />';
	}
	
	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
	}
	if(!empty($rel)) {
		if(!is_array($rel)) $rel = array($rel);
	}
	if(!empty($xfn)) {
		if(!is_array($xfn)) $xfn = array($xfn);
	}
	if(!empty($title) && is_string($title)) {
		$returnTitle = ' title="'.formatText(strip_tags($title)).'"';
	}
	if(!empty($accesskey) && is_string($accesskey) && strlen($accesskey)==1) {
		$returnAccesskey = ' accesskey="'.$accesskey.'"';
	}
	if(!empty($tabindex) && is_numeric($tabindex)) {
		$returnTabindex = ' tabindex="'.$tabindex.'"';
	}
	
	if(!empty($rel) && !empty($xfn)) $rels = array_merge($rel,$xfn);
	elseif(!empty($rel) && addClass($rel)) $rels = $rel;
	elseif(!empty($xfn) && addClass($xfn))  $rels = $xfn;
	
	if(is_array($rels)) {
		$returnRel = ' rel="'.addClass($rels).'"';
	}

	if((!empty($class) && addClass($class)) || !empty($metadata_string)) {
		$class_string = '';
		if(!empty($class) && addClass($class)) $class_string .= addClass($class);
		if(!empty($metadata_string)) $class_string .= $metadata_string;
		$returnClasses = ' class="'.trim($class_string).'"';
	}
	
	return $returnTitle.$returnType.$returnName.$returnID.$returnRel.$returnClasses.$returnAccesskey.$returnTabindex;
}
?>