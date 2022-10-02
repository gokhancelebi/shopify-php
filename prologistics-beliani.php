<?php

require_once __DIR__ . '/init.php';

use Lib\Product;
use Lib\ProductDB;
use Lib\Collection;

the_app_status();

ProductDB::update_product_data();

echo Product::product_count() . " Products in Store" . PHP_EOL;

//if (!file_exists(__DIR__ . '/data.csv')) {
      file_put_contents(__DIR__ . '/data.csv', http_get_request(FILE_URL));
//}

$file = __DIR__ . '/data.csv';

$datas = [];
$sku_array = [];

// Open the file
if (($handle = fopen($file, 'r')) !== false) {
    $line = 0;
    // Read each line split by ';'.
    while (($data = fgetcsv($handle, 0, ';')) !== false) {

        update_app_status("running");

        if ($line == 0) {
            file_put_contents(__DIR__ . '/columns.json', json_encode($data));
        }

        $line++;


//        if ($line % 2 != 1){
//            continue;
//        }

        if ($line == 1) {
            continue;
        }

        $product_name = $data[4];
        $description = $data[68];
        $price_include_vat = $data[6];
        $price = 1.25 * $price_include_vat;
        $picture = $data[11];
        $stock = $data[14]; // envanter // Lagerbestand
        $delivery_time = $data[15]; // teslim süresi // Lieferzeit
        $kategorie_1 = $data[16];
        $kategorie_2 = $data[17];
        $kategorie_3 = $data[18];
        $kategorie_4 = $data[19];

        #trim all categories
        $kategorie_1 = trim($kategorie_1);
        $kategorie_2 = trim($kategorie_2);
        $kategorie_3 = trim($kategorie_3);
        $kategorie_4 = trim($kategorie_4);


        $categories = [];

        if ($kategorie_1 != '') {
            $categories[] = $kategorie_1;
        }
        if ($kategorie_2 != '') {
            $categories[] = $kategorie_2;
        }
        if ($kategorie_3 != '') {
            $categories[] = $kategorie_3;
        }
        if ($kategorie_4 != '') {
            $categories[] = $kategorie_4;
        }


        $farbe = ucfirst($data[21]); // renk

        if ($farbe == "") {
            $farbe = null;
        }

        $stoff = ucfirst($data[22]); // Malzeme

        if ($stoff == "") {
            $stoff = null;
        }

        $url = $data[10];
        $sku = $data[0];


        // if ($sku != "13207") continue;

        $sku_array[$sku] = 1;


        $images = [];

        if (isset($data[11]) && !empty($data[11])) {
            $images[] = ["src" => $data[11]];
        }

        if (isset($data[23]) && !empty($data[23])) {
            $images[] = ["src" => $data[23]];
        }
        if (isset($data[24]) && !empty($data[24])) {
            $images[] = ["src" => $data[24]];
        }
        if (isset($data[25]) && !empty($data[25])) {
            $images[] = ["src" => $data[25]];
        }
        if (isset($data[26]) && !empty($data[26])) {
            $images[] = ["src" => $data[26]];
        }
        if (isset($data[27]) && !empty($data[27])) {
            $images[] = ["src" => $data[27]];
        }
        if (isset($data[28]) && !empty($data[28])) {
            $images[] = ["src" => $data[28]];
        }
        if (isset($data[29]) && !empty($data[29])) {
            $images[] = ["src" => $data[29]];
        }

        $purchase_cost = $data[47]; // alış fiyatı // Einkaufspreis

        $video = $data[69]; // video


        echo "SKU : " . $product_name. " " . $sku . " " . $price . PHP_EOL;


        $options = [];

        $product_id = ProductDB::get_product_id($sku);

        if (!$product_id) {

            echo " - Product not found. Creating new product. " . PHP_EOL;

            $product = Product::product_create([
                "title" => $product_name,
                "body_html" => $description
            ]);

            $product_id = $product->id;
            $variation_id = $product->variants[0]->id;

            # Farbe
            if (isset($data[21]) && !empty($data[21])) {
                $options[] = ["name" => "Farbe", "values" => [$data[21]], "position" => 1, "product_id" => $product_id, "variation_id" => $variation_id];
            }

            #stoff
            if (isset($data[22]) && !empty($data[22])) {
                $options[] = ["name" => "Stoff", "values" => [$data[22]], "position" => 2, "product_id" => $product_id, "variation_id" => $variation_id];
            }


            $inventory_item_id = Product::product_inventory_item_id($product_id, $variation_id);
            $inventory_item_id = $inventory_item_id->variant->inventory_item_id;

            $inventory_levels = Product::product_inventory_level($inventory_item_id);
            $location_id = $inventory_levels->inventory_levels[0]->location_id;

            $variant = [
                "id" => $variation_id,
                "title" => "Default Title",
                "price" => $price,
                "quantity" => $stock,
                "inventory_quantity" => $stock,
                "old_inventory_quantity" => $stock,
                "inventory_management" => "shopify",
                "fulfillment_service" => "manual",
                "inventory_policy" => "continue",
                "compare_at_price" => null,
                "position" => 1,
                "taxable" => true,
                "requires_shipping" => true,
                "sku" => $sku
            ];

            if ($farbe != null) {
                $variant["option1"] = $farbe;
            }

            if ($stoff != null) {
                $variant["option2"] = $stoff;
            }

            $product_data = [
                "id" => $product_id,
                "title" => $product_name,
                "body_html" => $description,
                "vendor" => "Beliani",
                "product_type" => "Beliani",
                "tags" => "Beliani",
                "status" => "active",
                "variants" => [
                    $variant
                ]
            ];

            if (count($options) > 0) {
                $product_data["options"] = $options;
            }else{
                $product_data["options"] = null;
            }

            if (count($images) > 0) {
                $product_data["images"] = $images;
            }else{
                $product_data["images"] = null;
            }

            $update = Product::product_update($product_id, $product_data);

            if (isset($update["errors"])){
                echo " - Product update error. " . PHP_EOL;
                print_r($update);
                file_put_contents(__DIR__ . '/product_update_error.json', json_encode($update) . PHP_EOL, FILE_APPEND);
                # delete product
                Product::product_delete($product_id);
                continue;
            }

            $update_stock = Product::update_stock($product_id, $variation_id, $stock);

            ProductDB::add_product($product_id, $variation_id, $sku);

            $cat_text = [];

            foreach ($categories as $category) {
                $cat_text[] = $category;
                Collection::assign_product_to_collection(implode(" ", $cat_text), $product_id);
            }

            echo " - Product created. " . PHP_EOL;

            echo STORE_DOMAIN . "admin/products/" . $product_id . PHP_EOL;


        } else {

            echo " - Product found. Updating product. " . PHP_EOL;

            # update stock
            $product_variation_id = ProductDB::get_product_variation_id($sku);
            $update_stock = Product::update_stock($product_id, $product_variation_id, $stock);

            echo "Quantity : " . $stock . PHP_EOL;
            # update price
            $update_price = Product::product_update_price($product_id, $price);

        }

    }
    // Close the file
    fclose($handle);
}

update_app_status("done");
file_put_contents(__DIR__ . '/count.txt', count($sku_array));
