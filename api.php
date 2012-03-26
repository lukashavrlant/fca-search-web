<?php
setlocale(LC_ALL, 'cs_CZ.utf8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('LOCALHOST', $_SERVER['HTTP_HOST'] == 'localhost');
define('ROOT', dirname(__FILE__) . '/');
define('CACHE', ROOT . 'cache/');
require_once 'constants.php';

$supportedActions = array('d', 'q', 'f', 'links', 'linkscount', 'words', 'freq', 'docid', 'tf', 'findURL', 'finddocid', 
	'docfreq', 'docinfo', 'wordscount', 'title', 'description', 'keywords', 'url', 'id', 'stem', 'lang');

function search($options, $supported) {
	$query = '';

	foreach ($options as $key => $value) {
		if (in_array($key, $supported)) {
			$key = strlen($key) == 1 ? "-$key" : "--$key";
			$escaped = escapeshellarg($value);
			$query .= "$key $escaped ";
		}
	}
	if ($query) {
	    $command = PYTHON3 . FCASEARCH . $query;
	    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
	    return $data;
	} else {
		return "";
	}
}

/**
 * Source: http://www.php.net/manual/en/function.var-dump.php#105343
 * Better GI than print_r or var_dump -- but, unlike var_dump, you can only dump one variable.  
 * Added htmlentities on the var content before echo, so you see what is really there, and not the mark-up.
 * 
 * Also, now the output is encased within a div block that sets the background color, font style, and left-justifies it
 * so it is not at the mercy of ambient styles.
 *
 * Inspired from:     PHP.net Contributions
 * Stolen from:       [highstrike at gmail dot com]
 * Modified by:       stlawson *AT* JoyfulEarthTech *DOT* com 
 *
 * @param mixed $var  -- variable to dump
 * @param string $var_name  -- name of variable (optional) -- displayed in printout making it easier to sort out what variable is what in a complex output
 * @param string $indent -- used by internal recursive call (no known external value)
 * @param unknown_type $reference -- used by internal recursive call (no known external value)
 */
function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{
    $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';
    
    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar]))
    {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else
    {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar))
        {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar))
        {
            echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color\"".htmlentities($avar)."\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".htmlentities($avar)."</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#666666'>$type(".strlen($avar).")</span> ".htmlentities($avar)."<br>";

        $var = $var[$keyvar];
    }
    
    echo "</div>";
}

// basic API
if (count($_GET)) {
	$data = search($_GET, $supportedActions);
	if (isset($_GET['pretty'])) {
		echo '<meta charset="utf-8"><pre>';
		do_dump(json_decode($data));
	} else {
		echo $data;
	}
}

if ($_POST) {
    $data = $_POST['data'];
    $name = md5($data) . '.txt';
    $directory = CACHE . '__temp/';
    if(!file_exists($directory)) {
        mkdir($directory);
    }
    $path = $directory . $name;
    file_put_contents($path, $data);

    $command = PYTHON3 . FCASEARCH . '--tempsearch ' . $path;
    $data = shell_exec("LANG=cs_CZ.utf-8; " . $command);
    echo $data;
}


















