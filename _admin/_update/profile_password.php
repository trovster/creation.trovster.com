<?php
if(!empty($_POST['method']) && $_POST['method']=='profile-password') {
	$stripped_post_array = stripTags($_POST,'',true);
	if($_POST['action']=='new') {
	
	}
	elseif($_POST['action']=='update' && !empty($_POST['identifier']) && is_numeric($_POST['identifier'])) {
		$update_sql = "UPDATE author_details
SET Password = '".md5($_POST['password-required'])."'
WHERE ID = '".mysql_real_escape_string($stripped_post_array['identifier'])."'";
		if(mysqli_query($connect_admin, $update_sql)) {
			$updated_array = array(
				'method' => 'success',
				'heading' => 'Cool!',
				'description' => 'Your password has been successfully updated.',
				'class' => array('updated')
			);
		}
		else {
			$updated_array = array(
				'method' => 'error',
				'heading' => 'Database Error',
				'description' => 'There was a problem with the database insert.',
				'class' => array('database'),
				'sql' => $update_sql
			);
		}
	}
}
?>