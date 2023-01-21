<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
if(!empty($_GET['date']) && !empty($_GET['month']) && !empty($_GET['permalink'])) {

	$sql_extra = " AND d.Active = '1'";
	if(!empty($_GET['preview']) && $_GET['preview']==true) {
		$sql_extra = '';
	}

	$news_sql = "SELECT
				 d.ID AS Detail_ID,
				 d.Created AS Detail_Created,
				 d.Updated AS Detail_Updated,
				 d.Title AS Detail_Title,
				 d.Safe_URL AS Detail_Safe_URL,
				 d.Summary AS Detail_Summary,
				 d.Description AS Detail_Description,
				 d.Comments AS Comments_Active,
				 d.Active AS Detail_Status,
				 COUNT(cj.ID) AS Comments_Total,
				 s.Section AS Section_Name,
				 ad.ID AS Author_ID,
				 ad.Forename as Author_Forename,
				 ad.Surname AS Author_Surname,
				 ad.Email AS Author_Email,
				 CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name,
				 ap.Title AS Author_Title
				 FROM news_details AS d
				 LEFT JOIN news_comments_join AS cj ON cj.News_Detail_ID = d.ID
				 LEFT JOIN news_section AS s ON s.ID = d.News_Section_ID
				 LEFT JOIN author_details AS ad ON ad.ID = d.CreatedID
				 LEFT JOIN author_profile AS ap ON ad.ID = ap.Author_Detail_ID
				 WHERE d.News_Section_ID = '1'
				 ".$sql_extra."
				 AND d.Safe_URL = '".mysqli_real_escape_string($connect_admin, $_GET['permalink'])."/'
				 AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $_GET['date'])."%'
				 GROUP BY d.ID
				 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
				 LIMIT 0,1";

	$news_query = mysqli_query($connect_admin, $news_sql);

	if(mysqli_num_rows($news_query)==0) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));

	$news_array = mysqli_fetch_array($news_query);
	if(!empty($_GET['preview']) && $_GET['preview']==1) {
		$news_array['Comments_Active'] = 0;
	}
	$news_array_standard = news_setup($news_array);
	$this_page_url = $news_array_standard['permalink']['link'];

}
else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));


/* information setup
============================================================================================================= */
$g_filesArray[$news_array_standard['author']['url'].'-atom'] = $news_array_standard['author']['feeds']['xml']['atom'];
$g_filesArray[$news_array_standard['author']['url'].'-rss'] = $news_array_standard['author']['feeds']['xml']['rss'];
$g_filesArray[$news_array_standard['safe'].'-atom'] = $news_array_standard['xml']['atom'];
$g_filesArray[$news_array_standard['safe'].'-rss'] = $news_array_standard['xml']['rss'];


/* form setup
============================================================================================================= */
$g_skiplinksArray['comments-view'] = array('text' => 'Skip to comments', 'title' => 'Skip to the comments for &#8220;'.$news_array_standard['title'].'&#8221;', 'link' => '#comments-view');
$g_skiplinksArray['comments-form'] = array('text' => 'Skip to comments form', 'title' => 'Skip to the comments form for &#8220;'.$news_array_standard['title'].'&#8221;', 'link' => '#comments-form');
$g_skiplinksArray['content-nav']['link'] = '#image-navigation';

$news_dom_array = array('scripts/jquery.jcarousel.js','scripts/jquery.metadata.js','scripts/jquery.validate.min.js','forms.js','news.js');


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $news_array_standard['title'].' | News | '.$header->title;
$header->className = $currentSection;
$header->className[] = 'hatom';
$header->stylesheet[] = array('file' => 'specifics/news.css', 'media' => 'screen');
$header->stylesheet[] = array('file' => 'specifics/jcarousel.css', 'media' => 'screen');
//$header->stylesheet[] = array('file' => 'specifics/niceforms.css', 'media' => 'screen');
$header->heading = 'News';
$header->dom = array_merge($header->dom,$news_dom_array);
$header->dom = array_merge($header->dom,$logged_in_dom_array);
if(!empty($news_array_standard['description']['summary'])) $header->metaDescription = $news_array_standard['description']['summary'];
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();

//echo $news_sql.'<br /><br />';
//print_r($news_array_standard);
?>

	<div id="content-primary" class="hfeed">
		<?php echo news_display_permalink($news_array_standard); ?>
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
