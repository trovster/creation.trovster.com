<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/forms.profile.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/forms.news.php');

// general error message
$updated_array = array(
	'method' => 'error',
	'heading' => 'Error',
	'description' => 'Something went wrong, sorry…',
	'class' => array()
);
function active_select_setup($select_text='Status…',$select_id='status-required',$active_id='1') {
	$select_array = array(
		'1' => array(
			'text' => 'Disabled',
			'value' => '1'
		),
		'2' => array(
			'text' => 'Active',
			'value' => '2'
		)
	);
	if(!empty($active_id) && is_numeric($active_id) && array_key_exists($active_id,$select_array)) {
		$select_array[$active_id]['selected'] = 'selected';
	}
	else {
		$select_array['1']['selected'] = 'selected';
	}
	return createOptions($select_array,$select_text,$select_id);
}
function image_fieldset_setup($key='image',$fieldset,$settings=array(),$image=array()) {
	$array = array(
		$key => array(
			'fieldset' => $fieldset,
			'id' => @$settings['id'],
			'class' => @$settings['class'],
			'elements' => array(
				$key => array('label' => 'Image', 'type' => 'file', 'name' => $key, 'id' => $key, 'class' => array(), 'metadata' => array('max' => 1, 'accept' => 'png,gif,jpg,jpeg')) // multi
			)
		)
	);
	
	if(!empty($settings['elements']) && in_array('news',$settings['elements'])) {
		if(!empty($image['large'])) {
			$image['text'] = $image['large']['text'];
			$image['identifier'] = $image['large']['identifier'];
		}
		$array[$key]['elements'][$key.'-large'] = array('label' => 'Large Image', 'type' => 'file', 'name' => $key.'-large', 'id' => $key.'-large');
		$array[$key]['elements'][$key.'-small'] = array('label' => 'Small Image', 'type' => 'file', 'name' => $key.'-small', 'id' => $key.'-small');
		unset($array[$key]['elements'][$key]);
	}
	
	if(!empty($settings['elements'])) {
		if(in_array('alt',$settings['elements'])) {
			$array[$key]['elements'][$key.'-alt'] = array('label' => 'Image Alternative Text', 'type' => 'text', 'name' => $key.'-alt', 'id' => $key.'-alt', 'value' => @$image['text']['alt']);
		}
		if(in_array('title',$settings['elements'])) {
			$array[$key]['elements'][$key.'-title'] = array('label' => 'Image Title', 'type' => 'text', 'name' => $key.'-title', 'id' => $key.'-title', 'value' => @$image['text']['title']);
		}
		if(in_array('description',$settings['elements'])) {
			$array[$key]['elements'][$key.'-description'] = array('label' => 'Image Description', 'type' => 'text', 'name' => $key.'-description', 'id' => $key.'-description', 'value' => @$image['description']);
		}
		if(in_array('link',$settings['elements'])) {
			$array[$key]['elements'][$key.'-link'] = array('label' => 'Image Link', 'type' => 'text', 'name' => $key.'-link', 'id' => $key.'-link', 'value' => @$image['link']);
		}
		if(in_array('extra',$settings['elements'])) {
			if(!empty($image['file']['full-path'])) {
				$current_image = image_show($image);
				if(!empty($array[$key]['elements'][$key.'-alt'])) $array[$key]['elements'][$key.'-alt']['note'] = array($current_image);
				elseif(!empty($array[$key]['elements'][$key.'-small'])) $array[$key]['elements'][$key.'-small']['note'] = array($current_image);
				elseif(!empty($array[$key]['elements'][$key])) $array[$key]['elements'][$key]['note'] = array($current_image);
			}
		}
		if(in_array('news',$settings['elements'])) {
			$array[$key]['elements'][$key.'-identifier'] = array('label' => 'Image Identifier', 'type' => 'hidden', 'name' => $key.'-identifier', 'id' => $key.'-identifier', 'value' => @$image['identifier']);
			
			// also show the current image...
			if(!empty($image['large']['file']['full-path']) && !empty($image['small'])) {
				$current_image = '<a href="'.$image['large']['file']['full-path'].'" title="'.$image['small']['text']['title'].'" class="thickbox thumbnail">'.image_show($image['small']).'</a>';
				$array[$key]['elements'][$key.'-alt']['note'] = array($current_image);
			}
			
			if(in_array('flickr',$settings['elements'])) {
				$array[$key]['elements'][$key.'-flickr-title'] = array('label' => 'Flickr Image Title', 'type' => 'text', 'name' => $key.'-flickr-title', 'id' => $key.'-flickr-title', 'value' => @$image['flickr']['title']);
				$array[$key]['elements'][$key.'-flickr-id'] = array('label' => 'Flickr Image ID', 'type' => 'text', 'name' => $key.'-flickr-id', 'id' => $key.'-flickr-id', 'value' => @$image['flickr']['identifier']);
				$array[$key]['elements'][$key.'-flickr-link'] = array('label' => 'Flickr Image Link (Path)', 'type' => 'text', 'name' => $key.'-flickr-link', 'id' => $key.'-flickr-link', 'value' => @$image['flickr']['link']);
			}
		}
	}
	if(!empty($settings['position'])) {
		if(!empty($settings['position']['total'])) {
			$position_select_array = array(); $selected_id = '';
			for($p=1; $p<=$settings['position']['total']; $p++) {
				$position_select_array[$p] = array(
					'text' => ucwords(int_to_words($p)).' ('.$p.')',
					'value' => $p
				);
				if(!empty($_POST[$key.'-position-select']) && $_POST[$key.'-position-select']==$p) {
					$position_select_array[$p]['selected'] = 'selected';
					$selected_id = $p;
				}
				elseif(!empty($settings['position']['current']) && empty($_POST[$key.'-position-select']) && $settings['position']['current']==$p) {
					$position_select_array[$p]['selected'] = 'selected';
					$selected_id = $p;
				}
			}
			$position_select = createOptions($position_select_array,'Position…',$key.'-position-select');
			$array[$key]['elements'][$key.'-position-select'] = array('label' => 'Position', 'type' => 'select', 'name' => $key.'-position-select', 'id' => $key.'-position-select', 'class' => array('position'), 'value' => $position_select);
		}
		$array[$key]['elements'][$key.'-position'] = array('label' => 'Current Position', 'type' => 'hidden', 'name' => $key.'-position', 'id' => $key.'-position', 'value' => @$settings['position']['current']);
	}
	return $array;
}

?>