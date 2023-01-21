<?php
function age_from_dob($dob) {
	$birth = explode('-', $dob);
	$age = date('Y')-$birth[0];
	if(($birth[1] > date('m')) || ($birth[1] == date('m') && date('d') < $birth[2])) $age -= 1;
	return $age;
}
function company_navigation($team_list_array='') {
	global $connect_admin;
	$sql = "SELECT p.Created AS Profile_Created,
			p.Updated AS Profile_Updated,
			p.Title AS Profile_Title,
			p.Text_Corporate AS Profile_Corporate_Text,
			p.Text_Personal AS Profile_Personal_Text,
			p.DOB AS Profile_DOB,
			ad.ID AS Author_ID,
			ad.Forename as Author_Forename,
			ad.Surname AS Author_Surname,
			ad.Email AS Author_Email,
			CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name
			FROM author_profile AS p
			LEFT JOIN author_details AS ad ON ad.ID = p.Author_Detail_ID
			WHERE ad.Active = '1'
			ORDER BY ad.Forename = 'Leigh' DESC, ad.Surname = 'Webley' DESC, ad.Surname = 'Rees' DESC, ad.Surname ASC, ad.Forename ASC";

	$query = mysqli_query($connect_admin, $sql);
	$name_check_array = array(); $i=0;
	if(!empty($team_list_array) && is_array($team_list_array)) $i = count($team_list_array);
	while($array = mysqli_fetch_array($query)) {
		$name_array = name($array['Author_Forename'],$array['Author_Surname']);
		if(!in_array($name_array['full-name'],$name_check_array)) {
			$j=0;
			$name_check_array[] = $name_array['full-name'];
			$team_list_array[$i]['text'] = $name_array['full-name'];
			$team_list_array[$i]['link'] = profile_url($name_array);
			$team_list_array[$i]['class'] = array($name_array['url'],'column');
			$team_list_array[$i]['identifier'] = $array['Author_ID'];

			if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])===strtolower(formatText($name_array['full-name'],'url'))) {
				$team_list_array[$i]['class'][] = 'active';
			}
			$i++;
		}
	}
	return $team_list_array;
}
function profile_setup($sql_extra='',$show_news=true,$show_hottopics=true) {
	global $connect_admin;

	$sql = "SELECT p.ID,
			p.Created AS Profile_Created,
			p.Updated AS Profile_Updated,
			p.Title AS Profile_Title,
			p.Text_Corporate AS Profile_Corporate_Text,
			p.Text_Personal AS Profile_Personal_Text,
			p.DOB AS Profile_DOB,
			p.Joined AS Profile_Joined,
			ad.ID AS Author_ID,
			ad.Forename as Author_Forename,
			ad.Surname AS Author_Surname,
			ad.Email AS Author_Email,
			CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name
			FROM author_profile AS p
			LEFT JOIN author_details AS ad ON ad.ID = p.Author_Detail_ID
			WHERE ad.Active = 1
			".$sql_extra;

	$query = mysqli_query($connect_admin, $sql);
	$i=0; $name_check_array = array(); $profile_array = array();
	while($array = mysqli_fetch_array($query)) {
		$name_array = name($array['Author_Forename'],$array['Author_Surname']);
		if(!in_array($name_array['full-name'],$name_check_array)) {
			$profile_array[$i] = array(
				'identifier' => $array['ID'],
				'author' => $name_array,
				'permalink' => array(
					'safe' => profile_url($name_array)
				),
				'created' => $array['Profile_Created'],
				'updated' => $array['Profile_Updated'],
				'info' => array(
					'role' => $array['Profile_Title'],
					'age' => age_from_dob($array['Profile_DOB']),
					'dob' => $array['Profile_DOB'],
					'joined' => news_date_setup($array['Profile_Joined']),
					'email' => validate($array['Author_Email'],'email'),
					'birthday' => 0
				),
				'image' => array(
					'large' => image_setup('0',$name_array['full-name'],'gif','/images/icons/large/','Photo of '.$name_array['full-name'],'','',array('photo')),
					'small' => image_setup('0',$name_array['full-name'],'gif','/images/icons/small/','Photo of '.$name_array['full-name'],'','',array('logo')),
					'gravatar' => image_setup('0',$name_array['full-name'],'gif','/images/icons/','Gravatar for '.$name_array['full-name'],'','',array('logo','gravatar'))
				),
				'text' => array(
					'corporate' => $array['Profile_Corporate_Text'],
					'personal' => $array['Profile_Personal_Text']
				),
				'details' => array(
					array(
						'text' => 'Role:',
						'definition' => $array['Profile_Title'],
						'class' => array('role')
					),
					array(
						'text' => 'Age:',
						'definition' => age_from_dob($array['Profile_DOB']),
						'class' => array('age')
					),
					array(
						'text' => 'Email:',
						'definition' => '<a href="mailto:'.validate($array['Author_Email'],'email').'" class="email">'.validate($array['Author_Email'],'email').'</a>',
						'class' => array()
					),
				),
			);

			if(preg_replace('/[0-9]{4}-/','',$array['Profile_DOB'])==preg_replace('/[0-9]{4}-/','',formatDate(time('NOW'),'iso8601'))) {
				// it's their birthday TODAY!!!!
				$profile_array[$i]['info']['birthday'] = 1;
				$profile_array[$i]['details'][1]['definition'] .= ' - <strong class="birthday today">It\'s my Birthday!</strong>';
			}

			$profile_array[$i]['id'] = 'profile-'.url_encode($profile_array[$i]['author']['full-name']);
			$profile_array[$i]['permalink']['title'] = 'Profile for '.$profile_array[$i]['author']['full-name'];
			$profile_array[$i]['permalink']['anchor-not-closed-no-text'] = '<a href="'.$profile_array[$i]['permalink']['safe'].'"'.addAttributes($profile_array[$i]['permalink']['title'],'',array('fn','url')).'>';
			$profile_array[$i]['permalink']['anchor'] = $profile_array[$i]['permalink']['anchor-not-closed-no-text'].$profile_array[$i]['author']['full-name'].'</a>';
			$profile_array[$i]['author']['id'] = $array['Author_ID'];
			$profile_array[$i]['author']['n-optimised'] = $profile_array[$i]['author']['full-name'];
			$profile_array[$i]['author']['url'] = $profile_array[$i]['permalink'];
			$profile_array[$i]['author']['email'] = validate($array['Author_Email'],'email');
			$profile_array[$i]['author']['md5'] = md5($profile_array[$i]['author']['email']);

			$profile_array[$i]['xml'] = setup_feed_author_information($profile_array[$i]['author']['id']);
			if($show_news==true) $profile_array[$i]['articles'] = related_author_setup($profile_array[$i]);
			if($show_hottopics==true) $profile_array[$i]['hot-topics'] = hot_topic_setup(" AND ap.ID = '".$profile_array[$i]['author']['id']."'");
			$i++;
		}
	}

	return $profile_array;
}
function profile_details_display($array) {
	$output = '';
	$output .= '<div'.addAttributes('','',array('professional','information','description')).'>'."\n";
	$output .= '<h3 class="fn">'.$array['author']['full-name'].'</h3>'."\n";
	$output .= '<a href="#branding" class="include"></a>'."\n";
	$output .= createDefinitionList($array['details']);
	$output .= '</div>'."\n";
	$output .= '<div'.addAttributes('','',array('logo')).'>'.image_show($array['image']['large']).'</div>'."\n";
	return $output;
}
function profile_admin_display($array,$prefix='Welcome',$tagline='Welcome to the administration area') {

	if(is_array($prefix)) {
		$total = count($prefix);
		$rand = rand(0,$total-1);
		if(!empty($tagline) && is_array($tagline) && $total==count($tagline)) {
			if(!empty($_SESSION['login']['tag-id'])) $rand = $_SESSION['login']['tag-id'];
			else $_SESSION['login']['tag-id'] = $rand;
			$tagline = $tagline[$rand];
		}
		$prefix = $prefix[$rand];
	}

	$output = '';
	$output .= '<div'.addAttributes('','user-details',array('vcard','column','double')).'>'."\n";
	$output .= image_show($array['image']['gravatar']);
	$output .= '<h3>'.$prefix.' <span class="fn">'.$array['author']['forename'].'</span>!</h3>'."\n";
	$output .= '<p>'.$tagline.'</p>'."\n";
	$output .= '</div>'."\n";
	return $output;
}
function profile_display($array) {
	if(empty($array) || !is_array($array)) return;
	$output = '';
	$output .= profile_details_display($array);
	$output .= '<div'.addAttributes('','',array('description','corporate','note','summary')).'>'.formatText($array['text']['corporate'],'output').'</div>'."\n";
	$output .= '<div'.addAttributes('','',array('description','personal','note')).'>'."\n";
	$output .= '<h3>Personal</h3>'."\n";
	$output .= formatText($array['text']['personal'],'output');
	$output .= '</div>'."\n\n";

	return $output;
}

