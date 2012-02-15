<?php
class Sresults {
	public $totalLinks;
	public $linksOnPage;
	public $showURL;
	public $showDescription;
	
	private $documents;
	private $currentPage;
	private $roundPrecision;
	private $settings;
	private $meta;
	private $spellcheck;
	
	public function __construct($results, $page = 1) {
		// var_dump($results);
		$this->documents = $results->documents;
		$this->meta = $results->meta;
		$this->spellcheck = (array)$results->spellcheck;
		$this->totalLinks = count($this->documents);
		$this->currentPage = getGETValue('page', $page);
		$this->applySettings();
	}

	public function getMetaInfo() {
		$time = round($this->meta->time, 4);
		$showTime = $time == 0 ? '0 (cache)' : $time . " s";
		$html = "<div class='meta'>";
		$html .= "Total documents: $this->totalLinks, ";
		$html .= "search time: " . $showTime . ", ";
		$html .= "context objects: " . $this->meta->objects . ", ";
		$html .= "attributes: " . $this->meta->attributes;
		$html .= "</div>";
		return $html;
	}
	
	public function getLinksList() {
		if ($this->totalLinks == 0) {
			return '';
		} else {
			$from = ($this->currentPage - 1) * $this->linksOnPage; 
			
		    $html = '<ol>';
		    foreach (array_slice($this->documents, $from, $this->linksOnPage) as $document) {
		        $html .= $this->getItem($document);
		    }
		    $html .= '</ol>';
			
		    return $html;
		}
	}
	
	public function getPaginator() {
		$paginator = new Paginator($this->totalLinks, $this->settings);
		$steps = $paginator->getList($this->currentPage);
		return $steps;
	}

	public function getSpellSuggestions($query) {
		if ($this->totalLinks == 0 && count($this->spellcheck) > 0) {
			$suggLinkQuery = $this->replaceMismatch($query);
			$suggQuery = $this->replaceMismatch($query, true);
			$parameters = $_GET;
			$parameters['query'] = $suggLinkQuery;
			$href = getHTTPQuery($parameters);
			return "Did you mean „<a href='$href'>$suggQuery</a>”?";
		} else {
			return '';
		}
	}

	private function replaceMismatch($query, $wrapper = false) {
		foreach ($this->spellcheck as $key => $value) {
			if ($wrapper) 
				$query = str_replace($key, "<b><i>" . $value . "</i></b>", $query);
			else
				$query = str_replace($key, $value, $query);
		}
		return $query;
	}

	private function applySettings() {
		$this->linksOnPage = Settings::get('linksOnOnePage');
		$this->roundPrecision = Settings::get('roundPrecision');
		$this->showURL = Settings::get('showURL');
		$this->showDescription = Settings::get('showDescription');
	}
	
	private function getItem($document) {
		$url = $document->url;
        $title = $document->title;
		$score = round($document->score, $this->roundPrecision);
		
		
		$html = "<li><div class='item-score'>$score</div><div class='item-body'>";
		$html .= "<div class='item-title'><a href='$url'>$title</a></div>";

		if ($this->showURL)
			$html .= "<div class='item-url'><a href='$url'>$url</a></div>"; 
		
		if (isset($document->description) && $this->showDescription) {
			$description = $document->description;
			$html .= "<div class='item-description'>$description</div>";
		}
		
        return $html . "</div></li>";
	}
}
