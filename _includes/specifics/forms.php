<?php
function form_feedback_show() {
	global $this_page_url;

	$contact_form_array = array(
		'text' => array(
			'fieldset' => 'Contact Form',
			'elements' => array(
				'message-required' => array('label' => 'Your message', 'type' => 'textarea', 'name' => 'message-required', 'id' => 'message-required', 'accesskey' => '9'),
				'email-required' => array('label' => 'Your email address', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required'),
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'type', 'value' => 'feedback'),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Leave Feedback', 'class' => array('feedback'))
			)
		)
	);

	if(!empty($_POST['type']) && strtolower($_POST['type'])=='feedback') {
		$contact_form_array_checked = checkRequired($_POST);
		if(!empty($contact_form_array_checked)) $contact_form_array_checked = cleanArray($contact_form_array_checked);
		$post_array = stripTags($_POST,'db');

		if(empty($contact_form_array_checked)) {
			unset($contact_form_array);
			$feedback_array = form_feedback_send($post_array,$this_page_url);
			return '<div'.addAttributes($feedback_array['title'],'',$feedback_array['class']).'>'.formatText($feedback_array['text'],'output').'</div>'."\n";
		}
		else {
			$contact_form_array = createFormErrors($contact_form_array,$contact_form_array_checked);
		}
	}
	if(!empty($contact_form_array)) return createForm($contact_form_array,'post',$this_page_url.'#footer-extra-feedback');
}


function form_unsubscribe_eshot($page_url) {
	$unsubscribe_eshot_array = array(
		'text' => array(
			'fieldset' => 'Unsubscribe Form',
			'elements' => array(
				'email-required' => array('label' => 'Your email address', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required')
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'type', 'value' => 'unsubscribe'),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Unsubscribe', 'class' => array('unsubscribe'))
			)
		)
	);
	$return = '';
	$return = createForm($unsubscribe_eshot_array,'get',$page_url.'#eshots-unsubscribe');

	if(!empty($_GET['type']) && strtolower($_GET['type'])=='contact') {
		$array_checked = checkRequired($_GET);
		if(!empty($array_checked)) $array_checked = cleanArray($array_checked);
		$post_array = stripTags($_GET);

		if(empty($array_checked)) {
			//$feedback_array = form_feedback_send($post_array,$page_url);
			//$return = '<div'.addAttributes($feedback_array['title'],'',$feedback_array['class']).'>'.formatText($feedback_array['text'],'output').'</div>'."\n";

			// check the DB for this email address
			// if doesn't exist, feedback saying the email doesn't exist
			// else email that address with confirmation of unsubscription
			//      md5(ID-email@example.com)
			//      feedback screen saying a confirmation has been sent to the email@example.com

			if(!empty($feedback_array) && !empty($feedback_array['type']) && strtolower($feedback_array['type'])=='error') {
				// there is an error... show the form again
				//$contact_us_array = createFormErrors($contact_us_array,$contact_form_array_checked);
				$return .= createForm($unsubscribe_eshot_array,'get',$page_url.'#eshots-unsubscribe');
			}
			else unset($contact_us_array);
		}
		else {
			$unsubscribe_eshot_array = createFormErrors($unsubscribe_eshot_array,$array_checked);
			$return = createForm($unsubscribe_eshot_array,'get',$page_url.'#eshots-unsubscribe');
		}
	}

	return $return;
}



function form_contact_feedback_show($page_url) {
	$contact_us_array = array(
		'text' => array(
			'fieldset' => 'Contact Form',
			'elements' => array(
				'name-required' => array('label' => 'Your name', 'type' => 'text', 'name' => 'name-required', 'id' => 'name-required'),
				'email-required' => array('label' => 'Your email address', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required'),
				'subject' => array('label' => 'Subject', 'type' => 'text', 'name' => 'subject', 'id' => 'subject'),
				'message-required' => array('label' => 'Your message…', 'type' => 'textarea', 'name' => 'message-required', 'id' => 'message-required', 'class' => array('extend')),
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'type', 'value' => 'contact'),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Submit', 'class' => array('feedback','replaced'))
			)
		)
	);
	$return = '';
	$return = createForm($contact_us_array,'post',$page_url.'#contact-feedback');

	if(!empty($_POST['type']) && strtolower($_POST['type'])=='contact') {
		$contact_form_array_checked = checkRequired($_POST);
		if(!empty($contact_form_array_checked)) $contact_form_array_checked = cleanArray($contact_form_array_checked);
		$post_array = stripTags($_POST);

		if(empty($contact_form_array_checked)) {

			$feedback_array = form_feedback_send($post_array,$page_url);
			$return = '<div'.addAttributes($feedback_array['title'],'',$feedback_array['class']).'>'.formatText($feedback_array['text'],'output').'</div>'."\n";

			if(!empty($feedback_array) && !empty($feedback_array['type']) && strtolower($feedback_array['type'])=='error') {
				// there is an error... show the form again
				//$contact_us_array = createFormErrors($contact_us_array,$contact_form_array_checked);
				$return .= createForm($contact_us_array,'post',$page_url.'#contact-feedback');
			}
			else unset($contact_us_array);
		}
		else {
			if(!empty($contact_form_array_checked['email-required']) && strtolower($contact_form_array_checked['email-required'])=='invalid email address') {
				$contact_form_array_checked['email-required'] = 'Invalid';
			}
			$contact_us_array = createFormErrors($contact_us_array,$contact_form_array_checked);
			$return = createForm($contact_us_array,'post',$page_url.'#contact-feedback');
		}
	}

	return $return;
}


