<?php
setlocale(LC_ALL, 'cs_CZ.utf8');
require_once 'core/loader.php';

$query = getGETValue('query');
$database = getGETValue('database', 'matweb');

$databases = array('matweb', 'jpw', 'inf');

?>
<!DOCTYPE HTML>
<meta charset="utf-8">
<title>Chandler &mdash; FCA vyhledávač</title>

<h1><a href="./">Chandler &mdash; FCA vyhledávač</a></h1>

<form method="get">
    <input type="text" size="50" name="query" value="<?=$query?>"> <input type="submit" name="search"><br>
    <select name="database">
        <?php
        foreach ($databases as $dtb) {
            $selected = $dtb == $database ? 'selected' : '';
            echo "<option $selected>$dtb</option>\n";
        }
        ?>
    </select>
</form>

<?php
if($query) {
    $searchResults = search($query, $database);
    $jsonDecode = json_decode($searchResults);

    echo '<h2>Výsledky hledání</h2>';
    echo getLinksList($jsonDecode);

    echo '<h2>Další možnosti</h2>';
    $fca = getFcaExtension($jsonDecode, $query, $database);
    echo $fca['spec'], '<br><hr>', $fca['sib'];
}