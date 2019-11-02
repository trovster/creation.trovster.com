<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
$sql_extra = '';
if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='archive' && !empty($_GET['month'])) {
	$sql_extra = " AND d.Created LIKE '".mysql_real_escape_string($_GET['month'])."%'";
}

$news_sql = "SELECT
			 d.ID AS Detail_ID,
			 d.Created AS Detail_Created,
			 d.Updated AS Detail_Updated,
			 d.Title AS Detail_Title,
			 d.Safe_URL AS Detail_Safe_URL,
			 d.Summary AS Detail_Summary,
			 d.Description AS Detail_Description,
			 d.Active AS Detail_Status,
			 d.Comments AS Comments_Active,
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
			 WHERE d.Active = '1'
			 AND d.News_Section_ID = '1'
			 ".$sql_extra."
			 GROUP BY d.ID
			 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
			 LIMIT 0,6";
			 
$news_query = mysqli_query($connect_admin, $news_sql);

$news_array_setup = array(); $i=0;
while($news_array = mysqli_fetch_array($news_query)) {
	$news_array_setup[$i] = news_setup($news_array);
	if(!empty($_GET['permalink']) && strtolower($_GET['permalink'])==trim($news_array_setup[$i]['permalink']['safe'],'/')) {
		$active_article = $news_array_setup[$i];
		$news_array_setup[$i]['class'][] = 'active';
	}
	elseif(empty($_GET['permalink']) && $i==0) {
		$active_article = $news_array_setup[$i];
		$news_array_setup[$i]['class'][] = 'active';
	}
	$i++;
}
if(empty($active_article)) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));


/* information setup
============================================================================================================= */
$style_block = '';
$content_navigation_array = news_display_content_navigation();
if(!empty($content_navigation_array['stylesheet'])) $style_block = $content_navigation_array['stylesheet'];

/* form setup
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'News | '.$header->title;
$header->className = $currentSection;
$header->className[] = 'hatom';
$header->stylesheet[] = array('file' => 'specifics/news.css', 'media' => 'screen');
$header->stylesheetBlock = $style_block;
$header->heading = 'News';
$header->dom = array_merge($header->dom,$logged_in_dom_array);
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
//$header->dom = '';
$header->Display();
?>

	<div id="content-primary" class="hfeed">
		
	<?php echo news_display_summary($active_article,'',array('column','double','first','hfeed','news')); ?>
	
	<div id="content-navigation" class="column double last">
		<h3>Content Navigation</h3>
		<?php if(!empty($content_navigation_array['navigation'])) echo createList($content_navigation_array['navigation']); ?>
	<!-- end of div id #content-navigation -->
	</div>
	
	<?php	
	echo '<div'.addAttributes('','',array('column','news','mini-container','hfeed','vcard','author','selection')).'>'."\n";
	echo '<h3>More… <em>News</em></h3>'."\n";
	echo news_display_mini($news_array_setup);
	echo '</div>'."\n";
	
	echo '<div'.addAttributes('','news-archives',array('column','last','news')).'>'."\n";

		echo '<div'.addAttributes('','',array()).'>'."\n";
		echo '<h3>News Archive <em>2007</em></h3>'."\n";
		echo news_display_archive_months();
		echo '</div>'."\n";
		
		/*
		echo '<div'.addAttributes('','',array('second')).'>'."\n";
		echo '<h3>Tag Archive</h3>'."\n";
		echo news_display_archive_tags();
		echo '</div>'."\n";
		*/
		
		$subscribe_array = array();
		foreach($g_filesArray as $key => $rss_feed_array) {
			$check_files[] = $key;
			if(in_array('latest-news-atom',$check_files) && $key=='latest-news-rss') continue;
			if(in_array('latest-comments-atom',$check_files) && $key=='latest-comments-rss') continue;
			if(in_array('latest-hot-topics-atom',$check_files) && $key=='latest-hot-topics-rss') continue;
			if(strtolower($rss_feed_array['type'])!='xml') continue;
			if(!is_file($_SERVER['DOCUMENT_ROOT'].$rss_feed_array['path'].$rss_feed_array['name'])) continue;
			$subscribe_array[] = array(
				'text' => ltrim(rtrim(rtrim($rss_feed_array['title'],'  (Atom)'),'  (RSS)'),'Creation: '),
				'link' => $rss_feed_array['permalink'],
				'mime' => $rss_feed_array['mime'],
				'rel' => $rss_feed_array['rel'],
				'class' => $rss_feed_array['class']
			);
		}
		
		if(!empty($subscribe_array)) {
			echo '<div'.addAttributes('','news-subscribe',array('second','subscribe','feed')).'>'."\n";
			echo '<h3>Subscribe to… <em>Feeds</em></h3>'."\n";
			echo createList($subscribe_array);
			echo '</div>'."\n";
		}
	
	echo '<!-- end of div id #news-archives -->'."\n";
	echo '</div>'."\n";
	?>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>