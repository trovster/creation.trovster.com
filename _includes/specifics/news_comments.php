<?php
function news_comments_setup($setup_array,$sql_order='',$author='',$admin=false) {
	global $connect_admin;
	if(empty($setup_array) || !is_array($setup_array)) return false;

	$where = '';
	$where .= " AND c.Active = '1'";
	$where .= " AND c.Is_Spam = '0'";
	if($admin==true) {
		$where = '';
	}

	if(empty($sql_order)) $sql_order = 'c.Created ASC';
	if(!empty($setup_array['id'])) $where .= " AND  cj.News_Detail_ID = '".mysqli_real_escape_string($connect_admin, $setup_array['id'])."'";
	if(!empty($author) && is_numeric($author)) {
		$where .= " AND c.Author = '1' AND c.Author_ID = '".mysqli_real_escape_string($connect_admin, $author)."'";
	}

	$comment_sql = "SELECT
					c.ID AS Comment_ID,
					c.Created AS Comment_Created,
					c.Updated AS Comment_Updated,
					c.Name as Comment_Name,
					c.Email AS Comment_Email,
					c.Website AS Comment_Website,
					c.Text AS Comment_Text,
					c.Author AS Comment_Author_Value,
					c.Author_ID AS Comment_Author_ID,
					c.IP AS Comment_IP,
					c.Is_Spam,
					c.Active AS Is_Active,

					xfn.Relation AS Comment_Relation,

					d.ID AS Detail_ID,
					d.Created AS Detail_Created,
					d.Safe_URL as Detail_Safe_URL,
					d.Title AS Detail_Title,

					s.Section AS Section_Name
					FROM news_comments AS c
					LEFT JOIN news_comments_join AS cj ON c.ID = cj.News_Comment_ID
					LEFT JOIN news_details AS d ON d.ID = cj.News_Detail_ID
					LEFT JOIN news_section AS s ON s.ID = d.News_Section_ID
					LEFT JOIN links_details AS ld ON c.Website = ld.Website AND c.Email = ld.Email
					LEFT JOIN links_details_xfn_join AS xfn_j ON ld.ID = xfn_j.Links_Detail_ID
					LEFT JOIN general_xfn AS xfn ON xfn.ID = xfn_j.XFN_ID
					WHERE 1 = 1
					".$where."
					ORDER BY ".$sql_order;

	//echo $comment_sql;
	$comment_query = mysqli_query($connect_admin, $comment_sql);

	$return = array(); $check_array = array(); $i=0;
	while($array = mysqli_fetch_array($comment_query)) {
		extract($array);
		if(!in_array($Comment_ID,$check_array)) {
			$check_array[] = $Comment_ID;

			$microformat_name = new Guess($Comment_Name);
			$microformat_name_array = $microformat_name->guess;
			if(is_array($microformat_name_array) && !empty($microformat_name_array['meta'])) unset($microformat_name_array['meta']);
			$microformat_name_optimised = $microformat_name->output();

			$permalink = news_permalink_setup($Detail_ID,$Detail_Created,$Detail_Safe_URL,$Section_Name,$Detail_Title);

			$return[$i]['id'] = $Comment_ID;
			$return[$i]['created'] = news_date_setup($Comment_Created);
			$return[$i]['updated'] = news_date_setup($Comment_Updated);
			$return[$i]['ip'] = $Comment_IP;
			$return[$i]['permalink']['link'] = $permalink['link'].'#c_'.$Comment_ID;
			$return[$i]['permalink']['anchor-not-closed-no-text'] = '<a href="'.$return[$i]['permalink']['link'].'" title="Permalink to this comment" rel="bookmark">';
			$return[$i]['permalink']['anchor'] = $return[$i]['permalink']['anchor-not-closed-no-text'].'<abbr class="published updated entry-title" title="'.$return[$i]['created']['iso'].'">'.preg_replace('/(at [0-9]{2}:[0-9]{2})/','<span>\\1</span>',$return[$i]['created']['comments']).'</abbr></a>';
			$return[$i]['permalink']['atom-id'] = 'tag:'.preg_replace('#http://(www.)?#','','http://creation.trovster.com').','.$return[$i]['created']['iso8601'].':'.$return[$i]['permalink']['link'];

			$return[$i]['status'] = $Is_Active+1;
			$return[$i]['spam'] = $Is_Spam+1;

			$return[$i]['description']['plain'] = $Comment_Text;
			$return[$i]['description']['markdown'] = $Comment_Text;
			$return[$i]['description']['main'] = formatText($Comment_Text,'output');
			$return[$i]['description']['summary'] = formatText($Comment_Text,'output');

			//$return[$i]['author'] = $microformat_name_array;
			$return[$i]['author']['n-optimised'] = $microformat_name_optimised;
			$return[$i]['author']['plain'] = $Comment_Name;
			$return[$i]['author']['email'] = validate($Comment_Email,'email');
			$return[$i]['author']['md5'] = md5(validate($Comment_Email,'email'));
			$return[$i]['author']['url'] = validate($Comment_Website,'url');
			$return[$i]['author']['image'] = '';

			$return[$i]['author']['image']['text']['alt'] = 'Gravatar for '.$return[$i]['author']['plain'];
			$return[$i]['author']['image']['dimensions']['height'] = '48';
			$return[$i]['author']['image']['dimensions']['width'] = '48';
			$return[$i]['author']['image']['file']['name'] = 'general_avatar.gif';
			$return[$i]['author']['image']['file']['path'] = '/css/images/';
			$return[$i]['author']['image']['file']['full-path'] = '/css/images/general_avatar.gif';
			$return[$i]['author']['image']['file']['type'] = 'gif';
			$return[$i]['author']['image']['class'] = array('gravatar');

			// get the gravatar via caching
			$return[$i]['author']['image']['file']['name'] = '/images/gravatars/cache/creation/'.$return[$i]['author']['md5'];
			$return[$i]['author']['image']['file']['full-path'] = '/images/gravatars/cache/creation/'.$return[$i]['author']['md5'];

			//http://create/images/gravatars/cache/creation/a489725ad8451b6e87fcb9ed4f189cf9

			if(!empty($Comment_Author_Value) && $Comment_Author_Value==1 && $admin_author = news_comments_check_author($return[$i]['author']['url'],$return[$i]['author']['email'],$return[$i]['author']['plain'])) {
				$return[$i]['author']['admin'] = $admin_author;
				$return[$i]['author']['admin']['author-id'] = $Comment_Author_ID;
				$return[$i]['author']['image'] = image_setup('0',$admin_author['full-name'],'gif','/images/icons/','Gravatar for '.$admin_author['full-name'],'','',array('gravatar','logo'));
				$return[$i]['author']['n-optimised'] = $admin_author['full-name']; // set the author name to the one from the DB
			}

			if(!empty($Comment_Relation)) {
				$return[$i]['relationship'] = array($Comment_Relation);
			}
			$i++;
		}
		elseif(!empty($Comment_Relation)) {
			$return[$i-1]['relationship'][] = $Comment_Relation;
		}
	}
	return $return;
}


