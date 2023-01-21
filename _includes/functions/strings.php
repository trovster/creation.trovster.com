<?php
/* FORMAT TEXT -- basic formatting of text
-------------------------------------------------------------------------------------------------- */
function formatText($var,$type='') {
	global $connect_admin;
	$var = trim(stripslashes($var));
	$type = strtolower($type);

	if($type=='output') {
		$var = wordwrap(trim(SmartyPants(markdown($var))),125)."\n";
		$var = abbreviate($var);
	}
	elseif($type=='title') {
		$var = ucfirst(strtolower(htmlentities($var, ENT_QUOTES, 'UTF-8')));
		//$var = abbreviate($var);
	}
	elseif($type=='capitals') {
		$var = ucwords(strtolower(htmlentities($var, ENT_QUOTES, 'UTF-8')));
		//$var = abbreviate($var);
	}
	elseif($type=='url') {
		//$var = preg_replace();
		$var = strtolower(str_replace(' ','-',$var));
	}
	elseif($type=='db') {
		$var = mysqli_real_escape_string($connect_admin, strip_tags($var));
	}
	else {
		if(!preg_match('/<[^<^>]+>/',$var)) {
			if(preg_match('/ & /',$var,$array)!==false) $var = str_replace(' & ',' &#38; ',$var);
			if(preg_match('/\'/',$var,$array)!==false) $var = str_replace('\'','&#39;',$var);
			if(preg_match('/"/',$var,$array)!==false) $var = str_replace('"','&#34;',$var);
		}
	}

	return $var;
}

function url_encode($var) {
	$var = str_replace(array('ü','é'),array('u','e'),$var);
	$characters = preg_split('//', $var, -1, PREG_SPLIT_NO_EMPTY);
	$characters_array = str_replace(array('  ',' '),array(' ','-'),strtolower(implode(preg_grep('/[ _A-Za-z0-9]/', $characters))));
	return $characters_array;
}
function url_create($array) {
	$text = '/';
	if(!is_array($array)) return '/'.$array.'/';
	else {
		foreach($array as $value) {
			if(empty($value)) continue;
			$text .= trim($value,'/').'/';
		}
	}
	return $text;
}
function url_query_build($array) {
	if(!is_array($array)) return;
	$return = array();
	foreach($array as $k => $v) $return[] = urlencode($k).'='.urlencode($v);
	return implode('&', $return);
}


function name($f,$s) {
	$array = array();
	$array['forename'] 	= formatText($f,'title');
	$array['surname'] 	= formatText($s,'title');
	$array['full-name']	= $array['forename'].' '.$array['surname'];
	$array['url']		= url_encode($array['full-name']);
	return $array;
}
function profile_url($array) {
	return '/company/'.$array['url'].'/';
}
function abbreviate($var) {
	global $g_company_name;
	global $g_abbreviations;
	if(empty($g_abbreviations) || !is_array($g_abbreviations)) return $var;

	$var = preg_replace('#<abbr title="(.*?)">(.*?)</abbr>#', '\2', $var);
	$var = preg_replace('#<acronym title="(.*?)">(.*?)</acronym>#', '\2', $var);
	foreach($g_abbreviations as $abbr => $def) {
		$var = str_highlight($var, $abbr, STR_HIGHLIGHT_WHOLEWD, '<abbr title="'.formatText($def).'">'.strtoupper($abbr).'</abbr>');
	}
	$var = str_highlight($var, $g_company_name, STR_HIGHLIGHT_WHOLEWD, '<strong class="org">'.$g_company_name.'</strong>');
	return $var;
}
function process_links($var) {
	preg_match_all('#(\[(.*?)\])(\((.*?)\))#', $var, $anchors_array);
	$anchors_url_check = array(); $anchors_title_check = array(); $anchors = array(); $i=0;
	if(!empty($anchors_array) && is_array($anchors_array)) {
		//$anchors_array[2] - the link phrase
		//$anchors_array[4] - the url
		$a=0;
		for($i=0; $i<count($anchors_array[4]); $i++) {
			if(!in_array($anchors_array[4][$i],$anchors_url_check) && !in_array($anchors_array[2][$i],$anchors_title_check) && (substr($anchors_array[4][$i],0,7)!='mailto:')) {
				$anchors_url_check[] = $anchors_array[4][$i];
				$anchors_title_check[] = $anchors_array[2][$i];
				$anchors[$a] = array(
					'text' => ucfirst($anchors_array[2][$i]),
					'link' => $anchors_array[4][$i],
					'class' => array(),
					'link-class' => array(),
				);
				if(preg_match('#^http://#',$anchors_array[4][$i])) {
					$anchors[$a]['rel'][] = 'external';
					$anchors[$a]['anchor-class'][] = 'external';
				}
				$a++;
			}
		}
	}
	return $anchors;
}

