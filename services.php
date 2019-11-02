<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
$sql = "SELECT

		DISTINCT pd.ID AS Detail_ID,
		pd.Title AS Detail_Title,
		pd.Type AS Detail_Type,
		pd.Created AS Detail_Created,
		pd.Updated AS Detail_Updated,
		pd.Safe_URL AS Detail_Safe_URL,
		pd.Summary AS Detail_Summary,
		pd.Description AS Detail_Description,
		pd.Price AS Detail_Price,

		cat.ID AS Category_ID,
		cat.Category,
		cat.Safe_URL AS Category_Safe_URL,
		cat.Created AS Category_Created,
		cat.Updated AS Category_Updated,
		cat.Colour_Dark AS Category_Colour_Dark,
		cat.Colour_Light AS Category_Colour_Light
		
		FROM services_details AS pd
		LEFT JOIN services_category AS cat ON cat.ID = Services_Category_ID
		
		WHERE pd.Active = '1'
		GROUP BY pd.ID
		ORDER BY
		cat.Category = 'Consultancy' DESC, cat.Category = 'Print' DESC, cat.Category = 'Websites' DESC,
		cat.Category = 'Branding' DESC, cat.Category = 'Advertising' DESC, cat.Category = 'Display' DESC,
		cat.Category ASC, pd.Position ASC, pd.Title ASC";
   
$query = mysqli_query($connect_admin, $sql);

$header_title = ''; $section_title = '';
$project_detail_array = array();
$category_list = array();
$array_setup = array(); $i=0;
$category_array_check = array(); $j=0; $t=0;
$pricelist_table_array = array(
	'header' => array(
		array('text' => 'Title', 'id' => 'title'),
		array('text' => 'Category', 'id' => 'category'),
		array('text' => 'Summary', 'id' => 'summary'),
		array('text' => 'Starting From (£)', 'id' => 'price'),
	),
	'rows' => array(),
	'footer' => array()
);

while($array = mysqli_fetch_array($query)) {
	$array_setup[$i] = services_setup($array);
	
	if($array_setup[$i]['type']!='introduction' && $array_setup[$i]['price']!='0') {
		$pricelist_table_array['rows'][$t]['value'][] = array('text' => '<a href="'.$array_setup[$i]['permalink'].'">'.$array_setup[$i]['title'].'</a>');
		$pricelist_table_array['rows'][$t]['value'][] = array('text' => $array_setup[$i]['category']['title']);
		$pricelist_table_array['rows'][$t]['value'][] = array('text' => $array_setup[$i]['description']['summary']);
		$pricelist_table_array['rows'][$t]['value'][] = array('text' => '£'.$array_setup[$i]['price']);
		$pricelist_table_array['rows'][$t]['class'][] = $array_setup[$i]['category']['safe'];
		$t++;
	}

	if(!in_array($array_setup[$i]['category']['safe'],$category_array_check)) {
		$category_array_check[] = $array_setup[$i]['category']['safe'];
		$category_list[$i]['text'] = $array_setup[$i]['category']['title'];
		$category_list[$i]['link'] = $array_setup[$i]['category']['permalink'];
		$category_list[$i]['class'] = array($array_setup[$i]['category']['safe'],'column');
		$category_list[$i]['rel'] = array('tag','subsection');
		$category_list[$i]['colour']['dark'] = $array_setup[$i]['category']['colour']['dark'];
		$category_list[$i]['colour']['light'] = $array_setup[$i]['category']['colour']['light'];
		if(!empty($gl_ir) && $gl_ir==true) $category_list[$i]['gilder'] = true;
		
		if(!empty($_GET['category']) && $_GET['category']==$array_setup[$i]['category']['safe']) {
			$category_list[$i]['class'][] = 'active';
			$header_title = $array_setup[$i]['category']['title'].' | ';
			$section_title = rtrim($array_setup[$i]['category']['title'],'s');
			$currentSection[] = 'category-'.$array_setup[$i]['category']['safe'];
			$category_matched = true;
		}
		elseif(empty($_GET['category']) && $i==0) {
			$category_list[$i]['class'][] = 'active';
			$header_title = $array_setup[$i]['category']['title'].' | '; 
			$section_title = rtrim($array_setup[$i]['category']['title'],'s');
			$_GET['category'] = $array_setup[$i]['category']['safe'];
			$currentSection[] = 'category-'.$array_setup[$i]['category']['safe'];
			$category_matched = true;
		}
	}
	$i++;
}

