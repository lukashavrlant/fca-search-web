<?php
class Fca {
	public $maxSpec = 10;
	public $maxSib = 5;
	
	private $results;
	private $originQuery;
	private $database;
	private $colors;
	
	public function __construct($results) {
		$this->results = $results->fca;
		$this->originQuery = getGETValue('query');
		$this->database = getGETValue('database', 'matweb');
	}
	
	public function getSpecialization($symbol = '+') {
		$data = array();
    	$specialization = array_slice($this->results->spec, 0, $this->maxSpec);
		
		if (count($specialization) > 0) {
			$minmax = $this->getMinMax($specialization);
			$min = $minmax['min'];
			$max = $minmax['max'];
			$diff = $minmax['max'] - $minmax['min'];
			
		    foreach ($specialization as $sugg) {
		    	$words = $sugg->words;
		        $text = $symbol . ' ' . implode(", ", $words);
		        $par = array(
		            'database' => $this->database,
		            'query' => $this->originQuery . ' ' . implode(" ", $words)
		        );
		        
		        $href = getHTTPQuery($par);
				$class = $this->normalizeLength($sugg->length, $min, $max);
				$class = $this->normalizeLength($sugg->rank, $min, $max);
				$link = "\n<a href='$href' class='color-$class'>$text</a>";
		        array_push($data, $link);
		    }
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
	
	private function normalizeLength($length, $min, $max) {
		$diff = $max - $min;
		$norm = (($length - $diff) / ($max - $diff)) * 16;
		return max(round($norm) - 1, 3);
	}
	
	private function getMinMax($specialization) {
		$len = count($specialization);
		if($len > 0) {
			return array(
			'max' => $specialization[0]->length,
			'min' => $specialization[$len-1]->length
			'max' => $specialization[0]->rank,
			'min' => $specialization[$len-1]->rank
			);
		} else {
			return array('min' => 0, 'max' => 0);
		}
	}
}