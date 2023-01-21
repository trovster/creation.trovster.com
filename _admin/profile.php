<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* database setup
============================================================================================================= */
$profile_array = profile_setup('',false,false);

$hot_topic_specific_array = array();
$hot_topic_type = 'new';
$profile_list = array(); $i=0;
$hot_topics_list = array(); $h=0;
$hot_topics_list_saved = array(); $h_s=0;
$update_person = false; $hot_topic_list = false;
foreach($profile_array as $profile_details_array) {
	$profile_list[$i] = array(
		'text' => $profile_details_array['author']['full-name'],
		'link' => '/admin/profile/'.$profile_details_array['identifier'].'/'
	);

	if($profile_details_array['identifier']==$person_details_array['identifier']) {
		$profile_list[$i]['text'] .= ' ← You!';
	}
	if(!empty($_GET['person']) && is_numeric($_GET['person']) && $_GET['person']==$profile_details_array['identifier']) {
		$profile_list[$i]['class'][] = 'active';
		$update_person = true; $hot_topic_list = true;
		$edit_details_array = $profile_details_array;
		$edit_details_array['person-id'] = $edit_details_array['identifier'];
		$new_hot_topic_link = '/admin/profile/'.$edit_details_array['identifier'].'/hot-topic/';
		$hot_topic_specific_array['edit-link'] = $new_hot_topic_link;

		// hot-topic setup
		$hot_topics_array = hot_topic_setup("AND ap.ID = '".mysqli_real_escape_string($connect_admin, $edit_details_array['identifier'])."'",'','',false);
		foreach($hot_topics_array as $hot_topic_details_array) {

			if($hot_topic_details_array['status']==1) {
				// disabled
				$hot_topics_list_type = 'hot_topics_list_saved';
				$ht = 'h_s';
			}
			else {
				$hot_topics_list_type = 'hot_topics_list';
				$ht = 'h';
			}

			${$hot_topics_list_type}[${$ht}] = array(
				'text' => $hot_topic_details_array['title'],
				'link' => '/admin/profile/'.$edit_details_array['identifier'].'/hot-topic/'.$hot_topic_details_array['id'].'/'
			);
			$hot_topic_specific_array['person-id'] = $edit_details_array['identifier'];
			if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='hot-topic') {
				$update_hot_topic = true;

				if(!empty($_GET['update']) && is_numeric($_GET['update'])) {
					if($_GET['update']==$hot_topic_details_array['id']) {
						${$hot_topics_list_type}[${$ht}]['class'][] = 'active';
						$hot_topic_type = 'update';
						$hot_topic_specific_array = $hot_topic_details_array;
						$hot_topic_specific_array['edit-link'] = $new_hot_topic_link.$hot_topic_details_array['id'].'/';
					}
					//else header('location: /admin/profile/'.$profile_details_array['identifier'].'/hot-topic/');
				}
			}
			${$ht}++;
		}
	}
	$i++;
}


/* information setup
============================================================================================================= */
$hot_topics_form_output = ''; $profile_text_form_output = '';
if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='password') {
	if(!empty($_GET['redirect'])) {
		// redirect to this persons password management.
		header('Location: '.$change_password_link);
	}
	$profile_password_form_output = profile_password_form($edit_details_array);
	$hot_topic_list = false;
	$admin_js_array[] = 'scripts/jquery.forms.pstrength.js';
}
elseif(!empty($update_hot_topic) && $update_hot_topic==true) {
	$hot_topics_form_output = hot_topics_form($hot_topic_type,$hot_topic_specific_array);
}
elseif(!empty($update_person) && $update_person==true) {
	$profile_text_form_output = profile_text_form($edit_details_array);
}



/* validate / error form setup & post to update
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Profile | Administration area | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Administration';
$header->stylesheet[] = $admin_css_array;
$header->stylesheet = array_merge($header->stylesheet,$admin_css_array);
$header->dom = array_merge($header->dom,$admin_js_array);
$header->Display();
?>

	<div id="content-primary">

		<div id="introduction">
			<?php echo profile_admin_display($person_details_array,$greet_array,$greet_tagline_array); ?>
			<div id="section-specific" class="column double last">
				<h3>Profile</h3>
				<p>Update your profile text and manage your hot topics.</p>
			</div>
		</div>

		<div class="column">
			<?php
			if(!empty($profile_list)) {
				echo '<div class="info-box">'."\n";
				echo '<h3>Select Employee</h3>';
				echo createList($profile_list);
				echo '</div>';

				if($update_person==true && $hot_topic_list==true) {
					$hot_topic_class = array('new');
					if(!empty($_GET['update']) && strtolower($_GET['update'])=='new') $hot_topic_class[] = 'active';

					echo '<div class="info-box second">'."\n";
					echo '<h3>Hot Topics</h3>';
					echo '<p'.addAttributes('','',$hot_topic_class).'><a href="'.$new_hot_topic_link.'">Add a new Hot Topic</a></p>'."\n";

					if(!empty($hot_topics_list)) {
						echo createList($hot_topics_list);
					}
					if(!empty($hot_topics_list_saved)) {
						echo '<h4>Saved Hot Topics</h4>'."\n";
						echo createList($hot_topics_list_saved);
					}
					echo '</div>';
				}
			}
			?>
		</div>

		<?php
		if(!empty($hot_topics_form_output)) {
			echo $hot_topics_form_output;
		}
		elseif(!empty($profile_text_form_output)) {
			echo $profile_text_form_output;
		}
		elseif(!empty($profile_password_form_output)) {
			echo $profile_password_form_output;
		}
		?>

	<!-- end of div id #content-primary -->
	</div>

	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
