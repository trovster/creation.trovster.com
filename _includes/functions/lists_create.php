<?php
/* FORMAT LIST INFORMATION -- adding the information to <li> or <dt>
-------------------------------------------------------------------------------------------------- */
function formatListItem($array,$type,$i,$special='',$gilder='') {
	$itemArray = $array[$i];
	
	if(isset($itemArray['class']) && !is_array($itemArray['class'])) $itemArray['class'] = array($itemArray['class']);
	
	if($i==0) {
		$itemArray['class'][] = 'fli';
	}
	if($i==(count($array)-1)) {
		$itemArray['class'][] = 'lli';
	}
	if(!isEven($i)) $itemArray['class'][] = 'even';
	
	$extraneous_span = '';
	if((!empty($gilder) && $gilder==true) || (!empty($itemArray['gilder']) && $itemArray['gilder']==true)) {
		$extraneous_span = '<span class="gl-ir"></span>';
		//$itemArray['class'][] = 'gl-ir';
	}
	
	$anchor_class = array();
	if(!empty($itemArray['anchor-class'])) {
		if(!is_array($itemArray['anchor-class'])) $itemArray['anchor-class'] = array($itemArray['anchor-class']);
		$anchor_class = array_merge($itemArray['anchor-class'],$anchor_class);
	}
	if($special=='hfeed') {
		// add class 'hentry' to the li
		// add class 'entry-title' to the anchor 
		// add rel permalink to the anchor
		$anchor_class[] = 'entry-title';
		$itemArray['class'][] = 'hentry';
		
		if(empty($itemArray['rel'])) $itemArray['rel'] = array();
		if(!empty($itemArray['rel']) && !is_array($itemArray['rel'])) $itemArray['rel'] = array($itemArray['rel']);
		if(!in_array('permalink',$itemArray['rel'])) {
			$itemArray['rel'][] = 'permalink';
		}
	}
	
	if(strtolower($type)=='dt' && !empty($itemArray['class'])) {
		if(!is_array($itemArray['class'])) $itemArray['class'] = array($itemArray['class']);
		for($i=0; $i<count($itemArray['class']); $i++) {
			if(strtolower($itemArray['class'][$i])=='role') {
				$itemArray['class'][] = 'vcard_'.$itemArray['class'][$i];
				unset($itemArray['class'][$i]);
			}
		}
	}
	
	$text = $itemArray['text'];
	if(!preg_match('/<[^<^>]+>/',$text)) $text = formatText($text); // if the text DOESN'T contain HTML, format it.
	
	$sub = '';
	if(!empty($itemArray['sub']) && is_array($itemArray['sub'])) $sub = "\n".createList($itemArray['sub'],'','sub');
	
	if(!empty($itemArray['link'])) {
		$list = '<'.$type.addAttributes('',@$itemArray['id'],@$itemArray['class']).'><a href="'.$itemArray['link'].'"'.addAttributes(@$itemArray['title'],'',$anchor_class,@$itemArray['xfn'],@$itemArray['rel'],@$itemArray['accesskey'],@$itemArray['tabindex'],'','',@$itemArray['mime']).'>'.$extraneous_span.$text.'</a>'.$sub.'</'.$type.'>';
	}
	else {
		$list = '<'.$type.addAttributes(@$itemArray['title'],@$itemArray['id'],@$itemArray['class']).'>'.$extraneous_span.$text.$sub.'</'.$type.'>';
	}
	
	return '  '.$list."\n";
}


/* FORMAT LIST INFORMATION -- adding the information to <li> or <dt>
-------------------------------------------------------------------------------------------------- */
function formatDefItem($array,$i) {
	$itemArray = $array[$i];
	
	if(!is_array($itemArray['definition'])) $itemArray['definition'] = array($itemArray['definition']);
	
	if(!empty($itemArray['class'])) {
		if(!is_array($itemArray['class'])) $itemArray['class'] = array($itemArray['class']);
	}
	else $itemArray['class'] = array();
	
	if($i==0) {
		$itemArray['class'][] = 'fli';
	}
	if($i==(count($array)-1)) {
		$itemArray['class'][] = 'lli';
	}
	if(!isEven($i)) $itemArray['class'][] = 'even';
	
	$definition = '';
	for($d=0; $d<count($itemArray['definition']); $d++) {
		$text = $itemArray['definition'][$d];
		if(empty($text)) continue;
		if(!preg_match('/<[^<^>]+>/',$text)) $text = formatText($text); // if the text DOESN'T contain HTML, format it.
		$definition .= "  ".'<dd'.addAttributes('','',@$itemArray['class']).'>'.$text.'</dd>'."\n";
	}
	
	return $definition;
}