function hot_topic_setup($sql_extra='',$sql_limit='',$active='',$status=true) {
	global $connect_admin;
	global $now_timestamp;

	if($status==true) {
		$sql_extra .= " AND d.Status = '1'";
	}

	$hot_topics_sql = "SELECT
						d.ID AS Detail_ID,
						d.Created AS Detail_Created,
						d.Updated AS Detail_Updated,
						d.Title AS Detail_Title,
						d.Safe_URL AS Detail_Safe_URL,
						d.Summary AS Detail_Summary,
						d.Description AS Detail_Description,
						d.Status AS Detail_Status,

						d.Image_Alt_Text AS Detail_Image_Alt_Text,
						d.Image_Title AS Detail_Image_Title,
						d.Image_Extension AS Detail_Image_Extension,
						d.Image_Link AS Detail_Image_Link,

						ad.ID AS Author_ID,
						ad.Forename as Author_Forename,
						ad.Surname AS Author_Surname,
						ad.Email AS Author_Email,
						CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name,
						ap.Title AS Author_Title,
						s.Section AS Section_Name
						FROM author_topics AS d
						LEFT JOIN author_profile AS ap ON ap.ID = d.Author_Detail_ID
						LEFT JOIN author_details AS ad ON ad.ID = d.Author_Detail_ID
						LEFT JOIN news_section AS s ON s.ID = 4
						WHERE ad.Active = '1'
						".$sql_extra."
						ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
						".$sql_limit;

	//echo $hot_topics_sql;

	$hot_topics_query = mysqli_query($connect_admin, $hot_topics_sql);
	$return = array(); $i=0;
	while($hot_topics_array = mysqli_fetch_array($hot_topics_query)) {
		$return[$i] = news_setup($hot_topics_array,'hot');
		$return[$i]['image']['text']['alt'] = formatText($hot_topics_array['Detail_Image_Alt_Text']);
		$return[$i]['image']['text']['title'] = formatText($hot_topics_array['Detail_Image_Title']);
		$return[$i]['image']['link'] = $hot_topics_array['Detail_Image_Link'];
		//$return[$i]['xml'] = '';
		if(!empty($active) && rtrim($hot_topics_array['Detail_Safe_URL'],'/')==$active) {
			$return[$i]['class'][] = 'active';
		}
		$i++;
	}
	return $return;
}

