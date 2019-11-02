<?php
function news_display_meta($array,$class) {
	if(empty($array) || !is_array($array)) return false;
	
	$output = ''; $profile_image = ''; $permalink_Class = array();
	$entry_title = $array['permalink']['anchor'];
	$comment_link = $array['permalink']['link'];
	
	if(!empty($class) && is_array($class)) {
		if(in_array('permalink',$class)) {
			$entry_title = $array['title'];
			$comment_link = '';
			$permalink_Class[] = 'scrollto';
		}
		if(!in_array('mini',$class)) {
			$profile_image_setup = image_setup('0',$array['author']['full-name'],'gif','/images/icons/small/','Photo of '.$array['author']['full-name'],'','',array('logo'));
			$profile_image = image_show($profile_image_setup)."\n";
		}
	}

	if(!empty($array['images'])) {
		$output .= '<div class="image">';
		
		if(in_array('permalink',$class)) {
			// add list of images here.
			$image_navigation = array();
			for($i=0; $i<count($array['images']); $i++) {
				if($i>=6) continue;
				$image_navigation[$i]['text'] = $array['images'][$i]['small']['text']['title'];
				$image_navigation[$i]['link'] = $array['images'][$i]['small']['permalink'];
				$image_navigation[$i]['class'] = $array['images'][$i]['class'];
				$image_navigation[$i]['id'] = 'p'.$array['images'][$i]['no'];
			}
			if(!empty($image_navigation)) {
				$output .= createList($image_navigation,'position-navigation','','ol');
			}
		}
		
		if(!empty($_GET['image'])) {
			// show large image
			$image_search = array_search_recursive($_GET['image'],$array['images']);
			$output .= image_show($array['images'][$image_search[0]]['large']);
		}
		elseif(!empty($array['images'][0]['large'])) {
			if(!in_array('permalink',$class)) {
				// link the first large image when not on the rotate/homepage
				$output .= $array['permalink']['anchor-not-closed-no-text'].image_show($array['images'][0]['large']).'</a>';
			}
			else $output .= image_show($array['images'][0]['large']);
		}
		$output .= '</div>';
	}
	
	$output .= '<h3 class="entry-title">'.$entry_title.'</h3>'."\n";
	$output .= $profile_image;
	$output .= '<p class="entry-timestamp timestamp">'.news_display_date($array['created'],$array['updated']).'</p>'."\n";
	$output .= '<p class="author">Author: <a href="'.$array['author']['url'].'" class="fn url" title="View the profile page for '.$array['author']['full-name'].'">'.$array['author']['full-name'].'</a></p>'."\n";
	$output .= '<a href="#branding" class="include"></a>';
	
	if(!isset($array['mini'])) {
		$profile_image_small = image_setup('0',url_encode($array['author']['full-name']),'gif','/images/icons/small/','Photo of '.$array['author']['full-name'],'','',array('logo'));
		$output .= image_show($profile_image_small);
		if($array['comments']['total']>0) {
			$comment_suffix = 's';
			if($array['comments']['total']==1) $comment_suffix = '';
			$output .= '<p class="comments"><a href="'.$comment_link.'#comments-view"'.addAttributes('View the reaction comment'.$comment_suffix,'',$permalink_Class).'>'.$array['comments']['total'].' Reaction'.$comment_suffix.' to this article</a></p>'."\n";
		}
	}
	
	return $output;
}

