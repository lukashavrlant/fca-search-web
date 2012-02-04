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
	
	public function __construct($results, $settings, $page = 1) {
		$this->documents = $results->documents;
		$this->totalLinks = count($this->documents);
		$this->currentPage = getGETValue('page', $page);
		$this->settings = $settings;
		$this->applySettings($settings);
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

	private function applySettings($settings) {
		$this->linksOnPage = $settings->get('linksOnOnePage');
		$this->roundPrecision = $settings->get('roundPrecision');
		$this->showURL = $settings->get('showURL');
		$this->showDescription = $settings->get('showDescription');
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
