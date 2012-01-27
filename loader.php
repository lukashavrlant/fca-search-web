<?php

$filenames = glob('core/*'); 
foreach ($filenames as $filename) {
    require_once $filename;
}