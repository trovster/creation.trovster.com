<?php
class Cache {

	function Cache() {
		$this->options = array(
			'caching' => true,
			'cacheDir' => $_SERVER['DOCUMENT_ROOT'].'/_cache/',
			'lifeTime' => 604800, // a week!
			'masterFile' => $_SERVER['DOCUMENT_ROOT'].'/_cache/cache-lite.config'
		);
		$this->setup = new Cache_Lite_File($this->options);
		$this->exclude_array = array('admin');
		$this->exclude = false;
		$this->enabled = false;
		$this->hash = '';
		$this->group = 'default';
	}
	
	function start() {
		if(!is_array($this->exclude_array)) $this->exclude_array = array($this->exclude_array);
		foreach($this->exclude_array as $exclude) {
			if(strpos($_SERVER['REQUEST_URI'],$exclude)!==false) $this->exclude = true;
		}
		if(!empty($this->hash) && $this->exclude==false) {
			if(!is_dir($this->options['cacheDir']) && function_exists('mkdirr')) mkdirr($this->options['cacheDir']);
			if(!is_writable($this->options['cacheDir'])) chmod($this->options['cacheDir'],0777);
			$this->enabled = true;
			if($this->data = $this->setup->get($this->hash,$this->group)) {
				header('Content-Type: text/html; charset=utf-8');
				echo '<small id="cached">Cached page</small>';
				echo $this->data;
				exit();
			}
			ob_start('ob_gzhandler');
		}
	}
	
	function end() {
		if($this->enabled===true && !empty($this->hash)) {
			$this->setup->save((str_replace(' />', '>', ob_get_contents())),$this->hash,$this->group);
			ob_end_flush();
		}
	}
	
	function delete() {
		if(!empty($this->hash)) {
			if(!is_array($this->hash)) $this->hash = array($this->hash);
			foreach($this->hash as $delete_hash) {
				$this->setup->remove($delete_hash,$this->group);
			}
		}
	}
	
	function clean() {
		// delete ALL cache for the site.
		$this->setup->clean($this->group);
	}
}
?>