<?php
class Cache {
	private $path; 
	
	public function __construct() {
		$this->path = ROOT . 'cache/';
	}
	
	public function load($query) {
		$name = md5($query);
		$filepath = $this->path . $name . '.txt';
		if(file_exists($filepath)) {
			return file_get_contents($filepath);
		} else {
			return false;
		}
	}
	
	public function save($query, $result) {
		$name = md5($query);
		file_put_contents($this->path . $name . '.txt', $result);
	}
}
