<?php

require_once __DIR__ . '/init.php';

use Lib\Product;

echo "Product Count : " . Product::product_count() . ' Tıme : ' . date('Y-m-d H:i:s') . PHP_EOL;