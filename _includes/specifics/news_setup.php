<?php
/* news-related functions
============================================================================================================= */
function news_permalink_setup($id,$date,$tab,$section,$title,$profile='') {
	if(empty($id) || !is_numeric($id) || empty($date) || empty($tab) || empty($section) || empty($title)) return NULL;

	$return = array(); $url_array = array();
	$title_type = 'full article';
	$class_array = array('url','permalink','bookmark'); $rel_array = array('bookmark');
	$title_attr = '&#8220;'.$title.'&#8221;';
	if(strtolower($section)=='hot-topic' && !empty($profile)) {
		$url_array[] = $profile;
		$url_array[] = formatDate($date,'long-url');
		$url_array[] = $tab;
		//$return['link'] = $profile.formatDate($date,'long-url').$tab;
		$class_array[] = 'hot-topic';
	}
	elseif(strtolower($section)=='eshot') {
		$url_array[] = 'eshots';
		$url_array[] = formatDate($date,'eshot-url');
		$url_array[] = $tab;
		//$return['link'] = '/eshots/'.formatDate($date,'eshot-url').$tab;
		$title_type = 'eshot';
		$class_array[] = 'eshot';
	}
	elseif(strtolower($section)=='tags') {
		$url_array[] = 'news';
		$url_array[] = 'tags';
		$url_array[] = $tab;
		//$return['link'] = '/news/tags/'.$tab;
		$title_type = 'articles';
		$rel_array[] = 'tag';
		$title_attr = 'the tag &#8220;'.$title.'&#8221;';
	}
	else {
		//$url_array[] = 'news';
		if($section=='articles') $section = 'news';
		$url_array[] = $section;
		$url_array[] = formatDate($date,'long-url');
		$url_array[] = $tab;
		//$return['link'] = '/news/'.$section.formatDate($date,'long-url').$tab;
	}
	$link_title = 'Read the '.$title_type.' for '.$title_attr;

	$return['safe'] = $tab;
	$return['array'] = $url_array;
	$return['link'] = url_create($url_array);
	$return['identifier'] = 'id-'.$id.'-'.trim($tab,'/');
	$return['atom-id'] = 'tag:'.preg_replace('#http://(www.)?#','','http://creation.trovster.com').','.formatDate($date,'iso8601').':'.$return['link'];
	$return['anchor-not-closed-no-text'] = '<a href="'.$return['link'].'"'.addAttributes($link_title,'',$class_array,$rel_array).'>';
	$return['anchor-not-closed'] = $return['anchor-not-closed-no-text'].$title;
	$return['anchor'] = $return['anchor-not-closed'].'</a>';

	$class_array[] = 'preview';
	$return['preview']['link'] = $return['link'].'?preview=true';
	$return['preview']['anchor-not-closed-no-text'] = '<a href="'.$return['preview']['link'].'"'.addAttributes($link_title,'',$class_array,$rel_array).'>';
	$return['preview']['anchor-not-closed'] = $return['preview']['anchor-not-closed-no-text'].$title;
	$return['preview']['anchor'] = $return['preview']['anchor-not-closed'].'</a>';

	//$return['continue']['link'] = '/news/'.$section.$tab;
	//$return['continue']['anchor'] = '<a href="'.$return['continue']['link'].'">'.$title.'</a>';

	return $return;
}
function news_date_setup($date,$date_old='') {
	$return = array();
	$return['comments'] = formatDate($date,'comments');
	$return['long'] = formatDate($date,'long-date');
	$return['full'] = formatDate($date,'full-date');
	$return['short'] = formatDate($date,'short-date');
	$return['iso'] = formatDate($date,'iso');
	$return['sql'] = formatDate($date,'sql');
	$return['rfc'] = formatDate($date,'rfc');
	$return['iso8601'] = formatDate($date,'iso8601');
	$return['time'] = formatDate($date);

	if(!empty($date_old) && formatDate($date_old)<$return['time']) {
		$return['ago'] = time_since(formatDate($date_old),$return['time']);
	}
	return $return;
}
function news_xml_setup($array,$type='comments') {
	global $g_company_name;

	$return = array();
	$return['path'] = '/xml/'.strtolower($type).'/';
	$return['file'] = $array['id'].'_'.trim($array['safe'],'/');

	if(strtolower($type)=='hot-topics') {
		$return['file'] = $array['author']['id'].'_'.url_encode($array['author']['full-name']);
	}

	$return['rss']['class'] = array('rss','feed');
	$return['rss']['rel'] = 'alternate';
	$return['rss']['type'] = 'xml';
	$return['rss']['mime'] = 'application/rss+xml';
	$return['rss']['name'] = $return['file'].'_rss.xml';
	$return['rss']['path'] = $return['path'];
	$return['rss']['local'] = $return['path'].$return['rss']['name'];
	$return['rss']['permalink'] = $array['permalink']['link'].'rss/';
	$return['rss']['xsl'] = '/xml/xsl/comments_rss.xsl';
	$return['rss']['title'] = 'Comments on '.$g_company_name.' article &#8220;'.$array['title'].'&#8221; (RSS)';

	$return['atom']['class'] = array('atom','feed');
	$return['atom']['rel'] = 'alternate';
	$return['atom']['type'] = 'xml';
	$return['atom']['mime'] = 'application/atom+xml';
	$return['atom']['name'] = $return['file'].'_atom.xml';
	$return['atom']['path'] = $return['path'];
	$return['atom']['local'] = $return['path'].$return['atom']['name'];
	$return['atom']['permalink'] = $array['permalink']['link'].'atom/';
	$return['atom']['xsl'] = '/xml/xsl/comments_atom.xsl';
	$return['atom']['title'] = 'Comments on '.$g_company_name.' article &#8220;'.$array['title'].'&#8221; (Atom)';

	if(strtolower($type)=='hot-topics') {
		$return['rss']['title'] = $g_company_name.': Latest Hot Topics by '.$array['author']['full-name'].' (RSS)';
		$return['atom']['title'] = $g_company_name.': Latest Hot Topics by '.$array['author']['full-name'].' (Atom)';
		$return['rss']['xsl'] = '/xml/xsl/hot_topic_rss.xsl';
		$return['atom']['xsl'] = '/xml/xsl/hot_topic_atom.xsl';
		$return['atom']['permalink'] = $array['author']['url'].'atom/hot-topics/';
		$return['rss']['permalink'] = $array['author']['url'].'rss/hot-topics/';
	}

	return $return;
}

