<?php

function hot_topics_form($type='new',$array=array()) {
	global $now_timestamp;
	global $updated_array;
	global $person_details_array;
	$return = '';
	
	$type = strtolower($type);
	if($type!='new' && $type!='update') $type = 'new';
	
	if($type=='update') {
		$type_text = 'Update a';
		$submit_text = 'Update';
	}
	else {
		$type_text = ucwords($type);
		$submit_text = 'Add a New';
	}
	$submit_text .= ' Hot Topic';
	
	if($type=='new' && empty($array['created']['sql'])) {
		$array['created']['sql'] = $now_timestamp;
	}

	$status_select = active_select_setup('Status…','status-required',@$array['status']);
	$form_array = array(
		'text' => array(
			'fieldset' => $submit_text,
			'class' => array('main-details'),
			'elements' => array(
				'title-required' => array('label' => 'Title', 'type' => 'text', 'name' => 'title-required', 'id' => 'title-required', 'value' => @$array['title']),
				'date-required' => array('label' => 'Date', 'type' => 'text', 'name' => 'date-required', 'id' => 'date-required', 'value' => $array['created']['sql'], 'class' => array('date','optional','auto')),
				'summary-required' => array('label' => 'Summary', 'type' => 'text', 'name' => 'summary-required', 'id' => 'summary-required', 'value' => @$array['description']['summary'], 'class' => array('small')),
				'text-required' => array('label' => 'Text', 'type' => 'textarea', 'name' => 'text-required', 'id' => 'text-required', 'value' => @$array['description']['markdown'], 'class' => array('extend','markdown')),
				'status-required' => array('label' => 'Status', 'type' => 'select', 'name' => 'status-required', 'id' => 'status-required', 'value' => $status_select)
			)
		),
	);
	// image setup
	$settings = array('class' => array('images'),'elements' => array('alt','title','link'));
	$form_image_array = image_fieldset_setup('image','Image Details',$settings,@$array['image']);
	$form_array = array_merge($form_array,$form_image_array);
	
	$form_array['submit'] = array(
		'fieldset' => 'Complete the form',
		'elements' => array(
			array('type' => 'hidden', 'name' => 'method', 'value' => 'hot-topic'),
			array('type' => 'hidden', 'name' => 'action', 'value' => $type),
			array('type' => 'hidden', 'name' => 'person', 'value' => $array['person-id']),
			array('type' => 'hidden', 'name' => 'edit-link', 'value' => $array['edit-link']),
			array('type' => 'submit', 'name' => 'submit', 'value' => $submit_text, 'class' => array($type))
		)
	);
	
	if($type=='update' && !empty($array['id'])) {
		$form_array['submit']['elements'][] = array('type' => 'hidden', 'name' => 'identifier', 'value' => $array['id']);
	}
	
	$show_form = true;
	if(!empty($_POST['method']) && strtolower($_POST['method'])=='hot-topic') {
		$form_errors = checkRequired($_POST);
		if(!empty($form_errors)) $form_errors = cleanArray($form_errors);
		$stripped_post_array = stripTags($_POST,'',true);
				
		if(empty($form_errors) || (!empty($_REQUEST['save']) && $_REQUEST['save']=='true' && !empty($_REQUEST['language_response']))) {
			$show_form = false;
			require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/profile_hot-topic.php');
			$return .= feedback_show($updated_array,'',array('column','double'));
		}
		else $form_array = createFormErrors($form_array,$form_errors);
	}
	if($show_form==true) {
		$return .= createForm($form_array,'post',$_SERVER['REQUEST_URI'],'',array('column','double','save'),'upload');
		
		if($type=='update') {
			$return .= '<div class="column last">';
				if(!empty($array['image']) && !empty($array['image']['file'])) {
					$return .= '<h4>Current Image</h4>'."\n";
					if(!empty($array['image']['link'])) unset($array['image']['link']);
					$return .= image_show($array['image']);
				}
				$return .= '<div class="info-box view-online">'."\n";
					if($array['status']==1) {
						$return .= '<h3>'.$array['permalink']['preview']['anchor-not-closed-no-text'].'Preview</a></h3>';
					}
					else {
						$return .= '<h3>'.$array['permalink']['anchor-not-closed-no-text'].'Permalink</a></h3>';
					}
				$return .= '</div>';
			$return .= '</div>';
		}
	}
	return $return;
}

