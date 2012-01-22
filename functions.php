<?php

function search($query, $database, $format = 'json') {
    $command = CMD . "-d $database -q \"$query\" -f $format";
    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
    return $data;
}

function getLinksList($results, $max = 15) {
    $documents = $results->documents;
    $number = count($documents);
    $html = '<ol>';
    foreach (array_slice($documents, 0, $max) as $document) {
        $url = $document->url;
        $title = $document->title;
        $html .= getLink($url, $title, 'li');
    }
    $html .= '</ol>';
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

function getHTTPQuery($parameters) {
    $httpQuery = '?' . http_build_query($parameters);
    $httpQuery = str_replace('&', '&amp;', $httpQuery);
    return $httpQuery;
}