function news_comments_form($array) {
	global $this_page_url;

	//$openid_note = '<p>If you have <a href="http://openid.net" rel="external" class="external">OpenID</a> use it here.</p>';

	$remember_checkbox_array = array(
		'agree' => array('value' => 'agree', 'label' => 'Remember My Details?', 'id' => 'agree')
	);
	//$remember_checkbox_array['agree']['checked'] = 'checked';

	$comment_form_array = array(
		'user-inputs' => array(
			'fieldset' => 'Comments: Your Details',
			'id' => 'comments-form-your-details',
			'class' => array('double','column','user-inputs'),
			'elements' => array(
				'comment-name-required' => array('label' => 'Your Name', 'type' => 'text', 'name' => 'comment-name-required', 'id' => 'comment-name-required', 'value' => ''),
				'comment-email-required' => array('label' => 'Email Address', 'type' => 'text', 'name' => 'comment-email-required', 'id' => 'comment-email-required', 'value' => ''),
				'comment-website' => array('label' => 'Website', 'type' => 'text', 'name' => 'comment-website', 'id' => 'comment-website', 'value' => ''),
				'comment-remember' => array('label' => 'Save my information', 'name' => 'comment-remember', 'type' => 'checkbox', 'checkbox' => $remember_checkbox_array),
			)
		),
		/*
		'open-id' => array(
			'fieldset' => 'Comments: Your OpenID Details',
			'id' => 'comments-form-your-details-openid',
			'class' => array('double','column','openid'),
			'elements' => array(
				'openid-identifier-required' => array('label' => 'OpenID', 'type' => 'text', 'name' => 'openid-identifier-required', 'id' => 'openid-identifier-required', 'value' => '', 'class' => array('openid'), 'note' => $openid_note),
			)
		),
		*/
		'comment' => array(
			'fieldset' => 'Contact: Add your thoughts',
			'id' => 'comments-form-your-comment',
			'class' => array('column','double','last'),
			'elements' => array(
				'comment-required' => array('label' => 'Your comment', 'type' => 'textarea', 'name' => 'comment-required', 'id' => 'comment-required', 'class' => array('extend')),
			)
		),
		'submit' => array(
			'fieldset' => 'Complete the form',
			'elements' => array(
				array('type' => 'hidden', 'name' => 'type', 'value' => 'comment'),
				array('type' => 'submit', 'name' => 'submit', 'value' => 'Post my comment', 'class' => array('comment','replaced'))
			)
		)
	);

	if(!empty($_COOKIE['c_uk_comment']) && is_array($_COOKIE['c_uk_comment'])) {
		if(!empty($_COOKIE['c_uk_comment']['name'])) 	$comment_form_array['user-inputs']['elements']['comment-name-required']['value'] = $_COOKIE['c_uk_comment']['name'];
		if(!empty($_COOKIE['c_uk_comment']['email'])) 	$comment_form_array['user-inputs']['elements']['comment-email-required']['value'] = $_COOKIE['c_uk_comment']['email'];
		if(!empty($_COOKIE['c_uk_comment']['website'])) $comment_form_array['user-inputs']['elements']['comment-website']['value'] = $_COOKIE['c_uk_comment']['website'];
		$comment_form_array['user-inputs']['elements']['comment-remember']['checkbox']['agree']['checked'] = 'checked';
	}

	//'remember' => array('label' => 'Remember My Details'),

	if(!empty($_POST['type']) && $_POST['type']=='comment') {
		$checkedArray = checkRequired($_POST);
		if(!empty($checkedArray)) $checkedArray = cleanArray($checkedArray);
		$post_array = stripTags($_POST,'db');
		comment_cookie_toggle($_POST);

		if(empty($checkedArray)) {
			// comment was successful, submit it
			unset($comment_form_array);
			$feedback_array = news_comments_add($array,$post_array,$_POST);
			return '<div'.addAttributes('','',$feedback_array['class']).'><h3>'.formatText($feedback_array['title']).'</h3>'.formatText($feedback_array['text'],'output').'</div>'."\n";
		}
		else {
			if(!empty($_POST['comment-remember']) && is_array($_POST['comment-remember']) && in_array('agree',$_POST['comment-remember'])) {
				$comment_form_array['user-inputs']['elements']['comment-remember']['checkbox']['agree']['checked'] = 'checked';
			}
			else {
				unset($comment_form_array['user-inputs']['elements']['comment-remember']['checkbox']['agree']['checked']);
			}
			$comment_form_array = createFormErrors($comment_form_array,$checkedArray);
		}
	}
	if(!empty($comment_form_array)) {
		return createForm($comment_form_array,'post',$this_page_url.'#comments-form');
	}
}





