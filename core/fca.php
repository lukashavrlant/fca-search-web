<?php
class Fca {
	public $maxSpec = 10;
	public $maxSib = 5;
	public $maxGen = 5;
	
	private $results;
	private $originQuery;
	private $database;
	private $colors;
	
	public function __construct($results) {
		$this->results = $results->fca;
		$this->originQuery = getGETValue('query');
		$this->database = getGETValue('database', 'matweb');
	}
	
	public function getGeneralization($symbol = '−') {
		$data = array();
    	$generalization = array_slice($this->results->gen, 0, $this->maxGen);
		
		if(count($generalization) > 0) {
			foreach ($generalization as $sugg) {
				$minmax = $this->getMinMax($generalization);
				$min = $minmax['min'];
				$max = $minmax['max'];
		    	$words = $sugg->words;
		        $text = $symbol . ' ' . implode(", ", $words);
				
				$newQuery = strtolower($this->originQuery);
				foreach ($words as $word) {
					$newQuery = str_replace(strtolower($word), '', $newQuery);
				}
				$newQuery = trim($newQuery);
				$newQuery = preg_replace('/\s+/', ' ', $newQuery);
				
		        $par = array(
		            'database' => $this->database,
		            'query' => $newQuery
		        );
		        
		        $href = getHTTPQuery($par);
				$class = $this->normalizeLength($sugg->rank, $min, $max);
				$link = "\n<a href='$href' class='color-$class' title='Min documents: $sugg->rank'>$text</a>";
		        array_push($data, $link);
		    }
		}
		
		return implode(' | ', $data);
	}
	
	public function getSpecialization($symbol = '+') {
		$data = array();
    	$specialization = array_slice($this->results->spec, 0, $this->maxSpec);
		
		if (count($specialization) > 0) {
			$minmax = $this->getMinMax($specialization);
			$min = $minmax['min'];
			$max = $minmax['max'];
			
		    foreach ($specialization as $sugg) {
		    	$words = $sugg->words;
		        $text = $symbol . ' ' . implode(", ", $words);
		        $par = array(
		            'database' => $this->database,
		            'query' => $this->originQuery . ' ' . implode(" ", $words)
		        );
		        
		        $href = getHTTPQuery($par);
				$class = $this->normalizeLength($sugg->rank, $min, $max);
				$link = "\n<a href='$href' class='color-$class'>$text</a>";
		        array_push($data, $link);
		    }
		}
		    
		return implode(' | ', $data);
	}
	
	public function getSimilar($symbol = "±") {
		$data = array();
    	$siblings = array_slice($this->results->sib, 0, $this->maxSib);
	
		if(count($siblings) > 0) {
			
			$minmax = $this->getMinMax($siblings);
			$min = $minmax['min'];
			$max = $minmax['max'];
			
			
			
		    foreach ($siblings as $sugg) {
		    	$words = $sugg->words;
				$rank = $sugg->rank;
				
		        $text = $symbol . ' ' . implode(', ', $words);
		        $parameters = array(
		            'query' => implode(' ', $words),
		            'database' => $this->database
		        );
		        $href = getHTTPQuery($parameters);
		        $class = $this->normalizeLength($rank, $min, $max);
				$link = "\n<a href='$href' class='color-$class' title='similarity: $rank'>$text</a>";
		        
		        array_push($data, $link);
		    }
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
			'max' => $specialization[0]->rank,
			'min' => $specialization[$len-1]->rank
			);
		} else {
			return array('min' => 0, 'max' => 0);
		}
	}
}