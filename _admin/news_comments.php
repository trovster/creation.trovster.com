<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* basic setup
============================================================================================================= */
$news_link = '/admin/news/comments/';
$news_link_class = array('new');
$news_update_specific_array = array('section' => 'news', 'section-id' => 1);
$page_heading = 'News Comments';
$page_tagline = 'Add and manage news comments.';
$page_new_text = 'Add a new comment';


/* database setup
============================================================================================================= */
$setup_array = array('meh' => 'yeh'); $sql_order = 'c.Created DESC';
$comments_array = news_comments_setup($setup_array,$sql_order,'',true);
$comments_array_total = count($comments_array);

$news_comments_person_list = array(); $c_p=0;
$news_comments_author_list = array(); $c_a=0;
$news_comments_list = array(); $c_l=0;
$news_comments_list_disabled = array(); $c_d=0;
$news_comments_list_spam = array(); $c_s=0;

for($a=0; $a<$comments_array_total; $a++) {
	$list_string = '';
	$comment_array = $comments_array[$a];
	
	if(!empty($comment_array['author']['admin'])) {
		if($comment_array['author']['admin']['id']==$person_array['ID']) {
			$i = $c_p; $list_string = 'news_comments_person_list';
			$c_p++;
		}
		else {
			$i = $c_a; $list_string = 'news_comments_author_list';
			$c_a++;
		}
	}
	else {
		if($comment_array['spam']==2) {
			$i = $c_s; $list_string = 'news_comments_list_spam';
			$c_s++;
		}
		elseif($comment_array['status']==1) {
			$i = $c_d; $list_string = 'news_comments_list_disabled';
			$c_d++;
		}
		else {
			$i = $c_l; $list_string = 'news_comments_list';
			$c_l++;
		}
	}
	
	if(!empty($list_string)) {
		${$list_string}[$i] = array(
			'text' => $comment_array['id'],
			'link' => $news_link.$comment_array['id'].'/'
		);
		$comments_array[$a]['spam-link'] = ${$list_string}[$i]['link'].'spam/';
		if(!empty($_GET['update']) && is_numeric($_GET['update']) && $_GET['update']==$comment_array['id']) {
			${$list_string}[$i]['class'][] = 'active';
			$active_id = $a;
		}
	}
}
// unset($news_comments_list_spam);

/* information setup
============================================================================================================= */
if(!empty($_GET['update'])) {
	if(is_numeric($_GET['update'])) {
		$news_update_type = 'update';
		$news_articles_specific_array = $comments_array[$active_id];
		$news_update_specific_array = array_merge($news_update_specific_array,$news_articles_specific_array);
		$news_update_specific_array['edit-link'] = $news_link.$news_update_specific_array['id'].'/';
		$news_update_specific_array['person-id'] = $news_update_specific_array['id'];
	}
	elseif(strtolower($_GET['update'])=='new') {
		$news_link_class[] = 'active';
		$news_update_type = 'new';
		$news_update_specific_array['edit-link'] = $news_link;
		$news_update_specific_array['person-id'] = $person_details_array['identifier'];
	}
}


/* validate / error form setup & post to update
============================================================================================================= */
$news_form_output = news_form_comments($news_update_type,$news_update_specific_array);
$currentSection[] = 'comments';

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'News Comments | Administration area | '.$header->title;
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
			echo '<h3>Your Comments</h3>';
			echo '<p'.addAttributes('','',$news_link_class).'><a href="'.$news_link.'">'.$page_new_text.'</a></p>'."\n";
			
			if(!empty($news_comments_person_list)) {
				echo '<h4>Your Comments ('.count($news_comments_person_list).')</h4>'."\n";
				echo createList($news_comments_person_list);
			}
			if(!empty($news_comments_author_list)) {
				echo '<h4>Author Comments ('.count($news_comments_author_list).')</h4>'."\n";
				echo createList($news_comments_author_list);
			}
			if(!empty($news_comments_list)) {
				echo '<h4>Other Comments ('.count($news_comments_list).')</h4>'."\n";
				echo createList($news_comments_list);
			}
			if(!empty($news_comments_list_disabled)) {
				echo '<h4>Disabled Comments ('.count($news_comments_list_disabled).')</h4>'."\n";
				echo createList($news_comments_list_disabled);
			}
			if(!empty($news_comments_list_spam)) {
				echo '<h4>Spam Comments ('.count($news_comments_list_spam).')</h4>'."\n";
				echo createList($news_comments_list_spam);
			}
			
			echo '</div>';
			?>
		</div>
		
		<?php echo $news_form_output; ?>
		
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>