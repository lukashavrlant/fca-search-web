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
	font-weight: bold;
}

.left {
	text-align: left;
	font-size: 80%;
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

		echo "<div class='left'>Upper neighbors:</div>";
		printIntentLine($lattice->upper, 'Upper neighbor');
		echo "<hr>";
		echo "<div class='left'>Siblings:</div>";
		echo "<table><tr><td>";
		printIntentLine($left, 'Sibling');
		echo "</td><td width='200'>";
		echo "<span class='intent searchconcept' title='Search concept'>" . implode(", ", $lattice->concept) . "</span>";
		echo "</td><td>";
		printIntentLine($right, 'Sibling');
		echo "</td></tr></table><hr>";
		echo "<div class='left'>Lower neighbors:</div>";
		printIntentLine($lattice->lower, 'Lower neighbor');

	} else {
		echo $path;
	}
}

function printIntentLine($intents, $title) {
	foreach ($intents as $intent) {
		echo intent2string($intent, $title);
	}
}

function intent2string($concept, $title) {
	return "<span class='intent' title='$title'>" . implode(",&nbsp;", $concept) . "</span class='intent'> ";
}
?>
</div>