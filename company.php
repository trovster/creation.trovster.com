<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

$primary_class_array = array('column','double','first');
$primary_id = 'about-creation'; $my_feed_text = '';
$team_list_array = array(); $header_title = '';
$content_class_array = array(); $hot_topics_specific_array = array();
$hot_topics_specific = '';

/* database setup
============================================================================================================= */
/* page information */
$page_sql = "SELECT Created, Updated, TabName, TabURL, Title, Text
			 FROM page_details
			 WHERE TabURL = '/company/'
			 AND Active = '1'
			 LIMIT 0,1";

$page_query = mysqli_query($connect_admin, $page_sql);
$page_array = mysqli_fetch_array($page_query);
$header_title = $page_array['Title'];
$page_text = $page_array['Text'];
$team_list_array[0]['text'] = 'Company';
$team_list_array[0]['link'] = $page_array['TabURL'];
$team_list_array[0]['class'] = array('company','profile','column');
if(!empty($_GET['subsection']) && strtolower($_GET['subsection']==='about')) $team_list_array[0]['class'][] = 'active';
$team_list_array = company_navigation($team_list_array);


if(!empty($_GET['subsection']) && strtolower($_GET['subsection']=='testimonials')) {
	$header_title = 'Testimonials | Company';
	$testimonials_array = testimonials_setup();
	$primary_id = 'about-testimonials';
}
elseif(!empty($_GET['subsection']) && strtolower($_GET['subsection']!=='about')) {
	global $connect_admin;
	$profile_sql = "AND ad.SafeURL = '".mysqli_real_escape_string($connect_admin, $_GET['subsection'])."'";
	$profile_array = profile_setup($profile_sql);

	if(empty($profile_array)) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));

	$header_title = 'Profile of '.$profile_array[0]['author']['full-name'].' | Company';

	$profile_details = profile_details_display($profile_array[0]);
	$profile = profile_display($profile_array[0]);

	$primary_class_array[] = 'my-profile';
	$primary_class_array[] = 'vcard';
	//$primary_class_array[] = 'hresume';
	$content_class_array[] = 'hresume';
	$primary_id = $profile_array[0]['id'];

	// hot topic setup
	$hot_topic_sql_extra = " AND ap.ID = '".mysqli_real_escape_string($connect_admin, $profile_array[0]['author']['id'])."'";
	$hot_topic_sql_limit = 'LIMIT 0,10';

	$status = true;
	if(!empty($_GET['preview']) && $_GET['preview']=='true') $status = false;

	$hot_topics_array = hot_topic_setup($hot_topic_sql_extra,$hot_topic_sql_limit,'',$status);
	if(!empty($hot_topics_array)) $hot_topics_standard = $hot_topics_array[0];

	if(!empty($_GET['hot-topic'])) {
		$hot_topics_specific_sql_extra = $hot_topic_sql_extra;
		$hot_topics_specific_sql_limit = 'LIMIT 0,20';
		$hot_topics_highlight = '';
		$hot_topics_specific_title_middle = 'Archive';

		if(!empty($_GET['date']) && !empty($_GET['type'])) {
			if(!empty($_GET['permalink'])) {
				$hot_topics_highlight = $_GET['permalink'];
			}
			if(!empty($_GET['year']) && strtolower($_GET['type'])=='year') {
				$date_search = formatDate($_GET['date'],'admin-sql-check-year');
				$hot_topics_specific_title_middle = 'Archive for '.$_GET['year'];
			}
			else {
				$date_search = formatDate($_GET['date'],'admin-sql-check');
				$hot_topics_specific_title_middle = formatDate($_GET['date'],'news-archive');
			}
			if(!empty($date_search)) $hot_topics_specific_sql_extra .= " AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $date_search)."'";
		}
		$hot_topics_specific_title = '<h3>Hot Topics <em>'.$hot_topics_specific_title_middle.'</em></h3>'."\n";
		$hot_topics_specific_array = hot_topic_setup($hot_topics_specific_sql_extra,$hot_topics_specific_sql_limit,$hot_topics_highlight,$status);

		if(!empty($hot_topics_highlight)) {
			$hot_topics_search_array = array_search_recursive('active',$hot_topics_specific_array);
			if(count($hot_topics_search_array)==3) {
				$hot_topics_specific_standard = $hot_topics_specific_array[$hot_topics_search_array[0]];
			}
			else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
		}
		if(count($hot_topics_specific_array)==0) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
	}

	// related author setup
	$news_array_standard = related_author_setup($profile_array[0]);
	$setup_feed_author_array = setup_feed_author_information($profile_array[0]['author']['id']);
	$setup_feed_author_hot_topics_array = setup_feed_author_information($profile_array[0]['author']['id'],'hot-topics');
	$setup_feed_author_comments_array = setup_feed_author_information($profile_array[0]['author']['id'],'comments');
	if(!empty($setup_feed_author_array)) {
		$g_filesArray[$profile_array[0]['author']['url'].'-atom'] = $setup_feed_author_array['xml']['atom'];
		$g_filesArray[$profile_array[0]['author']['url'].'-rss'] = $setup_feed_author_array['xml']['rss'];

		if(!empty($setup_feed_author_array['xml']['atom']) && is_file($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml']['atom']['path'].$setup_feed_author_array['xml']['atom']['name'])) {
			$my_feed_info = $setup_feed_author_array['xml']['atom'];
		}
		elseif(!empty($setup_feed_author_array['xml']['rss']) && is_file($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml']['rss']['path'].$setup_feed_author_array['xml']['rss']['name'])) {
			$my_feed_info = $setup_feed_author_array['xml']['rss'];
		}

		if(!empty($my_feed_info)) {
			$my_feed_text = '<a href="'.$my_feed_info['permalink'].'"'.addAttributes(rtrim(rtrim($my_feed_info['title'],'  (Atom)'),' (RSS)'),'',$my_feed_info['class'],'',$my_feed_info['rel'],'','','','',$my_feed_info['mime']).'>';
			$my_feed_text .= 'Subscribe</a>';
		};
	}
	if(!empty($setup_feed_author_hot_topics_array)) {
		$g_filesArray[$profile_array[0]['author']['url'].'-hot-topic-atom'] = $setup_feed_author_hot_topics_array['xml']['atom'];
		$g_filesArray[$profile_array[0]['author']['url'].'-hot-topic-rss'] = $setup_feed_author_hot_topics_array['xml']['rss'];
	}
	if(!empty($setup_feed_author_comments_array)) {
		$g_filesArray[$profile_array[0]['author']['url'].'-comments-atom'] = $setup_feed_author_comments_array['xml']['atom'];
		$g_filesArray[$profile_array[0]['author']['url'].'-comments-rss'] = $setup_feed_author_comments_array['xml']['rss'];
	}
}



/* information setup
============================================================================================================= */
// http://www.businesslink.gov.uk/bdotg/action/home?domain=www.businesslink.gov.uk&target=http://www.businesslink.gov.uk/
$links_array = array(
	array('text' => 'British Library', 'link' => 'http://www.bl.uk'),
	//array('text' => 'Business Link', 'link' => '#'),
	array('text' => 'Buy Fonts', 'link' => 'http://www.atomictype.co.uk'),
	array('text' => 'Chamber of Commerce', 'link' => 'http://www.chamberonline.co.uk'),
	//array('text' => 'Dictionary and Thesaurus', 'link' => 'http://www.webster.com'),
	array('text' => 'Wikipedia', 'link' => 'http://www.wikipedia.org'),
	array('text' => 'Phone Directory (BT)', 'link' => 'http://www.thephonebook.bt.com/publisha.content/en/find/residential/residential_numbers.publisha'),
	array('text' => 'Route planner (RAC)', 'link' => 'http://www.rac.co.uk/web/routeplanner/'),
	array('text' => 'Train Times', 'link' => 'http://www.qjump.co.uk'),
	array('text' => 'Traffic/Weather (BBC)', 'link' => 'http://www.bbc.co.uk'),
	array('text' => 'UK Patent Office', 'link' => 'http://www.patent.gov.uk')
);

$downloads_array_setup = downloads();


/* form setup
============================================================================================================= */



/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $header_title.' | '.$header->title;
$header->className = $currentSection;
$header->stylesheet[] = array('file' => 'specifics/company.css', 'media' => 'screen');
$header->heading = 'Company';
$header->dom = array_merge($header->dom,$logged_in_dom_array);
//$header->metaKeywords = '';
//$header->dom = '';
if(!empty($profile_array) && !empty($profile_array[0]['text']) && !empty($profile_array[0]['text']['corporate'])) {
	$header->metaDescription = $profile_array[0]['author']['full-name'].' – '.$profile_array[0]['info']['role'].': '.formatText($profile_array[0]['text']['corporate'],'output');
}
else $header->metaDescription = $g_company_title.' – '.$g_company_vision.': '.formatText($page_text,'output');
$header->Display();


	echo '<div'.addAttributes('','content-primary',$content_class_array).'>'."\n";
	echo '<div'.addAttributes('',$primary_id,$primary_class_array).'>'."\n";
	if(!empty($hot_topics_specific_array) && is_array($hot_topics_specific_array)) {
		// display the hot-topic information in here...
		echo $profile_details;

		echo '<div'.addAttributes('','hot-topic-select',array('column','news','mini-container','hfeed','vcard','author','selection')).'>';
		echo $hot_topics_specific_title;
		echo news_display_mini($hot_topics_specific_array,'','','summary');
		echo '<!-- end of div id #hot-topic-select -->'."\n";
		echo '</div>'."\n";

		if(!empty($hot_topics_specific_standard)) {
			echo '<div class="column last">'."\n";
			echo news_display_hot_topic($hot_topics_specific_standard);
			echo '</div>'."\n";
		}
	}
	elseif(!empty($profile_array) && is_array($profile_array) && !empty($profile)) {
		// display the profile information
		echo $profile;
	}
	elseif(!empty($testimonials_array) && is_array($testimonials_array)) {
		//echo '<pre>'; print_r($testimonials_array); echo '</pre>';
		echo '<h3>Testimonials</h3>'."\n";
		echo testimonials_display($testimonials_array);
	}
	else {
		echo '<h3 id="vision"><span class="gl-ir"></span>'.$g_company_vision.'</h3>'."\n";
		echo formatText($page_text,'output');
	}
	echo '<!-- end of div id #'.$primary_id.' -->'."\n";
	echo '</div>'."\n";
	?>

	<div id="content-navigation" class="column double last">
		<h3>Content Navigation</h3>
	<?php
	if(!empty($team_list_array) && is_array($team_list_array)) {
		if(!empty($gl_ir) && $gl_ir==true) for($i=0; $i<count($team_list_array); $i++) $team_list_array[$i]['gilder'] = true;
		echo createList($team_list_array);
	}
	?>
	<!-- end of div id #content-navigation -->
	</div>

	<?php
	if(!empty($profile_array) && is_array($profile_array) && !empty($profile)) {
		// display the profile information
		echo '<div'.addAttributes('','latest-posts',array('column','news','mini-container','hfeed','vcard','author','selection')).'>'."\n";
		echo '<h3 class="section-title">Latest Posts <em class="fn">'.$profile_array[0]['author']['full-name'].'</em></h3>'."\n";
		echo image_show($profile_array[0]['image']['small']);
		if(!empty($news_array_standard) && is_array($news_array_standard)) {
			echo news_display_mini($news_array_standard);
			echo $my_feed_text;
		}
		else {
			echo '<p class="none">There are no current posts.</p>'."\n";
		}

		/*
		if(!empty($hot_topics_array) && count($hot_topics_array)>1) {
			echo '<div'.addAttributes('','hot-topic-latest',array('second','news','mini-container','hfeed','vcard','author','selection')).'>';
			echo '<h3 class="section-title">Hot Topics <em class="fn">'.$profile_array[0]['author']['full-name'].'</em></h3>'."\n";
			echo image_show($profile_array[0]['image']['small']);
			echo news_display_mini($hot_topics_array,'','','summary',array(1));
			echo '<!-- end of div id #hot-topic-latest -->'."\n";
			echo '</div>'."\n";
		}
		*/

		echo '<!-- end of div id #latest-posts -->'."\n";
		echo '</div>'."\n";

		if(!empty($hot_topics_specific_array) && is_array($hot_topics_specific_array)) {
			echo '<h3>What are… <em>Hot Topics</em></h3>';
			echo '<blockquote><p>A thought for the moment. Something we recommend. Just a informal chat.</p></blockquote>'."\n";
		}
		elseif(!empty($hot_topics_standard) && is_array($hot_topics_standard)) {
			echo news_display_hot_topic($hot_topics_standard);
		}
	}
	else {
		if(!empty($downloads_array_setup) && is_array($downloads_array_setup)) {
			echo '<div id="company-downloads" class="column">'."\n";
			echo '<h3>Downloads… <em>Helpful documents</em></h3>'."\n";
			echo createList($downloads_array_setup);
			echo '</div>'."\n";
		}
		if(!empty($links_array) && is_array($links_array)) {
			echo '<div id="company-links" class="column last">'."\n";
			echo '<h3>Links… <em>Take a look</em></h3>'."\n";
			echo createList($links_array);
			echo '</div>'."\n";
		}
	}
	?>
	<!-- end of div id #content-primary -->
	</div>

	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
