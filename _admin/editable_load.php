<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* basic setup
============================================================================================================= */
$return_data = '';
if(!empty($_POST['id']) && !empty($_POST['type']) && !empty($_POST['area']) && is_numeric($_POST['id'])) {
	$edit_id = $_POST['id'];
	if($_POST['type']=='ht') {
		// this is a HOT TOPIC
		$specific_array = hot_topic_setup(" AND d.ID = '".mysql_real_escape_string($edit_id)."'",' LIMIT 0,1');
	}
	elseif($_POST['type']=='e') {
		// this is a NEWS ARTICLE
		$specific_array = related_setup(" AND d.ID = '".mysql_real_escape_string($edit_id)."'",' LIMIT 0,1');
	}
	
	if(!empty($specific_array) && count($specific_array==1)) {
		if($_POST['area']=='entry-content') {
			//$return['description']['markdown']
			//$return['description']['main']
			$return_data = $specific_array[0]['description']['markdown'];
		}
		elseif($_POST['area']=='entry-summary') {
			//$return['description']['summary-markdown']
			//$return['description']['summary-main']
			$return_data = $specific_array[0]['description']['summary-markdown'];
		}
		elseif($_POST['area']=='entry-title') {
			//$return['title-plain'] = $Detail_Title;
			//$return['title'] = formatText($Detail_Title);
			$return_data = $specific_array[0]['title-plain'];
		}
	}
}
echo $return_data;
?>