function news_setup($array,$type='news') {
	if(empty($array) || !is_array($array)) return false; // no array, nothing to do

	global $now_timestamp;
	extract($array);
	$return = array();

	$type = strtolower($type);

	$return['id'] = $Detail_ID;
	$return['title'] = formatText($Detail_Title);
	$return['title-plain'] = $Detail_Title;
	$return['description']['summary'] = wordwrap($Detail_Summary,125);
	$return['description']['summary-markdown'] = $Detail_Summary;
	$return['description']['summary-main'] = formatText($Detail_Summary,'output');
	$return['description']['markdown'] = $Detail_Description;
	$return['description']['main'] = formatText($return['description']['markdown'],'output');
	$return['section'] = strtolower($Section_Name);

	$return['safe'] = $Detail_Safe_URL;
	$return['class'] = array();
	$return['status'] = $Detail_Status+1;

	$return['created'] = news_date_setup($Detail_Created,$now_timestamp);
	$return['updated'] = news_date_setup($Detail_Updated,$Detail_Created);

	if($return['section']=='extra') {
		if(!empty($return['image'])) unset($return['image']);
		$extra_image_array = get_file($return['id'],'/images/extra/');
		if(!empty($extra_image_array)) {
			$return['image'] = image_setup($extra_image_array['identifier'],rtrim($Detail_Safe_URL,'/'),$extra_image_array['ext'],$extra_image_array['path'],$return['title'],'','',array('news-extra-image'));
		}
	}
	elseif($type=='hot') {
		if(!empty($return['image'])) unset($return['image']);
		$return['image'] = image_setup($Detail_ID,$Detail_Safe_URL,$Detail_Image_Extension,'/images/hot-topics/',$Detail_Image_Alt_Text,$Detail_Image_Title,'',array('hot-topic-image'));
	}
	else {
		$return['images'] = news_setup_images($return);
		$return['images'] = cleanArray($return['images']);
	}

	$name_array = name($Author_Forename,$Author_Surname);
	$return['author'] = $name_array;
	$return['author']['id'] = $Author_ID;
	$return['author']['email'] = validate($Author_Email,'email');
	$return['author']['title'] = formatText($Author_Title,'capitals');
	$return['author']['url'] = profile_url($name_array);
	$return['author']['image'] = image_setup('0',$name_array['full-name'],'gif','/images/icons/large/','Photo of '.$name_array['full-name'],'','',array('photo'));
	$return['author']['feeds'] = setup_feed_author_information($Author_ID);

	$return['permalink'] = news_permalink_setup($Detail_ID,$Detail_Created,$Detail_Safe_URL,$return['section'],$return['title'],$return['author']['url']);

	if($type!='hot') {
		$return['comments'] = news_comments_setup($return);
		$return['comments']['total']  = count($return['comments']);
		$return['comments']['active'] = $Comments_Active+1;
		$return['comments']['title']  = $return['title'];

		$return['tags'] = news_tags_setup($Detail_ID);
		$return['xml'] = news_xml_setup($return);
		$return['links']['related'] = news_links_setup($Detail_ID);
		$return['links']['contain'] = process_links($Detail_Description);
	}
	elseif($type=='hot') {
		//$return['author']['feeds']['hot-topics'] = news_xml_setup($return,'hot-topics');
		$return['author']['feeds']['hot-topics'] = setup_feed_author_information($Author_ID,'hot-topics');
		if(!empty($return['image'])) $return['image']['link'] = validate($Detail_Image_Link,'url');
	}


	return $return;
}

