<?php
/* FILE SIZE -- Take a number and return the number followed by filesize suffix
-------------------------------------------------------------------------------------------------- */
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/size_readable.php');

/* ORDINAL NUMBERS -- Take a number and adds the English ordinal suffix
-------------------------------------------------------------------------------------------------- */
function ordinalNumber($number) {
    if($number % 100 > 10 && $number %100 < 14):
        $suffix = "th";
    else:
        switch($number % 10) {
            case 0:
                $suffix = "th";
                break;
            case 1:
                $suffix = "st";
                break;
            case 2:
                $suffix = "nd";
                break;
            case 3:
                $suffix = "rd";
                break;
            default:
                $suffix = "th";
                break;
        }
    endif;
    return $number.'<sup>'.$suffix.'</sup>';
}

/* LONGITUDE / LATITUDE - Taken from Google with a postcode
-------------------------------------------------------------------------------------------------- */
function coordinates($postcode) {
	$results = file_get_contents('http://maps.google.com/maps?q='.$postcode);
	
	// Relying on the fact that the long and lat coordinates will be somewhere on the maps page...
	preg_match('*lat\:\ -?\d+\.\d+\,lng\:\ -?\d+\.\d+*', $results, $matches);
	
	// Start splitting up the results
	$latlong = explode(",",$matches[0]);
	$lat = substr(substr($latlong[0], 0, -1), 5);
	$long = substr(substr($latlong[1], 0, -1), 5);
	
	// echo the coordinates
	return array('lon' => $long, 'lat' => $lat);
}

function isEven($int) {
	if(is_numeric($int)) {
		if($int % 2 == 0) return TRUE;
		else return FALSE;
	}
	else return FALSE;
}
?>