function news_display_summary($array,$id='',$class='') {
	if(empty($array) || !is_array($array)) return false;

	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
		$class[] = 'hentry';
		if(!isset($array['mini'])) $class[] = 'summary';
		$class[] = 'vcard';
		$class[] = 'author';
	}
	else $class = array('hentry','summary','vcard','author');
	
	$output = '<div'.addAttributes('','e_'.$array['id'],$class).'>'."\n";
	$output .= news_display_meta($array,$class);
	$output .= '<p class="entry-summary">'.$array['description']['summary'].'</p>'."\n";
	$output .= '<p class="more"><a href="'.$array['permalink']['link'].'" class="bookmark permalink" rel="bookmark" title="Read the full article for &#8220;'.formatText($array['title']).'&#8221;">Continue reading… <strong>'.$array['title'].'</strong></a></p>'."\n";
	
	if(!empty($id) && validToken($id)) $output .= '<!-- end of div id #'.validToken($id).' -->'."\n";
	$output .= '</div>'."\n\n";
	return $output;
}
function news_display_mini($array,$id='',$class='',$type='date',$exclude='') {
	if(empty($array) || !is_array($array)) return false;
	
	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
	}
	else $class = array($class);
	
	if(!empty($exclude)) {
		if(!is_array($exclude)) $exclude = array($exclude);
	}
	else $exclude = array($exclude);

	$array_output = array(); $i=0; $j=0;
	foreach($array as $array_setup) {
		
		$j++;
		if(!empty($exclude) && is_array($exclude) && in_array($j,$exclude)) continue;
	
		$array_output[$i]['text'] = $array_setup['permalink']['anchor'];
		if(!empty($array_setup['class'])) $array_output[$i]['class'] = $array_setup['class'];
		
		if(strtolower($type)=='summary') $array_output[$i]['definition'] = $array_setup['description']['summary'];
		else $array_output[$i]['definition'] = news_display_date($array_setup['created'],$array_setup['updated']);

		if(!empty($array_setup['comments']) && is_array($array_setup['comments']) && !empty($array_setup['comments']['total']) && $array_setup['comments']['total']!=0) {
			//$array_output[$i]['text'] .= ' ('.$array_setup['comments']['total'].')';
		}
		
		$i++; $j++;
	}
	return createDefinitionList($array_output,$id,$class);
}
function news_display_archive_months() {
	$sql = "SELECT
			d.Created AS Detail_Created
			FROM news_details AS d
			WHERE d.Active = '1'
			AND d.News_Section_ID = '1'
			GROUP BY EXTRACT(YEAR_MONTH FROM d.Created)
			ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC";
	$query = mysqli_query($connect_admin, $sql);
	
	$output_array = array(); $i = 0;
	while($array = mysqli_fetch_array($query)) {
		$output_array[$i]['text'] = formatDate($array['Detail_Created'],'news-archive');
		$output_array[$i]['link'] = '/news/'.formatDate($array['Detail_Created'],'short-url');
		if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='archive' && strtolower($_GET['month'])==trim(formatDate($array['Detail_Created'],'admin-sql-check'),'%')) {
			$output_array[$i]['class'][] = 'active';
		}
		$i++;
	}
	return createList($output_array);
}
function news_display_archive_tags() {
	$tag_array = news_tags_setup();
	
	$output_array = array(); $i = 0;
	foreach($tag_array as $tag) {
		$output_array[$i]['text'] = $tag['permalink']['anchor'];
		if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='tags' && !empty($_GET['tag']) && strtolower($_GET['tag'])==rtrim($tag['permalink']['safe'],'/')) {
			$output_array[$i]['class'][] = 'active';
		}
		$i++;
	}
	return createList($output_array);
}

