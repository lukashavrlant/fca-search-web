<!DOCTYPE HTML> 
<meta charset="utf-8"> 
<title>Context &mdash; FCA search engine</title> 
<style type="text/css">
table {
	border-collapse: collapse;
}

td, th {
	border: 1px solid gray;
	text-align: center;
}

th {
	padding: 0 5px;
}

.red {
	color: red;
}
</style>

<?php
define('ROOT', dirname(__FILE__) . '/');

if ($_GET['hash'] && $_GET['database']) {
	$hash = $_GET['hash'];
	$database = $_GET['database'];
	$path = ROOT . 'cache/' . $database . '/' . $hash . '.txt';
	if (file_exists($path)) {
		$data = file_get_contents($path);
		$json = json_decode($data);
		$context = $json->meta->context;
		$extent = $json->lattice->conceptextent;
		$intent = $json->lattice->conceptintent;
		echo context2html($context->table, $context->objects, $context->attributes, $extent, $intent);
	}
}

function context2html($table, $objects, $attributes, $markObjects, $markAttrsNames) {
	$markAttr = array();
	$html = '<table>';

	$html .= '<tr><td></td>';
	for($i = 0; $i < count($attributes); $i++) {
		$attr = $attributes[$i];
		if (in_array($attr, $markAttrsNames)) {
			$class = ' class="red"';
			$markAttr[] = $i;
		} else {
			$class = '';
		}
		$html .= "<th$class>" . $attr . '</th>';
	}
	$html .= '</tr>';

	for ($i=0; $i < count($table); $i++) {
		$html .= '<tr>';
		$url = parse_url($objects[$i]);
		if (in_array($i, $markObjects)) {
			$class = ' class="red"';
		} else {
			$class = '';
		}
		$html .= "<td><b$class>" . $url['path'] . '</b></td>';
		for ($j=0; $j < count($table[$i]); $j++) { 
			if (in_array($j, $markAttr) && in_array($i, $markObjects)) {
				$class = ' class="red"';
			} else {
				$class = '';
			}

			$html .= "<td$class>" . $table[$i][$j] . '</td>';
		}
		$html .= '</tr>';
	}

	$html .= '</table>';
	return $html;
}
?>