function profile_text_form($array) {
	global $now_timestamp;
	global $updated_array;
	global $person_details_array;
	$return = '';

	$form_array = array(
		'text' => array(
			'fieldset' => 'Update Profile',
			'elements' => array(
				'full-name-required' => array('label' => 'Full Name', 'type' => 'text', 'name' => 'full-name-required', 'id' => 'full-name-required', 'value' => $array['author']['full-name'], 'class' => array('optional','auto')),
				'email-required' => array('label' => 'Email Address', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required', 'value' => $array['author']['email'], 'class' => array('optional','auto')),
				'job-role-required' => array('label' => 'Title / Job Role', 'type' => 'text', 'name' => 'job-role-required', 'id' => 'job-role-required', 'value' => $array['info']['role']),
				'professional-text-required' => array('label' => 'Professional Text', 'type' => 'textarea', 'name' => 'professional-text-required', 'id' => 'professional-text-required', 'class' => array('extend','markdown'), 'value' => $array['text']['corporate']),
				'personal-text-required' => array('label' => 'Personal Text', 'type' => 'textarea', 'name' => 'personal-text-required', 'id' => 'personal-text-required', 'class' => array('extend','markdown'), 'value' => $array['text']['personal']),
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'method', 'value' => 'profile-details'),
				array('type' => 'hidden', 'name' => 'action', 'value' => 'update'),
				array('type' => 'hidden', 'name' => 'person', 'value' => $array['person-id']),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Update Profile Text', 'class' => array('update'))
			)
		)
	);
	
	if(!empty($array['identifier'])) {
		$form_array['submit']['elements'][] = array('type' => 'hidden', 'name' => 'identifier', 'value' => $array['identifier']);
	}
	
	$show_form = true;
	if(!empty($_POST['method']) && strtolower($_POST['method'])=='profile-details') {
		$form_errors = checkRequired($_POST);
		if(!empty($form_errors)) $form_errors = cleanArray($form_errors);
		$stripped_post_array = stripTags($_POST,'',true);
		
		if(empty($form_errors) || (!empty($_REQUEST['save']) && $_REQUEST['save']=='true' && !empty($_REQUEST['language_response']))) {
			$show_form = false;
			$updated_array = array();
			require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/profile_details.php');
			$return .= feedback_show($updated_array,'',array('column','double'));
		}
		else $form_array = createFormErrors($form_array,$form_errors);
	}
	if($show_form==true) {
		$return .= createForm($form_array,'post',$_SERVER['REQUEST_URI'],'',array('column','double'));
	}
	
	$return .= '<div class="column last">'."\n";
		$array['image']['large']['class'][] = 'center';
		$return .= image_show($array['image']['large']);
		$return .= '<div class="info-box view-online">'."\n";
			$return .= '<h3>'.$array['permalink']['anchor-not-closed-no-text'].'Permalink</a></h3>';
		$return .= '</div>';
	$return .= '</div>';
	
	return $return;
}

function profile_password_form($array) {
	global $now_timestamp;
	global $updated_array;
	global $person_details_array;
	$return = '';

	$form_array = array(
		'text' => array(
			'fieldset' => 'Update Password',
			'elements' => array(
				'password-required' => array('label' => 'Password', 'type' => 'password', 'name' => 'password-required', 'id' => 'password-required', 'value' => '', 'class' => array('password')),
				'password-match-required' => array('label' => 'Confirm Password', 'type' => 'password', 'name' => 'password-match-required', 'id' => 'password-match-required', 'value' => '', 'class' => array('password','confirm')),
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'method', 'value' => 'profile-password'),
				array('type' => 'hidden', 'name' => 'action', 'value' => 'update'),
				array('type' => 'hidden', 'name' => 'person', 'value' => $array['person-id']),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Update Your Password', 'class' => array('update'))
			)
		)
	);
	if(!empty($array['identifier'])) {
		$form_array['submit']['elements'][] = array('type' => 'hidden', 'name' => 'identifier', 'value' => $array['identifier']);
	}
	
	$show_form = true;
	if(!empty($_POST['method']) && strtolower($_POST['method'])=='profile-password') {
		$form_errors = checkRequired($_POST);
		if(!empty($form_errors)) $form_errors = cleanArray($form_errors);
		$stripped_post_array = stripTags($_POST,'',true);
		
		if(!empty($_POST['password-required']) && !empty($_POST['password-match-required'])) {
			if($_POST['password-required']!==$_POST['password-match-required']) {
				// passwords don't match.
				$form_errors['password-required'] = 'Your passwords must match.';
			}
			elseif(strtolower($_POST['password-required'])=='password') {
				$form_errors['password-required'] = 'Please don\'t use \'password\'.';
			}
		}
		
		if(empty($form_errors)) {
			$show_form = false;
			$updated_array = array();
			require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/profile_password.php');
			$return .= feedback_show($updated_array,'',array('column','double'));
		}
		else $form_array = createFormErrors($form_array,$form_errors);
	}
	if($show_form==true) {
		$password_title = 'Change Your Password';
		if($array['identifier']!==$person_details_array['identifier']) {
			$password_title = 'Change '.$person_details_array['author']['full-name'].'\'s Password';
		}
		$return .= '<h4>'.$password_title.'</h4>'."\n";
		$return .= createForm($form_array,'post',$_SERVER['REQUEST_URI'],'',array('column','double'));
	}
	
	return $return;
}

?>