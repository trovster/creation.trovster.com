<?php

function portfolio_sql($id='',$sql_extra='') {
	global $connect_admin;
	$sql_where = '';

	if(!empty($id) && is_numeric($id)) {
		$sql_where = " AND pd.ID = '".mysqli_real_escape_string($connect_admin, $id)."' ";
	}

	$sql = "SELECT pd.ID AS Detail_ID,
			pd.Title AS Detail_Title,
			pd.Created AS Detail_Created,
			pd.Updated AS Detail_Updated,
			pd.Safe_URL AS Detail_Safe_URL,
			pd.Summary AS Detail_Summary,
			pd.Description AS Detail_Description,
			pd.Website AS Detail_Website,
			pd.Landing AS Detail_Landing,

			cat.ID AS Category_ID,
			cat.Category,
			cat.Safe_URL AS Category_Safe_URL,
			cat.Created AS Category_Created,
			cat.Updated AS Category_Updated,
			cat.TaglineHeading AS Category_Tagline_Heading,
			cat.Tagline AS Category_Tagline,

			ps.Sector AS Sector_Title,
			ps.Safe_URL AS Sector_Safe_URL,
			ps.Description AS Sector_Description,
			comp.Company AS Company_Title,
			comp.Safe_URL AS Company_Safe_URL,
			comp.Description AS Company_Description

			FROM portfolio_details AS pd
			LEFT JOIN portfolio_company AS comp ON comp.ID = pd.Portfolio_Company_ID
			LEFT JOIN portfolio_sector AS ps ON ps.ID = comp.Portfolio_Sector_ID
			LEFT JOIN portfolio_category AS cat ON cat.ID = Portfolio_Category_ID

			WHERE pd.Active = '1'".$sql_where.$sql_extra."
			ORDER BY
			cat.Category = 'Concepts' DESC, cat.Category = 'Print' DESC, cat.Category = 'Websites' DESC,
			cat.Category = 'Branding' DESC, cat.Category = 'Advertising' DESC, cat.Category = 'Display' DESC,
			cat.Category ASC, pd.Landing ASC, pd.Title ASC, comp.Company ASC";

	// echo $sql; exit;
	$query = mysqli_query($connect_admin, $sql);

	return $query;
}

