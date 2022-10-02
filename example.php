<?php

require_once __DIR__ . '/init.php';

use Lib\Product;

$product = Product::product_create(["title" => "New Product","product_type" => "New Product Type"]);
$product_id = $product->id;