function form_feedback_send($array,$my_page='',$via_ajax=0) {
	global $connect_admin;
	global $now_timestamp;
	global $g_company_feedback_email;
	global $domain;
	global $g_apiArray;

	if(!empty($my_page)) $this_page_url = $my_page;
	else $this_page_url = '';

	$feedback_array = array();
	$sent_value = 0; $is_spam = 0;
	$name = ''; $subject = '';
	if(!empty($array['name-required'])) $name = $array['name-required'];
	if(!empty($array['subject'])) $subject = $array['subject'];

	if(empty($array) || !is_array($array)) {
		$feedback_array['title'] = 'Feedback Error';
		$feedback_array['text'] = 'There was an unexpected error, please try again.';
		$feedback_array['class'] = array('error','unexpected');
		$feedback_array['type'] = 'error';
		return $feedback_array;
	}
	elseif(empty($array['email-required']) || !validate($array['email-required'],'email')) {
		$feedback_array['title'] = 'Invalid Email';
		$feedback_array['text'] = 'The email you provided is invalid, please try again.';
		$feedback_array['class'] = array('error','invalid','email');
		$feedback_array['type'] = 'error';
		return $feedback_array;
	}


	// insert in to our DB forms_feedback
	$mail = new PHPMailer();
	$mail->AddAddress($g_company_feedback_email,'Creation Feedback');
	$mail->AddBCC('trevor@creation.uk.com','Creation Feedback');
	$mail->AddCustomHeader('Content-Type: text/plain; charset=utf8');
	$mail->AddReplyTo(validate($array['email-required'],'email'),validate($array['email-required'],'email'));

	$email_subject = 'Feedback Form';
	if(!empty($subject)) $email_subject .= ' — Subject: '.$subject."\n";

	$body = 'New feedback comment'."\n\n";

	$body .= 'Referring Page: '.$domain.$this_page_url."\n";
	$body .= 'User Agent:     '.$_SERVER['HTTP_USER_AGENT']."\n";
	$body .= 'IP Address:     '.$_SERVER['REMOTE_ADDR']."\n";
	$body .= 'Whois:          http://whoisx.co.uk/'.$_SERVER['REMOTE_ADDR']."\n";

	$body .= "\n".'Comment information'."\n";

	if(!empty($name)) $body .= 'Name:    '.$name."\n";
	$body .= 'Email:   '.validate($array['email-required'],'email')."\n";
	if(!empty($subject)) $body .= 'Subject: '.$subject."\n";
	$body .= 'Message: '."\n".str_replace("\r\n","\n",$array['message-required'])."\n";

	$mail->Subject 	= $email_subject;
	$mail->From 	= validate($array['email-required'],'email');
	if(!empty($name)) $mail->FromName = $name;
	else $mail->FromName = validate($array['email-required'],'email');
	$mail->Body		= $body;
	$mail->Mailer	= 'mail';

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

	// finally insert in to our database
	$feedback_sql = "INSERT INTO forms_feedback (Created, IP, UA, Page, Subject, Message, Name, Email, Sent, Is_Spam, Via_AJAX)
					 VALUES('".$now_timestamp."',
					 		'".mysqli_real_escape_string($connect_admin, $_SERVER['REMOTE_ADDR'])."',
							'".mysqli_real_escape_string($connect_admin, $_SERVER['HTTP_USER_AGENT'])."',
							'".mysqli_real_escape_string($connect_admin, $this_page_url)."',
							'".mysqli_real_escape_string($connect_admin, $subject)."',
							'".mysqli_real_escape_string($connect_admin, $array['message-required'])."',
							'".mysqli_real_escape_string($connect_admin, $name)."',
							'".mysqli_real_escape_string($connect_admin, validate($array['email-required'],'email'))."',
							'".mysqli_real_escape_string($connect_admin, $sent_value)."',
							'".$is_spam."',
							'".$via_ajax."')";

	mysqli_query($connect_admin, $feedback_sql);

	unset($_POST);
	unset($array);
	return $feedback_array;
}
?>