function news_comments_display($array,$id='',$class='') {
	global $g_company_domain;
	if(empty($array['comments']) || !is_array($array['comments'])) return false;
	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
		$class[] = 'hfeed';
	}
	else $class = array('hfeed');

	$subscribe_class_array = array();
	if($array['comments']['active'] && $array['comments']['total']>0 && !empty($array['xml'])) {
		if(is_file($_SERVER['DOCUMENT_ROOT'].$array['xml']['atom']['local'])) {
			$subscribe_class_array = $array['xml']['atom']['class'];
			$subscribe_feed_array = $array['xml']['atom'];
		}
		elseif(is_file($_SERVER['DOCUMENT_ROOT'].$array['xml']['rss']['local'])) {
			$subscribe_class_array = $array['xml']['rss']['class'];
			$subscribe_feed_array = $array['xml']['rss'];
		}
	}

	$article_author_array = $array['author'];
	$output = '';
	$comment_total = $array['comments']['total'];
	$comment_active = $array['comments']['active'];
	$comment_title = $array['comments']['title'];
	unset($array['comments']['total']);
	unset($array['comments']['active']);
	unset($array['comments']['title']);

	$reaction_title = 'Reactions';
	if($comment_total==1) {
		$reaction_title = rtrim($reaction_title,'s');
	}

	$output .= '<div'.addAttributes('',$id,$class).'>'."\n";
	$output .= '<h3 class="section-title column triple">'.$comment_total.' × '.$reaction_title.'… <em>'.$comment_title.'</em></h3>'."\n";

	if(!empty($subscribe_feed_array)) {
		$output .= '<div'.addAttributes('','news-subscribe',array('subscribe','feed','column','last')).'>'."\n";
		$output .= '<h3>Subscribe to… <em>Comments</em></h3>'."\n";
		$output .= '<p'.addAttributes('','',$subscribe_class_array).'>';
		$output .= '<a href="'.$subscribe_feed_array['permalink'].'"'.addAttributes('Subscribe to comments for '.$array['title'],'','','',$subscribe_feed_array['rel'],'','','','',$subscribe_feed_array['mime']).'>';
		$output .= 'Subscribe to comments for <em>'.$array['title'].'</em>';
		$output .= '</a></p>';
		$output .= '</div>'."\n";
	}

	$array = $array['comments'];
	foreach($array as $comment_array) {
		// actual comments bits
		$comment_class = array('hentry','triple','column');
		$link_start = '';
		$link_end = '';
		$gravatar_show = '';
		$rel_link = array('external','nofollow');
		$class_array = array('external','url');
		$link_title = '';
		$author_comment_class = array('fn');

		$gravatar = array();
		if(!empty($comment_array['author']['admin'])) {
			// a comment by one of us..
			$comment_class[] = 'company-comment';
			$class_array = array('url');
			$rel_link = array();
			if(strtolower($comment_array['author']['plain'])=='creation') {
				$comment_class[] = 'creation-comment';
				$author_comment_class[] = 'org';
				$comment_array['author']['url'] = validate($g_company_domain,'website');

			}
			else {
				if(url_encode($article_author_array['full-name'])==url_encode($comment_array['author']['plain'])) {
					$comment_class[] = 'author-comment';
				}
				$comment_class[] = url_encode($comment_array['author']['plain']);
				$comment_array['author']['full-name'] = $comment_array['author']['admin']['full-name'];
				$comment_array['author']['url'] = profile_url($comment_array['author']['admin']);
				$link_title = 'View my company profile page';
			}
			if(!empty($comment_array['author']['class'])) $author_comment_class = array_merge($author_comment_class,$comment_array['author']['class']);
		}
		$gravatar_show = image_show($comment_array['author']['image']);

		if(!empty($comment_array['author']['url'])) {
			// we have a URL
			$link_start = '<a href="'.$comment_array['author']['url'].'"'.addAttributes($link_title,'',$class_array,'',$rel_link).'>';
			$link_end = '</a>';
		}

		$output .= '<div'.addAttributes('','c_'.$comment_array['id'],$comment_class).'>'."\n";
		$output .= '<div class="vcard author column">'."\n";
		if(!empty($gravatar_show)) $output .= $link_start.$gravatar_show.$link_end;
		$output .= '<p class="'.addClass($author_comment_class).'">'.$link_start.$comment_array['author']['n-optimised'].$link_end.'</p>'."\n";
		$output .= '<p class="timestamp">'.$comment_array['permalink']['anchor'].'</p>'."\n";

		$output .= '</div>'."\n";
		$output .= '<div class="entry-content column double last">'.$comment_array['description']['main']."\n".'<!-- end of div class .entry-content -->'."\n".'</div>'."\n";
		$output .= '</div>'."\n\n";
	}

	if($comment_active==1) {
		$output .=  '<p class="comments-closed">Reactions are now closed for this article!</p>'."\n";
	}

	if(!empty($id) && validToken($id)) {
		$output .= '<!-- end of div id #'.validToken($id).' -->'."\n";
	}
	$output .= '</div>'."\n\n";
	return $output;
}


