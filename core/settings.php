<?php
class Settings
{
	private $settings;
	private $defaultSettings;

	public function __construct() {
		$string = file_get_contents(ROOT . 'settings.json');
		$this->settings = json_decode($string);
		$this->initDefaultSettings();
	}

	public function get($key) {
		if (isset($this->settings->$key))
			return $this->settings->$key;
		else
			return $this->defaultSettings[$key];
	}

	private function initDefaultSettings() {
		$this->defaultSettings = array(
				"linksOnOnePage" => 15,
				"roundPrecision" => 1,
				"showMaxSteps" => 9,
				"showNextButton" => true,
				"showURL" => true,
				"showDescription" => true,
				"minLinksToShowSpecialization" => 8,
				"maxSpecializationLinks" => 10,
				"maxSiblingsLinks" => 5,
				"maxGeneralizationLinks" => 5
			);
	}
}