<?php
class Paginator {
	public $onPage = 15;
	public $showMax = 10;
	
	private $number;
	
	public function __construct($number) {
		$this->number = $number;
	}
	
	public function getList() {
		if($this->number <= $this->onPage) 
			return '';
		
		$steps = ceil($this->number / $this->onPage);
		$html = '<div class="steps">';
		
		for($i=1; $i < $steps; $i++) {
			$html .= $this->addStep($i);
		}
		
		$html .= '</div>';
		return $html;
	}
	
	private function addStep($page)
	{
		$href = $this->getHref($page);
		return "<span><a href='$href'>$page</a></span>";
	}
	
	private function getHref($page) {
		$parameters = $_GET;
		$parameters['page'] = $page;
		return getHTTPQuery($parameters);;
	}
}