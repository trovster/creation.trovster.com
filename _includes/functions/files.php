<?php
if(version_compare(phpversion(), "5.2.0", "<")) {
	function pathinfo_filename($path) {
		$temp = pathinfo($path);
		if($temp['extension']) {
			$temp['filename'] = substr($temp['basename'],0 ,strlen($temp['basename'])-strlen($temp['extension'])-1);
		}
		return $temp;
	}
}
else {
	function pathinfo_filename($path) {
		return pathinfo($path);
	}
}

function file_setup($id,$name,$ext,$path='/',$check=false,$root='') {	
	if(!isset($id) || !is_numeric($id) || empty($name) || empty($ext)) return NULL;
	if($root=='') $root = $_SERVER['DOCUMENT_ROOT'];
	if($id!=0) $file_name = '['.$id.']_'.$name.'.'.$ext;
	else $file_name = url_encode($name).'.'.$ext;
	
	//if($path=='/images/portfolio/advertising/') $path = '/images/portfolio/promotion/';
	//if($path=='/images/services/advertising/') $path = '/images/services/promotion/';
	$path = str_replace('/advertising/','/promotion/',$path);
	
	$path_local = $path;
	
	if($path=='/') {
		$file_name = trim(strrchr($name,'/'),'/');
		$path = substr($name,0,strlen($name)-strlen($file_name));
		$path_local = $path;
	}
	if(substr($path,0,8)=='/_files/') $path = substr_replace($path, '/files/', 0, 8);

	if(is_file($root.$path_local.$file_name) || $check==true) {
		return array('name' => $file_name, 'file' => $file_name, 'path' => $path, 'full-path' => $path.$file_name, 'type' => $ext, 'local' => $path_local);
	}
	else return NULL;
}
function create_file($id,$name,$path,$upload) {
	if(empty($id) || !is_numeric($id) || empty($name) || empty($upload)) return array('text' => ' There has been an unexpected error during this upload');
	
	$file_info_array = pathinfo($_FILES[$upload]['name']);
	$ext = $file_info_array['extension'];
	$file_name = '['.$id.']_'.url_encode($name).'.'.$ext;

	move_uploaded_file($_FILES[$upload]['tmp_name'],$_SERVER['DOCUMENT_ROOT'].$path.$file_name);
	chmod($_SERVER['DOCUMENT_ROOT'].$path.$file_name,0777);
	return array('text' => ' File uploaded successfully.', 'type' => $ext);
}
function get_file($id,$path,$type=false,$root='') {
	if($root=='') $root = $_SERVER['DOCUMENT_ROOT'];
	if($handle = @opendir($root.$path)) {
		while(false !== ($file = readdir($handle))) {
			if($file != '.' && $file != '..' && preg_match('/\['.$id.'\]/',$file)) {
				if(($type==false && preg_match('/_small\./',$file)==0) || ($type=='small' && preg_match('/_small\./',$file))) {
					$local = $root.$path.$file;
					$file_info_array = pathinfo_filename($local);
					return array(
						'identifier' => $id,
						'path' => $path,
						'file' => $file,
						'name' => $file_info_array['filename'],
						'san-id' => ltrim(preg_replace('#\[([0-9]+)\]#','',$file_info_array['filename']),'_'),
						'local' => $local,
						'ext' => $file_info_array['extension']
					);
				}
			}
		}
	}
	return FALSE;
}
function delete_file($id,$path,$type=false,$root='') {
	if(empty($id) || !is_numeric($id)) return array('text' => ' The file has not been deleted, no ID found.', 'type' => false);
	if($root=='') $root = $_SERVER['DOCUMENT_ROOT'];
	if(get_file($id,$path,$type)) {
		$file = get_file($id,$path,$type);
		unlink($root.$file['path'].$file['file']);
		$return_text = ' The file has successfully been deleted.';
		$return_type = true;
	}
	else {
		$return_text = ' The file has not been deleted, no file found.';
		$return_type = false;
	}
	return array('text' => $return_text, 'type' => $return_type);
}
function rename_file($id,$name,$path,$type=false,$root='') {
	if(empty($id) || !is_numeric($id)) return array('text' => ' The file has not been deleted, no ID found.', 'type' => false);
	if($root=='') $root = $_SERVER['DOCUMENT_ROOT'];
	$return_array = array('text' => ' There was an error.', 'type' => false);
	if(get_file($id,$path,$type)) {
		$file = get_file($id,$path,$type);
		$file_details = pathinfo($file['local']);
		$file_ext = strtolower($file_details['extension']);
		$new_file = file_setup($id,$name,$file_ext,$path,true);
		if(file_exists($root.$file['path'].$file['file'])) {
			rename($file['local'],$root.$new_file['path'].$new_file['name']);
			chmod($root.$new_file['path'].$new_file['name'],0777);
			$return_array = array('text' => ' The file has been renamed.', 'type' => false);
		}
		else $return_array = array('text' => ' The does not exist.', 'type' => false);
	}
	return $return_array;
}
?>