<?php

require_once __DIR__ . '/init.php';

use Lib\ProductDB;

//ProductDB::update_product_data();

//file_put_contents(__DIR__ . '/test.csv',http_get_request(FILE_URL));

$file = __DIR__ . '/test.csv';

$datas = [];
$sku_array = [];

// Open the file
if (($handle = fopen($file, 'r')) !== false) {
    $line = 0;
    // Read each line split by ';'.
    while (($data = fgetcsv($handle, 0, ';')) !== false) {

        $line++;



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


         if ($sku != "") continue;

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

        echo "SKU : " . $sku . " " . $price . ' ' . $stock . PHP_EOL;

//        break;
    }
    // Close the file
    fclose($handle);
}

file_put_contents(__DIR__ . '/count.txt',$line);