function news_setup_images($array,$from_admin=false) {
	if(empty($array) || !is_array($array)) return false;

	global $now_timestamp;
	global $connect_admin;
	$image_path = '/images/news/';

	$sql = "SELECT
			ndi.ID AS Image_ID,
			ndi.Image_Alt_Text,
			ndi.Safe_URL AS Image_Safe_URL,
			ndi.Description AS Image_Description,
			ndi.Extension AS Image_Extension,
			ndi.Position AS Image_Position,

			ndi.Flickr_ID AS Image_Flickr_ID,
			ndi.Flickr_URL AS Image_Flickr_URL,
			ndi.Flickr_Title AS Image_Flickr_Title,

			d.ID AS Detail_ID,
			d.Created AS Detail_Created,
			d.Title AS Detail_Title,
			d.Safe_URL AS Detail_Safe_URL,

			s.Section AS Section_Name

			FROM news_details_images AS ndi
			LEFT JOIN news_details_images_join AS ndij ON ndi.ID = ndij.news_details_images_ID
			LEFT JOIN news_details AS d ON d.ID = ndij.news_details_ID
			LEFT JOIN news_section AS s ON s.ID = d.News_Section_ID

			WHERE ndij.news_details_ID = '".mysqli_real_escape_string($connect_admin, $array['id'])."'
			ORDER BY ndi.Position ASC, ndi.Image_Alt_Text ASC";
			//if($from_admin==true) echo $sql.'<br /><br />';

	$query = mysqli_query($connect_admin, $sql);
	$return = array(); $i=0;
	while($image_array = mysqli_fetch_array($query)) {
		$image_name = trim($image_array['Image_Safe_URL']);
		$array['permalink'] = news_permalink_setup($image_array['Detail_ID'],$image_array['Detail_Created'],$image_array['Detail_Safe_URL'],$image_array['Section_Name'],$image_array['Detail_Title']);
		$array['title'] = formatText($image_array['Detail_Title']);

		$j = $i+1;
		$alt_text = '';
		//$alt_text = $array['title'].' - '.$image_array['Image_Alt_Text'];
		$alt_text = $image_array['Image_Alt_Text'];

		if(image_setup($image_array['Image_ID'],$image_name,$image_array['Image_Extension'],$image_path) || $from_admin==true) {

			$return[$i]['large'] = image_setup($image_array['Image_ID'],$image_name,$image_array['Image_Extension'],$image_path,$alt_text,$image_array['Image_Alt_Text'],'l'.$j);
			$return[$i]['large']['permalink'] = $array['permalink']['link'].$image_array['Image_Safe_URL'];
			$return[$i]['large']['safe'] = trim($image_array['Image_Safe_URL'],'/');
			$return[$i]['large']['id'] = 'l'.$j;
			$return[$i]['large']['identifier'] = $image_array['Image_ID'];

			if(empty($return[$i]['large']['text'])) {
				$return[$i]['large']['text'] = array(
					'alt' => $alt_text,
					'title' => $image_array['Image_Alt_Text']
				);
			}

			if(!empty($image_array['Image_Flickr_ID']) && !empty($image_array['Image_Flickr_URL'])) {
				$return[$i]['flickr']['id'] = $image_array['Image_Flickr_ID'];
				$return[$i]['flickr']['text'] = $image_array['Image_Flickr_Title'];
				$return[$i]['flickr']['path'] = $image_array['Image_Flickr_URL'];
				$return[$i]['flickr']['link'] = 'http://www.flickr.com'.$image_array['Image_Flickr_URL'];
				$return[$i]['flickr']['rel'][] = 'external';
				$return[$i]['flickr']['anchor-class'][] = 'external';
			}

			$image_name = trim($image_name,'/').'_small/';
			$return[$i]['small'] = image_setup($image_array['Image_ID'],$image_name,$image_array['Image_Extension'],$image_path,$alt_text,$image_array['Image_Alt_Text'],'s'.$j);
			$return[$i]['small']['permalink'] = $array['permalink']['link'].$image_array['Image_Safe_URL'];
			$return[$i]['small']['safe'] = trim($image_array['Image_Safe_URL'],'/');
			$return[$i]['small']['id'] = 's'.$j;
			$return[$i]['small']['identifier'] = $image_array['Image_ID'];

			if(empty($return[$i]['small']['text'])) {
				$return[$i]['small']['text'] = array(
					'alt' => $alt_text,
					'title' => $image_array['Image_Alt_Text']
				);
			}

			$return[$i]['no'] = $j;
			$return[$i]['class'] = array();
			if(!empty($_GET['image']) && $_GET['image']==$return[$i]['small']['safe']) {
				$return[$i]['class'][] = 'active';
			}
			if(empty($_GET['image']) && $i==0) {
				$return[$i]['class'][] = 'active';
			}

			$i++;
		}
		/*
		else {
			$return[$i] = array(
				'large' => array(
					'text' => array(
						'alt' => $alt_text,
						'title' => $image_array['Image_Alt_Text']
					),
					'safe' => trim($image_array['Image_Safe_URL'],'/'),
					'permalink' => $array['permalink']['link'].$image_array['Image_Safe_URL'],
					'identifier' => $image_array['Image_ID']
				)
			);
			if(!empty($image_array['Image_Flickr_ID']) && !empty($image_array['Image_Flickr_URL'])) {
				$return[$i]['flickr'] = array(
					'id' => $image_array['Image_Flickr_ID'],
					'text' => $image_array['Image_Flickr_Title'],
					'path' => $image_array['Image_Flickr_URL'],
					'link' => 'http://www.flickr.com'.$image_array['Image_Flickr_URL']
				);
			}
		}
		*/
	}
	return $return;
}


