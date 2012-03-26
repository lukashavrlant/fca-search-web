<?php
setlocale(LC_ALL, 'cs_CZ.utf8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('ROOT', dirname(__FILE__) . '/');
define('MIN_LINKS_TO_SHOW_SPECIALIZATION', 10);
define('LOCALHOST', $_SERVER['HTTP_HOST'] == 'localhost');

require_once 'loader.php';

$query = getGETValue('query', '');
$database = getGETValue('database', 'matweb');
$lang = getGETValue('lang', 'cs');

Settings::loadSettings();

if (LOCALHOST) {
	$databases = array('matweb', 'jpw', 'inf', 'small', 'jakpodnikat', 'radim', 'matwebloc');
} else {
	$databases = Settings::get('databases');
	if (isset($databases->$lang)) {
		$databases = $databases->$lang;
	} else {
		$databases = $databases->cs;
	}
}

if (isset($_GET['clearcache'])) {
	Cache::clearCache($_GET['clearcache']);
}

if ($query) {
	$cache = new Cache($database);
	$searchResults = false;

	try {
		$searchResults = search($query, $database, $cache);
		$sresults = new Sresults($searchResults);
		$sresults->queryHash = $cache->name;
	} catch (SearchException $ex) {
		$logInfo = $query . "\n";
		error_log($logInfo, 3, "errors.log");
	}
}
?>
<!DOCTYPE HTML>
<meta charset="utf-8">
<title>Chandler &mdash; FCA search engine</title>
<link rel="stylesheet" media="screen" href="styly.css">

<div class="web">
	<div class="header"><h1><a href="./">Chandler &mdash; FCA search engine</a></h1></div>
	
	<div class="content">
		<form method="get" class="search">
		    <div class="searchinput">
		    	<input type="text" size="50" name="query" value="<?=htmlspecialchars($query, ENT_QUOTES)?>" class="in"> 
		    	<input type="submit" name="search" class="ok" value="search">
		    	<input type="hidden" name="lang" value="<?=$lang?>">
		    
			    <select name="database">
			        <?php
			        foreach ($databases as $dtb) {
			            $selected = $dtb == $database ? 'selected' : '';
			            echo "<option $selected>$dtb</option>\n";
			        }
			        ?>
			    </select>

			    <a href="?lang=cs"><img src="images/cs.gif" alt="cs"></a>
			    <a href="?lang=en"><img src="images/en.gif" alt="en"></a>
		    </div>
		</form>
		
		<div class="suggestions">
			<?php
				if($query && $searchResults) {
					$fca = new Fca($searchResults);
				    $siblStr = $fca->getSimilar();
					$genStr = $fca->getGeneralization();
					$specStr = $fca->getSpecialization();
										
				    echo "<div class='spec-sugg'>", $specStr, "\n</div>\n<div class='sibl-sugg'>", $siblStr, "\n</div>\n", '<div class="gen-sugg">', $genStr, '</div>';
				}
			?>
		</div>
		
		<div class="results">
			<?php
			if($query && $searchResults) {
				echo $sresults->getSpellSuggestions($query);						
			    echo '<h2>Search results</h2>';
				echo $sresults->getMetaInfo();
   				echo $sresults->getLinksList();
				echo $sresults->getPaginator();
			}
			?>
		</div>

		<?php 
		if ($query && !$searchResults) {
			echo '<div class="error">Something went terribly wrong... Try different query.</div>';
		}
		?>
	</div>
</div>