function news_display_content_navigation() {
	global $gl_ir;
	$sql_extra = '';
	if(!empty($_GET['subsection']) && strtolower($_GET['subsection'])=='archive') {
		$sql_extra = " AND d.Created LIKE '".mysql_real_escape_string($_GET['month'])."%'";
	}
	$sql = "SELECT
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
	
	$query = mysqli_query($connect_admin, $sql);
	$news_array_standard = array(); $i=0; $j=0; $output_array = array();
	$stylesheet_string = '';
	while($array = mysqli_fetch_array($query)) {
		$news_array_standard[$i] = news_setup($array);
		if(!empty($news_array_standard[$i]['images'][0]['small'])) {
			$output_array[$j]['text'] = $news_array_standard[$i]['title'];
			$output_array[$j]['link'] = $news_array_standard[$i]['permalink']['link'];
			//$output_array[$j]['link'] = $news_array_standard[$i]['permalink']['continue']['link'];
			$output_array[$j]['id'] = 'n_'.$news_array_standard[$i]['id'];
			if(!empty($gl_ir) && $gl_ir==true) $output_array[$j]['gilder'] = true;
			
			$output_array[$j]['class'][] = 'column';
			if(!empty($_GET['permalink']) && strtolower($_GET['permalink'])==trim($news_array_standard[$i]['permalink']['safe'],'/')) {
				$output_array[$j]['class'][] = 'active';
			}
			elseif(empty($_GET['permalink']) && $j==0) {
				$output_array[$j]['class'][] = 'active';
			}
			
			$stylesheet_string .= "\t".'#content #'.$output_array[$j]['id'].' a,#content #'.$output_array[$j]['id'].' a span.gl-ir {'."\n";
			$stylesheet_string .= "\t\t".'background-image: url('.$news_array_standard[$i]['images'][0]['small']['file']['full-path'].');'."\n";
			$stylesheet_string .= "\t".'}'."\n";
			$j++;
		}
		$i++; 
	}
	return array('stylesheet' => $stylesheet_string, 'navigation' => $output_array);
}
function news_display_permalink_meta($array) {
	if(empty($array) || !is_array($array)) return false;
	global $g_filesArray;
	
	$grammar = array(
		'past' => array('plural' => array('was' => 'were'), 'single' => array('was' => 'was')),
		'present' => array('plural' => array('was' => 'are'), 'single' => array('was' => 'is'))
	);
	
	$plural_s = 's';
	$grammar_type = 'plural';	
	if($array['comments']['total']==1) {
		$plural_s = '';
		$grammar_type = 'single';
	}
	
	$image_navigation = array(); $image_navigation_2 = array(); $j=0; $flickr_array = array();
	for($i=0; $i<count($array['images']); $i++) {
		if($i>=6) continue;
		if($i>=3) {
			$image_navigation_2[$j]['text'] = image_show($array['images'][$i]['small']);
			$image_navigation_2[$j]['link'] = $array['images'][$i]['small']['permalink'];
			$image_navigation_2[$j]['class'] = $array['images'][$i]['class'];
			$j++;
		}
		else {
			$image_navigation[$i]['text'] = image_show($array['images'][$i]['small']);
			$image_navigation[$i]['link'] = $array['images'][$i]['small']['permalink'];
			$image_navigation[$i]['class'] = $array['images'][$i]['class'];
		}
		
		if(!empty($array['images'][$i]['flickr']) && is_array($array['images'][$i]['flickr'])) {
			$flickr_array[] = $array['images'][$i]['flickr'];
		}
	}
	
	$author_news_sql = "SELECT
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
						AND ad.ID = '".$array['author']['id']."'
						AND d.ID != '".$array['id']."'
						GROUP BY d.ID
						ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
						LIMIT 0,5";
	
	$profile_image_small = image_setup('0',$array['author']['full-name'],'gif','/images/icons/small/','Photo of '.$array['author']['full-name'],'','',array('logo'));
	$news_array_standard = array();
	$author_news_query = mysqli_query($connect_admin, $author_news_sql);
	while($author_news_array = mysqli_fetch_array($author_news_query)) {
		$news_array_standard[] = news_setup($author_news_array);
	}
	
	$output = '';
	
	if(!empty($image_navigation) || !empty($image_navigation_2)) {
		$output .= '<div'.addAttributes('','image-navigation',array('column','double','last')).'>'."\n";
		$output .= '<h3>Image Navigation</h3>'."\n";
		$output .= createList($image_navigation,'',array('column'));
		if(!empty($image_navigation_2)) $output .= createList($image_navigation_2,'',array('column','last'));
		$output .= '</div>'."\n";
	}
	
	
	$output .= '<div'.addAttributes('','entry-meta',array('column')).'>'."\n";
		$output .= '<div'.addAttributes('','entry-meta-summary','').'>'."\n";
		$output .= '<h3>Brief summary of… <em class="entry-title">'.$array['title'].'</em></h3>'."\n";
		$output .= '<p class="entry-summary">'.$array['description']['summary'].'</p>'."\n";
		//$output .= '<!-- end of div id #entry-meta-summary -->'."\n";
		//$output .= '</div>'."\n";
		

		if(!empty($array['tags']) && is_array($array['tags']) && $array['tags']['total']>0 && (!empty($array['tags']['standard']) || !empty($array['tags']['machine']))) {
			$output .= '<div id="entry-meta-related-tags">'."\n";
			$output .= '<h4>Related Tags</h4>'."\n";
			if(!empty($array['tags']['standard'])) $output .= createList($array['tags']['standard'],'',array('tag','standard'));
			//if(!empty($array['tags']['machine'])) $output .= createList($array['tags']['machine'],'',array('tag','machine'));
			$output .= '<!-- end of div id #entry-meta-related-tags -->'."\n";
			$output .= '</div>'."\n";
		
		
			// go through the machine tags
			// if event, use Upcoming API, cache return, output link to event and details.
			if(!empty($array['tags']['events'])) {
				$output .= '<div id="entry-meta-related-events" class="hcalendar">'."\n";
				$output .= '<h4>Related Events</h4>'."\n";
					foreach($array['tags']['events'] as $event) {
						$summary = $event['text'];
						if(!empty($event['permalink'])) $summary = $event['permalink'];
					
						$output .= '<div'.addAttributes('',$event['id'],$event['class']).'>'."\n";
						$output .= '<h5 class="summary">'.$summary.'</h5>'."\n";
						$output .= '<p>'.$event['date'];
						if(!empty($event['venue']) && !empty($event['venue']['name']) && !empty($event['venue']['region'])) {
							$output .= ' at <span class="adr location vcard">';
							$output .= '<span class="fn org">'.$event['venue']['name'].'</span>, ';
							$output .= '<span class="locality">'.$event['venue']['region'].'</span>, ';
							$output .= '<abbr title="'.$event['venue']['country']['name'].'" class="country">'.$event['venue']['country']['code'].'</abbr>';
							$output .= '</span>';
						}
						$output .= '.</p>';
						
						if(!empty($event['description']) && !empty($event['description']['html'])) {
							//$output .= '<div class="description">'.$event['description']['html'].'</div>'."\n";
						}
						
						$output .= '<!-- end of div id #'.$event['id'].' -->'."\n";
						$output .= '</div>'."\n";
					}
				$output .= '<!-- end of div id #entry-meta-related-events -->'."\n";
				$output .= '</div>'."\n";
			}
			
			if(!empty($array['tags']['photos'])) {
				//echo '<pre style="background-color: #fff;">'; print_r($array['tags']['photos']); echo '</pre>';
				$output .= '<div id="entry-meta-related-photos">'."\n";
				$output .= '<h4>Related Photos</h4>'."\n";
				$output .= createList($array['tags']['photos'][0]['data']);
				$output .= '<!-- end of div id #entry-meta-related-photos -->'."\n";
				$output .= '</div>'."\n";
			}
		}

		$output .= '<!-- end of div id #entry-meta-summary -->'."\n";
		$output .= '</div>'."\n";
		
		
		if(!empty($news_array_standard)) {
			$my_feed_text = '';
			if(!empty($array['author']['feeds'])) {
				if(!empty($array['author']['feeds']['xml']['atom']) && is_file($_SERVER['DOCUMENT_ROOT'].$array['author']['feeds']['xml']['atom']['path'].$array['author']['feeds']['xml']['atom']['name'])) {
					$my_feed_info = $array['author']['feeds']['xml']['atom'];
				}
				elseif(!empty($array['author']['feeds']['xml']['rss']) && is_file($_SERVER['DOCUMENT_ROOT'].$array['author']['feeds']['xml']['atom']['path'].$array['author']['feeds']['xml']['atom']['name'])) {
					$my_feed_info = $array['author']['feeds']['xml']['rss'];
				}
				
				if(!empty($my_feed_info)) {
					$my_feed_text = '<a href="'.$my_feed_info['permalink'].'"'.addAttributes(rtrim($my_feed_info['title'],'  (Atom)'),'',$my_feed_info['class'],'',$my_feed_info['rel'],'','','','',$my_feed_info['mime']).'>';
					$my_feed_text .= 'Subscribe</a>';
				};
			}
			
			$output .= '<div'.addAttributes('','entry-meta-author-posts',array('column','last','news','mini-container','hfeed','vcard','author','selection','second')).'>'."\n";
			$output .= '<h3 class="section-title">More Posts By <em class="fn"><a href="'.$array['author']['url'].'" class="url">'.$array['author']['full-name'].'</a></em></h3>'."\n";
			$output .= image_show($profile_image_small);
			$output .= news_display_mini($news_array_standard);
			$output .=  $my_feed_text;
			$output .= '<!-- end of div id #entry-meta-author-posts -->'."\n";
			$output .= '</div>'."\n";
		}
	$output .= '<!-- end of div id #entry-meta -->'."\n";
	$output .= '</div>'."\n";
	
	
	
	$output .= '<div'.addAttributes('','entry-meta-extra',array('column','last')).'>'."\n";
		$output .= '<div id="entry-meta-extra-explain">'."\n";
		$output .= '<h3>What is this page? <em>Explained</em></h3>'."\n";
		$output .= '<p>This is a news article written by <a href="'.$array['author']['url'].'" class="fn">'.$array['author']['full-name'].'</a>. This entry is titled &#8220;';
		$output .= '<em class="entry-title">'.$array['title'].'</em>&#8221;. It was published on ';
		$output .= '<abbr title="'.$array['created']['iso8601'].'" class="created">'.$array['created']['full'].'</abbr>.';
		if(!empty($array['created']['ago'])) {
			$output .= ' ('.$array['created']['ago'].')';
		}
		if($array['comments']['active'] && $array['comments']['active']==2) {
			if(!empty($array['comments']['total'])) {
				$output .= ' There '.$grammar['present'][$grammar_type]['was'].' currently <a href="#comments-view" class="comments-total scrollto" title="View the reaction comment'.$plural_s.'">'.int_to_words($array['comments']['total']).' comment'.$plural_s.'</a>. I';
			}
			else $output .= ' There are currently no comments, i';
			$output .= 'f you have something to say, feel free to <a href="#comments-form" class="scrollto">add your own!</a>';
		}
		elseif(!empty($array['comments']['total'])) {
			$output .= ' Comments are now closed and <a href="#comments-view" class="comments-total scrollto" title="View the reaction comment'.$plural_s.'">'.int_to_words($array['comments']['total']).' comment'.$plural_s.'</a> '.$grammar['past'][$grammar_type]['was'].' added.';
		}
		$output .= '</p>'."\n";
		
		if(!empty($flickr_array)) {
			$output .= '<h4>Image Attribution</h4>'."\n";
			$output .= createList($flickr_array);
		}
		
		if(!empty($array['links']) && is_array($array['links'])) {
			if(!empty($array['links']['contain']) && is_array($array['links']['contain'])) {
				//$output .= '<h4>Links used in this article</h4>'."\n";
				//$output .= createList($array['links']['contain']);
			}
			if(!empty($array['links']['related']) && is_array($array['links']['related'])) {
				$output .= '<h4>Related Links</h4>'."\n";
				$output .= createList($array['links']['related']);
			}
		}

		$output .= '<!-- end of div id #entry-meta-extra-explain -->'."\n";
		$output .= '</div>'."\n";
				
	$output .= '<!-- end of div id #entry-meta-extra -->'."\n";
	$output .= '</div>'."\n";
	return $output;
}

