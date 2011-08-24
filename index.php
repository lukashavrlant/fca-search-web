<?php
require_once 'loader.php';
$query = '';
if(isset ($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);
}
?>

<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Chandler &mdash; FCA vyhledávač</title>

<h1>Chandler &mdash; FCA vyhledávač</h1>

<form method="get">
    <input type="text" size="50" name="query" value="<?=$query?>"> <input type="submit" name="search">
</form>

<?php
$database = 'matweb';


if($query) {
    $searchResults = search($query, $database);
    $jsonDecode = json_decode($searchResults);

    echo '<h2>Výsledky hledání</h2>';
    echo getLinksList($jsonDecode);

    echo '<h2>Další možnosti</h2>';
    $fca = getFcaExtension($jsonDecode, $query);
    echo $fca['spec'], '<br><hr>', $fca['sib'];
}