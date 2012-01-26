<?php

function search($query, $database, $format = 'json') {
    $command = CMD . "-d $database -q \"$query\" -f $format";
    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
    return $data;
}

function getLinksList($results, $page = 1, $linksCount = 15) {
	$page = getGETValue('page', $page);
	$from = ($page - 1) * $linksCount; 
    $documents = $results->documents;
    $number = count($documents);
    $html = '<ol>';
	
    foreach (array_slice($documents, $from, $linksCount) as $document) {
        $url = $document->url;
        $title = $document->title;
        $html .= getLink($url, $title, 'li');
    }
    $html .= '</ol>';
	
	$paginator = new Paginator($number);
	$html .= $paginator->getList($page);
	
    $html .= '<strong>Total documents: ' . $number . '</strong>';
    return $html;
}

function getFcaExtension($results, $originQuery, $database) {
    $fca = $results->fca;
    $specStr = fcaSpec($fca->spec, $originQuery, $database);
    $siblStr = fcaSiblings($fca->sib, $database);
    #$genStr = fcaExt2string($fca->gen, '-');
    return array('spec' => $specStr, 'sib' => $siblStr);
}

function fcaSpec($fca, $originQuery, $database, $symbol = '+') {
    $data = array();
    
    foreach ($fca as $sugg) {
        $text = $symbol . ' ' . implode(", ", $sugg);
        $par = array(
            'database' => $database,
            'query' => $originQuery . ' ' . implode(" ", $sugg)
        );
        
        $href = getHTTPQuery($par);
        array_push($data, getLink($href, $text));
    }
    
    return implode(' | ', $data);
}

function fcaSiblings($fca, $database, $symbol = "≈") {
    $data = array();
    
    foreach ($fca as $sugg) {
        $text = $symbol . ' ' . implode(', ', $sugg);
        $parameters = array(
            'query' => implode(' ', $sugg),
            'database' => $database
        );
        $href = getHTTPQuery($parameters);
        array_push($data, getLink($href, $text));
    }
    
    return implode(' | ', $data);
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
        return htmlspecialchars($_GET[$name]);
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