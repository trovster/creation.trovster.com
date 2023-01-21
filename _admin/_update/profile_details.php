<?php
if(!empty($_POST['method']) && $_POST['method']=='profile-details') {
	$stripped_post_array = stripTags($_POST,'',true);

	if($_POST['action']=='new') {
		// insert not being used at the moment.
		/*
		$insert_sql = "INSERT INTO author_profile (CreatedID,UpdatedID,Author_Detail_ID,Created,Updated,Title,Text_Corporate,Text_Personal)
VALUES(
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."',
	NOW(), NOW(),
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['job-role-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['professional-text-required'])."',
	'".mysqli_real_escape_string($connect_admin, $stripped_post_array['personal-text-required'])."'
)";

		if(mysqli_query($connect_admin, $insert_sql)) {
			$updated_array = array(
				'method' => 'success',
				'heading' => 'Cool!',
				'description' => 'Your profile details were added.',
				'class' => array('added')
			);
		}
		else {
			$updated_array = array(
				'method' => 'error',
				'heading' => 'Database Error',
				'description' => 'There was a problem with the database insert.',
				'class' => array('database')
			);
		}
		*/
	}
	elseif($_POST['action']=='update' && !empty($_POST['identifier']) && is_numeric($_POST['identifier']) && !empty($_REQUEST['update']) && is_numeric($_REQUEST['update'])) {

		if($_POST['identifier']==$_REQUEST['update']) {

			$update_sql = "UPDATE author_profile
SET UpdatedID = '".mysqli_real_escape_string($connect_admin, $person_details_array['identifier'])."',
Updated = NOW(),
Title = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['job-role-required'])."',
Text_Corporate = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['professional-text-required'])."',
Text_Personal = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['personal-text-required'])."'
WHERE Author_Detail_ID = '".mysqli_real_escape_string($connect_admin, $stripped_post_array['identifier'])."'";

			if(mysqli_query($connect_admin, $update_sql)) {
				global $edit_details_array;
				$updated_array = array(
					'method' => 'success',
					'heading' => 'Cool!',
					'description' => 'Your profile details have been successfully updated. Make further [changes](/admin/profile/'.$stripped_post_array['identifier'].'/).',
					'class' => array('updated')
				);
				if(!empty($edit_details_array)) {
					$updated_array['description'] .= ' And you can [check out your updates]('.$edit_details_array['permalink']['safe'].').';
				}
			}
			else {
				$updated_array = array(
					'method' => 'error',
					'heading' => 'Database Error',
					'description' => 'There was a problem with the database insert.',
					'class' => array('database')
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
}
?>
