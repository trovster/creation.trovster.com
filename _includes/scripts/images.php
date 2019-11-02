<?php 
class picture {
	//ErrorMessages
	var $no_gd = "No gdlib version found!";
	var	$not_supported = "This Graphictype is not supported in your current GDlib Version";		
	var $wrong_filetype = "The chosen filetype is not a graphic format";
	var $wrong_graphictype = "The chosen filetype is a graphic format which is not supported";
	var $file_to_big = "The chosen filesize is to big to upload and convert";
	var $upload_successful = "Fileupload was successful!";
	var $internal_error = "Internal Error";				
	
	//Constructor Function
	function picture($temp_path='../tmp/',$lang='en') {
		if($this->gdVersion() == TRUE) {
			$this->temp_dir = $temp_path;
			$gen_info = $this->getGDinfo();
			$this->support_png = $gen_info["PNG Support"];
			$this->support_gif_read = $gen_info["GIF Read Support"];
			$this->support_gif_write = $gen_info["GIF Create Support"];	
			$this->support_jpg = $gen_info["JPG Support"];
			$this->support_wml = $gen_info["WBMP Support"];	
			if($this->support_gif_read == TRUE && $this->support_gif_write == TRUE) $this->support_gif_full = TRUE;
		}
		else {
			$this->setMsg($this->no_gd);
		}
	}
	
	//Private Functions
	function bytesTokb ($int) {
		 return round($int/1000,2); //kb /1024
	}	
	
	function setMsg($msg=NULL, $file=NULL, $error=true) {
		$this->error = $error;		
		if (is_file($file) && $file != NULL) @unlink($file);		
		if ($msg != NULL) return $this->error_message = $msg;
	}
		
	function getMimeType($im) {
		if (is_array($im)) {
			return $im['type'];
		} else {
			$size = @getimagesize ($im);
			$preg = "/image\//";
			return $size['mime'];
		}
	}
	
