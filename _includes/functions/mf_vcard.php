<?php
class hCard {
	var $Organisation;
	var $StreetAddress;
	var $ExtendedAddress;
	var $Locality;
	var $Region;
	var $Postcode;
	var $Latitude;
	var $Longitude;
	
	var $ContactNumber;
	var $EmailAddress;
	var $Map;
	
	var $telType;
	var $emailType;
	var $addressType;
	
	// constructor
	function hCard() {
		$this->telType 		= array('voice', 'home', 'msg', 'work', 'fax', 'cell', 'video', 'pager', 'bbs', 'modem', 'car', 'isdn', 'pcs');
		$this->emailType 	= array('internet', 'x400', 'pref');
		$this->addressType	= array('intl', 'postal', 'parcel', 'work', 'dom', 'home');
	}

	function Display() {
		if(empty($this->Organisation)) return; // if there is no organistation, no point!
		
		$vcard = '<dl class="vcard location">'."\n";
		$vcard .= "\t".'<dt class="vcard_address">Address</dt>'."\n";
		$vcard .= "\t".'<dd>'."\n";
		$vcard .= "\t\t".'<address class="adr">'."\n";
		$vcard .= "\t\t\t".'<strong class="org fn">'.formatText($this->Organisation,'capitals').'</strong>'."\n";
		if(isset($this->StreetAddress) && !empty($this->StreetAddress)) {
			$vcard .= "\t\t\t".'<span class="street-address">'.formatText($this->StreetAddress,'capitals').'</span>'."\n";
		}
		if(isset($this->ExtendedAddress) && !empty($this->ExtendedAddress)) {
			if(!is_array($this->ExtendedAddress)) $this->ExtendedAddress = array($this->ExtendedAddress);
			foreach($this->ExtendedAddress as $ext_address) {
				if(empty($ext_address)) continue;
				$vcard .= "\t\t\t".'<span class="extended-address">'.formatText($ext_address,'capitals').'</span>'."\n";
			}
		}
		if(isset($this->Locality) && !empty($this->Locality)) {
			$vcard .= "\t\t\t".'<span class="locality">'.formatText($this->Locality,'capitals').'</span>'."\n";
		}
		if(isset($this->Region) && !empty($this->Region)) {
			$vcard .= "\t\t\t".'<span class="region">'.formatText($this->Region,'capitals').'</span>'."\n";
		}
		if(isset($this->Postcode) && !empty($this->Postcode) && validate($this->Postcode,'postcode')) {
			$vcard .= "\t\t\t".'<span class="postal-code">'.validate($this->Postcode,'postcode').'</span>'."\n";
		}
		$vcard .= "\t\t".'</address>'."\n";
		$vcard .= "\t".'</dd>'."\n";
		
		if(isset($this->ContactNumber) && !empty($this->ContactNumber) && is_array($this->ContactNumber)) {
			$i=1; $checkArray = array();
			foreach($this->ContactNumber as $this->number => $this->type) {
				if(empty($this->type) || empty($this->number) || !validate($this->number,'telephone')) continue;
				if(!in_array(strtolower($this->type),$this->telType)) continue;
				
				// don't want duplicate numbers
				if(!in_array(validate($this->number,'telephone'),$checkArray)) $checkArray[] = validate($this->number,'telephone');
				else continue;
				
				if($i==1)$vcard .= "\t".'<dt class="vcard_tel"><acronym title="Telephone">Tel</acronym>:</dt>'."\n";
				$vcard .= "\t".'<dd class="tel"><span class="type">'.strtolower($this->type).'</span> <span class="value">'.validate($this->number,'telephone').'</span></dd>'."\n";
				$i++;
			}
		}
		
		if(isset($this->EmailAddress) && !empty($this->EmailAddress) && validate($this->EmailAddress,'email')) {
			$vcard .= "\t".'<dt class="vcard_email">Email:</dt>'."\n";
			$vcard .= "\t".'<dd class="email"><a href="mailto:'.validate($this->EmailAddress,'email').'">'.validate($this->EmailAddress,'email').'</a></dd>'."\n";
		}
		if(isset($this->Longitude) && isset($this->Latitude) && !empty($this->Longitude) && !empty($this->Latitude)) {
			$vcard .= "\t".'<dt class="vcard_geo">Geographical information</dt>'."\n";
			$vcard .= "\t".'<dd class="geo">'."\n";
			$vcard .= "\t\t".'<abbr class="latitude">'.$this->Latitude.'</abbr>'."\n";
			$vcard .= "\t\t".'<abbr class="longitude">'.$this->Longitude.'</abbr>'."\n";
			$vcard .= "\t".'</dd>'."\n";
		}
		$vcard .= '</dl>'."\n";		
		return $vcard;
	}
}
?>