function news_comments_add($array,$post_array,$post_data,$via_ajax=0) {
	global $connect_admin;
	global $now_timestamp;
	global $g_apiArray;
	global $g_company_domain;
	global $domain;

	$feedback_array = array();
	$is_author = 0; $author_id = 0;
	$is_spam = 0;
	$is_active = 1;
	$disabled_text = '';

	if(empty($post_array) || !is_array($post_array) || empty($array) || !is_array($array)) {
		$feedback_array['title'] = 'Comment Error';
		$feedback_array['text'] = 'There was an unexpected error, please try again.';
		$feedback_array['class'] = array('error','unexpected');
		return $feedback_array;
	}
	elseif(empty($post_array['comment-email-required']) || !validate($post_array['comment-email-required'],'email')) {
		$feedback_array['title'] = 'Invalid Email';
		$feedback_array['text'] = 'The email you provided is invalid, please try again.';
		$feedback_array['class'] = array('error','invalid','email');
		return $feedback_array;
	}

	comment_cookie_toggle($post_array);

	// akismet
	//$g_apiArray['wordpress'] = '';
	if(isset($g_apiArray) && !empty($g_apiArray['wordpress']) && !empty($post_data)) {
		$akismet_comment_array = array(
			'author'    => $post_data['comment-name-required'],
			'email'     => $post_data['comment-email-required'],
			'website'   => $post_data['comment-website'],
			'body'      => $post_data['comment-required'],
			'permalink' => $domain.$array['permalink']['link']
		);
		$akismet = new Akismet($domain, $g_apiArray['wordpress'], $akismet_comment_array);
		if($akismet->errorsExist()) {
			// returns true if any errors exist
			if($akismet->isError('AKISMET_INVALID_KEY')) {}
			elseif($akismet->isError('AKISMET_RESPONSE_FAILED')) {}
			elseif($akismet->isError('AKISMET_SERVER_NOT_FOUND')) {}
		}
		else {
			// No errors, check for spam
			if($akismet->isSpam()) {
				// returns true if Akismet thinks the comment is spam
				// do something with the spam comment
				$is_spam = 1;
				$is_active = 0; // disable the comment
				$disabled_text = ' Your comment in a queue awaiting moderation.';
			}
			else {
				// do something with the non-spam comment
			}
		}
	}

	$is_author_array = news_comments_check_author_insert($post_array); // check whether author is logged in, and details match.
	if(!empty($is_author_array) && is_array($is_author_array)) {
		$is_author = 1;
		if(!empty($is_author_array['id']) && is_numeric($is_author_array['id'])) $author_id = $is_author_array['id'];
	}

	$sql_insert = "INSERT INTO news_comments (Created, Updated, IP, UA, Is_Spam, Via_AJAX, Name, Email, Website, Text, Active, Author, Author_ID)
				   VALUES('".$now_timestamp."',
						  '".$now_timestamp."',
						  '".mysqli_real_escape_string($connect_admin, $_SERVER['REMOTE_ADDR'])."',
						  '".mysqli_real_escape_string($connect_admin, $_SERVER['HTTP_USER_AGENT'])."',
						  '".$is_spam."',
						  '".$via_ajax."',
						  '".$post_array['comment-name-required']."',
						  '".validate($post_array['comment-email-required'],'email')."',
						  '".validate($post_array['comment-website'],'website')."',
						  '".$post_array['comment-required']."',
						  '".$is_active."',
						  '".$is_author."',
						  '".$author_id."')";


	if(mysqli_query($connect_admin, $sql_insert) && is_numeric(mysql_insert_id())) {
		// the comment was added successfully, join it to the article
		$sql_insert_join = "INSERT INTO news_comments_join (News_Comment_ID,News_Detail_ID)
							VALUES('".mysql_insert_id()."','".mysqli_real_escape_string($connect_admin, $array['id'])."')";
		mysqli_query($connect_admin, $sql_insert_join);

		// grab gravatar here and cache it
		$email_md5 = md5(validate($post_array['comment-email-required'],'email'));

		// create / update the RSS feed for the comments
		setup_feed($array,'comments');
		if(!empty($author_id) && is_numeric($author_id)) {
			$author_comments_array = setup_feed_author_information($author_id,'comments');
			$author_array['author']['id'] = $author_id;
			$return_author_comments_array = setup_comments_author($author_array);
			create_feed($author_comments_array,$return_author_comments_array);
		}

		$feedback_array['title'] = 'Comment Added';
		$feedback_array['text'] = 'Thanks!'.$disabled_text;
		$feedback_array['class'] = array('comment','confirmation','added');

		$redirect_header = $array['permalink']['link'];
		if(!empty($is_spam) && $is_spam==1) {
			$redirect_header .= '#comments-view';
			echo $feedback_array['text'];
			exit;
		}
		else $redirect_header .= '#c_'.mysql_insert_id();
		header('Location: '.$redirect_header);
		exit;

	}
	else {
		$feedback_array['title'] = 'Comment Error';
		$feedback_array['text'] = 'There was an unexpected error, please try again.';
		$feedback_array['class'] = array('error','unexpected');
	}
	return $feedback_array;
}


