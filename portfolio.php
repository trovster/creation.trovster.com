<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */		   
$query = portfolio_sql();

/* information setup
============================================================================================================= */
$header_title = ''; $i=-1; $d=-1;
$style_block = '';
$actives_array = array();
$category_array_check = array();
$portfolio_array = array();
while($array = mysqli_fetch_array($query)) {
	$array_setup = portfolio_setup($array);
	$c = $array_setup['category']['id'];
	$j = $array_setup['id'];

	if(!in_array($array_setup['category']['safe'],$category_array_check) && !empty($array_setup['stylesheet'])) {
		$category_array_check[] = $array_setup['category']['safe'];
		$i++; $d++;
		$portfolio_array[$c]['text'] = $array_setup['category']['title'];
		$portfolio_array[$c]['link'] = $array_setup['category']['permalink'];
		$portfolio_array[$c]['class'] = array($array_setup['category']['safe'],'column');
		$portfolio_array[$c]['rel'] = array('tag','subsection');
		if(!empty($gl_ir) && $gl_ir==true) $portfolio_array[$c]['gilder'] = true;
		
		$portfolio_array[$c]['detail'][$j] = $array_setup;
		
		if(
			(!empty($_GET['category']) && $_GET['category']==$array_setup['category']['safe']) ||
			(empty($_GET['category']) && $i==0)
		) {
			$portfolio_array[$c]['class'][] = 'active';
			$_GET['category'] = $array_setup['category']['safe'];
			$_GET['category_id'] = $array_setup['category']['id'];
			$category_matched=true;
		}
		
		$actives_array[$c]['style'] = $array_setup['stylesheet'];
		$actives_array[$c]['link'] = $array_setup['permalink'];
	}
	else {
		$d++;
		$portfolio_array[$c]['detail'][$j] = $array_setup;
	}
}

if(!empty($_GET['category']) && (empty($category_matched) || $category_matched==false)) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));

$i=0; $project_list_array = array(); $c=0; $p_c=0;
foreach($portfolio_array[$_GET['category_id']]['detail'] as $key => $project) {
	if(
		(!empty($_GET['project']) && !empty($_GET['category']) && $_GET['project']==$project['safe']) ||
		(empty($_GET['project']) && $i==0)) {
		
		// this is the active project detail
		$project_detail_array = $project;
		
		// add this active project in to the style link
		$c = $project['category']['id'];
		$actives_array[$c]['style'] = $project['stylesheet'];
		$actives_array[$c]['link'] = $project['permalink'];
		$p_c = $i;
		
		$portfolio_array[$c]['detail']['class'][] = 'active';
		$project['class'][] = 'active';
		
		// header for the page
		$header_title = $project['title'].' | '.$project['category']['title'].' |  A Portfolio Item by ';
		
		// set the category ID to the project ID
		setcookie('c_uk['.$project['category']['id'].'][id]', $project['id'], time()+(3600*24*7), '/');
	}
	$project_list_array[$i] = $project;
	$project_list_array[$i]['title'] = $project_list_array[$i]['meta-description'];
	$i++;
}

if(!empty($_GET['project']) && !empty($_GET['category']) && empty($project_detail_array)) {
	die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
}

$project_pagination = array();
if(!empty($project_list_array[$p_c-1]) && is_array($project_list_array[$p_c-1])) {
	$project_class = array('prev');
	$project_pagination[] = array(
		'text' => 'Previous Project',
		'link' => $project_list_array[$p_c-1]['permalink'],
		'class' => array_merge($project_class,$project_list_array[$p_c-1]['class']),
		'rel' => 'prev',
		'title' => $project_list_array[$p_c-1]['meta-description']
	);
}
if(!empty($project_list_array[$p_c+1]) && is_array($project_list_array[$p_c+1])) {
	$project_class = array('next');
	$project_pagination[] = array(
		'text' => 'Next Project',
		'link' => $project_list_array[$p_c+1]['permalink'],
		'class' => array_merge($project_class,$project_list_array[$p_c+1]['class']),
		'rel' => 'next',
		'title' => $project_list_array[$p_c+1]['meta-description']
	);
}



// set the active stylesheet based upon the cookie set
// but not for the active category
if(!empty($_COOKIE['c_uk']) && is_array($_COOKIE['c_uk'])) {
	foreach($_COOKIE['c_uk'] as $key => $category) {
		$id = $category['id'];
		if($_GET['category_id']!=$key && !empty($portfolio_array[$key])) {
			$actives_array[$key]['style'] = $portfolio_array[$key]['detail'][$id]['stylesheet'];
			$actives_array[$key]['link'] = $portfolio_array[$key]['detail'][$id]['permalink'];
			$portfolio_array[$key]['link'] = $actives_array[$key]['link'];
		}
	}
}
// loop through the active states and set the style block
foreach($actives_array as $key => $active) {
	$style_block .= $active['style']['full'];
}


/* form setup
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = $header_title.$header->title;
$header->className = $currentSection;
$header->stylesheet[] = array('file' => formatText('specifics/'.$currentSection[0],'url').'.css', 'media' => 'screen');
$header->stylesheet[] = array('file' => 'specifics/jcarousel.css', 'media' => 'screen');
$header->stylesheetBlock = $style_block;
$header->dom[] = 'scripts/jquery.jcarousel.js';
$header->dom[] = 'portfolio.js';
if(!empty($pagination_array) && is_array($pagination_array)) $header->rel = array_merge($header->rel,$pagination_array);
$header->heading = formatText($currentSection[0],'capitals');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
if(!empty($project_detail_array['meta-description'])) $header->metaDescription = $project_detail_array['meta-description'].' | '.$header->metaDescription;
$header->Display();

?>

	<div id="content-primary">
		<?php 
		if(!empty($project_detail_array) && is_array($project_detail_array)) {
			echo '<div'.addAttributes('','e_'.$project_detail_array['id'],array('project','column','double','first','vcard')).'>'."\n";
			echo portfolio_display_project_details($project_detail_array);
			
			if(!empty($project_pagination) && is_array($project_pagination)) echo pagination_display($project_pagination,'project-pagination','pagination');
			echo '<!-- end of div class .project -->'."\n";
			echo '</div>'."\n";
		}
		
		?>

		<div id="content-navigation" class="column double last">
			<h3>Content Navigation</h3>
			<?php
			if(!empty($portfolio_array) && is_array($portfolio_array)) {
				//for($i=1; $i<=count($portfolio_array); $i++) $portfolio_array[$i]['gilder'] = true;
				echo createList($portfolio_array);
			}
			?>
		<!-- end of div id #content-navigation -->
		</div>
		
		<div id="services-select" class="column selection">
			<h3>More… <em>Projects</em></h3>
			<?php
			if(!empty($project_list_array) && is_array($project_list_array)) {
				echo createDefinitionList($project_list_array);
			}
			?>
		</div>
		
		<div class="tagline column last">
			<?php
			if(!empty($project['category']['tagline'])) {
				echo $project['category']['tagline']['title'];
				echo '<blockquote>'.$project['category']['tagline']['text'].'</blockquote>';
			}
			?>
		</div>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>