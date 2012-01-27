<?php
class Paginator {
	public $onPage = 15;
	public $showMax = 10;
	
	private $totalLinks;
	
	public function __construct($totalLinks) {
		$this->totalLinks = $totalLinks;
	}
	
	public function getList($currentPage) {
		if($this->totalLinks <= $this->onPage) 
			return '';
		
		$steps = ceil($this->totalLinks / $this->onPage);
		
		if ($steps > $this->showMax) {
			$from = max(1, $currentPage - (int)($this->showMax / 2));
			$to = min($steps, $from + $this->showMax);
		} else {
			$from = 1;
			$to = $steps+1;
		}
		
		$html = "<div class='steps'>\n";
		
		for($i=$from; $i < $to; $i++) {
			$html .= $this->addStep($i, $i == $currentPage);
		}
		
		$html .= '<div class="cleaner"></div></div>';
		return $html;
	}
	
	private function addStep($page, $curr) {
		$href = $this->getHref($page);
		$class = $curr ? ' class="currentpage"' : '';
		return "<span$class><a href='$href'>$page</a></span>\n";
	}
	
	private function getHref($page) {
		$parameters = $_GET;
		$parameters['page'] = $page;
		return getHTTPQuery($parameters);;
	}
}