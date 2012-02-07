<?php

function search($query, $database, $format = 'json') {
	$cache = new Cache($database);
	$data = $cache->load($query);
	
	if($data) {
        $json = json_decode($data);
        $json->meta->time = 0;
		return $json;
    }
	
    $espacedQuery = escapeshellarg($query);
    $command = FCASEARCH . "-d $database -q $espacedQuery -f $format";
    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
	$cache->save($query, $data);
    return json_decode($data);
}

function getFcaExtension($results) {   
   	$fca = new Fca($results);
   	$specStr = $fca->getSpecialization();
    $siblStr = $fca->getSimilar();
   
    return array('spec' => $specStr, 'sib' => $siblStr);
}

function getLink($href, $text, $wrapper = '') {
    $html = "<a href='$href'>$text</a>";
    if($wrapper) {
        $html = "<$wrapper>$html</$wrapper>";
    }
    return $html;
}

function getGETValue($name, $default = '') {
    if(isset ($_GET[$name])) {
        return $_GET[$name];
    } else {
        return $default;
    }
}

function getHTTPQuery($parameters = false) {
	if (!$parameters)
		$parameters = $_GET;
    $httpQuery = '?' . http_build_query($parameters);
    $httpQuery = str_replace('&', '&amp;', $httpQuery);
    return $httpQuery;
}