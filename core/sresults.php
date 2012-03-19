<?php
class Sresults {
	public $totalLinks;
	public $linksOnPage;
	public $showURL;
	public $showDescription;
	public $queryHash;
	
	private $documents;
	private $currentPage;
	private $roundPrecision;
	private $settings;
	private $meta;
	private $spellcheck;
	
	public function __construct($results, $page = 1) {
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
		$html = "<div class='meta'>\n";
		$html .= "Total documents: $this->totalLinks, ";
		$html .= "search time: " . $showTime . ".";
		$html .= "<br>FCA info: Objects: " . $this->meta->objects . ", ";
		$html .= "attributes: " . $this->meta->attributes . ", ";
		$html .= "lower neighbor concepts: " . $this->meta->lower . ", ";
		$html .= "upper: " . $this->meta->upper . ", ";
		$html .= "siblings: " . $this->meta->neighbor . ", ";

		if ($this->queryHash) {
			$database = htmlspecialchars($_GET['database']);
			$html .= "<a href='lattice.php?hash=" . $this->queryHash . "&amp;database=" . $database . "'>part of concept lattice</a>";
		}

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

	private function cropText($text, $maxLength)
	{
		if (strlen($text) > $maxLength) {
			$crop = substr($text, 0, $maxLength);
			if ($text[$maxLength] == ' ' || $text[$maxLength-1] == ' ') {
				return trim($crop) . '…';
			} else {
				$cropArr = explode(' ', $crop);
				$cropArr = array_slice($cropArr, 0, count($cropArr) - 1);
				$lowerCrop = implode(' ', $cropArr);
				if (strlen($lowerCrop) < (strlen($crop) / 2)) {
					return trim($crop) . '…';
				} else {
					return trim($lowerCrop) . '…';
				}
			}
		} else {
			return $text;
		}
	}

	private function escapeTitle($title)
	{
		$title = $this->cropText($title, Settings::get('maxTitleLength'));
		$title = htmlspecialchars($title);
		return $title;
	}

	private function espaceURL($url)
	{
		$maxLength = Settings::get('maxURLLength');
		if (strlen($url) > $maxLength) {
			$url = substr($url, 0, $maxLength - 5) . '…';
		}
		$url = htmlspecialchars($url);
		return $url;
	}

	private function espaceDesc($desc)
	{
		$desc = $this->cropText($desc, Settings::get('maxDescriptionLength'));
		$desc = htmlspecialchars($desc);
		return $desc;
	}
	
	private function getItem($document) {
		$url = $document->url;
		$esurl = $this->espaceURL($document->url);
        $title = $this->escapeTitle($document->title);
		$score = round($document->score, $this->roundPrecision);
		
		
		$html = "<li><div class='item-score'>$score</div><div class='item-body'>";
		$html .= "<div class='item-title'><a href='$url'>$title</a></div>";

		if ($this->showURL)
			$html .= "<div class='item-url'><a href='$url'>$esurl</a></div>"; 
		
		if (isset($document->description) && $this->showDescription) {
			$description = $this->espaceDesc($document->description);
			$html .= "<div class='item-description'>$description</div>";
		}
		
        return $html . "</div></li>";
	}
}
