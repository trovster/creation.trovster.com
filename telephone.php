<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
$currentSection = array('telephone');

/* information setup
============================================================================================================= */
$table_array = array(
	'header' => array(
		array('text' => 'Company Name', 'id' => 'company'),
		array('text' => 'Contact', 'id' => 'contact'),
		array('text' => 'Internal', 'id' => 'internal'),
		array('text' => 'Phone Number', 'id' => 'phone-number'),
		//array('text' => 'External <abbr title="Extension">Ext</abbr>', 'id' => 'external-extension')
	),
	'rows' => array()
);
$summary = 'Telephone Numbers';
$caption = 'Telephone Numbers';
$total_headers = count($table_array['header']);


/* file setup
=============================================================================================================*/
$handle = fopen($_SERVER['DOCUMENT_ROOT'].'/_files/telephone-numbers.csv', 'r');
$i=0;
while(($data = fgetcsv($handle, 1000, ','))!==FALSE) {
	$num = count($data);
	if($num<$total_headers || empty($data[0])) continue;
	$table_array['rows'][$i]['value'] = array();
	$table_array['rows'][$i]['class'] = array('vcard');
	
	for($c=0; $c<$num; $c++) {
		//$data[0] // company title
		//$data[1] // contact
		//$data[2] // internal
		//$data[3] // phone number
		//$data[4] // extental extension
		if($c==0) $class = array('org');
		if($c==1) $class = array('fn');
		if($c==2) $class = array('');
		if($c==3) $class = array('tel');
		if($c==4) {
			$class = array('');
			continue;
		}
		$table_array['rows'][$i]['value'][] = array(
			'text' => formatText(str_replace(array('&'),array('&#38;'),$data[$c])),
			'class' => $class,
		);
	}
	$i++;
}
fclose($handle);


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Telephone Numbers | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Telephone Numbers';
$header->dom[] = 'scripts/jquery.quicksearch.js';
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<?php echo createTable($table_array,$summary,$caption,'telephone-numbers'); ?>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>