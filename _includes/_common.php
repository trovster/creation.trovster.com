<?php

/* SETUP ARRAYS
============================================================================================================= */
$g_cssmediaArray = array('all','screen','projection','print','handheld','aural','braille','embossed','tty','tv');
$g_forminputArray = array('text','password','checkbox','radio','submit','reset','file','hidden','image','button');
$g_naughty_words = array('left','right','center','centre','black','white','bold','fuck');

/* NAVIGATION ARRAYS
============================================================================================================= */
$g_skiplinksArray = array(
	'content' => array('text' => 'Skip to content', 'title' => 'Skip to the main content area of the current page', 'link' => '#content-primary', 'accesskey' => '2'),
	'content-nav' => array('text' => 'Skip to content navigation', 'title' => 'Skip to the content navigation for this page', 'link' => '#content-navigation'),
	'navigation' => array('text' => 'Skip to navigation', 'title' => 'Skip to the main navigation of the site', 'link' => '#navigation'),
	'footer' => array('text' => 'Skip to footer', 'title' => 'Skip to the footer of the current page', 'link' => '#footer')
);
$navigationArray = array(
	array('text' => 'Home', 'title' => 'Home', 'link' => '/', 'rel' => array('home'), 'class' => array('home','welcome'), 'accesskey' => '1'),
	array('text' => 'Company', 'title' => 'Company', 'link' => '/company/', 'rel' => array('section'), 'class' => array('company')),
	array('text' => 'Portfolio', 'title' => 'Portfolio', 'link' => '/portfolio/', 'rel' => array('section'), 'class' => array('portfolio')),
	array('text' => 'Services', 'title' => 'Services', 'link' => '/services/', 'rel' => array('section'), 'class' => array('services')),
	array('text' => 'Contact', 'title' => 'Contact', 'link' => '/contact/', 'rel' => array('section','author'), 'class' => array('contact'), 'accesskey' => '9'),
	array('text' => 'News', 'title' => 'News', 'link' => '/news/', 'rel' => array('section'), 'class' => array('blog','news','archives'))
);
if(!empty($gl_ir) && $gl_ir==true) for($i=0; $i<count($navigationArray); $i++) $navigationArray[$i]['gilder'] = true;

$contact_sub_nav = array(
	'national' => array('link' => '/', 'alt' => 'National', 'zoom' => '6', 'accesskey' => '1'),
	'regional' => array('link' => '/regional/','alt' => 'Regional', 'zoom' => '10', 'accesskey' => '2'),
	'local' => array('link' => '/local/','alt' => 'Local', 'zoom' => '14', 'accesskey' => '3')
);

$api_google_maps = 'A';

$g_apiArray = array(
	'google' => array(
		'maps' => $api_google_maps
	),
	'wordpress' => '',
	'upcoming' => '',
	'flickr' => '',
);
$g_cache_options = array(
	'caching' => true,
	'cacheDir' => '/cache/',
	'lifeTime' => 60,
	'masterFile' => $_SERVER['DOCUMENT_ROOT'].'/cache/cache-lite.config'
);



/* VALIDATION
============================================================================================================= */
$g_validationArray = array(
	'email' => '/(?:^|\s)[-a-z0-9_.]+@([-a-z0-9]+\.)+[a-z]{2,6}(?:\s|$)/i',
	'website' => '#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i',
	'postcode' => '/^[A-Z]{1,2}[0-9]{1,2}[[:space:]]?[0-9][A-Z]{2}$/i',
	'telephone' => '/^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,3})|(\(?\d{2,3}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$/'
);

/* INCLUDES
============================================================================================================= */
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/markdown.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/smartypants.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/highlighting.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/images.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/number_to_words.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/mf_best_guess.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/time_since.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/mkdirr.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/phpmailer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/akismet.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/cache-lite.file.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/duration.class.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/database.php');

$connect_admin = dbConnect();
mysqli_query($connect_admin, "SET NAMES 'utf8'");

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/arrays.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/strings.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/numbers.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/attributes.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/validation.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/images.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/files.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/stylesheets.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/lists_create.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/forms_create.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/functions/cache.class.inc.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/cookies.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/forms.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/company_setup.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/news_setup.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/news_display.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/news_comments.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/portfolio_setup.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/services_setup.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/eshot_setup.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/x_feeds.php');


$g_filesArray = array(
	'sitemap-xml' => array(
		'title' => 'Google sitemap',
		'rel' => 'alternate',
		'type' => 'feed',
		'mime' => 'application/xml',
		'name' => 'sitemap.xml',
		'path' => '/',
		'local' => '/sitemap.xml',
		'permalink' => '/sitemap/',
		'xsl' => '',
		'accesskey' => '3'
	)
);
$setup_feed_array = setup_feed_information();
$g_filesArray['latest-news-atom'] = $setup_feed_array['articles']['xml']['atom'];
$g_filesArray['latest-news-rss'] = $setup_feed_array['articles']['xml']['rss'];
$g_filesArray['latest-comments-atom'] = $setup_feed_array['comments']['xml']['atom'];
$g_filesArray['latest-comments-rss'] = $setup_feed_array['comments']['xml']['rss'];
$g_filesArray['latest-hot-topics-atom'] = $setup_feed_array['hot-topics']['xml']['atom'];
$g_filesArray['latest-hot-topics-rss'] = $setup_feed_array['hot-topics']['xml']['rss'];
?>