if(!empty($_GET['category']) && (empty($category_matched) || $category_matched==false)) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));

$header_title = formatText($currentSection[0],'capitals').' offered by ';

$project_list_array = array(); $i=0;
foreach($array_setup as $key => $project) {
	if(!empty($_GET['category']) && $_GET['category']==$project['category']['safe']) {
		if(!empty($_GET['project']) && !empty($_GET['category']) && $_GET['project']==$project['safe']) {
			$project['class'][] = 'active';
			$project_detail_array = $project;
			$this_page_url = $project_detail_array['permalink'];
			$category_search_array = array_search_recursive($project['category']['safe'],$category_list);
			
			if(!empty($stylesheet_category_search_array)) {
				$stylesheet_array[$stylesheet_category_search_array[0]] = $project['stylesheet'];
			}
			if(!empty($category_search_array)) {
				$category_list[$category_search_array[0]]['link'] = $project_detail_array['permalink'];
			}
			$header_title = $project_detail_array['page-title'].' | '.$project_detail_array['category']['title'].' '.$header_title;
		}
		elseif(empty($_GET['project']) && $i==0) {
			$project['class'][] = 'active';
			$project_detail_array = $project;
			$header_title = $project_detail_array['page-title'].' | '.$header_title;
		}
		$project_list_array[] = $project;
		$i++;
	}
}
if(!empty($_GET['project']) && !empty($_GET['category']) && empty($project_detail_array)) {
	die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
}


// general listings
$listing_sql = "SELECT sl.ID AS Service_ID,
				sl.Service AS Service_Title,
				sl.Created AS Service_Created,
				sl.Updated AS Service_Updated,
				sl.Service AS Service_Title,
				sl.Safe_URL AS Service_Safe_URL,
				sl.Description AS Service_Description
				
				FROM services_listing AS sl
				ORDER BY sl.Service ASC, sl.ID ASC";
				
$listing_query = mysqli_query($connect_admin, $listing_sql);

$listing_array = array(); $i=0; $listing_array_check = array();
while($array = mysqli_fetch_array($listing_query)) {
	$array_setup = service_listing_setup($array);
	if(!in_array($array_setup['safe'],$listing_array_check)) {
		$listing_array_check[] = $array_setup['safe'];
		$listing_array[$i]['text'] = $array_setup['title'];
		$listing_array[$i]['id'] = 'service-'.$array_setup['safe'];
		$listing_array[$i]['definition'] = $array_setup['description']['main'];
		$i++;
	}
}

/* information setup
============================================================================================================= */
if(!empty($_GET['pricelist'])) die(require_once($_SERVER['DOCUMENT_ROOT'].'/services-pricelist.php'));


/* form setup
============================================================================================================= */


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $header_title.$header->title;
$header->className = $currentSection;
$header->stylesheet[] = array('file' => 'specifics/services.css', 'media' => 'screen');
$header->heading = 'Services';
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->dom[] = 'scripts/jquery.accordion.pack.js';
$header->dom[] = 'services.js';
if(!empty($project_detail_array['meta-description'])) $header->metaDescription = $project_detail_array['meta-description'].' | '.$header->metaDescription;
$header->Display();
?>

	<div id="content-primary">

		<div id="service" class="column double first vcard">
			<?php if(!empty($project_detail_array) && is_array($project_detail_array)) echo services_display_project_details($project_detail_array); ?>
		<!-- end of div id #project -->
		</div>
		
		<div id="content-navigation" class="column double last">
			<h3>Content Navigation</h3>
			<?php
			if(!empty($category_list) && is_array($category_list)) {
				//for($i=0; $i<count($category_list); $i++) $category_list[$i]['gilder'] = true;
				echo createList($category_list);
			}
			?>
		<!-- end of div id #content-navigation -->
		</div>
		
		<div id="services-select" class="column selection">
			<h3><?php echo $section_title; ?> Solutions… <em>Keeping it Simple</em></h3>
			<?php
			if(!empty($project_list_array) && is_array($project_list_array)) {
				echo createDefinitionList($project_list_array);
			}
			?>
		</div>
		
		<div id="services-listing" class="column last">
			<h3>Our Services <em>What we do…</em></h3>
			<?php if(!empty($listing_array) && is_array($listing_array)) echo createDefinitionList($listing_array); ?>
		</div>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>