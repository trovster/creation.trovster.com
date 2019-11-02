<?php

class Guess	{
	
	var $prefixes = array('Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Herr', 'Monsieur', 'Hr', 'Frau', 'A V M', 'Admiraal', 'Admiral', 'Air Cdre', 'Air Commodore', 'Air Marshal', 'Air Vice Marshal', 'Alderman', 'Alhaji', 'Ambassador', 'Baron', 'Barones', 'Brig', 'Brig Gen', 'Brig General', 'Brigadier', 'Brigadier General', 'Brother', 'Canon', 'Capt', 'Captain', 'Cardinal', 'Cdr', 'Chief', 'Cik', 'Cmdr', 'Col', 'Col Dr', 'Colonel', 'Commandant', 'Commander', 'Commissioner', 'Commodore', 'Comte', 'Comtessa', 'Congressman', 'Conseiller', 'Consul', 'Conte', 'Contessa', 'Corporal', 'Councillor', 'Count', 'Countess', 'Crown Prince', 'Crown Princess', 'Dame', 'Datin', 'Dato', 'Datuk', 'Datuk Seri', 'Deacon', 'Deaconess', 'Dean', 'Dhr', 'Dipl Ing', 'Doctor', 'Dott', 'Dott sa', 'Dr', 'Dr Ing', 'Dra', 'Drs', 'Embajador', 'Embajadora', 'En', 'Encik', 'Eng', 'Eur Ing', 'Exma Sra', 'Exmo Sr', 'F O', 'Father', 'First Lieutient', 'First Officer', 'Flt Lieut', 'Flying Officer', 'Fr', 'Frau', 'Fraulein', 'Fru', 'Gen', 'Generaal', 'General', 'Governor', 'Graaf', 'Gravin', 'Group Captain', 'Grp Capt', 'H E Dr', 'H H', 'H M', 'H R H', 'Hajah', 'Haji', 'Hajim', 'Her Highness', 'Her Majesty', 'Herr', 'High Chief', 'His Highness', 'His Holiness', 'His Majesty', 'Hon', 'Hr', 'Hra', 'Ing', 'Ir', 'Jonkheer', 'Judge', 'Justice', 'Khun Ying', 'Kolonel', 'Lady', 'Lcda', 'Lic', 'Lieut', 'Lieut Cdr', 'Lieut Col', 'Lieut Gen', 'Lord', 'M', 'M L', 'M R', 'Madame', 'Mademoiselle', 'Maj Gen', 'Major', 'Master', 'Mevrouw', 'Miss', 'Mlle', 'Mme', 'Monsieur', 'Monsignor', 'Mr', 'Mrs', 'Ms', 'Mstr', 'Nti', 'Pastor', 'President', 'Prince', 'Princess', 'Princesse', 'Prinses', 'Prof', 'Prof Dr', 'Prof Sir', 'Professor', 'Puan', 'Puan Sri', 'Rabbi', 'Rear Admiral', 'Rev', 'Rev Canon', 'Rev Dr', 'Rev Mother', 'Reverend', 'Rva', 'Senator', 'Sergeant', 'Sheikh', 'Sheikha', 'Sig', 'Sig na', 'Sig ra', 'Sir', 'Sister', 'Sqn Ldr', 'Sr', 'Sr D', 'Sra', 'Srta', 'Sultan', 'Tan Sri', 'Tan Sri Dato', 'Tengku', 'Teuku', 'Than Puying', 'The Hon Dr', 'The Hon Justice', 'The Hon Miss', 'The Hon Mr', 'The Hon Mrs', 'The Hon Ms', 'The Hon Sir', 'The Very Rev', 'Toh Puan', 'Tun', 'Vice Admiral', 'Viscount', 'Viscountess', 'Wg Cdr');
	
	var $suffixes = array('Jr', 'Snr', 'Esq', 'Esquire', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'AA', 'AB', 'ABA', 'ABS', 'AS', 'BAcc', 'BE', 'BMath', 'BA Law', 'BA', 'BAdm', 'BBA', 'BBus', 'BCL (Oxon)', 'BCL', 'BCS', 'BCom', 'BComm', 'BD', 'BE', 'BEc', 'BEng', 'BFA', 'BHE', 'BHK', 'BJ', 'BM BS', 'BMus', 'BPE', 'BPharm', 'BPhil', 'BS', 'BSc', 'CAS', 'CE', 'ChE', 'DA', 'DBA', 'DC', 'DCL', 'DD', 'DDS', 'DJur', 'DLitt', 'DMA', 'DMD', 'DMus', 'DNursSci', 'DO', 'DOM', 'DPM', 'DPT', 'DPhil', 'DSW', 'DSc', 'DrPH', 'EAA', 'ECS', 'EE', 'EdS', 'EdD', 'EngD', 'EnvE', 'FdA', 'FdBus', 'FdEd', 'FdEng', 'FdMus', 'FdSc', 'FdTech', 'GP', 'JD', 'JSD', 'LLB', 'LLD', 'LLL', 'LLM', 'MEng', 'MDiv', 'MA', 'MALD', 'MApol', 'MB BCh BAO', 'MB BChir', 'MB BS', 'MB ChB', 'MBA', 'MBio', 'MChem', 'MD', 'MESci', 'MFA', 'MGeol', 'MJ', 'MLIS', 'MLitt', 'MMath', 'MMus', 'MPA', 'MPAff', 'MPH', 'MPM', 'MPP', 'MPT', 'MPhil', 'MPhys', 'MRE', 'MRes', 'MS', 'MSSc', 'MSW', 'MSc', 'MSci', 'MSt', 'MTCM', 'MTS', 'MTh', 'MTheol', 'MatE', 'MechE', 'NavE', 'NuclE', 'OD', 'OMD', 'OceanE', 'PhD', 'PharmD', 'PsyD', 'SB', 'SJD', 'SSP', 'STD', 'SysE', 'ThD', 'ThM', 'Hons', 'Cantab', 'Oxon');
	
