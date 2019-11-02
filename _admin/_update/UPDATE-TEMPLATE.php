<?php
if($_POST['method']=='METHOD') {
	
	if($_POST['action']=='add') {
		
		$insert_sql = "INSERT INTO ";
		
		if(mysqli_query($connect_admin, $insert_sql)) {
			$updated_array = array(
				'method' => 'success',
				'heading' => 'Victory!',
				'description' => 'Your METHOD was added  Go [check it out](#).',
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
	}
	elseif($_POST['action']=='update' && !empty($_POST['identifier']) && is_numeric($_POST['identifier']) && !empty($_GET['update']) && is_numeric($_GET['update'])) {
	
		if($_POST['identifier']==$_GET['update']) {
			
			$update_sql = "UPDATE
						   SET
						   WHERE ID = ''";
			
			if(mysqli_query($connect_admin, $update_sql)) {
				$updated_array = array(
					'method' => 'success',
					'heading' => 'Victory!',
					'description' => 'Your METHOD has been successfully updated  Go [check it out](#).',
					'class' => array('updated')
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
		}
		else $updated_array = array(
			'method' => 'error',
			'heading' => 'Error',
			'description' => 'The identifier doesn\'t match the URL.',
			'class' => array('tampered')
		);	
	}
}
?>