function news_display_hot_topic($array,$type='general') {
	if(empty($array) || !is_array($array)) return false;
	$output = '';
	if(empty($type)) $type = 'general';
	$output .= '<div'.addAttributes('','hot-topic',array('column','last','news','mini-container','hfeed')).'>'."\n";
	if(strtolower($type)=='general') 	$output .= news_display_hot_topic_general($array);
	if(strtolower($type)=='extra') 		$output .= news_display_hot_topic_extra($array);
	$output .= '<!-- end of div id #hot-topics -->'."\n";
	$output .= '</div>'."\n";
	return $output;
}
function news_display_hot_topic_general($array) {
	$output = '';
	$output .= '<h3>Hot Topics <em>What I\'m Watching</em></h3>'."\n";
	$output .= '<div'.addAttributes('','ht_'.$array['id'],array('hentry')).'>'."\n";
	$output .= '<h3 class="entry-title">'.$array['title'].'</h3>'."\n";
	$output .= '<p class="timestamp">'.news_display_date($array['created'],$array['updated']).'</p>'."\n";
	$output .= '<div'.addAttributes('','',array('entry-content')).'>'.formatText($array['description']['main']).'</div>'."\n"; // edit_ht_content_'.$array['id']
	$output .= image_show($array['image']);
	$output .= '</div>'."\n";
	return $output;
}