function news_comments_check_author($url,$email,$name,$type='') {
	if(empty($url) || empty($email) || empty($name)) return false;

	global $g_company_domain;
	global $g_company_name;
	global $g_company_comment_email;
	global $connect_admin;

	$our_url 	= validate($g_company_domain,'url');
	$user_url 	= strtolower(validate($url,'url'));
	$user_email = strtolower(validate($email,'email'));
	$user_name 	= strtolower($name);

	$check_users_sql = "SELECT ad.ID AS Author_ID,
						ad.Forename as Author_Forename,
						ad.Surname AS Author_Surname,
						ad.Email AS Author_Email,
						CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name
						FROM author_details AS ad";

	if($type=='insert') {
		$check_users_sql .= " WHERE ad.ID = '".mysqli_real_escape_string($connect_admin, $_SESSION['login']['id'])."'";
	}

	$check_users_query = mysqli_query($connect_admin, $check_users_sql);
	while($check_users_array = mysqli_fetch_array($check_users_query)) {
		extract($check_users_array);
		$our_author = name($Author_Forename,$Author_Surname);
		$our_author['id'] = $Author_ID;
		if($user_url===$our_url && $user_email==validate($Author_Email,'email') && $user_name===strtolower($our_author['full-name'])) {
			return $our_author;
			break;
		}
	}

	// none of our profiles, so check globally
	if($user_url===$our_url && $user_email==validate($g_company_comment_email,'email') && $user_name===strtolower($g_company_name)) {
		return array('full-name' => 'Creation');
	}
	return false;
}
function news_comments_check_author_insert($array) {
	if(empty($array) || !is_array($array)) return 0;

	if(authorise()) {
		$author_check = news_comments_check_author($array['comment-website'],$array['comment-email-required'],$array['comment-name-required'],'insert');
		if(!empty($author_check) && is_array($author_check)) return $author_check;
	}
	return 0;
}
?>
