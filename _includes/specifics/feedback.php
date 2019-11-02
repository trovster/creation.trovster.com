<?php

/* element setup
============================================================================================================= */
function form_element_setup($array) {
	$element_setup = array(
		'identifier' => $array['Question_ID'],
		'label' => $array['Question'],
		'name' => 'q-'.$array['Question_ID'],
		'id' => 'q-'.$array['Question_ID'],
		'type' => strtolower($array['Type']),
		'position' => $array['Position'],
		'class' => array('q-'.$array['Question_ID']),
		'value'	=> @$array['Question_Answer']
	);
	if($array['Required']==1) {
		$element_setup['id'] .= '-required';
		$element_setup['name'] .= '-required';
	}
	if($element_setup['type']=='rating') {
		$array['type'] = 'radio';
		$element_setup['type'] = 'radio';
		$element_setup['class'][] = 'rating';
	}
	if($element_setup['type']=='checkbox' || $element_setup['type']=='radio' || $element_setup['type']=='select') {
		$element_setup['id'] .= '-multiple';
		$element_setup['name'] .= '-multiple';
	}
	$element = array_merge($element_setup,form_element_value_setup($element_setup));
	return array($element_setup['id'] => $element);
}

function form_element_value_setup($array) {
	if($array['type']=='text' || $array['type']=='textarea') {
		return array();
	}
	elseif($array['type']=='select') {
		$multiple_array = form_element_multiple_setup($array);
		$multiple_options = createOptions($multiple_array,'Select',$array['id']);
		return array('value' => $multiple_options, 'options' => $multiple_array, 'text' => 'Select', 'multiple-array' => $multiple_array);
	}
	elseif($array['type']=='radio' || $array['type']=='checkbox') {
		$multiple_array = form_element_multiple_setup($array);
		return array($array['type'] => $multiple_array, 'multiple-array' => $multiple_array);
	}
}

// 22/03 - odd fellows hall - opposite then swan


/* checkboxes, radio buttons and selects setup
============================================================================================================= */
function form_element_multiple_setup($input) {
	$sql = "SELECT am.ID, am.Answer, fq_am.ID AS Join_ID
			FROM feedback_answer_multiple AS am
			LEFT JOIN feedback_question_to_answer_multiple AS fq_am ON fq_am.feedback_answer_multiple_ID = am.ID
			WHERE fq_am.feedback_question_ID = '".mysql_real_escape_string($input['identifier'])."'
			ORDER BY fq_am.ID ASC";
	$query = mysqli_query($connect_admin, $sql);
	$multiple = array(); $check_array = array(); $i=0;
	while($array = mysqli_fetch_array($query)) {
		if(!in_array($array['Join_ID'],$check_array)) {
			$multiple[$array['Join_ID']] = array(
				'value' => $array['Join_ID'],
				'text' => $array['Answer']
			);
			if($input['type']=='select') {
				$multiple[$array['Join_ID']]['text'] = $array['Answer'];
			}
			else {
				$multiple[$array['Join_ID']]['text'] = $array['Answer'];
				$multiple[$array['Join_ID']]['label'] = $array['Answer'];
				$multiple[$array['Join_ID']]['id'] = 'r-'.$array['Join_ID'];
			}
			if(!empty($input['value']) && $input['value']==$array['ID']) {
				$multiple[$array['Join_ID']]['selected'] = 'selected';
			}
			$i++;
		}
	}
	//echo '<pre>'; print_r($multiple); echo '</pre>';
	return $multiple;
}