function portfolio_setup($array,$full=true) {
	if(empty($array) || !is_array($array)) return false; // no array, nothing to do

	extract($array);
	$return = array();

	$return['id'] = $Detail_ID;
	$return['title'] = formatText($Detail_Title);
	$return['description']['summary'] = wordwrap($Detail_Summary,125);
	$return['description']['main'] = formatText($Detail_Description,'output');
	$return['created'] = news_date_setup($Detail_Created);
	$return['updated'] = news_date_setup($Detail_Updated,$Detail_Created);
	$return['permalink'] = portfolio_setup_category_permalink($Category_Safe_URL).$Detail_Safe_URL;
	$return['safe'] = trim($Detail_Safe_URL,'/');
	$return['class'] = array(trim($Detail_Safe_URL,'/'));

	if(!empty($Detail_Website) && $Detail_Website_Valid = validate($Detail_Website,'url')) $return['website'] = $Detail_Website_Valid;

	$return['category']['id'] = $Category_ID;
	$return['category']['permalink'] = portfolio_setup_category_permalink($Category_Safe_URL);
	$return['category']['title'] = $Category;
	$return['category']['safe'] = trim($Category_Safe_URL,'/');
	$return['category']['created'] = news_date_setup($Category_Created);
	$return['category']['updated'] = news_date_setup($Category_Created,$Category_Created);

	$return['category']['tagline']['title'] = formatText($Category_Tagline_Heading,'output');
	$return['category']['tagline']['text'] = formatText($Category_Tagline,'output');

	$return['text'] = $return['title'];
	$return['link'] = $return['permalink'];
	$return['definition'] = $return['description']['summary'];

	$return['meta-description'] = $return['title'].' - '.$return['description']['summary'];

	if($full===true) {
		$return['stylesheet'] = portfolio_setup_stylesheet($return);
		$return['company'] = portfolio_setup_company($array);
		$return['images'] = portfolio_setup_images($return);
		if($return['images']>0) $return['images']['total'] = count($return['images']);
		$return['images'] = cleanArray($return['images']);
	}
	return $return;
}
function portfolio_setup_stylesheet($array) {
	if(empty($array) || !is_array($array)) return false;
	$stylesheet_path = '/images/portfolio/'.$array['category']['safe'].'/';
	$stylesheet_image = image_setup($array['id'],trim($array['safe'],'/').'_navigation','jpg',$stylesheet_path);

	if(empty($stylesheet_image['file'])) return false;

	$return = array();
	$return['image'] = $stylesheet_image['file']['full-path'];
	$return['selector'] = "\t".'#content-navigation ul li.'.$array['category']['safe'].' a, #content-navigation ul li.'.$array['category']['safe'].' a span.gl-ir';
	$return['category'] = $array['category']['safe'];
	$return['full'] = $return['selector'].'{'."\n\t\t".'background-image: url('.$return['image'].');'."\n\t".'}'."\n";

	return $return;
}
function portfolio_setup_images($array) {
	global $connect_admin;

	if(empty($array) || !is_array($array)) return false;

	$image_path = '/images/portfolio/'.$array['category']['safe'].'/';

	$sql = "SELECT pdi.ID AS Image_ID,
			pdi.Image_Alt_Text,
			pdi.Safe_URL AS Image_Safe_URL,
			pdi.Description AS Image_Description,
			pdi.Extension AS Image_Extension,
			pdi.Position AS Image_Position
			FROM portfolio_details_images AS pdi
			LEFT JOIN portfolio_details_images_join AS pdij ON pdi.ID = pdij.Portfolio_Image_ID
			WHERE pdij.Portfolio_Detail_ID = '".mysqli_real_escape_string($connect_admin, $array['id'])."'
			ORDER BY pdi.Position ASC, pdi.Image_Alt_Text ASC";

	$query = mysqli_query($connect_admin, $sql);
	$return = array(); $i=0;
	while($image_array = mysqli_fetch_array($query)) {
		$image_name = $array['id'].'-'.trim($image_array['Image_Safe_URL']);
		$j = $i+1;
		$alt_text = $array['company']['title'].' '.$array['title'].' '.$image_array['Image_Alt_Text'];
		if(image_setup($image_array['Image_ID'],$image_name,$image_array['Image_Extension'],$image_path)) {
			$return[$i] = image_setup($image_array['Image_ID'],$image_name,$image_array['Image_Extension'],$image_path,$alt_text,$image_array['Image_Alt_Text'],'','i'.$j);
			$return[$i]['permalink'] = $array['permalink'].$image_array['Image_Safe_URL'];
			$return[$i]['safe'] = trim($image_array['Image_Safe_URL'],'/');
			$return[$i]['id'] = 'i'.$j;
			$return[$i]['image-id'] = 'p'.$j;
			$i++;
		}
	}

	return $return;
}
function portfolio_setup_company($array) {
	if(empty($array) || !is_array($array)) return false;
	$return = array();
	$return['title'] = formatText($array['Company_Title']);
	$return['safe'] = $array['Company_Safe_URL'];
	$return['description'] = formatText($array['Company_Description']);
	$return['permalink'] = '/portfolio/company/'.$array['Company_Safe_URL'];
	return $return;
}
function portfolio_setup_category_permalink($category) {
	if(empty($category)) return false;
	return '/portfolio/'.$category;
}
function portfolio_display_mini($array) {
	if(empty($array) || !is_array($array)) return false;
	$return = array(); $i=0;
	foreach($array as $key => $project) {
		$return[$i]['text'] = '<strong>'.$project['title'].'</strong> '.$project['company']['title'];
		$return[$i]['link'] = $project['permalink'];
		$return[$i]['class'] = $project['class'];
		$i++;
	}
	return createList($return);
}
function portfolio_display_project_details($array) {
	if(empty($array) || !is_array($array)) return false;
	$return = portfolio_display_project_image($array['images']);
	$return .= '<h3>'.$array['title'].' <em class="fn org" id="company-'.trim($array['company']['safe'],'/').'">'.$array['company']['title'].'</em></h3>'."\n";
	if(!empty($array['website'])) $return .= '<p class="link"><a href="'.$array['website'].'" class="url external" rel="external" rev="author">'.trim(trim($array['website'],'http://'),'www.').'</a></p>'."\n";
	$return .= '<div class="description">'.$array['description']['main'].'</div>'."\n";
	return $return;
}

function portfolio_display_project_image($array) {
	if(empty($array) || !is_array($array)) return false;
	$array_setup = portfolio_display_project_images($array);
	$return = '<div class="image">'."\n";
	$return .= createList($array_setup['navigation'],'position-navigation','','ol');
	$return .= $array_setup['image'];
	$return .= pagination_display($array_setup['pagination'],'image-pagination');
	$return .= '</div>'."\n";
	return $return;
}

function portfolio_display_project_images($array) {
	if(empty($array) || !is_array($array) || empty($array['total']) || !is_numeric($array['total'])) return false;

	$image_total = $array['total'];
	unset($array['total']);

	$i=0; $image_key_next = 1; $image_key_prev = -1;
	$pagination_array = array(); $image = ''; $pagination = ''; $image_navigation = array();
	foreach($array as $key => $image_array) {
		$image_navigation[$i] = array(
			'text' => $image_array['text']['title'],
			'link' => $image_array['permalink'],
			'class' => array(),
			'id' => $image_array['image-id']
		);
		if(!empty($_GET['image']) && $_GET['image']==$image_array['safe']) {
			// specific image
			$image = image_show($image_array);
			$image_permalink = $image_array['permalink'];
			$image_navigation[$i]['class'] = 'active';
			$image_key_next = $key+1;
			if($key>=1) $image_key_prev = $key-1;
		}
		elseif(empty($_GET['image']) && $i==0) {
			// default image
			$image = image_show($image_array);
			$image_permalink = '';
			$image_navigation[$i]['class'] = 'active';
		}
		$pagination_array[] = array('link' => $image_array['permalink'], 'title' => @$image_array['text']['alt']);
		$i++;
	}

	$pagnation = pagination_setup($pagination_array,$image_key_next,$image_key_prev,$image_total);

	return array('image' => $image, 'pagination' => $pagnation, 'permalink' => $image_permalink, 'navigation' => $image_navigation);
}
?>
