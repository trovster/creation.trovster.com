<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* database setup
============================================================================================================= */

/* form setup
============================================================================================================= */
$loginArray = array(
	'login' => array(
		'fieldset' => 'Administration Login',
		'class' => array('column'),
		'elements' => array(
			'username-required' => array('label' => 'Username', 'type' => 'text', 'name' => 'username-required', 'id' => 'username-required', 'value' => '', 'tabindex' => 1),
			'password-required' => array('label' => 'Password', 'type' => 'password', 'name' => 'password-required', 'id' => 'password-required', 'value' => '', 'tabindex' => 2)
		)
	),
	'submit' => array(
		'fieldset' => 'Complete the form',
		'elements' => array(
			array('type' => 'hidden', 'name' => 'type', 'id' => 'type', 'value' => 'login'),
			array('type' => 'submit', 'name' => 'submit', 'id' => 'submit', 'value' => 'Login', 'class' => array('login'), 'tabindex' => 3)
		)
	)
);


/* information setup
============================================================================================================= */
$message = 'Welcome to the administration area.';
$messageClass = array('login');

if(isset($_GET['logout'])) {
	if(isset($_SESSION['login'])) unset($_SESSION['login']);
	$message = 'You have been <strong>logged out</strong>.';
	$messageClass[] = 'logout';
}
elseif(isset($_GET['login'])) {
	$message = 'Administration area requires you to <strong>login</strong>.';
	$messageClass[] = 'warning';
}
elseif(authorise()) {
	header('Location: /admin/dashboard/');
} 
elseif(isset($_POST['type'])) {
	$message = '<strong>Invalid</strong> username and/or password';
	$messageClass[] = 'invalid';
}
$navigationArray = ''; // remove the navigation menu from the admin login page



/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Login to the administration area | '.$header->title;
$header->heading = 'Welcome to the administration area';
$header->className = $currentSection;
$header->stylesheet = array_merge($header->stylesheet,$admin_css_array);
$header->dom = array_merge($header->dom,$admin_js_array);
$header->Display();
?>

	<div id="content-primary">
		<h3>Please login</h3>
		<?php
		echo '<p class="'.addClass($messageClass).'">'.$message.'</p>'."\n"; 
		echo createForm($loginArray,'post','/admin/','admin-login');
		?>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>