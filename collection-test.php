<?php

require_once __DIR__ .'/init.php';

use Lib\Product;
use Lib\Collection;
use Lib\CollectionDB;


$custom_collection      = "Custom Test Collection 1";
$podduct_id = 7348234879168;

$assign = Collection::assign_product_to_collection($custom_collection, $podduct_id);

