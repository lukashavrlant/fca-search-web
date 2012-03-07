<!DOCTYPE HTML> 
<meta charset="utf-8"> 
<title>Lattice &mdash; FCA search engine</title> 
<style type="text/css">
body {
	width: 1000px;
	margin: auto;
}

.intent {
	border: 1px dashed gray;
	padding: 5px;
	margin-right: 10px;
	line-height: 50px;
}

hr {
	clear: both;
}

.searchconcept {
	color: red;
	border: 1px dashed red;
}
</style>

<div style="text-align:center;">
<?php
define('ROOT', dirname(__FILE__) . '/');

if ($_GET['hash'] && $_GET['database']) {
	$hash = $_GET['hash'];
	$database = $_GET['database'];
	$path = ROOT . 'cache/' . $database . '/' . $hash . '.txt';
	if (file_exists($path)) {
		$data = file_get_contents($path);
		$json = json_decode($data);
		$lattice = $json->lattice;
		$siblings = $lattice->siblings;
		$lensibl = count($siblings);
		$half = intval($lensibl / 2);
		$left = array_slice($siblings, 0, $half);
		$right = array_slice($siblings, $half);


		printIntentLine($lattice->upper);
		echo "<hr>";
		// printIntentLine($lattice->siblings);
		printIntentLine($left);
		echo "<span class='intent searchconcept'>" . implode(", ", $lattice->concept) . "</span>";
		printIntentLine($right);
		echo "<hr>";
		printIntentLine($lattice->lower);

	} else {
		echo $path;
	}
}

function printIntentLine($intents) {
	foreach ($intents as $intent) {
		echo intent2string($intent);
	}
}

function intent2string($concept) {
	return "<span class='intent'>" . implode(", ", $concept) . "</span class='intent'>";
}
?>
</div>