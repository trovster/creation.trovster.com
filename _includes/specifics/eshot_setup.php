<?php
function eshot_sql($sql_extra='',$sql_limit='') {
	global $connect_admin;
	$eshot_sql = "SELECT
				 es.ID AS EShot_ID,
				 es.Created AS EShot_Created,
				 es.Updated AS EShot_Updated,
				 es.Sent_Date AS EShot_Sent_Date,
				 es.Title AS EShot_Title,
				 es.Safe_URL AS EShot_Safe_URL,
				 es.Text_Plain AS EShot_Text_Plain,
				 es.Text_Markdown AS EShot_Text_Markdown,
				 es.Text_Summary AS EShot_Text_Summary
				 FROM eshot_details AS es
				 WHERE es.Active = '1'
				 ".$sql_extra."
				 GROUP BY es.ID
				 ORDER BY es.Created DESC, es.Title ASC, es.Updated DESC
				 ".$sql_limit;

	$eshot_query = mysqli_query($connect_admin, $eshot_sql);
	$return = array(); $i=0;
	while($eshot_array = mysqli_fetch_array($eshot_query)) {
		$return[$i] = eshot_setup($eshot_array);
		$i++;
	}
	return $return;
}
function eshot_setup($array) {
	if(empty($array) || !is_array($array)) return false;
	global $now_timestamp;

	$return = array();
	$return['id'] = 'es_'.$array['EShot_ID'];
	$return['title'] = formatText($array['EShot_Title']);
	$return['permalink'] = news_permalink_setup($array['EShot_ID'],$array['EShot_Sent_Date'],$array['EShot_Safe_URL'],'eshot',$return['title']);
	$return['description']['plain'] = $array['EShot_Text_Plain'];
	$return['description']['summary'] = $array['EShot_Text_Summary'];
	$return['description']['main'] = formatText($array['EShot_Text_Markdown'],'output');
	$return['sent'] = news_date_setup($array['EShot_Sent_Date'],$now_timestamp);
	$return['created'] = news_date_setup($array['EShot_Created'],$now_timestamp);
	$return['updated'] = news_date_setup($array['EShot_Updated'],$array['EShot_Created']);
	$return['class'] = array($return['id']);
	$return['image']['main'] = image_setup($array['EShot_ID'],$array['EShot_Safe_URL'],'jpg','/css/specifics/eshots/images/');
	$return['image']['header'] = image_setup($array['EShot_ID'],rtrim($array['EShot_Safe_URL'],'/').'_title','gif','/css/specifics/eshots/images/',$array['EShot_Title'],'','','');
	$return['image']['thumb'] = image_setup($array['EShot_ID'],rtrim($array['EShot_Safe_URL'],'/').'_thumb','gif','/images/eshots/',$array['EShot_Title'],'','','');

	$return['stylesheet']['internal'] = '';
	$return['stylesheet']['file'][] = array('file' => 'specifics/eshots/defaults.css', 'media' => 'screen');
	$return['stylesheet']['file'][] = array('file' => 'specifics/eshots/['.$array['EShot_ID'].']_'.trim($array['EShot_Safe_URL'],'/').'.css', 'media' => 'screen');

	$return['text'] = $return['title'];
	$return['link'] = $return['permalink']['link'];

	return $return;
}
function eshot_display($array,$id='',$class='') {
	if(empty($array) || !is_array($array)) return false;

	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
		$class[] = 'hentry';
	}
	else $class = array('hentry');
	$id = $array['id'];

	$output = '<div'.addAttributes('',$id,$class).'>'."\n";
	$output .= '<h3 class="entry-title"><span class="gl-ir"></span>'.$array['title'].'</h3>'."\n";
	$output .= '<div class="entry-content">'.$array['description']['main'].'</div>'."\n";
	$output .= '<!-- end of div id #'.$id.' -->'."\n";
	$output .= '</div>'."\n";
	return $output;
}
?>
