<?php
$user_id = 0;
if(!empty($_SESSION) && !empty($_SESSION['login']['id'])) {
	$user_id = $_SESSION['login']['id'];
}
elseif(isset($_GET['section']) && $_GET['section']!='login' && !authorise()) {
	header('Location: /admin/login/');
	exit();
}

$admin_css_array = array(
	array('file' => 'admin.css', 'media' => 'screen'),
	array('file' => 'thickbox.css', 'media' => 'screen'),
);
$admin_js_array = array(
	'scripts/jquery.thickbox.js',
	'scripts/jquery.metadata.js',
	'scripts/jquery.validate.min.js',
	'scripts/jquery.wait.js',
	'scripts/jquery.forms.multifile.pack.js',
	'scripts/jquery.forms.autosave.js',
	'scripts/jquery.forms.typewatch.pack.js',
	'forms.js','admin.js'
);
$currentSection[] = 'admin';
$admin = true;
$image_accept_array = array('gif','jpg');
$extra_text = '';

/* includes
============================================================================================================= */
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/forms.php');
$extra_show = false;

$greet_array = array('Jambo','Yasou','Góðan daginn','Mingalaba','Guten Tag','Fáilte','Bangawoyo','Përshëndetje','Shalom','Aloha','G\'day','Ni hao','Hej','Hala');
$language_greet_array = array('Swahili','Greek','Icelandic','Burmese','German','Irish','Korean','Albanian','Hebrew','Hawaiian','Australian','Mandarin','Swedish','Arabic');
foreach($language_greet_array as $greet_language) $greet_tagline_array[] = 'Now you know how to greet people in '.$greet_language.'!';


/* user details
============================================================================================================= */
$person_sql = "SELECT a_d.ID, a_d.Username, a_d.SafeURL, a_d.Forename, a_d.Surname, a_d.Email, CONCAT_WS(' ',a_d.Forename,a_d.Surname) AS FullName
				FROM author_details AS a_d
				WHERE a_d.Active = '1'
				AND a_d.ID = '".mysql_real_escape_string($user_id)."'";

$person_query = mysqli_query($connect_admin, $person_sql);
if(mysql_num_rows($person_query)==1) {
	$person_array = mysqli_fetch_array($person_query);
	$person_details_array = profile_setup(" AND ad.ID = '".mysql_real_escape_string($person_array['ID'])."'",false,false);
	$person_details_array = $person_details_array[0];
}
else {
	//session_destroy();
	//header('Location: /admin/login/');
}


/* general links
============================================================================================================= */
if(!empty($person_details_array)) {
	$profile_link = '/admin/profile/'.$person_details_array['identifier'].'/';
	$hot_topic_link = $profile_link.'hot-topic/';
	$change_password_link = $profile_link.'password/';
}


/* admin navigation
============================================================================================================= */
$profile_sub_array = array(
	//'main' => array('text' => 'Main', 'title' => '', 'link' => '/admin/profile/', 'class' => array('profile')),
	'personal' => array('text' => 'Personal Text', 'title' => '', 'link' => $profile_link, 'class' => array('profile','profile_personal_text')),
	'hottopic' => array('text' => 'Hot Topics', 'title' => '', 'link' => $hot_topic_link, 'class' => array('profile','profile_hottopic')),
	'password' => array('text' => 'Password', 'title' => '', 'link' => $change_password_link, 'class' => array('profile','profile_password')),
);
$portfolio_sub_array = array(
	//'main' => array('text' => 'Main', 'title' => '', 'link' => '/admin/portfolio/', 'class' => array('portfolio')),
	'company' => array('text' => 'Company', 'title' => '', 'link' => '/admin/portfolio/company/', 'class' => array('portfolio','portfolio_company'))
);
$services_sub_array = array(
	//'main' => array('text' => 'Main', 'title' => '', 'link' => '/services/', 'class' => array('services'))
);
$news_sub_array = array(
	//'main' => array('text' => 'Main', 'title' => '', 'link' => '/news/', 'class' => array('news')),
	'extra' => array('text' => 'Extra News', 'title' => '', 'link' => '/admin/news/extra/', 'class' => array('news','extra')),
	'comments' => array('text' => 'Comments', 'title' => '', 'link' => '/admin/news/comments/', 'class' => array('news','comments')),
);


if(!empty($_GET['section']) && $_GET['section']=='profile') {
	if(!empty($_GET['subsection']) && $_GET['subsection']=='hot-topic' && !empty($profile_sub_array['hottopic'])) $profile_sub_array['hottopic']['class'][] = 'active';
	elseif(!empty($_GET['subsection']) && $_GET['subsection']=='profile-text' && !empty($profile_sub_array['personal'])) $profile_sub_array['personal']['class'][] = 'active';
	elseif(!empty($_GET['subsection']) && $_GET['subsection']=='password' && !empty($profile_sub_array['password'])) $profile_sub_array['password']['class'][] = 'active';
}
elseif(!empty($_GET['section']) && $_GET['section']=='news') {
	if(!empty($_GET['type']) && $_GET['type']=='extra' && !empty($news_sub_array['extra'])) $news_sub_array['extra']['class'][] = 'active';
	elseif(!empty($_GET['type']) && $_GET['type']=='comments' && !empty($news_sub_array['comments'])) $news_sub_array['comments']['class'][] = 'active';
}

$navigationArray = array(
	array('text' => 'Dashboard', 'title' => '', 'link' => '/admin/', 'class' => array('dashboard')),
	array('text' => 'Profile', 'title' => '', 'link' => $profile_link, 'class' => array('profile'), 'sub' => $profile_sub_array),
	//array('text' => 'Company', 'title' => '', 'link' => '/admin/company/', 'class' => array('company')),
	//array('text' => 'Portfolio', 'title' => '', 'link' => '/admin/portfolio/', 'class' => array('portfolio'), 'sub' => $portfolio_sub_array),
	//array('text' => 'Services', 'title' => '', 'link' => '/admin/services/', 'class' => array('services'), 'sub' => $services_sub_array),
	array('text' => 'News', 'title' => '', 'link' => '/admin/news/', 'class' => array('blog','news','archives'), 'sub' => $news_sub_array)
);
?>