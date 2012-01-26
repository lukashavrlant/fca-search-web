<?php
setlocale(LC_ALL, 'cs_CZ.utf8');
define(ROOT, dirname(__FILE__) . '/');

require_once 'core/loader.php';

$query = getGETValue('query');
$database = getGETValue('database', 'matweb');

$databases = array('matweb', 'jpw', 'inf');

if ($query) {
	$searchResults = search($query, $database);
    $jsonDecode = json_decode($searchResults);
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
		    	<input type="text" size="50" name="query" value="<?=$query?>" class="in"> 
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
					//echo '<h2>Další možnosti</h2>';
				    $fca = getFcaExtension($jsonDecode);
				    echo '<div>', $fca['spec'], '</div><div>', $fca['sib'], '</div>';
				}
			?>
		</div>
		
		<div class="results">
			<?php
			if($query) {			
			    echo '<h2>Search results</h2>';
			    echo getLinksList($jsonDecode);
			}
			?>
		</div>
	</div>
</div>