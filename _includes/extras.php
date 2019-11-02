<?php

/* Extra area functions
============================================================================================================= */
function extra_display($text) {
	$return = '';
	if(!empty($text)) {
		$return .= '<div id="extra">'."\n";
			$return .= '<h3 id="extra-title">Things of interest <em>Well at least to us</em></h3>'."\n";
			$return .= $text;
		$return .= '</div>'."\n";
	}
	return $return;
}

/* Extra area setup
============================================================================================================= */
$extra_text = '';

if(!empty($extra_show) && $extra_show==true) {
	// latest EXTRA news
	$extra_news_article_array = related_setup('',' LIMIT 0,2', true, 3);
	if(!empty($extra_news_article_array) && !empty($extra_news_article_array[0])) {
		$extra_text .= '<div'.addAttributes('','news-extra',array('column')).'>'."\n";
		$extra_text .= news_display_extra($extra_news_article_array[0],'extra');
		$extra_text .= '</div>'."\n";
	}
	
	// second latest news
	$second_news_article_array = related_setup('',' LIMIT 1,1');
	if(!empty($second_news_article_array) && !empty($second_news_article_array[0])) {
		$extra_text .= '<div'.addAttributes('','news-article-no2',array('column')).'>'."\n";
		$extra_text .= news_display_extra($second_news_article_array[0],'extra');
		$extra_text .= '</div>'."\n";
	}
	
	// latest hot-topic
	$extra_hot_topic_array = hot_topic_setup('',' LIMIT 0,1'); $exclude_author = '';
	if(!empty($extra_hot_topic_array) && !empty($extra_hot_topic_array[0])) {
		$extra_text .= '<div'.addAttributes('','more-hot-topics',array('column')).'>'."\n";
		$extra_text .= news_display_hot_topic($extra_hot_topic_array[0],'extra');
		
		// another hot-topic (not from the same user)
		$exclude_author_sql = " AND ad.ID != '".$extra_hot_topic_array[0]['author']['id']."'";
		$extra_hot_topic_array_no2 = hot_topic_setup($exclude_author_sql,' LIMIT 0,1');
		if(!empty($extra_hot_topic_array_no2) && !empty($extra_hot_topic_array_no2[0])) {
			$extra_text .= '<div'.addAttributes('','',array('second')).'>'."\n";
			$extra_text .= news_display_hot_topic($extra_hot_topic_array_no2[0],'extra');
			$extra_text .= '</div>'."\n";
		}
		$extra_text .= '</div>'."\n";
	}
	
	// latest EXTRA news - part two
	if(!empty($extra_news_article_array) && !empty($extra_news_article_array[1])) {
		$extra_text .= '<div'.addAttributes('','news-extra',array('column','last')).'>'."\n";
		$extra_text .= news_display_extra($extra_news_article_array[1],'extra');
		$extra_text .= '</div>'."\n";
	}
	
	echo extra_display($extra_text);
}