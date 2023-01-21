<?php
if(!empty($_POST['method']) && $_POST['method']=='news') {

	global $image_accept_array;
	$stripped_post_array = stripTags($_POST,'',true);
	$image_path = '/images/news/';
	$image_size_array = array('small' => array('height' => 108, 'width' => 168), 'large' => array('height' => 356, 'width' => 352));

	$status = $stripped_post_array['status-required']-1;
	$comment_status = $stripped_post_array['comments-status-required']-1;

	if($_POST['action']=='new') {

		$insert_sql = "INSERT INTO news_details (CreatedID,UpdatedID,Created,Updated,
Title,Safe_URL,Summary,Description,Comments,Active,News_Section_ID)
VALUES(
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['title-required'])."',
	'".mysqli_real_escape_string($connect_admin, url_encode($stripped_post_array['title-required']))."/',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['summary-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['text-required'])."',
	'".mysqli_real_escape_string($connect_admin, $comment_status)."',
	'".mysqli_real_escape_string($connect_admin, $status)."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['section-id'])."'
)";

		if(mysqli_query($connect_admin, $insert_sql)) {
			$checkout = '';
			$entry_id 			= mysql_insert_id();
			$entry_edit_link 	= $stripped_post_array['edit-link'].$entry_id.'/';
			//$hot_topics_specific_array = hot_topic_setup("AND d.ID = '".$entry_id."'");
			//if(!empty($hot_topics_specific_array[0]['permalink']) && !empty($hot_topics_specific_array[0]['permalink']['link'])) {
				//$checkout = ' [check it out](#) or';
			//}
			$updated_array = array(
				'method' => 'success',
				'heading' => 'Victory!',
				'description' => 'Your **news article was added**. You can'.$checkout.' make further [changes]('.$entry_edit_link.').',
				'class' => array('added')
			);
		}
		else {
			$updated_array = array(
				'method' => 'error',
				'heading' => 'Database Error',
				'description' => 'There was a problem with the database insert.',
				'class' => array('database'),
				'sql' => $insert_sql
			);
		}
	}
	elseif($_POST['action']=='update' && !empty($_POST['identifier']) && is_numeric($_POST['identifier']) && !empty($_REQUEST['update']) && is_numeric($_REQUEST['update'])) {

		if($_POST['identifier']==$_REQUEST['update']) {

			$update_sql = "UPDATE news_details
						   	SET UpdatedID = '".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
							Created = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['date-required'])."',
							Updated = NOW(),
							Title = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['title-required'])."',
							Safe_URL = '".mysqli_real_escape_string($connect_admin, url_encode($stripped_post_array['title-required']))."/',
							Summary = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['summary-required'])."',
							Description = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['text-required'])."',
							Comments = '".mysqli_real_escape_string($connect_admin, $comment_status)."',
							Active = '".mysqli_real_escape_string($connect_admin, $status)."'
						   	WHERE ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."'";

			if(mysqli_query($connect_admin, $update_sql)) {

				$checkout = '';
				$entry_id 			= $stripped_post_array['identifier'];
				$entry_edit_link 	= $stripped_post_array['edit-link'];
				//$hot_topics_specific_array = hot_topic_setup("AND d.ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."'");
				//if(!empty($hot_topics_specific_array[0]['permalink']) && !empty($hot_topics_specific_array[0]['permalink']['link'])) {
					//$checkout = ' [check it out]('.$hot_topics_specific_array[0]['permalink']['link'].') or';
				//}

				$updated_array = array(
					'method' => 'success',
					'heading' => 'Victory!',
					'description' => 'Your **news article has been successfully updated**. You can'.$checkout.' make further [changes]('.$entry_edit_link.').',
					'class' => array('updated')
				);
			}
			else {
				$updated_array = array(
					'method' => 'error',
					'heading' => 'Database Error',
					'description' => 'There was a problem with the database insert.',
					'class' => array('database'),
					'sql' => $insert_sql
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

		if($stripped_post_array['section-id']==1) {
			// main news article, with multiple images
			require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/news_images.php');
		}
		elseif($stripped_post_array['section-id']==3) {
			// this is an EXTRA news article, one small image.
			// image-1
			$image_path = '/images/extra/';
			if(!empty($_FILES['image-1']) && !empty($_FILES['image-1']['name'])) {
				$file_info_array = pathinfo($_FILES['image-1']['name']);
				if(in_array(strtolower($file_info_array['extension']),$image_accept_array)) {
					$image_ext = strtolower($file_info_array['extension']);
					$image_upload_array = array(
						'id' => $entry_id,
						'name' => url_encode($stripped_post_array['title-required']),
						'extension' => $image_ext,
						'path' => $image_path,
						'upload' => 'image-1',
						'type' => 'extra',
						'size' => array(
							'width' => 56
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
		}

		if($status==1) {
			// status is active so...
			// setup the RSS feed for hot-topics AND this persons hot-topic...
			$author_array = setup_feed_author_information($stripped_post_array['person']);
			setup_feed($author_array,'author');
			setup_feed(setup_feed_information());
		}
	}
}
?>