function news_links_setup($id='') {
	global $now_timestamp;
	global $connect_admin;

	$sql_extra = '';
	if(!empty($id) && is_numeric($id)) {
		$sql_extra = " AND nl.news_details_ID = '".mysqli_real_escape_string($connect_admin, $id)."'";
	}
	$links_sql = "SELECT
				l.ID AS Link_ID,
				l.Created AS Link_Created,
				l.Updated AS Link_Updated,
				l.Title AS Link_Title,
				l.Name AS Link_Author_Name,
				l.Website AS Link_Website

				FROM links_details AS l
				LEFT JOIN news_details_links_join AS nl ON nl.links_details_ID = l.ID
				WHERE l.Active = 1
				".$sql_extra."
				ORDER BY l.Title ASC, l.Created ASC, l.ID ASC";

	$links_query = mysqli_query($connect_admin, $links_sql);

	$return = array(); $check_array = array(); $i=0;
	while($array = mysqli_fetch_array($links_query)) {
		$return[$i] = array(
			'text' => $array['Link_Title'],
			'link' => $array['Link_Website'],
			'author' => $array['Link_Author_Name'],
			'rel' => array('external'),
			'anchor-class' => array('external')
		);
		$i++;
	}
	return $return;
}

function news_tags_setup($id='') {
	global $now_timestamp;
	global $g_apiArray;
	global $connect_admin;

	$sql_extra = '';
	if(!empty($id) && is_numeric($id)) {
		$sql_extra = "AND tj.News_Detail_ID = '".mysqli_real_escape_string($connect_admin, $id)."'";
	}

	$tag_sql = "SELECT
				t.ID AS Tag_ID,
				t.Created AS Tag_Created,
				t.Updated AS Tag_Updated,
				t.Category AS Tag_Title,
				t.Safe_URL AS Tag_Safe_URL,
				t.Description AS Tag_Description
				FROM news_category AS t
				LEFT JOIN news_category_join AS tj ON tj.Category_ID = t.ID
				WHERE 1=1
				".$sql_extra."
				ORDER BY t.Category ASC, t.Created ASC";

	$tag_query = mysqli_query($connect_admin, $tag_sql);

	$tag_standard_array = array(); $tag_machine_array = array();
	$events_array = array(); $photos_array = array();
	$check_array = array(); $t=-1; $m=-1; $i=0; $e=0; $p=0;
	while($array = mysqli_fetch_array($tag_query)) {
		if(!in_array($array['Tag_ID'],$check_array)) {
			$check_array[] = $array['Tag_ID'];
			if(preg_match('#([^:]+):([^=]+)=([_A-Za-z0-9]+)#',$array['Tag_Title'],$machine_tag_matches) && count($machine_tag_matches)==4) {
				$array_name = 'tag_machine_array';
				$i = 'm';
				if($machine_tag_matches[1]=='upcoming' && $machine_tag_matches[2]=='event' && is_numeric($machine_tag_matches[3])) {
					// this is an upcoming event, pull in that detail
					// $g_apiArray['upcoming'];
					$events_array[$e] = upcoming_event_setup($machine_tag_matches);
					$e++;

					// setup flickr photos for this event.
					$photo_setup_array = array('key' => 'tag', 'value' => $array['Tag_Title']);
					//$photos_array[$p] = photo_setup($photo_setup_array);
					$p++;
				}
			}
			else {
				$array_name = 'tag_standard_array';
				$i = 't';
			}
			${$i}++;

			${$array_name}[${$i}]['identifier'] = $array['Tag_ID'];
			${$array_name}[${$i}]['text'] = $array['Tag_Title'];
			${$array_name}[${$i}]['safe'] = $array['Tag_Safe_URL'];
			${$array_name}[${$i}]['description']['summary'] = wordwrap($array['Tag_Description'],125);
			${$array_name}[${$i}]['description']['main'] = formatText(${$array_name}[${$i}]['description']['summary'],'output');
			${$array_name}[${$i}]['created'] = news_date_setup($array['Tag_Created'],$now_timestamp);
			${$array_name}[${$i}]['updated'] = news_date_setup($array['Tag_Updated'],$array['Tag_Created']);

			${$array_name}[${$i}]['class'] = array();
			${$array_name}[${$i}]['rel'][] = 'tag';

			${$array_name}[${$i}]['permalink'] = news_permalink_setup($array['Tag_ID'],$array['Tag_Created'],$array['Tag_Safe_URL'],'tags',$array['Tag_Title']);
			//${$array_name}[${$i}]['link'] = ${$array_name}[${$i}]['permalink']['link'];
		}
	}

	$tag_all_array = array_merge($tag_standard_array,$tag_machine_array);
	$total_tags = count($tag_all_array);
	return array(
		'standard' => $tag_standard_array,
		'machine' => $tag_machine_array,
		'total' => $total_tags,
		'combined' => $tag_all_array,
		'events' => $events_array,
		'photos' => $photos_array
	);
}

