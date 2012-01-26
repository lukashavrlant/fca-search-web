<?php

$filenames = array('functions', 'constants', 'paginator');
foreach ($filenames as $filename) {
    require_once $filename . '.php';
}