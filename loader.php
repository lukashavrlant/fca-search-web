<?php

$filenames = array('functions', 'constants', 'paginator', 'fca', 'cache', 'sresults');
foreach ($filenames as $filename) {
    require_once $filename . '.php';
}