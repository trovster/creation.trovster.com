<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/*
Eshots URL structure

/eshots/										=> introduction & form to subscribe to newsletter
/eshots/unsubscribe/							=> form to unsubscribe from newsletter
/eshots/unsubscribe/?email@example.com			=> sends an email to that address, with the MD5 unsubscribe link
/eshots/unsubscribe/email-md5-hash-string/		=> unsubscribes the user

/eshots/month-YY/								=> archive of eshots for that month/year
/eshots/month-YY/title-of-eshot/				=> online permalink to specific eshot campaign/design/content
/eshots/month-YY/title-of-eshot/email/			=> version sent through campaign monitor, with internal CSS
*/



/* defaults
============================================================================================================= */
$eshot_title = '';
$eshot_text = '';
$eshot_stylesheet_internal = '';
$meta_description = '';
$sql_extra = '';

/* page information */
$page_sql = "SELECT Created, Updated, TabName, TabURL, Title, Text
			 FROM page_details
			 WHERE TabURL = '/eshots/'
			 AND Active = '1'
			 LIMIT 0,1";
			 
$page_query = mysqli_query($connect_admin, $page_sql);
$page_array = mysqli_fetch_array($page_query);
$eshot_title_main = $page_array['Title'];
$eshot_title = $eshot_title_main;

$eshot_text = '<div class="column double description">'."\n";
$eshot_text .= formatText($page_array['Text'],'output');
$eshot_text .= '</div>'."\n\n";

if(!empty($_GET['date'])) {
	preg_match('#([a-z]+)-([0-9]{2})#',$_GET['date'],$date_array);
	if(count($date_array)!=3) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
	$_GET['month'] = $date_array[1];
	$_GET['year'] = '20'.$date_array[2];

	$date_search = $_GET['year'].'-'.date('m',strtotime('10 '.$_GET['month'].' '.$_GET['year'])).'%';
	$sql_extra .= " AND es.Created LIKE '".$date_search."'";
	$date_title = ucfirst($_GET['month']).' '.$_GET['year'];
}


/* database setup
============================================================================================================= */
if(!empty($_GET['month']) && !empty($_GET['year']) && !empty($_GET['permalink'])) {

	$sql_extra .= "AND es.Safe_URL = '".mysql_real_escape_string($_GET['permalink'])."/'";
	$eshot_array = eshot_sql($sql_extra);
	
	if(count($eshot_array)!=1) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
	
	$eshot_title = $eshot_array[0]['title'].' | An '.rtrim($eshot_title_main,'s');
	$eshot_text = eshot_display($eshot_array[0]);
	$eshot_stylesheet_array = $eshot_array[0]['stylesheet']['file'];
	$eshot_stylesheet_internal = $eshot_array[0]['stylesheet']['internal'];
	$meta_description = $eshot_array[0]['description']['summary'];
	$currentSection[] = 'permalink';
	$currentSection[] = 'eshot-permalink';
	$extra_show = false;
}
elseif(!empty($_GET['month']) && !empty($_GET['year'])) {
	$eshot_array = eshot_sql($sql_extra);
	if(count($eshot_array)==0) {
		header('Location: /eshots/');
		exit;
	}
	//print_r($eshot_array);
	$eshot_title = $date_title.' '.$eshot_title_main.' Archive';
	$eshot_text .= '<div id="eshots-archive" class="column selection">'."\n";
	$eshot_text .= '<h3>Eshot Archive <em>'.$date_title.'</em></h3>'."\n";
	$eshot_text .= news_display_mini($eshot_array,'','','summary');
	$eshot_text .= '</div>'."\n";
}
elseif(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='unsubscribe') {
	$eshot_title = 'Unsubscribe from our '.$eshot_title_main;
	$eshot_text_output = form_unsubscribe_eshot('/eshots/unsubscribe/');
	
	if(!empty($_GET['md5'])) {
		// check the DB for that MD5
		// check whether entry has 'unsubscribe' set to '1'
		// if true set Subscribed to '0' 
		//         show confirmation of unsubscribed email@example.com
		// else output an error message
		
		// 1-trovster@gmail.com => d43b4613e6e2ab49799937c04c496601
		// 2-trevor@creation.uk.com => 229eaf29a4409e68d873cd20443ee3f3
		
		$eshot_text_output .= 'Unsubscribe information here';
	}
	
	$eshot_text .= '<div id="eshots-unsubscribe" class="column double last">'."\n";
	$eshot_text .= '<h3>Unsubscribe… <em>from our Eshots</em></h3>'."\n";
	$eshot_text .= $eshot_text_output;
	$eshot_text .= '</div>'."\n";
}
else {
	$eshot_text .= '<div id="eshots-archive" class="column selection">'."\n";
	$eshot_text .= '<h3>Our Latest <em>Eshot Campaigns</em></h3>'."\n";
	$eshot_array = eshot_sql($sql_extra);
	$eshot_text .= news_display_mini($eshot_array,'','','summary');
	$eshot_text .= '</div>'."\n";
}


/* information setup
============================================================================================================= */
if(!empty($g_skiplinksArray['content-nav'])) unset($g_skiplinksArray['content-nav']); 
if(!empty($g_skiplinksArray['navigation'])) unset($g_skiplinksArray['navigation']); 

/* form setup
============================================================================================================= */
		

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $eshot_title.' | '.$g_company_title;
$header->className = $currentSection;
$header->className[] = 'hatom';
//$header->className[] = 'colour-blue';
//$header->className[] = 'price-195';
$header->stylesheet[] = array('file' => 'specifics/eshots.css', 'media' => 'screen');
$header->dom[] = 'scripts/jquery.metadata.js';
$header->dom[] = 'scripts/jquery.validate.min.js';
$header->dom[] = 'forms.js';
if(!empty($eshot_array[0]) && !empty($_GET['permalink'])) {
	$header->dom = '';
	if(!empty($eshot_stylesheet_array)) $eshot_stylesheet_array_combined = array_merge($header->stylesheet,$eshot_stylesheet_array);
	else $eshot_stylesheet_array_combined = $header->stylesheet;

	if(!empty($_GET['email']) && $_GET['email']=='true') {
		$header->stylesheetBlock = createInlineStyle($eshot_stylesheet_array_combined);
		$header->stylesheet = array();
		$header->eshot_details = array('link' => $eshot_array[0]['link']);
		$header->className[] = 'eshot-permalink-email';
	}
	else $header->stylesheet = $eshot_stylesheet_array_combined;
}
$header->heading = 'EShots';
$header->metaDescription = $meta_description;
$header->Display();
?>

	<div id="content-primary" class="hfeed">
		<?php echo $eshot_text; ?>
	<!-- end of div id #content-primary -->
	</div>

<?php
if(!empty($eshot_array[0]) && !empty($_GET['permalink'])) require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer_eshot.php');
else require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php');
?>