// related articles
function related_author_setup($array) {
	global $connect_admin;
	if(empty($array) || !is_array($array)) return false;
	$sql_extra = " AND ad.ID = '".mysqli_real_escape_string($connect_admin, $array['author']['id'])."'";
	$sql_extra .= " AND d.Safe_URL != '".mysqli_real_escape_string($connect_admin, $array['permalink']['safe'])."'";
	$sql_limit = 'LIMIT 0,5';
	return related_setup($sql_extra,$sql_limit);
}
function related_tags_setup($array) {
	global $connect_admin;
	if(empty($array) || !is_array($array)) return false;

	$sql_extra = " AND d.Safe_URL != '".mysqli_real_escape_string($connect_admin, $array['permalink']['safe'])."'";
	if(!empty($array['tags']) && is_array($array['tags'])) {
		$sql_extra .= ' AND tj.Category_ID IN (';
		$i=0; $total_tags = count($array['tags']);
		foreach($array['tags'] as $tag_array) {
			$sql_extra .= '\''.$tag_array['id'].'\'';
			$i++;
			if($i<$total_tags) $sql_extra .= ',';
		}
		$sql_extra .= ')';
	}
	else return false;

	$sql_limit = 'LIMIT 0,5';
	return related_setup($sql_extra,$sql_limit);
}

