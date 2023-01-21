<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/feedback.php');
$currentSection = array('feedback');


/* feedback details setup
============================================================================================================= */
$sql_array = array();
$extra_array = array();
if(!empty($_GET['person']) && is_numeric($_GET['person'])) {
	$extra_array['person']['id'] = $_GET['person'];
}
if(!empty($_GET['permalink'])) {
	$sql_array['url'] = $_GET['permalink'];
}
$feedback_setup_array = feedback_setup($sql_array,$extra_array);
$feedback_array = $feedback_setup_array['array'];
$feedback_active_i = $feedback_setup_array['active'];

if(!empty($_GET['permalink'])) {
	if(isset($feedback_active_i) && is_numeric($feedback_active_i)) $feedback = $feedback_array[$feedback_active_i];
	else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
}
elseif(empty($_GET['permalink']) && !empty($feedback_array)) {
	// go to the latest feedback questionnaire
	header('Location: '.$feedback_array[0]['permalink']['link']);
}


/* form setup
============================================================================================================= */
$feedback_form_array = array();
foreach($feedback['fieldset'] as $feedback_fieldset) {
	$feedback_form_array[$feedback_fieldset['safe']] = array(
		'fieldset' => $feedback_fieldset['text'],
		'elements' => $feedback_fieldset['questions'],
		'class' => array('features',$feedback_fieldset['safe']),
		'id' => $feedback_fieldset['id'],
	);
}
$feedback_form_array['personals'] = array(
	'fieldset' => 'Your Details',
	'class' => array(),
	'id' => 'your-details',
	'elements' => array(
		'forename-required' => array('label' => 'Forename', 'type' => 'text', 'name' => 'forename-required', 'id' => 'forename-required', 'value' => @$feedback['user']['forename']),
		'surname-required' => array('label' => 'Surname', 'type' => 'text', 'name' => 'surname-required', 'id' => 'surname-required', 'value' => @$feedback['user']['surname']),
		'email-required' => array('label' => 'Email', 'type' => 'text', 'name' => 'email-required', 'id' => 'email-required', 'value' => @$feedback['user']['email']),
		'website' => array('label' => 'Website', 'type' => 'text', 'name' => 'website', 'id' => 'website', 'value' => @$feedback['user']['website']),
	)
);
$feedback_form_array['submit'] = array(
	'fieldset' => 'Complete the form',
	'elements' => array(
		array('type' => 'hidden', 'name' => 'type', 'value' => 'feedback'),
		array('type' => 'hidden', 'name' => 'feedback_id', 'value' => $feedback['identifier']),
		array('type' => 'submit', 'name' => 'submit', 'value' => 'Leave Feedback', 'class' => array('replaced','feedback'))
	)
);


if(!empty($_GET['fname'])) $feedback_form_array['personals']['elements']['forename-required']['value'] = $_GET['fname'];
if(!empty($_GET['sname'])) $feedback_form_array['personals']['elements']['surname-required']['value'] = $_GET['sname'];
if(!empty($_GET['email'])) $feedback_form_array['personals']['elements']['email-required']['value'] = $_GET['email'];
if(!empty($_GET['web'])) $feedback_form_array['personals']['elements']['website']['value'] = $_GET['web'];


/* form completion
=============================================================================================================*/
$feedback_output = ''; $show_form = true; $form_error = false;
if(!empty($_POST['type']) && strtolower($_POST['type'])=='feedback') {
	$feedback_form_array_checked = checkRequired($_POST);
	$feedback_form_array_checked = array_merge($feedback_form_array_checked,check_form_required_elements($feedback_form_array));
	if(!empty($feedback_form_array_checked)) $feedback_form_array_checked = cleanArray($feedback_form_array_checked);
	$feedback_post_array = stripTags($_POST,'db');
	//print_r($feedback_form_array_checked);
	if(empty($feedback_form_array_checked)) {
		// insert feedback into database...
		$show_form = false;
		$form_feedback_data = form_feedback_insert($feedback,$feedback_post_array);
		if($form_feedback_data['type']=='success') {
			$show_form = false;
			unset($feedback_form_array);
			$feedback['class'][] = 'feedback-'.$feedback['safe'].'-complete';
		}
		else $feedback_form_array = createFormErrors($feedback_form_array,$feedback_form_array_checked);

		$feedback['description']['html'] = '';
		$feedback['description']['html'] .= '<div'.addAttributes('','',@$form_feedback_data['class']).'>'."\n";
		$feedback['description']['html'] .= formatText($form_feedback_data['text'],'output');
		$feedback['description']['html'] .= '</div>'."\n";
	}
	else {
		if(!empty($feedback_form_array_checked['email-required']) && strpos(strtolower($feedback_form_array_checked['email-required']),'invalid')!==false) {
			$feedback_form_array_checked['email-required'] = 'Invalid';
		}
		if(!empty($feedback_form_array_checked['website']) && strpos(strtolower($feedback_form_array_checked['website']),'invalid')!==false) {
			$feedback_form_array_checked['website'] = 'Invalid';
		}
		$feedback_form_array = createFormErrors($feedback_form_array,$feedback_form_array_checked);
		$form_error = true;
	}
}

if($show_form==true) {
	if($form_error==true) {
		$feedback_output .= '<div id="error-message" class="column double last">'."\n";
		$feedback_output .= '<h3>Sorry, please check required inputs.</h3>'."\n";
		$feedback_output .= '<p>Please compelte any missing or invalid entries and click \'Leave Feedback\' again. Thank you.</p>';
		$feedback_output .= '</div>'."\n";
		$feedback['class'][] = 'feedback-error-message';
	}
	$feedback_output .= createForm($feedback_form_array,'post',$feedback['link'],'feedback',$feedback['class']);
}


/* stylesheet setup
=============================================================================================================*/
$extra_show = false;
$style_block = '';



/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Give us your feedback | Online Survey | '.$header->title;
$header->className = $currentSection;
$header->className = array_merge($header->className,$feedback['class']);
$header->heading = 'Feedback';
$header->dom[] = 'forms.js';
$header->dom[] = 'feedback.js';
$header->stylesheetBlock = $style_block;
$header->stylesheet[] = array('file' => 'specifics/feedback.css', 'media' => 'screen');
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3 class="feedback-title"><?php echo $feedback['text']; ?></h3>
			<h4>Online Survey <em><?php echo $feedback['title']; ?></em></h4>
			<?php echo $feedback['description']['html']; ?>
		</div>
		<?php echo $feedback_output; ?>
	<!-- end of div id #content-primary -->
	</div>

	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
