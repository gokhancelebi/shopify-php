<?php

exit;

require_once __DIR__ .'/init.php';

use Lib\ProductDB;
use Lib\Product;

# update product data
ProductDB::update_product_data();

$products = ProductDB::get_all_products();

$i = 0;

foreach($products as $product){
    $sku = $product['sku'];
    $product_id = $product["id"];

    # delete product if sku is empty
    if ($sku == ""){
        $i++;
        echo "https://mr-homeland-deutschland.myshopify.com/admin/products/".$product_id . PHP_EOL;
        Product::product_delete($product_id);
    }
}


echo $i . " Adet Silindi" . PHP_EOL;