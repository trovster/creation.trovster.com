<?php
function service_listing_setup($array) {
	if(empty($array) || !is_array($array)) return false; // no array, nothing to do
	extract($array);
	$return = array();

	$return['id'] = $Service_ID;
	$return['title'] = formatText($Service_Title);
	$return['description']['summary'] = truncateString($Service_Description, 300);
	$return['description']['main'] = formatText($Service_Description,'output');
	$return['created'] = news_date_setup($Service_Created);
	$return['updated'] = news_date_setup($Service_Updated,$Service_Created);
	$return['safe'] = trim($Service_Safe_URL,'/');
	$return['class'] = array(trim($Service_Safe_URL,'/'));

	return $return;
}
function services_setup($array,$full=true) {
	global $connect_admin;

	if(empty($array) || !is_array($array)) return false;

	extract($array);
	$return = array();

	$return['id'] = $Detail_ID;
	$return['title'] = formatText($Detail_Title);
	$return['description']['summary'] = $Detail_Summary;
	$return['description']['main'] = formatText($Detail_Description,'output');
	$return['type'] = strtolower($Detail_Type);
	$return['price'] = $Detail_Price;

	$return['page-title'] = $return['title'];
	if($return['type']!='introduction') $return['page-title'] .= ' <span>'.$return['type'].'</span>';

	$return['created'] = news_date_setup($Detail_Created);
	$return['updated'] = news_date_setup($Detail_Updated,$Detail_Created);
	$return['permalink'] = services_setup_category_permalink($Category_Safe_URL).$Detail_Safe_URL;
	$return['safe'] = trim($Detail_Safe_URL,'/');

	$return['class'][] = $return['safe'];
	$return['class'][] = $return['type'];

	$return['text'] = $return['title'];
	$return['link'] = $return['permalink'];
	$return['definition'] = $return['description']['summary'];

	$return['meta-description'] = $return['title'].' - '.$return['description']['summary'];

	$return['category']['permalink'] = services_setup_category_permalink($Category_Safe_URL);
	$return['category']['title'] = $Category;
	$return['category']['safe'] = trim($Category_Safe_URL,'/');
	$return['category']['colour']['dark'] = $Category_Colour_Dark;
	$return['category']['colour']['light'] = $Category_Colour_Light;
	$return['category']['created'] = news_date_setup($Category_Created);
	$return['category']['updated'] = news_date_setup($Category_Created,$Category_Created);

	if($full===true) {

	$portfolio_join_sql = "SELECT s_p_j.portfolio_details_ID
							FROM services_portfolio_join AS s_p_j
							WHERE s_p_j.services_details_ID = '".$return['id']."'";

	$return['portfolio'] = array();
	$portfolio_join_query = mysqli_query($connect_admin, $portfolio_join_sql);
	$portfolio_join_total = mysql_num_rows($portfolio_join_query);
	if($portfolio_join_total>0) {
		while($portfolio_join_array = mysqli_fetch_array($portfolio_join_query)) {
			$portfolio_query = portfolio_sql($portfolio_join_array['portfolio_details_ID']);
			$portfolio_array = mysqli_fetch_array($portfolio_query);
			$return['portfolio'][] = portfolio_setup($portfolio_array,false);
		}
	}

	//if($return['type']!='introduction') {

		$sql = "SELECT
				s_e.Text AS Extra_Text,
				s_e_t.Heading AS Extras_Heading,
				s_e_t.Type AS Extras_Type

				FROM services_extras AS s_e
				LEFT JOIN services_extras_type AS s_e_t ON s_e_t.ID = s_e.services_extras_type_ID
				LEFT JOIN services_extras_join AS s_e_j ON s_e_j.services_extras_ID = s_e.ID

				WHERE s_e_j.services_details_ID = '".mysqli_real_escape_string($connect_admin, $return['id'])."'

				ORDER BY
				s_e_t.Type ASC,
				s_e.Text = 'FREE initial consultation' DESC,
				s_e.Text = 'FREE advice on further design, marketing and promotion' ASC,
				s_e.Text LIKE '250%' ASC, s_e.Text LIKE '100%' ASC, s_e.Text LIKE '50%' ASC,
				s_e.Text ASC";
				//s_e.ID ASC,  - means more searchable

		$query = mysqli_query($connect_admin, $sql);
		$extras_type = array(); $extras_type_check = array();
		while($array = mysqli_fetch_array($query)) {
			if(!in_array($array['Extras_Type'],$extras_type_check)) {
				$extras_type_check[] = $array['Extras_Type'];
				$return['extras']['info'][] = array('heading' => $array['Extras_Heading'], 'type' => $array['Extras_Type']);
			}

			$type = strtolower($array['Extras_Type']);
			$return['extras'][$type][] = array('text' => $array['Extra_Text']);
		}

	//}

		$image_path = '/images/services/'.$return['category']['safe'].'/';
		$return['image'] = image_setup($return['id'],$return['safe'],'jpg',$image_path);
	}
	return $return;
}

function services_setup_category_permalink($category) {
	if(empty($category)) return false;
	return '/services/'.$category;
}
function services_display_mini($array) {
	if(empty($array) || !is_array($array)) return false;
	$return = array(); $i=0;
	foreach($array as $key => $project) {
		$return[$i]['text'] = '<strong>'.$project['title'].'</strong>';
		$return[$i]['link'] = $project['permalink'];
		$return[$i]['class'] = $project['class'];
		$i++;
	}
	return createList($return);
}
function services_display_project_details($array) {
	if(empty($array) || !is_array($array)) return false;

	$return = '<div class="image">'."\n";
	$return .= image_show($array['image']);
	$return .= '</div>'."\n";

	$return .= '<h3 class="entry-title">'.$array['page-title'].'</h3>'."\n";
	$return .= '<h4 class="subtitle">'.$array['description']['summary'].'</h4>'."\n";
	$return .= '<div class="description">'.$array['description']['main'].'</div>'."\n";


	if(!empty($array['price']) && $array['price']!='0') {
		//  style="color: #'.$array['category']['colour']['dark'].'"
		$return .= '<p class="price">'.$array['page-title'].' <em>£'.$array['price'].'</em></p>';
	}

	if(!empty($array['portfolio']) && is_array($array['portfolio'])) {
		// portfolio peices associated with this service/package
		// link to them
		$portfolio_list_array = array();
		foreach($array['portfolio'] as $key => $portfolio) {
			$portfolio_list_array[] = array(
				'text' => $portfolio['title'],
				'link' => $portfolio['permalink']
			);
		}

		$related_portfolio_title = 'View examples';
		if(count($portfolio_list_array)==1) $related_portfolio_title  = 'View an example';
		$related_portfolio_title .= ' of this package in our Portfolio';

		$return .= '<div'.addAttributes('','related-portfolio').'>'."\n";
		$return .= '<h4>'.$related_portfolio_title.'</h4>'."\n";
		$return .= createList($portfolio_list_array);
		$return .= '</div>'."\n";
	}

	if(!empty($array['extras']) && is_array($array['extras'])) {
		foreach($array['extras']['info'] as $key => $extras_info_array) {
			$type = $extras_info_array['type'];
			if(!empty($array['extras'][$type])) {
				$return .= '<h4>'.$extras_info_array['heading'].'</h4>'."\n";
				$return .= createList($array['extras'][$type],'',$type);
			}
		}
	}

	if($array['category']['safe']=='websites' && $array['type']!='introduction') {
		//$return .= '<p class="more-info">Your site could be live within 7 days, find out more at <a href="http://creation.trovster.com">creation.trovster.com</a></p>';
	}

	return $return;
}
?>
