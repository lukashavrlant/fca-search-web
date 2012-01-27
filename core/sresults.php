<?php
class Sresults {
	public $totalLinks;
	public $linksOnPage;
	
	private $documents;
	private $currentPage;
	
	public function __construct($results, $page = 1) {
		$this->documents = $results->documents;
		$this->totalLinks = count($this->documents);
		$this->currentPage = getGETValue('page', $page);
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
		$paginator = new Paginator($this->totalLinks);
		$steps = $paginator->getList($this->currentPage);
		return $steps;
	}
	
	private function getItem($document) {
		$url = $document->url;
        $title = $document->title;
		
		
		
		$html = "<li><div class='item-title'><a href='$url'>$title</a></div>";
		$html .= "<div class='item-url'><a href='$url'>$url</a></div>"; 
		
		if (isset($document->description)) {
			$description = $document->description;
			$html .= "<div class='item-description'>$description</div>";
		}
		
        return $html . "</li>";
	}
}
