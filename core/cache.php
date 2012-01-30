<?php
class Cache {
	public $minQueries = 100;
	public $maxQueries = 200; 
	
	private $path; 
	
	public function __construct($database) {
		$this->path = ROOT . 'cache/' . $database . '/';
		if(!file_exists($this->path)) {
			mkdir($this->path);
		}
	}
	
	public static function clearCache($dtb) {
		$dtb = strtolower($dtb);
		
		if ($dtb == '__all') {
			$folders = glob(ROOT . 'cache/*'); 
			foreach ($folders as $folder) {
				self::clearCache(basename($folder));
				rmdir($folder);
			}
		} else {
			$files = glob(ROOT . 'cache/' . $dtb . '/*');
			foreach ($files as $file) {
				unlink($file);
			}
		}
	}
	
	public function load($query) {
		if ($query) {
			$normQuery = $this->normalizeQuery($query);
			
			if ($normQuery[strlen($normQuery) - 1] != '!') {
				$name = md5($normQuery);
				$filepath = $this->path . $name . '.txt';
				if(file_exists($filepath)) {
					return file_get_contents($filepath);
				}
			}
		} 
		
		return false;
	}

	public function save($query, $result) {
		$name = md5($this->normalizeQuery($query));
		file_put_contents($this->path . $name . '.txt', $result);
		$this->invalidate();
	}
	
	private function invalidate() {
		$files = glob($this->path . '*');
		$filesTime = array();
		$numberOfFiles = count($files);
		if($numberOfFiles > $this->maxQueries) {
			foreach ($files as $file) {
				$filesTime[$file] = filemtime($file);
			}
		}
		
		asort($filesTime);
		$oldFiles = array_slice($filesTime, 0, $numberOfFiles - $this->minQueries);
		
		foreach ($oldFiles as $filename => $val) {
			unlink($filename);
		}
	}
	
	private function normalizeQuery($query) {
		$query = trim(strtolower($query));
		return $query;
	}
}
