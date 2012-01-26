<?php

$filenames = array('functions', 'constants', 'paginator', 'fca');
foreach ($filenames as $filename) {
    require_once $filename . '.php';
}