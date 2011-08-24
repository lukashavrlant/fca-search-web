<?php

$filenames = array('functions', 'constants');
foreach ($filenames as $filename) {
    require_once $filename . '.php';
}