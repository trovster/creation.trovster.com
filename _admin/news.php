<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* basic setup
============================================================================================================= */
$news_link = '/admin/news/';
$news_link_class = array('new');
$news_update_specific_array = array('section' => 'news', 'section-id' => 1);
$page_heading = 'News';
$page_tagline = 'Add and manage news articles.';
$page_new_text = 'Add a new article';

if(strtolower($_GET['type'])=='comments') {
	die(require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/news_comments.php'));
}
elseif(strtolower($_GET['type'])=='extra') {
	$extra_updates = true;
	$news_link .= 'extra/';
	$page_heading = 'Extra News';
	$page_tagline = 'Add and manage "extra" articles.';
	$page_new_text = 'Add a new extra article';
	
	$news_section = 3;
	$news_update_specific_array['section'] = 'extra';
	$news_update_specific_array['section-id'] = 3;
}



/* database setup
============================================================================================================= */
$news_articles_user_array = related_setup('','',false,$news_update_specific_array['section-id']);

$news_articles_user_list_active = array(); $u_a=0;
$news_articles_user_list_disabled = array(); $u_d=0;
$news_articles_general_list_active = array(); $g_a=0;
$news_articles_general_list_disabled = array(); $g_d=0;

if(!empty($news_articles_user_array)) {
	foreach($news_articles_user_array as $news_article) {
		if(empty($news_article['title'])) continue;
		
		if($news_article['status']==1) {
			if($news_article['author']['id']==$person_details_array['identifier']) {
				$i = $u_d; $news_list_string = 'news_articles_user_list_disabled';
				$u_d++;
			}
			else {
				$i = $g_d; $news_list_string = 'news_articles_general_list_disabled';
				$g_d++;
			}
		}
		else {
			if($news_article['author']['id']==$person_details_array['identifier']) {
				$i = $u_a; $news_list_string = 'news_articles_user_list_active';
				$u_a++;
			}
			else {
				$i = $g_a; $news_list_string = 'news_articles_general_list_active';
				$g_a++;
			}
		}
		
		${$news_list_string}[$i] = array(
			'text' => $news_article['title'],
			'link' => $news_link.$news_article['id'].'/'
		);
		if(!empty($_GET['update']) && is_numeric($_GET['update']) && $_GET['update']==$news_article['id']) {
			${$news_list_string}[$i]['class'][] = 'active';
			$update = true;
		}
	}
}



/* information setup
============================================================================================================= */
if(!empty($_GET['update'])) {
	if(is_numeric($_GET['update'])) {
		$news_update_type = 'update';
		$news_sql_extra_specific = " AND d.ID = '".mysql_real_escape_string($_GET['update'])."'";
		$news_articles_specific_array = related_setup($news_sql_extra_specific,'',false,$news_update_specific_array['section-id']);
		$news_update_specific_array = array_merge($news_update_specific_array,$news_articles_specific_array[0]);
		$news_update_specific_array['edit-link'] = $news_link.$news_update_specific_array['id'].'/';
		$news_update_specific_array['person-id'] = $news_update_specific_array['author']['id'];
		
		$news_update_specific_array['images'] = news_setup_images($news_update_specific_array,true);
		$news_update_specific_array['images'] = cleanArray($news_update_specific_array['images']);
		
		// get the images...
	}
	elseif(strtolower($_GET['update'])=='new') {
		$news_link_class[] = 'active';
		$news_update_type = 'new';
		$news_update_specific_array['edit-link'] = $news_link;
		$news_update_specific_array['person-id'] = $person_details_array['identifier'];
	}
}

//news_form();

/* validate / error form setup & post to update
============================================================================================================= */
$news_form_output = news_form($news_update_type,$news_update_specific_array);

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $page_heading.' | Administration area | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Administration';
$header->stylesheet = array_merge($header->stylesheet,$admin_css_array);
$header->dom = array_merge($header->dom,$admin_js_array);
$header->Display();
?>

	<div id="content-primary">
	
		<div id="introduction">
			<?php echo profile_admin_display($person_details_array,$greet_array,$greet_tagline_array); ?>
			<div id="section-specific" class="column double last">
				<h3><?php echo $page_heading; ?></h3>
				<p><?php echo $page_tagline; ?></p>
			</div>
		</div>
		
		<div class="column">
			<?php
			echo '<div class="info-box">'."\n";
			echo '<h3>Your Articles</h3>';
			echo '<p'.addAttributes('','',$news_link_class).'><a href="'.$news_link.'">'.$page_new_text.'</a></p>'."\n";
			if(!empty($news_articles_user_list_active)) echo createList($news_articles_user_list_active);
			if(!empty($news_articles_user_list_disabled)) {
				echo '<h4>Saved Articles</h4>'."\n";
				echo createList($news_articles_user_list_disabled);
			}
			echo '</div>';
			
			if(!empty($news_articles_general_list_active) || !empty($news_articles_general_list_disabled)) {
				echo '<div class="info-box second">'."\n";
				echo '<h3>Other Articles</h3>'."\n";
				if(!empty($news_articles_general_list_active)) echo createList($news_articles_general_list_active);
				if(!empty($news_articles_general_list_disabled)) {
					echo '<h4>Saved Articles</h4>'."\n";
					echo createList($news_articles_general_list_disabled);
				}
				echo '</div>';
			}
			?>
		</div>
		
		<?php echo $news_form_output; ?>
		
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>