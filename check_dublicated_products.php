<?php

include __DIR__ . '/init.php';


\Lib\ProductDB::update_product_data();

$products = file_get_contents(__DIR__ . '/products.json');
$products = json_decode($products, true);

# check if there are any dublicated products by sku
$sku = [];

foreach ($products as $product) {

    if (in_array($product['sku'], $sku)) {

        echo 'Dublicated product: ' . $product['id'] . PHP_EOL;
        # delete
        \Lib\Product::product_delete($product['id']);
    }

    $sku[] = $product['sku'];

}