/* send feedback data
============================================================================================================= */
function feedback_setup($sql_extra_array=array(),$extra_array=array()) {
	global $logged_in;
	global $user_id;
	$sql_where = ''; $sql_join = '';
	$person_answer_array = array(); $person_question_id_array = array(); $user_array = array();
	if(!empty($sql_extra_array['id'])) $sql_where .= " AND f.ID = '".$sql_extra_array['id']."'";
	if(!empty($sql_extra_array['url'])) $sql_where .= " AND f.Feedback_Safe = '".$sql_extra_array['url']."'";
	
	if(!empty($extra_array['person']) && $logged_in==true && !empty($user_id) && is_numeric($user_id)) {
		// $extra_array['person']['id']
		// $sql_extra_array['id']
		// echo $extra_array['person']['id'];
		$person_sql = "SELECT f_u.ID AS feedback_user_ID,
						f_u.Forename AS forename, f_u.Surname As surname,
						f_u.Email AS email, f_u.Website AS website,
						f_u.Comments
						FROM feedback_user AS f_u
						WHERE f_u.ID = '".mysql_real_escape_string($extra_array['person']['id'])."'
						LIMIT 0,1";
		$person_query = mysqli_query($connect_admin, $person_sql);
		$user_array = mysqli_fetch_array($person_query);
		
		$person_answer_sql = "SELECT f_a_j.feedback_question_ID AS Question_ID,
						f_a_j.Answer AS Answer_Text
						FROM feedback_answer_join as f_a_j
						WHERE f_a_j.feedback_user_ID = '".mysql_real_escape_string($user_array['feedback_user_ID'])."'
						ORDER BY f_a_j.feedback_question_ID ASC";
		
		$person_multiple_sql = "SELECT fq_am.feedback_answer_multiple_ID AS Answer_ID,
								fq_am.feedback_question_ID AS Question_ID,
								f_a_m.Answer AS Answer_Text
								FROM feedback_answer_multiple as f_a_m
								LEFT JOIN feedback_question_to_answer_multiple AS fq_am ON fq_am.feedback_question_ID = f_a_m.ID
								LEFT JOIN feedback_answer_multiple_join AS f_a_m_i ON f_a_m_i.feedback_question_to_answer_multiple_ID = fq_am.ID
								WHERE f_a_m_i.feedback_user_ID = '".mysql_real_escape_string($user_array['feedback_user_ID'])."'
								ORDER BY fq_am.feedback_question_ID ASC";
		
		
		$person_answer_query = mysqli_query($connect_admin, $person_answer_sql);
		while($pq_array = mysqli_fetch_array($person_answer_query)) {
			$person_answer_array[$pq_array['Question_ID']] = $pq_array['Answer_Text'];
		}
		$person_multiple_query = mysqli_query($connect_admin, $person_multiple_sql);
		while($pq_array = mysqli_fetch_array($person_multiple_query)) {
			//$person_answer_array[$pq_array['Question_ID']] = $pq_array['Answer_Text'];
			//$person_question_id_array[$pq_array['Question_ID']] = $pq_array['Answer_ID'];
			$person_answer_array[$pq_array['Question_ID']] = $pq_array['Answer_ID'];
		}
	}	
	
	$sql = "SELECT f.ID AS Feedback_ID,
			f.Created AS Feedback_Created,
			f.Updated AS Feedback_Updated,
			f.Expires AS Feedback_Expires,
			f.Feedback AS Feedback_Title,
			f.Feedback_Safe AS Feedback_Title_Safe,
			f.Sub_Title AS Feedback_Sub_Title,
			f.Summary AS Feedback_Summary,
			f.Description AS Feedback_Description,
			
			f_q.ID AS Question_ID,
			f_q.Question,
			f_q.Type,
			f_q.Position,
			f_q.Required,
			
			f_f.ID AS Fieldset_ID,
			f_f.Created AS Fieldset_Created,
			f_f.Updated AS Fieldset_Updated,
			f_f.Fieldset AS Fieldset_Title,
			f_f.Fieldset_Safe AS Fieldset_Title_Safe
			
			FROM feedback AS f
			LEFT JOIN feedback_question AS f_q ON f_q.feedback_ID = f.ID
			LEFT JOIN feedback_question_fieldset_join AS f_q_f_j ON f_q_f_j.feedback_question_ID = f_q.ID
			LEFT JOIN feedback_fieldset AS f_f ON f_f.ID = f_q_f_j.feedback_fieldset_ID
			".$sql_join."
			
			WHERE f_q.Active = '1'
			".$sql_where."
			
			ORDER BY f.Created DESC, f_f.Position ASC, f_q.Position ASC, f.Feedback ASC";

	$query = mysqli_query($connect_admin, $sql);
	$feedback_array = array(); $i=-1; $active_i = 0;
	$check_array = array(); $fieldset_check = '';
	$feedback = array();
	while($array = mysqli_fetch_array($query)) {
		if(!in_array($array['Feedback_ID'],$check_array)) {
			$check_array[] = $array['Feedback_ID']; $i++; $q=0; $f=-1;
			$feedback_array[$i] = array(
				'identifier' => $array['Feedback_ID'],
				'created' => $array['Feedback_Created'],
				'updated' => $array['Feedback_Updated'],
				'text' => $array['Feedback_Title'],
				'safe' => $array['Feedback_Title_Safe'],
				'class' => array('feedback-'.$array['Feedback_Title_Safe']),
				'title' => $array['Feedback_Sub_Title'],
				'user' => @$user_array,
				'permalink' => array(
					'array' => array('feedback',$array['Feedback_Title_Safe'])
				),
				'summary' => array(
					'plain' => $array['Feedback_Summary'],
					'html' => formatText($array['Feedback_Summary'],'output')
				),
				'description' => array(
					'plain' => $array['Feedback_Description'],
					'html' => formatText($array['Feedback_Description'],'output')
				),
				'questions' => array()
				//'image' => image_setup($array['Feedback_ID'],$array['Feedback_Title_Safe'],'jpg','/css/images/feedback/','')
			);
			$feedback_array[$i]['permalink']['link'] = url_create($feedback_array[$i]['permalink']['array']);
			$feedback_array[$i]['link'] = $feedback_array[$i]['permalink']['link'];
			if(!empty($_GET['permalink']) && $_GET['permalink']==$feedback_array[$i]['safe']) {
				$active_i = $i;
			}
			$fieldset_check = 'fieldset_check_array_'.$feedback_array[$i]['identifier'];
			${$fieldset_check} = array();
		}
		if(!in_array($array['Fieldset_ID'],${$fieldset_check})) {
			${$fieldset_check}[] = $array['Fieldset_ID']; $f++;
			$feedback_array[$i]['fieldset'][$f] = array(
				'identifier' => $array['Fieldset_ID'],
				'created' => $array['Fieldset_Created'],
				'updated' => $array['Fieldset_Updated'],
				'text' => $array['Fieldset_Title'],
				'safe' => $array['Fieldset_Title_Safe'],
				'id' => 'fs_'.$array['Fieldset_ID'],
			);
			$feedback_array[$i]['fieldset'][$f]['questions'] = array();
		}
		
		// $person_answer_array = array(); $person_question_id_array = array();
		$array['Question_Answer'] = '';
		if(!empty($person_answer_array) && !empty($person_answer_array[$array['Question_ID']])) {
			$array['Question_Answer'] = $person_answer_array[$array['Question_ID']];
		}
		//$person_question_id_array[$pq_array['Question_ID']]
		
		$feedback_array[$i]['fieldset'][$f]['questions'] = array_merge($feedback_array[$i]['fieldset'][$f]['questions'],form_element_setup($array));
		$feedback_array[$i]['questions'] = array_merge($feedback_array[$i]['questions'],form_element_setup($array));
	}
	
	return array(
		'array' => $feedback_array,
		'user' => $user_array,
		'active' => $active_i
	);
}


