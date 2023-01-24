<?php
function image_setup($id,$name,$ext,$path,$alt='',$title='',$id_attr='',$class_attr='',$longdesc='',$root='') {
	if($root=='') $root = $_SERVER['DOCUMENT_ROOT'];
	$file_info_array = file_setup($id,trim($name,'/'),$ext,$path,false,$root);
	if(is_file($root.$file_info_array['path'].$file_info_array['name'])) {
		list($width, $height, $type, $attr) = getimagesize($root.$file_info_array['path'].$file_info_array['name']);
		$return = array();
		$return['dimensions']['height'] = $height;
		$return['dimensions']['width'] = $width;
		$return['file'] = $file_info_array;
		$return['text']['alt'] = formatText($alt);
		$return['text']['title'] = formatText($title);
		$return['longdesc'] = file_setup(0,$longdesc,'txt');
		$return['id'] = $id_attr;
		$return['class'] = $class_attr;
		return $return;
	}
	else return false;
}
function image_show($array) {
	if(empty($array) || !is_array($array) || empty($array['file'])) return false;
	$longdesc = '';
	if(!empty($array['longdesc'])) $longdesc = ' longdesc="'.$array['longdesc']['full-path'].'"';

	$link_start = ''; $link_end = '';
	if(!empty($array['link'])) {
		$link_start .= '<a href="'.$array['link'].'" class="image">';
		$link_end .= '</a>';
	}
	return $link_start.'<img src="'.$array['file']['full-path'].'" height="'.$array['dimensions']['height'].'" width="'.$array['dimensions']['width'].'" alt="'.$array['text']['alt'].'"'.$longdesc.addAttributes(@$array['text']['title'],@$array['id'],@$array['class']).' />'.$link_end."\n";
}

function image_upload($array=array()) {
	if(empty($array) || !is_array($array)) {
		return array(
			'method' => 'error',
			'heading' => 'Also…',
			'description' => 'There was a problem uploading the image you provided.',
			'class' => array('image')
		);
	}
	global $image_accept_array;
	$return_array = array();
	require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/image-editor.class.php');

	$file_array = file_setup($array['id'],$array['name'],$array['extension'],$array['path'],true);

	// original folder and image.
	$originals_folder = $_SERVER['DOCUMENT_ROOT'].$file_array['local'].'originals/';
	if(!is_dir($originals_folder)) {
		mkdir($originals_folder);
		chmod($originals_folder, 0777);
	}

	move_uploaded_file($_FILES[$array['upload']]['tmp_name'],$originals_folder.$file_array['name']);
	$image = new ImageEditor($file_array['name'],$originals_folder);
	if(is_file($originals_folder.$file_array['name'])) chmod($originals_folder.$file_array['name'], 0755);

	if(in_array(strtolower($image->getImageType()),$image_accept_array) && !empty($file_array) && is_file($originals_folder.$file_array['name'])) {
		list($width,$height) = getimagesize($_SERVER['DOCUMENT_ROOT'].$file_array['local'].'originals/'.$file_array['name']);
		$width_o = $width;
		$height_o = $height;
		$resize = false;
		$return_array = array(
			'method' => 'success',
			'heading' => 'Also…',
			'description' => 'Your image has been successfully uploaded.',
			'class' => array('image')
		);

		if(!empty($array['size'])) {

			if(!empty($array['size']['method']) && strtolower($array['size']['method'])=='resize' && !empty($array['size']['height']) && !empty($array['size']['width'])) {
				$height = $array['size']['height'];
				$width = $array['size']['width'];
				$resize = true;
			}
			else {
				if(!empty($array['size']['height'])) {
					if($height>$array['size']['height']) {
						$width = ceil(($array['size']['height']/$height)*$width);
						$height = $array['size']['height'];
						$resize = true;
					}
				}
				if(!empty($array['size']['width'])) {
					if($width>$array['size']['width']) {
						$height = ceil(($array['size']['width']/$width)*$height);
						$width = $array['size']['width'];
						$resize = true;
					}
				}
			}
		}
		if($image->error==FALSE) {
			$local_folder = $_SERVER['DOCUMENT_ROOT'].$file_array['local'];

			if(!empty($array['size']['method']) && strtolower($array['size']['method'])=='resize'
			&& !empty($array['size']['height']) && !empty($array['size']['width'])
			&& $height_o==$array['size']['height'] && $width_o==$array['size']['width']) {
				// image uploaded matches the dimensions, just move original, do not rescale.
				copy($originals_folder.$file_array['name'],$local_folder.$file_array['name']);
			}
			elseif(!empty($array['size']['width']) && $width_o==$array['size']['width']) {
				copy($originals_folder.$file_array['name'],$local_folder.$file_array['name']);
			}
			elseif(!empty($array['size']['height']) && $height_o==$array['size']['height']) {
				copy($originals_folder.$file_array['name'],$local_folder.$file_array['name']);
			}
			else {
				// resizing the image.
				if($resize==true) {
				$image->resize($width, $height);
					$return_array['class'][] = 'note';
					$return_array['class'][] = 'resize';
					$return_array['description'] .= ' The image was too large and has been **scaled to fit**.';
				}
				$image->outputFile($file_array['name'],$local_folder,100);
			}

			// thumbnail folder and image.
			$thumbnails_folder = $_SERVER['DOCUMENT_ROOT'].$file_array['local'].'thumbnails/';
			if(!is_dir($thumbnails_folder)) {
				mkdir($thumbnails_folder);
				chmod($thumbnails_folder, 0777);
			}
			$image->resize(100, 100);
			$image->outputFile($file_array['name'],$thumbnails_folder);
			if(is_file($thumbnails_folder.$file_array['name'])) chmod($thumbnails_folder.$file_array['name'], 0755);

			// crop folder and image.
			$crop_folder = $_SERVER['DOCUMENT_ROOT'].$file_array['local'].'crop/';
			if(!is_dir($crop_folder)) {
				mkdir($crop_folder);
				chmod($crop_folder, 0777);
			}
			$image->crop(0, 0, 100, 100);
			$image->outputFile($file_array['name'],$crop_folder);
			if(is_file($crop_folder.$file_array['name'])) chmod($crop_folder.$file_array['name'], 0755);
		}
		else {
			$return_array = array(
				'method' => 'error',
				'heading' => 'Also…',
				'description' => 'The image you provided could not be uploaded.'."\n",
				'class' => array('image')
			);
			if(!empty($image->errorText)) {
				$return_array['description'] .= ' Reason: *'.$image->errorText.'*.';
			}
		}

	}
	else {
		$return_array = array(
			'method' => 'error',
			'heading' => 'Also…',
			'description' => 'The image you provided is not allowed to be uploaded. Check the image type is one of the following: '."\n",
			'class' => array('image')
		);
		foreach($image_accept_array as $image_accept) {
			$return_array['description'] .= "\n".'* '.$image_accept;
		}

	}
	return $return_array;
}
?>
