<?php

function search($query, $database, $format = 'json') {
    $command = CMD . "-d $database -q \"$query\" -f $format";
    $data = shell_exec($command);
    return $data;
}

function getLinksList($results, $max = 15) {
    $documents = $results->documents;
    $number = count($documents);
    $html = '<ol>';
    foreach (array_slice($documents, 0, $max) as $document) {
        $url = $document->url;
        $html .= getLink($url, $url, 'li');
    }
    $html .= '</ol>';
    $html .= '<strong>Total documents: ' . $number . '</strong>';
    return $html;
}

function getFcaExtension($results, $originQuery) {
    $fca = $results->fca;
    $specStr = fcaSpec($fca->spec, $originQuery);
    $siblStr = fcaSiblings($fca->sib);
    #$genStr = fcaExt2string($fca->gen, '-');
    return array('spec' => $specStr, 'sib' => $siblStr);
}

function fcaSpec($fca, $originQuery, $symbol = '+') {
    $data = array();
    
    foreach ($fca as $sugg) {
        $text = $symbol . ' ' . implode(", ", $sugg);
        $href = '?query=' . $originQuery . ' ' . implode(" ", $sugg);
        array_push($data, getLink($href, $text));
    }
    
    return implode(' | ', $data);
}

function fcaSiblings($fca, $symbol = "≈") {
    $data = array();
    
    foreach ($fca as $sugg) {
        $text = $symbol . ' ' . implode(', ', $sugg);
        $href = '?query=' . implode(' ', $sugg);
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