/* CREATE LIST -- does exactly what it says on the tin!
-------------------------------------------------------------------------------------------------- */
function createList($array,$id='',$class='',$type='ul',$gilder='') {
	if(!is_array($array)) return false;
	
	$type = strtolower($type);
	if($type!='ul' && $type!='ol') $type = 'ul'; // defaults to <ul>
	
	$special = '';
	if(!is_array($class)) $class = array($class);
	if(in_array('hfeed',$class)) {
		$special = 'hfeed';
		$special_key = array_search($special, $class);
		unset($class[$special_key]);
	}
	
	$list = '';
	$array = array_values($array);
	for($i=0; $i<count($array); $i++) {
		$list .= formatListItem($array,'li',$i,$special,$gilder);
	}	
	
	return '<'.$type.addAttributes('',$id,$class).'>'."\n".$list.'</'.$type.'>'."\n";
}

/* CREATE DEFINITION LIST -- does exactly what it says on the tin!
-------------------------------------------------------------------------------------------------- */
function createDefinitionList($array,$id='',$class='') {
	if(!is_array($array)) return false;
	
	$list = '';
	for($i=0; $i<count($array); $i++) {
		$list .= formatListItem($array,'dt',$i);
		$list .= formatDefItem($array,$i);
	}
	
	return '<dl'.addAttributes('',$id,$class).'>'."\n".$list.'</dl>'."\n";
}

/* CREATE A TABLE -- does exactly what it says on the tin!
-------------------------------------------------------------------------------------------------- */
function createTable($array,$summary,$caption,$id='',$class='') {
	if(!is_array($array) || empty($summary) || empty($caption)) return false;
	
	$thead_text = '';
	$tbody_text = '';

	$header_total = count($array['header']);
	foreach($array['header'] as $key => $header) {
		if(!is_array($header)) $header = array('text' => $header);
		$thead_text .= '<th scope="col"'.addAttributes(@$header['title'],$header['id'],@$header['class']).'>'.formatText($header['text']).'</th>'."\n";
	}

	$i=0;
	foreach($array['rows'] as $key => $row_array) {
		$tbody_row = '';
		if(empty($row_array['class'])) $row_array['class'] = array();
		elseif(!is_array($row_array['class'])) $row_array['class'] = array($row_array['class']);
		if(!isEven($i)) $row_array['class'][] = 'odd';

		if(count($row_array['value'])!=$header_total) continue; // if the number of rows don't match header rows...
		foreach($row_array['value'] as $key => $row) {
			if(!is_array($row)) $row = array('text' => $row);
			$tbody_row .= '<td headers="'.$array['header'][$key]['id'].'"'.addAttributes('',@$row['id'],@$row['class']).'>'.$row['text'].'</td>'."\n";
		}
		$tbody_text .= '<tr'.addAttributes('',@$row_array['id'],@$row_array['class']).'>'."\n".$tbody_row.'</tr>'."\n";
		$i++;
	}
	if(empty($tbody_text)) return false;
	
	$table = '<table summary="'.formatText($summary).'"'.addAttributes('',$id,$class).'>
		<caption>'.formatText($caption).'</caption>
		<thead>'."\n".'<tr>'."\n".$thead_text.'</tr>'."\n".'</thead>
		<tbody>'."\n".$tbody_text.'</tbody>
	</table>'."\n";
	
	return $table;
}

/* PAGINATION
-------------------------------------------------------------------------------------------------- */
function pagination_setup($array,$next,$prev,$total) {
	if(empty($array) || !is_array($array) || empty($total)) return false;
	
	$text = ' Image';
	$text_next = 'Next'.$text;
	$text_prev = 'Previous'.$text;
	$text_first = 'First'.$text;
	$text_last = 'Last'.$text;
	
	$return = array();
	if(($next-1)!=0) {
		$return['first'] = array('text' => $text_first, 'link' => $array[0]['link'], 'title' => $array[0]['title'], 'class' => array('first'), 'rel' => array('first'));
	}
	if(isset($array[$next])) {
		$return['next'] = array('text' => $text_next, 'link' => $array[$next]['link'], 'title' => $array[$next]['title'], 'class' => array('next'), 'rel' => array('next'), 'accesskey' => 'x');
	}
	if(isset($array[$prev])) {
		$return['prev'] = array('text' => $text_prev, 'link' => $array[$prev]['link'], 'title' => $array[$prev]['title'], 'class' => array('prev'), 'rel' => array('prev'), 'accesskey' => 'z');
	}
	if($total>1 && ($prev+1)!=($total-1)) {
		// add last
		$return['last'] = array('text' => $text_last, 'link' => $array[$total-1]['link'], 'title' => $array[$total-1]['title'], 'class' => array('last'), 'rel' => array('last'));
	}
	return $return;
}
function pagination_display($array,$id='',$class='') {
	if(empty($array) || !is_array($array)) return false;
	if(empty($class)) $class = array();
	elseif(!is_array($class)) $class = array($class);
	$class[] = 'pagination';
	
	return createList($array,$id,$class);
}
?>