function related_setup($sql_extra='',$sql_limit='',$status=true,$news_section_id='1') {
	global $connect_admin;

	if($status==true) {
		$sql_extra .= " AND d.Active = '1'";
	}

	$sql_related = "SELECT
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
				 tj.Category_ID AS Tag_ID,
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
				 LEFT JOIN news_category_join AS tj ON tj.News_Detail_ID = d.ID
				 WHERE d.News_Section_ID = '".$news_section_id."'
				 ".$sql_extra."
				 GROUP BY d.ID
				 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
				 ".$sql_limit;

	$query_related = mysqli_query($connect_admin, $sql_related);
	$return = array();
	while($array = mysqli_fetch_array($query_related)) {
		$return[] = news_setup($array);
	}
	return $return;
}


function photo_setup($array) {
	global $g_apiArray;
	global $g_cache_options;

	$photo = array();
	$photo_cache_options = $g_cache_options;
	$photo_cache_options['cacheDir'] = $_SERVER['DOCUMENT_ROOT'].'/cache/flickr/photos/';

	if(empty($array) || !is_array($array) || empty($g_apiArray['flickr'])) return;

	$query_array = array(
		'method' => 'flickr.photos.search',
		'api_key' => $g_apiArray['flickr'],
		'content_type' => '1',
		'extras' => 'owner_name,icon_server,original_format'
	);

	if(strtolower($array['key'])=='tag') {
		$query_array['sort'] = 'interestingness-desc';
		$query_array['machine_tags'] = $array['value'];
		$photo['identifier'] = 'tag-'.$array['value'];
	}
	$photo['api'] = 'http://api.flickr.com/services/rest/?'.url_query_build($query_array);

	$photo_cache = new Cache_Lite_File($photo_cache_options);
	if(!$photo_data = $photo_cache->get($photo['identifier'])) {
		$photo_data = file_get_contents($photo['api']);
		$photo_cache->save($photo_data);
	}

	$photo_xml_parser = xml_parser_create();
	xml_parse_into_struct($photo_xml_parser,$photo_data,$photo_vals,$photo_index);
	xml_parser_free($photo_xml_parser);
	$photo_vals_length = count($photo_vals);

	$photo['data'] = array(); $pd = 0;
	for($i=0; $i<$photo_vals_length; $i++) {
		if(!empty($photo_vals[$i]['tag']) && strtolower($photo_vals[$i]['tag'])=='photo' && !empty($photo_vals[$i]['attributes'])) {
			$photo['data'][$pd] = array(
				'identifier' => $photo_vals[$i]['attributes']['ID'],
				'owner' => $photo_vals[$i]['attributes']['OWNER'],
				'secret' => $photo_vals[$i]['attributes']['SECRET'],
				'server' => $photo_vals[$i]['attributes']['SERVER'],
				'farm' => $photo_vals[$i]['attributes']['FARM'],
				'title' => $photo_vals[$i]['attributes']['TITLE']
			);
			$photo['data'][$pd]['image'] = 'http://farm'.$photo['data'][$pd]['farm'].'.static.flickr.com/'.$photo['data'][$pd]['server'].'/'.$photo['data'][$pd]['identifier'].'_'.$photo['data'][$pd]['secret'].'_s.jpg';
			$photo['data'][$pd]['link'] = 'http://www.flickr.com/photos/'.$photo['data'][$pd]['owner'].'/'.$photo['data'][$pd]['identifier'].'/';
			$photo['data'][$pd]['text'] = '<img src="'.$photo['data'][$pd]['image'].'" alt="'.formatText($photo['data'][$pd]['title']).'" width="75" height="75" />';
			$pd++;
		}
	}
	return $photo;
}