	function getmicrotime() { 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec);
	} 
	
	function gdVersion() {
	   if (!extension_loaded('gd')) return false;
	   ob_start();
	   phpinfo(8);
	   $info=ob_get_contents();
	   ob_end_clean();
	   $info=stristr($info, 'gd version');
	   preg_match('/\d/', $info, $gd);
	   return $gd[0];
	}
	
	function getUploadMaxFilesize() {
		$str =  ini_get('upload_max_filesize');
		$prefix = array('k','M');
		$nr = array('000','000000');  
		$val = str_replace($prefix,$nr,$str);
		return $val;	
	}
	
	function eval_imagecreatefrom($pic) {
		if (isset($this->current_format)) {
			if ($this->support_png == 1 && $this->current_format == 'png') {
				return imagecreatefrompng($pic);
			}
			if ($this->support_jpg == 1 && $this->current_format == 'jpg') {
				return imagecreatefromjpeg($pic);
			}
			if ($this->support_gif_write == 1 && $this->current_format == 'gif') {
				return imagecreatefromgif($pic);
			}
		} else {
			return false;
		}
	}
	
	function eval_imagecreate($_width, $_height, $bgrcolor=NULL) {
		if ($this->gdVersion() >=2) {	
			$im =  imagecreatetruecolor($_width, $_height);     
		} else {	
			$im = imagecreate($_width, $_height);
		}
		if ($bgrcolor != NULL) imagefill($im ,0 ,0 ,$this->eval_imagecolorallocate($im, $bgrcolor));
		return $im;
	}
	
	function eval_imagecolorallocate($im, $hex) {
		$color = str_replace('#','',$hex);
		$rgb = array('r' => hexdec(substr($color,0,2)),
				     'g' => hexdec(substr($color,2,2)),
				     'b' => hexdec(substr($color,4,2)));
		return imagecolorallocate($im,$rgb['r'],$rgb['g'],$rgb['b']);
	}
	
	
	function eval_imagecopy($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH, $smooth=true) {
		//resource dst_im, resource src_im, int dstX, int dstY, int srcX, int srcY, int dstW, int dstH, int srcW, int srcH
		if ($this->gdVersion() >=2 && $smooth == true) {	
			return imagecopyresampled($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH); 
		} else {
			return imagecopyresized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
		}
	}

	function eval_savepicture($pic, $jpeg_quality) {
		if($this->current_format == 'jpg') {
			touch($this->temp_dir.$this->con_file);
			imagejpeg($pic,$this->temp_dir.$this->con_file,$jpeg_quality);		
		} else if ($this->current_format == 'png') {
			imagepng($pic,$this->temp_dir.$this->con_file);
		} else if ($this->current_format == 'gif'){
			imagegif($pic,$this->temp_dir.$this->con_file);
		}						
	}

	function getExt($im) {
		$mime = $this->getMimeType($im);
		switch ($mime) {   
			case "image/pjpeg":     		//the uploaded image is a jpg/jpeg
			case "image/jpeg":   
				if ($this->support_jpg == NULL) $this->setMsg($this->not_supported); 
				return $this->current_format = "jpg";
				break;    
			case "image/x-png":        		//the uploaded image is a png	
			case "image/png":
				if ($this->support_jpg == NULL) $this->setMsg($this->not_supported);
				return $this->current_format = "png";
				break;    
			case "image/gif":				//the uploaded image is a gif					
				if ($this->support_gif_full == NULL) $this->setMsg($this->not_supported);
				return $this->current_format = "gif";
				break;
			default:
				$ext = "tmp";
				$del = $this->temp_dir.$this->timestamp.".".$ext;
				return $this->current_format = $ext;	
				break;	
		}
	}
	
	function saveImg($im) {
		$ext = $this->getExt($im);
		if(empty($this->error) || $this->error == NULL) {
			$this->old_filename = $this->timestamp.".".$ext;
			$path = $this->target_path = $this->temp_dir.$this->old_filename;	
			switch ($im) {   
				case is_array($im):
					copy($im['tmp_name'], $path);
					break;
				default:
					if (preg_match("/(http|ftp):\/\//i", $im)) { 
						$input = fopen($im, "rb"); 
						$im_data = @file_get_contents($im);
						fclose($input); 
						$fp = fopen($path,"w");
						fwrite($fp,$im_data);
						fclose($fp);
					} else {
						copy ($im,$path);
					}
			}	
			return $path;
		}
	}
	
	function getGDinfo() {
       $array = array("GD Version" => "","FreeType Support" => 0, "FreeType Support" => 0, "FreeType Linkage" => "",  
	   					"T1Lib Support" => 0, "GIF Read Support" => 0, "GIF Create Support" => 0,"JPG Support" => 0,   
						"PNG Support" => 0, "WBMP Support" => 0,"XBM Support" => 0);
       $gif_support = 0;
       ob_start();eval("phpinfo();"); $info = ob_get_contents();  ob_end_clean();    
       foreach(explode("\n", $info) as $line) {
           if(strpos($line, "GD Version")!==false)  		$array["GD Version"] = trim(str_replace("GD Version", "", strip_tags($line)));
           if(strpos($line, "FreeType Support")!==false)  	$array["FreeType Support"] = trim(str_replace("FreeType Support", "", strip_tags($line)));
           if(strpos($line, "FreeType Linkage")!==false)   	$array["FreeType Linkage"] = trim(str_replace("FreeType Linkage", "", strip_tags($line)));
           if(strpos($line, "T1Lib Support")!==false)  		$array["T1Lib Support"] = trim(str_replace("T1Lib Support", "", strip_tags($line)));
           if(strpos($line, "GIF Read Support")!==false)   	$array["GIF Read Support"] = trim(str_replace("GIF Read Support", "", strip_tags($line)));
           if(strpos($line, "JPG Support")!==false)    		$array["JPG Support"] = trim(str_replace("JPG Support", "", strip_tags($line)));
           if(strpos($line, "PNG Support")!==false)    		$array["PNG Support"] = trim(str_replace("PNG Support", "", strip_tags($line)));
           if(strpos($line, "WBMP Support")!==false) 		$array["WBMP Support"] = trim(str_replace("WBMP Support", "", strip_tags($line)));
           if(strpos($line, "XBM Support")!==false) 		$array["XBM Support"] = trim(str_replace("XBM Support", "", strip_tags($line)));
           if(strpos($line, "GIF Support")!==false)       	$gif_support = trim(str_replace("GIF Support", "", strip_tags($line)));
           if(strpos($line, "GIF Create Support")!==false)  $array["GIF Create Support"] = trim(str_replace("GIF Create Support", "", strip_tags($line)));   
       }       
       if($gif_support==="enabled") 				{$array["GIF Read Support"]  = 1; $array["GIF Create Support"] = 1;}
       if($array["FreeType Support"]==="enabled")	{$array["FreeType Support"] = 1;}
       if($array["T1Lib Support"]==="enabled")		{$array["T1Lib Support"] = 1;}    
       if($array["GIF Read Support"]==="enabled")	{$array["GIF Read Support"] = 1;} 
       if($array["GIF Create Support"]==="enabled") {$array["GIF Create Support"] = 1;}    
       if($array["JPG Support"]==="enabled")		{$array["JPG Support"] = 1;}           
       if($array["PNG Support"]==="enabled")		{$array["PNG Support"] = 1;}          
       if($array["WBMP Support"]==="enabled")		{ $array["WBMP Support"] = 1;}           
       if($array["XBM Support"]==="enabled")		{ $array["XBM Support"] = 1;}       
       return $array;
	}

	
	
	//Puplic Functions
	function Upload($im, $maxfilesize=NULL) {		//first function just to upload and check file
		if($maxfilesize == NULL) $maxfilesize = $this->getUploadMaxFilesize();
		$this->timestamp = $this->getmicrotime();
		$im = $this->saveImg($im);
		if(empty($this->error) || $this->error == NULL) {		
			$this->o_filesize =  $this->bytesTokb(filesize($im));		
			if ($this->o_filesize > $maxfilesize) {
				$this->setMsg($this->file_to_big, $this->target_path);
			} else {
				$file_info = getimagesize($this->target_path);
				$this->width = $file_info[0];				//save width
				$this->height = $file_info[1];				//save height
				if ($this->width > $this->height) {
					$this->pic_format = "-";
				} else if ($this->width < $this->height) {
					$this->pic_format = "|";
				} else {
					$this->pic_format = "o";
				}
				$this->setMsg($this->upload_successful, NULL, false);
				return $this->target_path;
			}
		} 
	}
	/* edited
	function copy($dest, $name=NULL) {
		$source = $this->converted_file;
		if ($name == NULL) $destination = $dest.$this->timestamp.".".$this->current_format;
		else $destination = $dest.$name.".".$this->current_format;
		copy($source, $destination);
		return $destination;
	}
	*/
	function copy($dest, $name=NULL) {
		$source = $this->converted_file;
		$format = $this->current_format;
		if(empty($this->current_format)) $format = '';
		else '.'.$this->current_format;

		if($name == NULL) $destination = $dest.$this->timestamp.$format;
		else $destination = $dest.$name.$format;
		copy($source, $destination);
		return $destination;
	}
	
	function SqualeFolderTo($folder, $_width, $_height, $func='SetSize', $jpeg_quality=75, $target_folder=NULL, $bgrcolor='#000000', $smooth=true) {
		$dir = @opendir($folder);

		if ($this->support_jpg == TRUE) $regEx = "pjpeg|jpeg";
		if ($this->support_png == TRUE) {
			if ($regEx == NULL) $regEx .= "png|x-png";
			else $regEx .= "|png|x-png";
		}
		if ($this->support_gif_full == TRUE) {
			if ($regEx == NULL) $regEx .= "gif";
			else $regEx .= "|gif";
		}
		while($file = readdir($dir)){
			if (preg_match("/^(image\/)($regEx)$/", $this->getMimeType($folder.$file))) {
				$im = $folder.$file;
				$this->timestamp = $this->getmicrotime();
				$im = $this->saveImg($folder.$file);
				$file_info = getimagesize($im);
				$this->width = $file_info[0];				//save width
				$this->height = $file_info[1];				//save heights
				if ($this->width > $this->height) $this->pic_format = "-";
				else if ($this->width < $this->height) $this->pic_format = "|";
				else $this->pic_format = "o";
						
				if ($this->error == NULL) {	
					if (is_array($_width) || is_array($_height) || is_array($target_folder) || is_array($func)) {
						$lenW = sizeof($_width);
						$lenH= sizeof($_height);
						$len = sizeof($target_folder);
						$lenF = sizeof($func);
						if ($lenW == $lenH && $lenH == $len && $len == $lenF) {
							for($i=0;$i<$len;$i++) {
								if ($func[$i] == 'SetSize' && $_height[$i] == NULL) $_height[$i] = $_width[$i];
								if ($func[$i] == 'SqualeTo' && $_height[$i] == NULL) $_height[$i] = $_width[$i]*($this->height/$this->width);
								
								$eval = '$src = $this->'.$func[$i].'('.$_width[$i].','.$_height[$i].','.$smooth.','.$jpeg_quality.',"'.$bgrcolor.'");';
								eval ($eval);
								if ($target_folder[$i] != NULL) {
									if (!is_dir( $target_folder[$i])) mkdir ($target_folder[$i], 0777);
									$dest = $target_folder[$i].$this->timestamp.".".$this->current_format;
									copy($src,$dest);
									$array[$i][] = $dest;
								} else {
									$array[$i][] = $src;
								}
							}
						} else {
							$this->setMsg($this->internal_error);
						}
						
					} else {
						$eval = '$src = $this->'.$func.'('.$_width.','.$_height.','.$smooth.','.$jpeg_quality.',"'.$bgrcolor.'");';
						eval ($eval);
						if ($target_folder != NULL) {
							if (!is_dir($target_folder)) mkdir ($target_folder, 0777);
							$dest = $target_folder.$this->timestamp.".".$this->current_format;
							copy($src,$dest);
							$array[] = $dest;
						} else {
							$array[] = $src;
						}
					}
				} 
			}
		}
		return $array;
	}
	
	function SetSize($_width, $_height=NULL, $smooth=true, $jpeg_quality=75 ,$bgrcolor='#000000', $newtimestamp=false) {
		if ($_height == NULL) $_height = $_width;
		if($this->error == 0 && isset($this->current_format)) {
			if ($newtimestamp == true || $this->timestamp == NULL) $this->timestamp = $this->getmicrotime();
			if ($bgrcolor == NULL) $bgrcolor='#000000';
			$this->con_file = $this->timestamp.".ss.".$this->current_format;
			$this->wanted_width = $_width;
			$this->wanted_height = $_height;	

			switch ($this->pic_format) {  
				case "-": //if picformat = landscape
					$scalefactor = $_height/$this->height;
					$temp_width = $this->width * $scalefactor;
					$temp_height = $this->height  * $scalefactor;
					$pox_x = (($temp_width/2)-($_width/2)) * (-1);
					$pox_y = 0;
					break;
				case "o":
				case "|": //if picformat = portait
					$scalefactor = $_width/$this->width;
					$temp_width = $this->width * $scalefactor;
					$temp_height = $this->height  * $scalefactor;
					$pox_x = 0;
					$pox_y = (($temp_height/2)-($_height/2)) * (-1);
					break;
			}
			
			$src_im = $this->eval_imagecreatefrom($this->target_path);
			$dst_im = $this->eval_imagecreate($_width, $_height, $bgrcolor);
			$this->eval_imagecopy($dst_im,$src_im, $pox_x, $pox_y, 0, 0,$temp_width,$temp_height,$this->width,$this->height, $smooth);						
			$this->eval_savepicture($dst_im, $jpeg_quality);
			$this->converted_file = $this->temp_dir.$this->con_file;
			$this->Size = $this->bytesTokb(filesize($this->converted_file));
			return  $this->converted_file;
		}		
	}
	
	function SqualeTo($_width, $_height=NULL, $smooth=true, $jpeg_quality=75, $bgrcolor='#000000', $newtimestamp=false){
		if ($_height == NULL) $_height = $_width*($this->height/$this->width);
		if($this->error == 0 && isset($this->current_format)) {
			if ($newtimestamp == true || $this->timestamp == NULL) $this->timestamp = $this->getmicrotime();
			$this->con_file = $this->timestamp.".st.".$this->current_format;
			
			$this->wanted_width = $_width;
			$this->wanted_height = $_height;

			if ($this->width <= $this->wanted_width) {
				$scalefactor = $this->wanted_width/$this->width;
				$temp_width = $this->width * $scalefactor;
				$temp_height = $this->height  * $scalefactor;			
			}
			if ($this->width >= $this->wanted_width) {
				$scalefactor = $this->wanted_width/$this->width;
				$temp_width = $this->width * $scalefactor;
				$temp_height = $this->height  * $scalefactor;
			}
			if ($temp_height >= $this->wanted_height) {
				$scalefactor = $this->wanted_height/$this->height;
				$temp_width = $this->width * $scalefactor;
				$temp_height = $this->height * $scalefactor;
			}

			$this->result_height = $temp_height;
			$this->result_width = $temp_width;

			$sourceIm = $this->eval_imagecreatefrom($this->target_path);
			$targetIm = $this->eval_imagecreate($temp_width, $temp_height, $bgrcolor);
			$this->eval_imagecopy($targetIm, $sourceIm ,0,0,0,0, $temp_width, $temp_height, $this->width, $this->height, $smooth);
			$path = $this->eval_savepicture($targetIm, $jpeg_quality);
			$this->converted_file = $this->temp_dir.$this->con_file;
			$this->Size = $this->bytesTokb(filesize($this->converted_file));
			return  $this->converted_file;
		}
	}
	
	function GetMostUsedColours($int_x=18, $int_y=NULL, $type='multi') {
						//type multi = multidimensionales array
						//type one =   eindimensionales array
						//type mostuse = 
						
		$file = $this->target_path;
		if ($int_y == NULL) $int_y = round($int_x*($this->height/$this->width));

		if ($this->error == NULL) {
			$int =  sqrt($int); 
			$xloop = $this->width / $int_x;
			$yloop = $this->height / $int_y;
			$srcImage = $this->eval_imagecreatefrom($file);

			for ($y=($int_y/2); $y<$this->height; $y+=$yloop) {  $pos_y++;
			 for ($x=($int_x/2); $x<$this->width; $x+=$xloop) { $pos_x++;
				$rgbNow		= imagecolorat($srcImage, $x, $y);
				$colorrgb	= imagecolorsforindex($srcImage,$rgbNow);
				foreach($colorrgb as $key => $val) {
					$t[$key] = dechex($val);
					if(strlen($t[$key]) == 1 ) {
						if( is_int($t[$key]) ) {
							$t[$key] = $t[$key] . "0";
						} else {
							$t[$key] = "0" . $t[$key];
						}
					}
					$t[$key]."<br>";
				}
				$rgb2 = strtoupper($t[red] . $t[green] . $t[blue]);
				if ($type == 'multi') {
					$colorArray[$pos_y-1][] = $rgb2;
					if(($pos_x % $int_x) == 0) $pos_x = NULL;
				} else $colorArray[] = $rgb2;
			}}
			if  ($type == 'mostuse') {
				$colorArray = array_count_values($colorArray);
				while(list($key, $val) = each($colorArray)) $array[] = $key;
				return $array;
			} else {
				return $colorArray;
			}
		}
	}
}
?>
