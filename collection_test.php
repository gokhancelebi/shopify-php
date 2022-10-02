<?php

require_once __DIR__ . '/init.php';

$collections = \Lib\Collection::get_all_collections();

print_r($collections);

echo count($collections);