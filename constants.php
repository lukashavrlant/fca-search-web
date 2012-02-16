<?php

if (LOCALHOST) {
	$fcasearch = '/Users/lukashavrlant/Python/fca-search/src/search ';
	$python3 = '/Library/Frameworks/Python.framework/Versions/3.2/bin/python3 ';
} else {
	$fcasearch = '/home/havrlanl/Chandler/src/search ';
	$python = '/home/havrlanl/python-3.2.2/bin/python3 ';
}


define('FCASEARCH', $fcasearch);
define('PYTHON3', $python3);