function upcoming_event_setup($array) {
	global $g_apiArray;
	global $g_cache_options;

	$event = array();
	$event['identifier'] = $array[3]; // the event ID
	$event['tag'][] = 'upcoming:event='.$event['identifier'];
	$event['tag'][] = 'upcoming:id='.$event['identifier'];
	$event['api']['event'] = 'http://upcoming.yahoo.com/services/rest/?api_key='.$g_apiArray['upcoming'].'&method=event.getInfo&event_id='.$event['identifier'];
	$event['link'] = 'http://upcoming.yahoo.com/event/'.$event['identifier'].'/';

	$event['id'] = 'event_'.$event['identifier'];
	$event['class'] = array('vevent');

	$event_cache_options = $g_cache_options;
	$event_cache_options['masterFile'] = $_SERVER['DOCUMENT_ROOT'].'/cache/upcoming/cache-lite.config';
	$event_cache_options['cacheDir'] = $_SERVER['DOCUMENT_ROOT'].'/cache/upcoming/events/';
	$event_cache = new Cache_Lite_File($event_cache_options);
	if(!$event_data = $event_cache->get($event['identifier'])) {
		$event_data = file_get_contents($event['api']['event']);
		$event_cache->save($event_data);
	}

	preg_match('/name="([^"]*)?" tags="([^"]*)?" description="([^"]*)?"/',$event_data,$event_info_array);
	preg_match('/url="([^"]*)?"/',$event_data,$event_url_array);
	preg_match('/start_date="([^"]*)?" end_date="([^"]*)?" start_time="([^"]*)?" end_time="([^"]*)?"/',$event_data,$event_date_array);
	preg_match('/venue_name="([^"]*)?" venue_address="([^"]*)?" venue_city="([^"]*)?" venue_state_name="([^"]*)?"/',$event_data,$event_venue_array);
	preg_match('/venue_id="([^"]*)"/',$event_data,$event_id_array);
	preg_match('/venue_country_name="([^"]*)?" venue_country_code="([^"]*)?"/',$event_data,$event_country_array);

	$event_tags_array = explode(',',$event_info_array[2]);

	$event['text'] = $event_info_array[1];
	$event['safe'] = url_encode($event_info_array[1]);
	$event['tag'] = array_merge($event['tag'],$event_tags_array);
	$event['description']['main'] = strip_tags(str_replace(array('&lt;strong&gt;','&lt;/strong&gt;','&lt;em&gt;','&lt;/em&gt;'),array('**','**','*','*'),$event_info_array[3]));
	$event['description']['html'] = formatText($event['description']['main'],'output');
	$event['start'] = news_date_setup(trim($event_date_array[1].' '.$event_date_array[3]));
	$event['start']['24hr'] = formatDate($event['start']['sql'],'time');
	$event['start']['12hr'] = convert_time_to_am_pm($event['start']['sql']);

	$event['venue'] = array(
		'identifier' => @$event_id_array[1],
		'name' => @$event_venue_array[1],
		'street-address' => @$event_venue_array[2],
		'locality' => @$event_venue_array[3],
		'region' => @$event_venue_array[4],
		'country' => array(
			'name' => @$event_country_array[1],
			'code' => strtoupper(@$event_country_array[2])
		),
	);

	if(!empty($event_date_array[2])) $end_date = $event_date_array[2];
	else $end_date = $event_date_array[1];

	if(!empty($event_date_array[4])) $end_time = $event_date_array[4];
	else $end_time = $event_date_array[3];

	$event['end'] = news_date_setup(trim($end_date.' '.$end_time));
	$event['end']['24hr'] = formatDate($event['end']['sql'],'time');
	$event['end']['12hr'] = convert_time_to_am_pm($event['end']['sql']);

	foreach($event['start'] as $start_event_key => $start_event_date) {
		$event['dtstart'][$start_event_key] = '<abbr title="'.$event['start']['iso'].'" class="dtstart">'.$start_event_date.'</abbr>';
	}
	foreach($event['end'] as $end_event_key => $end_event_date) {
		$event['dtend'][$end_event_key] = '<abbr title="'.$event['end']['iso'].'" class="dtend">'.$end_event_date.'</abbr>';
	}

	if($end_date==$event_date_array[1]) { // same day...
		$event['date'] = $event['dtstart']['long'].', '.$event['dtstart']['12hr'].' until '.$event['dtend']['12hr'];
	}
	else $event['date'] = $event['dtstart']['short'].' - '.$event['dtend']['short'];


	if(!empty($event_url_array[1])) {
		$event['permalink'] = '<a href="'.$event_url_array[1].'" rel="external" class="external url">'.$event['text'].'</a>';
	}

	return $event;
}
?>
