<?php
setlocale(LC_ALL, 'cs_CZ.utf8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('LOCALHOST', $_SERVER['HTTP_HOST'] == 'localhost');
define('ROOT', dirname(__FILE__) . '/');
require_once 'constants.php';

$supportedActions = array('d', 'q', 'f', 'links', 'linkscount', 'words', 'freq', 'docid', 'tf', 'findURL', 'finddocid', 
	'docfreq', 'docinfo', 'wordscount', 'title', 'description', 'keywords', 'url', 'id', 'stem', 'lang');

function search($options, $supported) {
	$query = '';

	foreach ($options as $key => $value) {
		if (in_array($key, $supported)) {
			$key = strlen($key) == 1 ? "-$key" : "--$key";
			$escaped = escapeshellarg($value);
			$query .= "$key $escaped ";
		}
	}
	if ($query) {
	    $command = PYTHON3 . FCASEARCH . $query;
	    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
	    return $data;
	} else {
		return "";
	}
}

if (count($_GET)) {
	$data = search($_GET, $supportedActions);
	if (isset($_GET['pretty'])) {
		echo '<meta charset="utf-8">';
		var_dump(json_decode($data));
	} else {
		echo $data;
	}
}