	var $guess = '';
	
	function Guess($name) {
		$this->guess = $this->best_guess($name);
	}
	function output() {
		return $this->mark_up($this->guess);
	}
	function best_guess($name) {
		$n 	= $this->implied_nickname($name);
		if (!$n) $n	= $this->implied_n($name);
		if (!$n) $n	= $this->guesstimate($name);
		return $n;
	}
	function implied_n($name) {
		if (sizeof(explode(' ', $name)) == 2) {
			$patterns	= array();
			$patterns[] = array('/^(\S+),\s*(\S{1})$/', 2, 1); 		// Lastname, Initial
			$patterns[] = array('/^(\S+)\s*(\S{1})\.*$/', 2, 1); 	// Lastname Initial(.)
			$patterns[] = array('/^(\S+),\s*(\S+)$/', 2, 1); 		// Lastname, Firstname
			$patterns[] = array('/^(\S+)\s*(\S+)$/', 1, 2); 		// Firstname Lastname

			foreach ($patterns as $pattern) {
				if (preg_match($pattern[0], $name, $matches) === 1) {
					return $name;
				}
			}
		}
		return false;
	}
	function implied_nickname($name) {
		if (sizeof(explode(' ', $name)) == 1) {
			return $name;
		}
		return false;
	}
	function guesstimate($name) {
		$n		= array();

		while ($prefix	= $this->find_prefix($name)) {
			$n['honorific-prefix'][]	= $prefix;
			$name	= substr($name, strlen($prefix)+1);
		}

		while($suffix = $this->find_suffix($name)) {
			$n['honorific-suffix'][]	= $suffix;
			$name	= substr($name, 0, strlen($name)-strlen($suffix)-1);
		}


		// family first?
		if (preg_match('/^\S+,\s.*/', $name)) {
			$n['meta']['family-first']	= true;
			$parts	= explode(' ', implode(array_reverse(explode(', ', $name)), ' '));
		}
		else {
			$n['meta']['family-first']	= false;
			$parts	= explode(' ', $name);
		}


		if (sizeof($parts)>1) {
			$n['given-name']	= $parts[0];
			$n['additional-name'] = array();

			if (sizeof($parts)>2) for ($i=1; $i<sizeof($parts)-1; $i++) $n['additional-name'][]	= $parts[$i];
		}

		$n['family-name']	= $parts[sizeof($parts)-1];

		return $n;
	}
	function find_prefix($name) {
		$list	= $this->prefixes;
		$match 	= '';

		foreach ($list as $prefix) {
			if (preg_match("/^\b($prefix\.*)\s.*/i", $name, $matches)>0){
				if (strlen($matches[1]) > strlen($match)) $match = $matches[1];
			}
		}

		if ($match != '') return $match;

		return false;
	}
	function find_suffix($name) {
		$list	= $this->suffixes;
		
		foreach ($list as $suffix) {
			if (preg_match("/.*\b($suffix\.*)\$/i", $name, $matches)>0){
				return $matches[1];
			}
		}
		return false;
	}
	function mark_up($n) {
		//header('Content-type: text/html');
		
		if (is_array($n)){
			if (isset($n['family-name'])) {
				$s	= "" . '<span class="n">' . "\n";

				if (isset($n['honorific-prefix'])) {
					if (is_array($n['honorific-prefix'])){
						foreach ($n['honorific-prefix'] as $hs) $s .= "\t" . '<span class="honorific-prefix">' . htmlspecialchars($hs) . '</span> '. "\n";
					}else{
						$s	.= "\t" . '<span class="honorific-prefix">' . htmlspecialchars($n['honorific-prefix']) . '</span> '. "\n";
					}
				}

				if ($n['meta']['family-first']) {
					$s	.= "\t" . '<span class="family-name">' . htmlspecialchars($n['family-name']) . '</span>, '. "\n";
				}

				if (isset($n['given-name'])) {
					$s	.= "\t" . '<span class="given-name">' . htmlspecialchars($n['given-name']) . '</span> '. "\n";
				}

				if (isset($n['additional-name']) && is_array($n['additional-name'])) {
					foreach($n['additional-name'] as $a){
						$s .= "\t" . '<span class="additional-name">'.htmlspecialchars($a).'</span> '. "\n";
					}
				}

				if (!$n['meta']['family-first']) {
					$tail  = '';
					$n['family-name']	= str_replace(',', '', $n['family-name']);
					//if ($count > 0) $tail = ',';

					$s	.= "\t" . '<span class="family-name">' . htmlspecialchars($n['family-name']) . '</span>'.$tail.' '. "\n";
				}

				if (isset($n['honorific-suffix'])) {
					if (is_array($n['honorific-suffix'])){
						$tmp = '';
						foreach ($n['honorific-suffix'] as $hs) $tmp = "\t" . '<span class="honorific-suffix">' . htmlspecialchars($hs) . '</span> '. "\n" . $tmp;
						$s	.= $tmp;
					}else{
						$s	.= "\t" . '<span class="honorific-suffix">' . htmlspecialchars($n['honorific-suffix']) . '</span> '. "\n";
					}
				}

				$s	.= "" . '</span>' . "\n";

				return $s;
			}
			else {
				return implode($n, ' ');
			}	
		}
		return $n;
	}
}
?>