/* insert feedback data and show thank-you
============================================================================================================= */
function form_feedback_insert($feedback_array,$array) {
	$standard_check_array = array('feedback_id','forename-required','surname-required','email-required','website','general-comments');
	$exclude_array = array('type','submit_form');
	
	$standard_array = array();
	$custom_array = array();
	$custom_multiple_array = array();
	$user_details_id = 0;
	
	// REMOVE THIS TO STORE DATA!
	/*
	return $feedback_array = array(
		'type' => 'success',
		'class' => array('success','complete','column','double'),
		'text' => 'Your feedback has been received. We appreciate the time you\'ve taken to fill in this online survey.'
	);
	*/
	
	foreach($array as $key => $value) {
		if(in_array($key,$standard_check_array)) {
			// standard for elements
			// feedback_user (feedback_ID,Forename,Surname,Email,Website,Comments)
			$standard_array[$key] = $value;
		}
		else {
			// these are the custom elements
			if(strpos($key,'multiple')===false) {
				// textarea or text input fields
				// feedback_answer_join (feedback_user_ID,feedback_question_ID,Answer)
				// q-10
				$numeric_key = ltrim($key,'q-');
				if(!empty($value) && !in_array($key,$exclude_array)) {
					$custom_array[$numeric_key] = $value;
				}
			}
			else {
				// either a select, radio or checkbox input
				// feedback_answer_multiple_join (feedback_user_ID,feedback_question_to_answer_multiple_ID)
				// r-6 && value="6"
				$custom_multiple_array[] = $value;
			}
		}
	}
	
	// basic user detail information
	$user_insert_sql = "INSERT INTO feedback_user (feedback_ID,Forename,Surname,Email,Website,Comments)
						VALUES(
							'".mysql_real_escape_string($standard_array['feedback_id'])."',
							'".mysql_real_escape_string($standard_array['forename-required'])."',
							'".mysql_real_escape_string($standard_array['surname-required'])."',
							'".mysql_real_escape_string($standard_array['email-required'])."',
							'".mysql_real_escape_string($standard_array['website'])."',
							'".mysql_real_escape_string(@$standard_array['general-comments'])."'
						)";
	$user_insert_query = mysqli_query($connect_admin, $user_insert_sql);
	$user_details_id = mysql_insert_id();
	
	// feedback details to email...
	$feedback_data_array = array(
		'user' => array('id' => $user_details_id, 'array' => $standard_array),
		'feedback' => array('id' => $standard_array['feedback_id'], 'array' => $_POST),
		'details' => $feedback_array
	);
	$feedback_data_array['user']['name'] = name($feedback_data_array['user']['array']['forename-required'],$feedback_data_array['user']['array']['surname-required']);
	feedback_send_email($feedback_data_array);
	
	if($user_details_id!=0) {
		if(!empty($custom_array)) {
			$total_inserts = count($custom_array);
			$value_sql_ids = ''; $i=0;
			foreach($custom_array as $key => $value) {
				$value_sql_ids .= "('".$user_details_id."','".mysql_real_escape_string($key)."','".mysql_real_escape_string($value)."')";
				if($i<($total_inserts-1)) $value_sql_ids .= ',';
				$i++;
			}
			$custom_insert_sql = "INSERT INTO feedback_answer_join (feedback_user_ID,feedback_question_ID,Answer)
									VALUES ".rtrim($value_sql_ids,',');
			mysqli_query($connect_admin, $custom_insert_sql);
		}
		if(!empty($custom_multiple_array)) {
			$total_inserts = count($custom_multiple_array);
			$value_sql_ids = '';
			for($i=0; $i<$total_inserts; $i++) {
				$value_sql_ids .= "('".$user_details_id."','".mysql_real_escape_string($custom_multiple_array[$i])."')";
				if($i<($total_inserts-1)) $value_sql_ids .= ',';
			}
			$custom_multiple_insert_sql = "INSERT INTO feedback_answer_multiple_join (feedback_user_ID,feedback_question_to_answer_multiple_ID)
											VALUES ".rtrim($value_sql_ids,',');
			mysqli_query($connect_admin, $custom_multiple_insert_sql);
		}
		$feedback_array = array(
			'type' => 'success',
			'class' => array('success','complete','column','double'),
			'text' => 'Your feedback has been received. We appreciate the time you\'ve taken to fill in this online survey.'
		);
	}
	else {
		// return an error...
		$feedback_array = array(
			'type' => 'error',
			'class' => array('error','column','double','last'),
			'text' => 'Something went wrong...'
		);
	}
	return $feedback_array;
}

