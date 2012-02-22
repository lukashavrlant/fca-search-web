<?php
class Settings
{
	private static $settings;
	private static $defaultSettings;

	// public function __construct() {
	// 	$string = file_get_contents(ROOT . 'settings.json');
	// 	$this->settings = json_decode($string);
	// 	$this->initDefaultSettings();
	// }

	public static function loadSettings() {
		$string = file_get_contents(ROOT . 'settings.json');
		self::$settings = json_decode($string);
		self::initDefaultSettings();
	}

	public static function get($key) {
		if (isset(self::$settings->$key))
			return self::$settings->$key;
		else
			return self::$defaultSettings[$key];
	}

	private function initDefaultSettings() {
		self::$defaultSettings = array(
			"linksOnOnePage" => 15,
			"roundPrecision" => 1,
			"showMaxSteps" => 9,
			"showNextButton" => true,
			"showURL" => true,
			"showDescription" => true,
			"minLinksToShowSpecialization" => 8,
			"maxSpecializationLinks" => 10,
			"maxSiblingsLinks" => 5,
			"maxGeneralizationLinks" => 5,
			"useCache" => true,
			"minCacheQueries" => 200,
			"maxCacheQueries" => 500,
			"maxTitleLength" => 80,
			"maxURLLength" => 100,
			"maxDescriptionLength" => 255
		);
	}

	
}