function feedback_show($feedback,$id='feedback',$class=array()) {
	if(empty($feedback[0])) $feedback = array($feedback);

	$output = '';
	$output .= '<div'.addAttributes('',$id,$class).'>';

	foreach($feedback as $info) {
		$feedback_class = array('feedback-box');
		if(!empty($info['class'])) $feedback_class = array_merge($feedback_class,$info['class']);
		if(!empty($info['method'])) $feedback_class[] = $info['method'];
		$output .= '<div'.addAttributes('','',$feedback_class).'>'."\n";
		$output .= '<h3>'.$info['heading'].'</h3>'."\n";
		$output .= formatText($info['description'],'output');
		if(!empty($info['sql'])) $output .= '<pre class="sql">'.$info['sql'].'</pre>';
		$output .= '</div>'."\n";
	}

	$output .= '</div>'."\n";
	return $output;
}



/* FORMAT DATE -- general formating of dates - http://php.net/date
-------------------------------------------------------------------------------------------------- */
function formatDate($var,$type='') {
	if(!is_int($var)) $var = strtotime($var); //$var = strtotime($var.' GMT');
	$type = strtolower($type);

	if($type=='short-date') {
		$var = date('d.m.Y',$var); // 2006.03.07
	}
	elseif($type=='long-date') {
		$var = date('l jS F Y',$var); // Monday 6th July 2006
	}
	elseif($type=='full-date') {
		$var = date('l jS F Y \a\t H:i',$var); // Monday 6th July 2006
	}
	elseif($type=='time') {
		$var = date('H:i',$var); // 19:47
	}
	elseif($type=='comments') {
		$var = date('D jS M Y \a\t H:i',$var); // Mon 6th Jan 2006 at 19:47
	}
	elseif($type=='long-url') {
		$var = date('Y/m/d/',$var); // yyyy/mm/dd/
	}
	elseif($type=='eshot-url') {
		$var = strtolower(date('F-y/',$var)); // january/
	}
	elseif($type=='short-url') {
		$var = date('Y/m/',$var); // admin area - yyyy/mm/
	}
	elseif($type=='news-archive') {
		$var = date('F Y',$var); // admin area - February 2006
	}
	elseif($type=='admin-sql-check') {
		$var = date('Y-m',$var).'%'; // admin area - yyyy-mm%
	}
	elseif($type=='admin-sql-check-year') {
		$var = date('Y',$var).'%'; // admin area - yyyy%
	}
	elseif($type=='rfc') {
		$var = date('r',$var); // rfc - Thu, 21 Dec 2000 16:01:07 +0200
	}
	elseif($type=='iso8601') {
		$var = date('Y-m-d',$var); // yyyy-mm-dd
	}
	elseif($type=='iso') { // atom feed date format. 'c' in PHP5
		$var_int = $var;
		$var = date('Y-m-d\TH:i:s', $var_int);
		$pre_timezone = date('O', $var_int);
		$time_zone = substr($pre_timezone, 0, 3).":".substr($pre_timezone, 3, 2);
		$var .= $time_zone;
	}
	elseif($type=='sql') {
		$var = date('Y-m-d H:i:s',$var); // format for database entry
	}
	return $var;
}

function convert_time_to_am_pm($time) {
	return date('g:ia',strtotime($time));
}


/* TRUNCATE STRING -- shorten a string at a certain length
-------------------------------------------------------------------------------------------------- */
function truncateString($str, $len, $el='...') {
	if (strlen($str) > $len) {
		$xl = strlen($el);
		if ($len < $xl) {
			return substr($str, 0, $len);
		}
		$str = substr($str, 0, $len-$xl);
		$spc = strrpos($str, ' ');

		if ($spc > 0) {
			$str = substr($str, 0, $spc);
		}
		return $str.$el;
	}
	return $str;
}


/* WORD COUNT -- ... ;)
-------------------------------------------------------------------------------------------------- */
function wordCount($var) {
	return substr_count($var,' ') + 1;
}
?>