function news_display_hot_topic_extra($array) {
	if(empty($array) || !is_array($array)) return false;
	$output = '';
	//$output .= '<h3>Latest <em>Hot Topic</em></h3>'."\n";
	$output .= '<div'.addAttributes('','ht_'.$array['id'].'_x',array('hentry')).'>'."\n";
	$output .= '<h3 class="entry-title"><a href="'.$array['author']['url'].'#hot-topic" class="url permalink bookmark hot-topic" rel="bookmark" title="Read the hot topic article for &#8220;'.formatText($array['title']).'&#8221;">'.$array['title'].'</a></h3>'."\n";
	//$output .= '<p class="timestamp">'.news_display_date($array['created'],$array['updated']).'</p>'."\n";
	$output .= '<div'.addAttributes('','',array('entry-summary')).'>'.formatText($array['description']['summary'],'output').'</div>'."\n";
	//$output .= '<div class="entry-content">'.formatText($array['description']['main']).'</div>'."\n";
	//$output .= image_show($array['image']);
	$output .= '<p class="more"><a href="'.$array['author']['url'].'#hot-topic" class="bookmark permalink hot-topic" rel="bookmark" title="Read the hot topic article for &#8220;'.formatText($array['title']).'&#8221;">More… <strong>'.$array['title'].'</strong></a></p>'."\n";
	$output .= '</div>'."\n";
	return $output;
}
function news_display_extra($array) {
	if(empty($array) || !is_array($array)) return false;
	
	$link_start = ''; $link_end = ''; $read_more = '';
	if($array['section']!='extra') {
		$link_start = '<a href="'.$array['permalink']['link'].'" class="url permalink bookmark" rel="bookmark" title="Read the news article for &#8220;'.formatText($array['title']).'&#8221;">';
		$link_end = '</a>';
		$read_more = '<p class="more"><a href="'.$array['permalink']['link'].'" class="bookmark permalink" rel="bookmark" title="Read the news article for &#8220;'.formatText($array['title']).'&#8221;">More… <strong>'.$array['title'].'</strong></a></p>'."\n";
	}
	
	$output = '';
	$output .= '<div'.addAttributes('','e_'.$array['id'].'_x',array('hentry')).'>'."\n";
	$output .= '<h3 class="entry-title">'.$link_start.$array['title'].$link_end.'</a></h3>'."\n";
	if($array['section']=='extra') {
		$output .= image_show($array['image']);
		$output .= '<div'.addAttributes('','',array('entry-content')).'>'.formatText($array['description']['main'],'output').'</div>'."\n";
	}
	else {
		$output .= '<div'.addAttributes('','',array('entry-summary')).'>'.formatText($array['description']['summary'],'output').'</div>'."\n";
	}
	$output .= $read_more;
	$output .= '</div>'."\n";
	return $output;
}

