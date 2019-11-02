<?php

function news_form($type='new',$array=array()) {
	global $now_timestamp;
	global $updated_array;
	global $person_details_array;
	$return = '';
	$form_id = 'news-article-update';
	
	$type = strtolower($type);
	if($type!='new' && $type!='update') $type = 'new';
	
	if($type=='update') {
		$type_text = 'Update a';
		$submit_text = 'Update';
	}
	else {
		$type_text = ucwords($type);
		$submit_text = 'Add a new';
	}
	
	if($array['section']=='extra') {
		$submit_text .= ' Extra News Article';
		$form_id .= '-extra';
		$total_images = 1;
	}
	else {
		$submit_text .= ' News Article';
		$total_images = 6;
	}
	
	if($type=='new' && empty($array['created']['sql'])) {
		$array['created']['sql'] = $now_timestamp;
	}

	$status_select = active_select_setup('Status…','status-required',@$array['status']);
	$comments_select = active_select_setup('Comments…','comments-status-required',@$array['comments']['active']);
	$form_array = array(
		'text' => array(
			'fieldset' => $submit_text,
			'class' => array('news-details','main-details'),
			'elements' => array(
				'title-required' => array('label' => 'Title', 'type' => 'text', 'name' => 'title-required', 'id' => 'title-required', 'value' => @$array['title']),
				'date-required' => array('label' => 'Date', 'type' => 'text', 'name' => 'date-required', 'id' => 'date-required', 'value' => $array['created']['sql'], 'class' => array('date','optional','auto')),
				'summary-required' => array('label' => 'Summary', 'type' => 'textarea', 'name' => 'summary-required', 'id' => 'summary-required', 'value' => @$array['description']['summary'], 'class' => array('small')),
				'text-required' => array('label' => 'Text', 'type' => 'textarea', 'name' => 'text-required', 'id' => 'text-required', 'value' => @$array['description']['markdown'], 'class' => array('extend','markdown')),
				'status-required' => array('label' => 'Status', 'type' => 'select', 'name' => 'status-required', 'id' => 'status-required', 'value' => $status_select, 'class' => array('status')),
				'comments-status-required' => array('label' => 'Comments Status', 'type' => 'select', 'name' => 'comments-status-required', 'id' => 'comments-status-required', 'value' => $comments_select, 'class' => array('status'))
			)
		),
	);
	
	// loop through the images...
	if($array['section']=='extra') {
		$form_array['text']['elements']['summary-required']['class'][] = 'tiny';
		$form_array['text']['elements']['text-required']['class'] = array('markdown','small');
		unset($form_array['text']['elements']['comments-status-required']);
	}
	
	for($i=0; $i<$total_images; $i++) {
		$n = $i+1;
		$fieldset_key = 'image-'.$n;
		$fieldset_text = 'Image Details';
		$settings = array('class' => array('images'), 'id' => $fieldset_key, 'elements' => array());
		$image_array = @$array['images'][$i];
		if($total_images>1) {
			$settings['class'][] = 'multiple';
			$settings['position'] = array('total' => $total_images, 'current' => $n);
			$settings['elements'][] = 'news';
			$fieldset_text .= ' - '.ucwords(int_to_words($n));
		}
		if($array['section']=='extra') {
			$settings['elements'][] = 'extra';
			$image_array = @$array['image'];
		}
		else {
			$settings['elements'][] = 'alt';
		}
		if(!empty($array['images'][$i])) {
			$array['images'][$i]['large']['text']['alt'] = $array['images'][$i]['large']['text']['title'];
			unset($array['images'][$i]['large']['text']['title']);
			unset($array['images'][$i]['small']['permalink']);
			unset($array['images'][$i]['large']['permalink']);
			//echo '<pre>'; print_r($array['images'][$i]); echo '</pre><br />';
		}
		$form_image_array = image_fieldset_setup($fieldset_key,$fieldset_text,$settings,$image_array);
		$form_array = array_merge($form_array,$form_image_array);
	}
	
	$form_array['submit'] = array(
		'fieldset' => 'Complete the form',
		'elements' => array(
			array('type' => 'hidden', 'name' => 'method', 'value' => 'news'),
			array('type' => 'hidden', 'name' => 'action', 'value' => $type),
			array('type' => 'hidden', 'name' => 'person', 'value' => $array['person-id']),
			array('type' => 'hidden', 'name' => 'section-id', 'value' => $array['section-id']),
			array('type' => 'hidden', 'name' => 'edit-link', 'value' => $array['edit-link']),
			array('type' => 'submit', 'name' => 'submit', 'value' => $submit_text, 'class' => array($type))
		)
	);
	
	if($array['section']=='extra') {
		$form_array['submit']['elements']['comments-status-required'] = array('type' => 'hidden', 'name' => 'comments-status-required', 'value' => '1');
	}
	
	if($type=='update' && !empty($array['id'])) {
		$form_array['submit']['elements'][] = array('type' => 'hidden', 'name' => 'identifier', 'value' => $array['id']);
	}
	
	$show_form = true;
	if(!empty($_POST['method']) && strtolower($_POST['method'])=='news') {
		$form_errors = checkRequired($_POST);
		$form_errors = array_merge($form_errors,check_form_required_elements($form_array));
		if(!empty($form_errors)) $form_errors = cleanArray($form_errors);
		$stripped_post_array = stripTags($_POST,'',true);
		
		$position_check_array = array();
		foreach($_POST as $key => $value) {
			if(is_numeric($value) && strstr($key,'position-select')!==false) {
				if(!in_array($value,$position_check_array)) $position_check_array[] = $value;
				else $form_errors[$key] = 'Duplicate position';
			}
		}
		
		if(empty($form_errors) || (!empty($_REQUEST['save']) && $_REQUEST['save']=='true' && !empty($_REQUEST['language_response']))) {
			$show_form = false;
			require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/news.php');
			$return .= feedback_show($updated_array,'',array('column','double'));
		}
		else $form_array = createFormErrors($form_array,$form_errors);
	}
	if($show_form==true) {
		$return .= createForm($form_array,'post',$_SERVER['REQUEST_URI'],$form_id,array('column','double','save'),'upload');
		
		if($type=='update') {
			$return .= '<div class="column last">';
				/*
				if(!empty($array['image']) && !empty($array['image']['file'])) {
					$return .= '<h4>Current Image</h4>'."\n";
					if(!empty($array['image']['link'])) unset($array['image']['link']);
					$return .= image_show($array['image']);
				}
				*/
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


function news_form_comments($type='new',$array=array()) {
	global $now_timestamp;
	global $updated_array;
	global $person_details_array;
	$return = '';
	$form_id = 'news-comment-update';
	
	$type = strtolower($type);
	if($type!='new' && $type!='update') $type = 'new';
	
	if($type=='update') {
		$type_text = 'Update a';
		$submit_text = 'Update';
	}
	else {
		$type_text = ucwords($type);
		$submit_text = 'Add a new';
	}
	if($type=='new' && empty($array['created']['sql'])) {
		$array['created']['sql'] = $now_timestamp;
	}
	
	$status_select = active_select_setup('Status…','status-required',@$array['status']);
	$form_array = array(
		'text' => array(
			'fieldset' => $submit_text,
			'class' => array('news-details','main-details'),
			'elements' => array(
				'name-required' => array('label' => 'Name', 'type' => 'text', 'name' => 'name-required', 'id' => 'name-required', 'value' => @$array['author']['plain']),
				'email-required' => array('label' => 'Email Address', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required', 'value' => @$array['author']['email']),
				'website' => array('label' => 'Website', 'type' => 'text', 'name' => 'website', 'id' => 'website', 'value' => @$array['author']['url']),
				'date-required' => array('label' => 'Date', 'type' => 'text', 'name' => 'date-required', 'id' => 'date-required', 'value' => $array['created']['sql'], 'class' => array('date','optional','auto')),
				'comment-required' => array('label' => 'Comment', 'type' => 'textarea', 'name' => 'comment-required', 'id' => 'comment-required', 'value' => @$array['description']['markdown'], 'class' => array('extend','markdown')),
				'status-required' => array('label' => 'Status', 'type' => 'select', 'name' => 'status-required', 'id' => 'status-required', 'value' => $status_select, 'class' => array('status')),
			)
		),
	);
	$form_array['submit'] = array(
		'fieldset' => 'Complete the form',
		'elements' => array(
			array('type' => 'hidden', 'name' => 'method', 'value' => 'news-comments'),
			array('type' => 'hidden', 'name' => 'action', 'value' => $type),
			array('type' => 'hidden', 'name' => 'person', 'value' => $array['person-id']),
			array('type' => 'hidden', 'name' => 'section-id', 'value' => $array['section-id']),
			array('type' => 'hidden', 'name' => 'edit-link', 'value' => $array['edit-link']),
			array('type' => 'submit', 'name' => 'submit', 'value' => $submit_text, 'class' => array($type))
		)
	);	
	if($type=='update' && !empty($array['id'])) {
		$form_array['submit']['elements'][] = array('type' => 'hidden', 'name' => 'identifier', 'value' => $array['id']);
	}
	
	$show_form = true;
	if(!empty($_POST['method']) && strtolower($_POST['method'])=='news') {
		$form_errors = checkRequired($_POST);
		$form_errors = array_merge($form_errors,check_form_required_elements($form_array));
		if(!empty($form_errors)) $form_errors = cleanArray($form_errors);
		$stripped_post_array = stripTags($_POST,'',true);
		
		$position_check_array = array();
		foreach($_POST as $key => $value) {
			if(is_numeric($value) && strstr($key,'position-select')!==false) {
				if(!in_array($value,$position_check_array)) $position_check_array[] = $value;
				else $form_errors[$key] = 'Duplicate position';
			}
		}
		
		if(empty($form_errors)) {
			$show_form = false;
			//require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_update/news_comments.php');
			//$return .= feedback_show($updated_array,'',array('column','double'));
		}
		else $form_array = createFormErrors($form_array,$form_errors);
	}
	if($show_form==true) {
		$return .= createForm($form_array,'post',$_SERVER['REQUEST_URI'],$form_id,array('column','double'));
		if($type=='update') {
			$return .= '<div class="column last">';
			if($array['status']==2 && $array['spam']==1) {
				$return .= '<div class="info-box view-online">'."\n";
					$return .= '<h3>'.$array['permalink']['anchor-not-closed-no-text'].'Permalink</a></h3>';
				$return .= '</div>';
			}
			if($array['spam']==1) {
				$return .= '<div class="info-box is-spam">'."\n";
					$return .= '<h3><a href="'.$array['spam-link'].'">Mark as Spam?</a></h3>';
				$return .= '</div>';
			}
			$return .= '</div>';
		}
	}
	
	return $return;
}
?>