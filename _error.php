<?php
header("HTTP/1.0 404 Not Found");
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
if(!empty($_GET['section']) && $_GET['section']=='news' && !empty($_GET['month']) && !empty($_GET['date'])) {
	$sql_exclude = '';

	if(!empty($_GET['permalink'])) {
		// partial article, but nothing found
		$sql_extra = " AND d.Safe_URL LIKE '".mysqli_real_escape_string($connect_admin, $_GET['permalink'])."%' AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $_GET['date'])."%'";
		$match_array_setup = related_setup($sql_extra);

		if(count($match_array_setup)==1) {
			$matched_article = $match_array_setup[0];
			$sql_exclude = " AND d.Safe_URL NOT LIKE '".$matched_article['safe']."%'";
		}
	}

	$sql_extra = " AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $_GET['month'])."%'".$sql_exclude;
	$month_array_setup = related_setup($sql_extra);
}

/* record this 404
============================================================================================================= */
$get_data = ''; $referrer = '';
if(!empty($_GET)) foreach($_GET as $key => $value) $get_data .= $key.'=>'.$value.', ';
$get_data = rtrim($get_data,',');

if(empty($g_company_domain)) $g_company_domain = '';
if(!empty($_SERVER['HTTP_REFERER'])) $referrer = $_SERVER['HTTP_REFERER'];

$error_sql = "INSERT INTO tracking_404 (Created, IP, UA, URL, Referrer, GET)
		VALUES('".$now_timestamp."',
				'".$_SERVER['REMOTE_ADDR']."',
				'".$_SERVER['HTTP_USER_AGENT']."',
				'".$g_company_domain.$_SERVER['REDIRECT_URL']."',
				'".$referrer."',
				'".$get_data."'
		)";
mysqli_query($connect_admin, $error_sql);


/* form setup
============================================================================================================= */

/* information setup
============================================================================================================= */
$navigationArray = '';
$subNavArray = '';

$all_else_fails_array = array(
	array('text' => 'Go back to the <a href="/">the homepage</a>.')
);
if(!empty($_SERVER['HTTP_REFERER']) && validate($_SERVER['HTTP_REFERER'],'website')) {
	$all_else_fails_array[]['text'] = '<a href="'.validate($_SERVER['HTTP_REFERER'],'website').'">Go back</a> from where you came from.';
}
$all_else_fails_array[]['text'] = 'Please <a href="/contact/">contact us</a> if you still have a problem.';


/* setup the header information
============================================================================================================= */
$currentSection[] = 'error';
$header = new Header();
$header->title = 'Oops, sorry… we might have lost this page | Creation';
$header->className = $currentSection;
$header->dom[] = 'error.js';
$header->heading = 'Error';
$header->Display();
?>

	<div id="content-primary">
		<div class="column double last">
			<h3>Oops, sorry… <em>This page could not be found</em></h3>
			<p>This may be because of a mis-typed URL, faulty referral from another site, out-of-date
			search engine listing or we simply deleted a file. Please try one of the links below.
			Alternatively you can click the logo to go back to the <a href="/">homepage</a>.</p>
		</div>

		<div class="column double last second">
		<?php
		if(!empty($all_else_fails_array) && is_array($all_else_fails_array)) {
			echo '<h3>If all else fails… <em>Try One of These</em></h3>'."\n";
			echo createList($all_else_fails_array);
		}

		if(!empty($matched_article) && is_array($matched_article)) {
			echo '<div'.addAttributes('','',array('column','news','second','mini-container','hfeed','vcard','author','selection')).'>'."\n";
			echo '<h3>Did You Mean <em>This Article?</em></h3>'."\n";
			echo news_display_mini(array($matched_article));
			echo '</div>'."\n";
		}
		if(!empty($month_array_setup) && is_array($month_array_setup)) {
			echo '<div'.addAttributes('','',array('column','last','second','news','mini-container','hfeed','vcard','author','selection')).'>'."\n";
			echo '<h3>Did You Mean… <em>One of These?</em></h3>'."\n";
			echo news_display_mini($month_array_setup);
			echo '</div>'."\n";
		}
		?>
		</div>

	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
