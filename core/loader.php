<?php

$filenames = array('functions', 'constants', 'paginator', 'fca', 'cache');
foreach ($filenames as $filename) {
    require_once $filename . '.php';
}