function downloads($sql_extra='',$sql_limit='',$active='') {
	global $connect_admin;

	$sql = "SELECT dd.ID AS Download_ID,
			dd.Title AS Download_Title,
			dd.Safe_URL as Download_Safe_URL,
			dd.Extension as Download_Extension
			FROM downloads_details AS dd
			WHERE dd.Active = 1
			AND dd.ID != '10'
			ORDER BY dd.Title ASC, dd.Created DESC";

	$query = mysqli_query($connect_admin, $sql);
	$downloads_array_setup = array();
	$download_path = '/_files/downloads/'; $i=0;
	while($downloads_array = mysqli_fetch_array($query)) {
		$file_array = file_setup($downloads_array['Download_ID'],rtrim($downloads_array['Download_Safe_URL'],'/'),$downloads_array['Download_Extension'],$download_path);
		preg_match('/^(Glossary)/i',$downloads_array['Download_Title'],$glossary_array);
		$downloads_array_setup[$i] = array(
			'text' => $downloads_array['Download_Title'],
			'link' => $file_array['full-path'],
			'class' => array($file_array['type'])
		);
		if(strtolower($file_array['type'])=='pdf') {
			$downloads_array_setup[$i]['mime'] = 'application/pdf';
		}
		if(!empty($glossary_array) && is_array($glossary_array)) {
			$downloads_array_setup[$i]['rel'][] = 'glossary';
		}
		$i++;
	}
	return $downloads_array_setup;
}

function testimonials_setup($sql_extra='',$sql_limit='',$active='') {
	global $connect_admin;

	$sql = "SELECT
			t.ID,
			t.Forename, t.Surname,
			CONCAT_WS(' ',t.Forename,t.Surname) AS Full_Name,
			t.Role,
			t.Description,

			pc.Company AS Company_Title,
			pc.Safe_URL AS Company_Safe_URL,
			pc.Description AS Company_Description

			FROM testimonials AS t
			LEFT JOIN portfolio_company AS pc ON pc.ID = t.Portfolio_Company_ID
			LEFT JOIN portfolio_details AS pd ON pd.ID = t.Portfolio_Detail_ID

			WHERE t.Active = '1' AND pc.Active = '1'
			".$sql_extra."
			ORDER BY pc.Company DESC, t.Updated DESC
			".$sql_limit;

	$query = mysqli_query($connect_admin, $sql);
	$return_array = array(); $i=0;
	while($array = mysqli_fetch_array($query)) {
		$return_array[$i] = array(
			'identifier' => $array['ID'],
			'text' => $array['Description'],
			'author' => name($array['Forename'],$array['Surname']),
			'company' => portfolio_setup_company($array)
		);
		$return_array[$i]['author']['title'] = $array['Role'];
		$i++;
	}
	return $return_array;
}
function testimonials_display($array) {
	if(empty($array) || !is_array($array)) return false;
	$return = '';

	foreach($array as $a) {
		$class_array = array('testimonial','hcite','vcard','author');
		$return .= '<div'.addAttributes('','',$class_array).'>'."\n";

		//$return .= '<div'.addAttributes('','',array('vcard','author')).'>'."\n";
		$return .= '<h4><cite class="fn">'.formatText($a['author']['full-name']).'</cite></h4>'."\n";
		$return .= '<p class="info"><span class="role">'.formatText($a['author']['title']).'</span> – <span class="org">'.formatText($a['company']['title']).'</span></p>'."\n";
		//$return .= '</div>'."\n";

		$return .= '<blockquote class="description">';
		$return .= formatText('"'.$a['text'].'"','output');
		$return .= '</blockquote>'."\n";
		$return .= '</div>'."\n\n";
	}

	return $return;
}
?>
