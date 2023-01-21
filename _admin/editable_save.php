<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* basic setup
============================================================================================================= */
$return_data = '';
if(!empty($_POST['id']) && !empty($_POST['type']) && !empty($_POST['area']) && is_numeric($_POST['id'])) {
	$edit_id = $_POST['id'];

	if($_POST['area']=='entry-content') $db_field = 'Description';
	elseif($_POST['area']=='entry-summary') $db_field = 'Summary';
	elseif($_POST['area']=='entry-title')  $db_field = 'Title';

	if($_POST['type']=='ht') {
		// this is a HOT TOPIC
		$sql_update = "UPDATE author_topics SET ".$db_field." = '".mysqli_real_escape_string($connect_admin, $_POST['value'])."',
						Updated = '".mysqli_real_escape_string($connect_admin, $now_timestamp)."',
						UpdatedID = '".mysqli_real_escape_string($connect_admin, $user_id)."'
						WHERE ID = '".mysqli_real_escape_string($connect_admin, $edit_id)."'";
		mysqli_query($connect_admin, $sql_update);
		$specific_array = hot_topic_setup(" AND d.ID = '".mysqli_real_escape_string($connect_admin, $edit_id)."'",' LIMIT 0,1');
	}
	elseif($_POST['type']=='e') {
		// this is a NEWS ARTICLE
		$sql_update = "UPDATE news_details SET ".$db_field." = '".mysqli_real_escape_string($connect_admin, $_POST['value'])."',
						Updated = '".mysqli_real_escape_string($connect_admin, $now_timestamp)."',
						UpdatedID = '".mysqli_real_escape_string($connect_admin, $user_id)."'
						WHERE ID = '".mysqli_real_escape_string($connect_admin, $edit_id)."'";
		mysqli_query($connect_admin, $sql_update);
		$specific_array = related_setup(" AND d.ID = '".mysqli_real_escape_string($connect_admin, $edit_id)."'",' LIMIT 0,1');
	}

	if(!empty($specific_array) && count($specific_array) === 1) {
		if($_POST['area']=='entry-content') {
			//$return['description']['markdown']
			//$return['description']['main']
			$return_data = $specific_array[0]['description']['main'];
		}
		elseif($_POST['area']=='entry-summary') {
			//$return['description']['summary-markdown']
			//$return['description']['summary-main']
			$return_data = $specific_array[0]['description']['summary-main'];
		}
		elseif($_POST['area']=='entry-title') {
			//$return['title-plain'] = $Detail_Title;
			//$return['title'] = formatText($Detail_Title);
			$return_data = $specific_array[0]['title'];
		}
	}
}
echo $return_data;
?>
