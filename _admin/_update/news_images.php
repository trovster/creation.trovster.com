<?php
if(!empty($entry_id) && is_numeric($entry_id) && $entry_id!=0) {
	$updated_array = array($updated_array);
	foreach($_POST as $key => $post_data) {
		$image_id = 0; $image_file_rename_type = false;
		$file_upload_array = array();

		if(preg_match('#image-([0-9]{1})-alt#',$key,$file_upload_array)) {
			//$file_upload_array[1] -> ID

			if(!empty($_POST['image-'.$file_upload_array[1].'-alt'])) {
				if(!empty($_POST['image-'.$file_upload_array[1].'-identifier'])) {
					$image_id = $_POST['image-'.$file_upload_array[1].'-identifier'];
					$image_update_sql = "UPDATE news_details_images SET
											Image_Alt_Text = '".mysqli_real_escape_string($connect_admin, $_POST['image-'.$file_upload_array[1].'-alt'])."',
											Safe_URL = '".mysqli_real_escape_string($connect_admin, url_encode($_POST['image-'.$file_upload_array[1].'-alt']))."/',
											Position = '".mysqli_real_escape_string($connect_admin, $_POST['image-'.$file_upload_array[1].'-position-select'])."'
											WHERE ID = '".mysqli_real_escape_string($connect_admin, $image_id)."'";
					mysqli_query($connect_admin, $image_update_sql);
				}
				else {
					$image_insert_sql = "INSERT INTO news_details_images (CreatedID,UpdatedID,Image_Alt_Text,Safe_URL,Position)
											VALUES(
												'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
												'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
												'".mysqli_real_escape_string($connect_admin, $_POST['image-'.$file_upload_array[1].'-alt'])."',
												'".mysqli_real_escape_string($connect_admin, url_encode($_POST['image-'.$file_upload_array[1].'-alt']))."/',
												'".mysqli_real_escape_string($connect_admin, $_POST['image-'.$file_upload_array[1].'-position-select'])."'
											)";
					mysqli_query($connect_admin, $image_insert_sql);
					$image_id = mysql_insert_id();

					// also join the image ($image_id) to the entry ($entry_id)
					$image_join_sql = "INSERT INTO news_details_images_join (news_details_images_ID,news_details_ID)
										VALUES('".mysqli_real_escape_string($connect_admin, $image_id)."','".mysqli_real_escape_string($connect_admin, $entry_id)."')";
					mysqli_query($connect_admin, $image_join_sql);
				}
			}


			if(!empty($image_id) && is_numeric($image_id) && $image_id!=0) {

				foreach($image_size_array as $image_size => $image_size_value_array) {
					$image_file_name = url_encode($_POST['image-'.$file_upload_array[1].'-alt']); // [4]_email-on-computer-screen.jpg
					if($image_size=='small') {
						$image_file_name .= '_small'; // [4]_email-on-computer-screen_small.jpg
						$image_file_rename_type = 'small';
					}

					if(!empty($_FILES['image-'.$file_upload_array[1].'-'.$image_size])) {
						$file_info_array = array();
						$file_info_array = pathinfo($_FILES['image-'.$file_upload_array[1].'-'.$image_size]['name']);

						if(!empty($file_info_array) && !empty($file_info_array['extension']) && in_array(strtolower($file_info_array['extension']),$image_accept_array)) {
							$image_ext = strtolower($file_info_array['extension']);
							if($image_size=='large') {
								$image_extension_sql = "UPDATE news_details_images
														SET Extension = '".mysqli_real_escape_string($connect_admin, $image_ext)."'
														WHERE ID = '".mysqli_real_escape_string($connect_admin, $image_id)."'";
								mysqli_query($connect_admin, $image_extension_sql);
							}

							// image details
							$image_upload_array = array(
								'id' => $image_id,
								'name' => $image_file_name,
								'extension' => $image_ext,
								'path' => $image_path,
								'upload' => 'image-'.$file_upload_array[1].'-'.$image_size,
								'type' => 'news',
								'size' => array(
									'width' => $image_size_value_array['width'],
									'height' => $image_size_value_array['height'],
									'method' => 'resize'
								),
							);

							// delete existing image, based upon ID number
							//delete_file($image_id,$image_path,$image_file_rename_type);
							//delete_file($image_id,$image_path.'orginials/',$image_file_rename_type);
							//delete_file($image_id,$image_path.'thumbnails/',$image_file_rename_type);

							$image_upload_information = image_upload($image_upload_array);
							$image_upload_information['class'][] = 'feedback-also';
							if(!empty($image_upload_information)) $updated_array[] = $image_upload_information;
						}
					}
					else {
						// rename the large image...
						//rename_file($image_id,$image_file_name,$image_path,$image_file_rename_type);
						//rename_file($image_id,$image_file_name,$image_path.'orginials/',$image_file_rename_type);
						//rename_file($image_id,$image_file_name,$image_path.'thumbnails/',$image_file_rename_type);
					}
				}
			}
		}
	}
}
?>
