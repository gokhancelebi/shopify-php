<?php

if (!file_exists(__DIR__ . '/config.php')){
    echo 'Rename config-sample.php to config.php and edit credentials';
    exit;
}

# show errors
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/vendor/autoload.php';

if (!file_exists(__DIR__ . '/collections.json')){
    file_put_contents(__DIR__ . '/collections.json', '[]');
}

if (!file_exists(__DIR__ . '/products.json')){
    file_put_contents(__DIR__ . '/products.json', '[]');
}