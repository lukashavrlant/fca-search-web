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
	
	public function __construct($results, $page = 1) {
		$this->documents = $results->documents;
		$this->meta = $results->meta;
		$this->totalLinks = count($this->documents);
		$this->currentPage = getGETValue('page', $page);
		$this->applySettings();
	}

	public function getMetaInfo() {
		$time = round($this->meta->time, 4);
		$showTime = $time == 0 ? '0 (cache)' : $time . " s";
		$html = "<div class='meta'>";
		$html .= "Total documents: $this->totalLinks, ";
		$html .= "search time: " . $showTime;
		$html .= "</div>";
		return $html;
	}
	
	public function getLinksList() {
		$from = ($this->currentPage - 1) * $this->linksOnPage; 
		
	    $html = '<ol>';
	    foreach (array_slice($this->documents, $from, $this->linksOnPage) as $document) {
	        $html .= $this->getItem($document);
	    }
	    $html .= '</ol>';
		
	    return $html;
	}
	
	public function getPaginator() {
		$paginator = new Paginator($this->totalLinks, $this->settings);
		$steps = $paginator->getList($this->currentPage);
		return $steps;
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