/* send feedback data
============================================================================================================= */
function feedback_send_email($array) {
	global $now_timestamp;
	global $g_company_feedback_email;
	global $domain;
			
	$mail = new PHPMailer();
	//$mail->AddAddress($g_company_feedback_email,'Creation Feedback');
	//$mail->AddBCC('trevor@creation.uk.com','Creation Feedback');
	//$mail->AddReplyTo();
	$mail->AddAddress('trevor@creation.uk.com','Creation Feedback');
	$mail->AddCustomHeader('Content-Type: text/plain; charset=utf8');
	
	$email_subject = 'Feedback: '.$array['details']['text'];
	
	$body = 'Feedback for '.$array['details']['text']."\n";
	$body .= $array['details']['summary']['plain']."\n";

	$body .= "\n\n".'# Technical information'."\n";
	$body .= 'Referring Page: '.$domain.$array['details']['link']."\n";
	$body .= 'User Agent:     '.$_SERVER['HTTP_USER_AGENT']."\n";
	$body .= 'IP Address:     '.$_SERVER['REMOTE_ADDR']."\n";
	$body .= 'Whois:          http://whoisx.co.uk/'.$_SERVER['REMOTE_ADDR']."\n";
	
	$body .= "\n\n".'# User information'."\n";
	$body .= 'User ID: '.$array['user']['id']."\n";
	$body .= 'Name:    '.$array['user']['name']['full-name']."\n";
	$body .= 'Email:   '.$array['user']['array']['email-required']."\n";
	if(!empty($array['user']['array']['website'])) $body .= 'Website: '.$array['user']['array']['website']."\n";
	
	// details from the form...
	$body .= "\n\n".'# Feedback information'."\n";
	
	foreach($array['details']['questions'] as $questions) {
		$answer = 'A: ';
		$post_key = $questions['name'];
		if($questions['type']=='checkbox' || $questions['type']=='radio' || $questions['type']=='select') {
			$answer_key_id = @$array['feedback']['array'][$post_key];
			if(!empty($answer_key_id)) $answer .= $questions['multiple-array'][$answer_key_id]['text'];
		}
		else {
			// just an input or textarea
			$answer .= '(typed) — '.@$array['feedback']['array'][$post_key];
		}
		$body .= 'Q: '.$questions['label']."\n";
		$body .= $answer."\n\n";
	}
	
	$mail->Subject 	= $email_subject;
	$mail->From 	= validate($array['user']['array']['email-required'],'email');
	$mail->FromName = $array['user']['name']['full-name'];
	$mail->Body		= $body;
	$mail->Mailer	= 'mail';
	
	$feedback_array = array();
	if($mail->Send()) {
		$feedback_array['title'] = 'Feedback Sent';
		$feedback_array['text'] = 'Thank-you for your feedback. We\'ll be in touch soon.';
		$feedback_array['class'] = array('sent','confirmation');
		$feedback_array['type'] = 'success';
		$sent_value = 1;
	}
	else {
		$feedback_array['title'] = 'Feedback Error';
		$feedback_array['text'] = 'Your feedback wasn\'t sent, please try again.';
		$feedback_array['class'] = array('error','mail');
		$feedback_array['type'] = 'error';
	}
}

?>