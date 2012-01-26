<?php
class Fca {
	public $maxSpec = 10;
	public $maxSib = 5;
	
	private $results;
	private $originQuery;
	private $database;
	
	public function __construct($results) {
		$this->results = $results->fca;
		$this->originQuery = getGETValue('query');
		$this->database = getGETValue('database', 'matweb');
	}
	
	public function getSpecialization($symbol = '+') {
		$data = array();
    
	    foreach (array_slice($this->results->spec, 0, $this->maxSpec) as $sugg) {
	        $text = $symbol . ' ' . implode(", ", $sugg);
	        $par = array(
	            'database' => $this->database,
	            'query' => $this->originQuery . ' ' . implode(" ", $sugg)
	        );
	        
	        $href = getHTTPQuery($par);
	        array_push($data, getLink($href, $text));
	    }
	    
	    return implode(' | ', $data);
	}
	
	public function getSimilar($symbol = "≈") {
		$data = array();
    
	    foreach (array_slice($this->results->sib, 0, $this->maxSib) as $sugg) {
	        $text = $symbol . ' ' . implode(', ', $sugg);
	        $parameters = array(
	            'query' => implode(' ', $sugg),
	            'database' => $this->database
	        );
	        $href = getHTTPQuery($parameters);
	        array_push($data, getLink($href, $text));
	    }
	    
	    return implode(' | ', $data);
	}
}