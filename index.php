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

Settings::loadSettings();

if (LOCALHOST)
	$databases = array('matweb', 'jpw', 'inf', 'small', 'jakpodnikat');
else 
	$databases = array('inf', 'jpwi', 'jakpodnikat');

if (isset($_GET['clearcache'])) {
	Cache::clearCache($_GET['clearcache']);
}

if ($query) {
	$searchResults = search($query, $database);
	$sresults = new Sresults($searchResults);
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
		    
			    <select name="database">
			        <?php
			        foreach ($databases as $dtb) {
			            $selected = $dtb == $database ? 'selected' : '';
			            echo "<option $selected>$dtb</option>\n";
			        }
			        ?>
			    </select>
		    </div>
		</form>
		
		<div class="suggestions">
			<?php
				if($query) {
					$fca = new Fca($searchResults);
				    $siblStr = $fca->getSimilar();
					$genStr = $fca->getGeneralization();
					$specStr = $fca->getSpecialization();
										
				    echo '<div>', $specStr, '</div><div>', $siblStr, '</div>', '<div>', $genStr, '</div>';
				}
			?>
		</div>
		
		<div class="results">
			<?php
			if($query) {
				echo $sresults->getSpellSuggestions($query);						
			    echo '<h2>Search results</h2>';
				// echo '<div class="meta">Total documents: ', $sresults->totalLinks, '</div>';
				echo $sresults->getMetaInfo();
   				echo $sresults->getLinksList();
				echo $sresults->getPaginator();
			}
			?>
		</div>
	</div>
</div>