<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

if(!empty($_POST['method']) && $_POST['method']=='hot-topic') {

	global $image_accept_array;
	$image_ext = 0; $entry_id = '';
	$stripped_post_array = stripTags($_POST,'',true);
	$upload_image = false;
	$image_path = '/images/hot-topics/';
	$status = $stripped_post_array['status-required']-1;

	if($_POST['action']=='new') {

		$insert_sql = "INSERT INTO author_topics (CreatedID,UpdatedID,Author_Detail_ID,Created,Updated,
Title,Safe_URL,Summary,Description,Image_Alt_Text,Image_Title,Image_Link,Status)
VALUES(
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['person'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['title-required'])."',
	'".mysqli_real_escape_string($connect_admin, url_encode($stripped_post_array['title-required']))."/',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['summary-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['text-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-alt'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-title'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-link'])."',
	'".mysqli_real_escape_string($connect_admin, $status)."'
)";

		if(mysqli_query($connect_admin, $insert_sql)) {
			$checkout = '';
			$entry_id 			= mysql_insert_id();
			$entry_edit_link 	= $stripped_post_array['edit-link'].$entry_id.'/';

			$hot_topics_specific_array = hot_topic_setup("AND d.ID = '".mysqli_real_escape_string($connect_admin, $entry_id)."'");
			if(!empty($hot_topics_specific_array[0]['permalink']) && !empty($hot_topics_specific_array[0]['permalink']['link'])) {
				$checkout = ' [check it out]('.$hot_topics_specific_array[0]['permalink']['link'].') or';
			}
			$updated_array = array(
				'method' => 'success',
				'heading' => 'Victory!',
				'description' => 'Your **hot topic was added**. You can'.$checkout.' make further [changes]('.$entry_edit_link.').',
				'class' => array('added')
			);

		}
		else {
			$updated_array = array(
				'method' => 'error',
				'heading' => 'Database Error',
				'description' => 'There was a problem with the database insert. Email your webmaster the code below…',
				'class' => array('database'),
				'sql' => $insert_sql,
			);
		}

	}
	elseif($_POST['action']=='update' && !empty($_POST['identifier']) && is_numeric($_POST['identifier']) && !empty($_REQUEST['update']) && is_numeric($_REQUEST['update'])) {

		if($_POST['identifier']==$_REQUEST['update']) {

			$update_sql = "UPDATE author_topics
SET UpdatedID = '".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
Author_Detail_ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['person'])."',
Created = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
Updated = NOW(),
Title = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['title-required'])."',
Safe_URL = '".mysqli_real_escape_string($connect_admin, url_encode($stripped_post_array['title-required']))."/',
Summary = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['summary-required'])."',
Description = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['text-required'])."',
Image_Alt_Text = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-alt'])."',
Image_Title = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-title'])."',
Image_Link = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['image-link'])."',
Status = '".mysqli_real_escape_string($connect_admin, $status)."'
WHERE ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."'";

			if(mysqli_query($connect_admin, $update_sql)) {

				$checkout = '';
				$entry_id 			= $stripped_post_array['identifier'];
				$entry_edit_link 	= $stripped_post_array['edit-link'];

				$hot_topics_specific_array = hot_topic_setup("AND d.ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."'");
				if(!empty($hot_topics_specific_array[0]['permalink']) && !empty($hot_topics_specific_array[0]['permalink']['link'])) {
					$checkout = ' [check it out]('.$hot_topics_specific_array[0]['permalink']['link'].') or';
				}

				$updated_array = array(
					'method' => 'success',
					'heading' => 'Victory!',
					'description' => 'Your **hot topic has been successfully updated**. You can'.$checkout.' make further [changes]('.$entry_edit_link.').',
					'class' => array('updated')
				);
			}
			else {
				$updated_array = array(
					'method' => 'error',
					'heading' => 'Database Error',
					'description' => 'There was a problem with the database insert. Email your webmaster the code below…',
					'class' => array('database'),
					'sql' => $update_sql,
				);
			}
		}
		else $updated_array = array(
			'method' => 'error',
			'heading' => 'Error',
			'description' => 'The identifier doesn\'t match the URL.',
			'class' => array('tampered')
		);
	}

	// XML reposnse check...
	require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/xml_reposonse.php');

	if(!empty($entry_id) && is_numeric($entry_id) && $entry_id!=0) {
		$updated_array = array($updated_array);
		if(!empty($_FILES['image']) && !empty($_FILES['image']['name'])) {
			$file_info_array = pathinfo($_FILES['image']['name']);
			if(in_array(strtolower($file_info_array['extension']),$image_accept_array)) {
				$image_ext = strtolower($file_info_array['extension']);
				$sql_image_update = "UPDATE author_topics
									SET Image_Extension = '".$image_ext."'
									WHERE ID = '".mysqli_real_escape_string($connect_admin, $entry_id)."'";
				mysqli_query($connect_admin, $sql_image_update);

				$image_upload_array = array(
					'id' => $entry_id,
					'name' => url_encode($stripped_post_array['title-required']),
					'extension' => $image_ext,
					'path' => $image_path,
					'upload' => 'image',
					'type' => 'hot-topic',
					'size' => array(
						'width' => 168
					),
				);
				// delete existing image, based upon ID number
				delete_file($entry_id,$image_path);
				delete_file($entry_id,$image_path.'orginials/');
				delete_file($entry_id,$image_path.'thumbnails/');

				$image_upload_information = image_upload($image_upload_array);
				$image_upload_information['class'][] = 'feedback-also';
				$updated_array[] = $image_upload_information;
			}
		}
		else {
			// rename existing image, incase [title] has changed
			rename_file($entry_id,url_encode($stripped_post_array['title-required']),$image_path);
			rename_file($entry_id,url_encode($stripped_post_array['title-required']),$image_path.'orginials/');
			rename_file($entry_id,url_encode($stripped_post_array['title-required']),$image_path.'thumbnails/');
		}


		if($status==1) {
			// status is active so...
			// setup the RSS feed for hot-topics AND this persons hot-topic...
			$author_array = setup_feed_author_information($stripped_post_array['person']);
			setup_feed($author_array,'author');
			setup_feed(setup_feed_information('hot-topics'));
		}
	}
}
?>
