<?php error_reporting(0);
// UTC_TIMESTAMP | for MySQL pre 4.1.1 use $UTC_TIMESTAMP instead of NOW() to get 'now' in GMT/UTC
$UTC_TIMESTAMP = "DATE_ADD( '1970-01-01', INTERVAL UNIX_TIMESTAMP() SECOND )";

// file paths
$g_image_path 				= '/images/';
$g_image_path_thumbnails 	= 'thumbnails/';
$g_file_path_local 			= '/_files/';
$g_file_path_download 		= '/files/';

// image settings
$g_image_large_max_width 	= '410';
$g_image_small_max_width 	= '270';
$g_image_gallery_width 		= '410';
$g_image_thumbnail_size 	= '110';

$charset = 'utf-8';
$mime = 'text/html';
$lang = 'en';

$gl_ir = true;
//$sifr = true;


function stripslashes_deep($value) {
	return is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
}

// On ocassion the webhost must pass sessions through the URL. The default raw ampersand aggregator
// causes XHTML validation failure, so here we specify the aggregator as a 'soft' ampersand
ini_set('arg_separator.output', '&amp;'); // Sets the aggregator
ini_set('magic_quotes_runtime', 0); // make sure magic_quotes_gpc is OFF
ini_set('session.use_trans_sid', 0); // session links

if(get_magic_quotes_gpc()) $_POST = array_map('stripslashes_deep', $_POST);
if(get_magic_quotes_gpc()) $_GET = array_map('stripslashes_deep', $_GET);
if(get_magic_quotes_gpc()) $_COOKIE = array_map('stripslashes_deep', $_COOKIE);

if(isset($_SERVER['HTTP_HOST'])) $domain = 'http://'.$_SERVER['HTTP_HOST'];

session_start();

/* RENDERING TIME
============================================================================================================= */
function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

/* INCLUDES
============================================================================================================= */
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_common.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/header.php');

// new caching details
$cache = new Cache();
$cache->exclude_array = array_merge($cache->exclude_array,array());
$cache->hash = $_SERVER['REQUEST_URI'];
if(!empty($_GET['section']) && $_GET['section']=='news') $cache->group = 'news';
//$cache->start();


$domain = 'http://'.$g_company_domain;

$now_timestamp = formatDate(time(), 'sql');


/* SOMEONE IS LOGGED IN.
============================================================================================================= */
$logged_in_dom_array = array(); $logged_in = false;
if(!empty($_SESSION) && !empty($_SESSION['login']['id'])) {
	$user_id = $_SESSION['login']['id'];
	//$logged_in_dom_array = array('scripts/jquery.jeditable.pack.js','admin.frontend.js');
	$logged_in = true;
}
// if($logged_in==true && !empty($user_id) && is_numeric($user_id))

/* SECTION SETUP
============================================================================================================= */
$currentSection = array();
if(isset($_GET['section'])) 	$currentSection[] = formatText($_GET['section'],'url');
if(isset($_GET['subsection'])) 	$currentSection[] = formatText($_GET['subsection'],'url');
if(count($currentSection)>0) 	$section = formatText($currentSection[0],'title');

// the WIDE class based on cookies
if(!empty($_COOKIE) && !empty($_COOKIE['c_uk_size'])) {
	//if(strtolower($_COOKIE['c_uk_size'])=='wide') $currentSection[] = 'wide';
}
$extra_show = true;
$extra_show = false;

$this_page_url = '/';
foreach($currentSection as $page) {
	if($page=='home') continue;
	if(!empty($_GET['subsection']) && $_GET['section']=='contact' && $_GET['subsection']=='national') {
		$this_page_url = '/contact/';
		continue;
	}
	if(!empty($_GET['subsection']) && $_GET['section']=='company' && $_GET['subsection']=='about') {
		$this_page_url = '/company/';
		continue;
	}
	if(!empty($_GET['section']) && $_GET['section']=='news') {
		$this_page_url = '/archives/';
		continue;
	}
	$this_page_url .= $page.'/';
}
?>