function news_display_permalink($array,$id='',$class='') {
	if(empty($array) || !is_array($array)) return false;
	
	$class_extra = array('hentry','permalink','vcard','author');
	if(!empty($class)) {
		if(!is_array($class)) $class = array($class);
		$class = array_merge($class,$class_extra);
	}
	else $class = $class_extra;

	$output = '<div'.addAttributes('','e_'.$array['id'],$class).'>'."\n";
	
	$output .= '<div class="column double">'."\n";
	$output .= news_display_meta($array,$class)."\n";
	$output .= '<div class="entry-content">'."\n".$array['description']['main'].'<!-- end of div class .entry-content -->'."\n".'</div>'."\n\n";
	$output .= '<!-- end of div class .column.double -->'."\n";
	$output .= '</div>'."\n";
	$output .= news_display_permalink_meta($array); // meta for permalink
	if(!empty($id) && validToken($id)) $output .= '<!-- end of div id #'.validToken($id).' -->'."\n\n";
	$output .= '</div>'."\n\n";

	if(!empty($array['comments']['total']) && $array['comments']['total']>=1) {
		$output .= news_comments_display($array,'comments-view'); // we have comments, so show the comments
	}
	if(!empty($array['comments']['active']) && $array['comments']['active']==2) {
		$output .= '<div id="comments-form">'."\n";
		$output .= news_comments_form($array); // show the comments form
		$output .= '<!-- end of div id #comments-form -->'."\n";
		$output .= '</div>'."\n\n";
	}

	return $output;
}

function news_display_date($created,$updated) {
	if(!is_array($created) || !is_array($updated)) return false;
	
	$created_sql = strtotime($created['sql']);
	$updated_sql = strtotime($updated['sql']);
	$difference_days = floor(($updated_sql-$created_sql)/(60*60*24));
	
	$date_class = array('updated');
	
	/*
	if($difference_days>=1) { // difference between updated and created is a day or more...
		// updated
		$date_text = $updated['long'];
		$date_title = $updated['iso8601'];
	}
	else {
	*/
		$date_class[] = 'published';
		$date_text = $created['long'];
		$date_title = $created['iso8601'];
	//}	
	return '<abbr class="'.addClass($date_class).'" title="'.$date_title.'">'.$date_text.'</abbr>';
}
?>