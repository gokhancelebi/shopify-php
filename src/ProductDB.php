<?php

namespace Lib;

# store collection id and title data in json file for later use
class ProductDB
{
    static function get_product_by_product_id($product_id){
        $products = json_decode(file_get_contents(__DIR__ . '/../products.json'), true);
        foreach ($products as $product) {
            if ($product['id'] == $product_id) {
                return $product;
            }
        }
        return false;
    }
    # get product data by sku
    public static function get_product_id($sku)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        foreach ($products as $product) {
            if ($product["sku"] == $sku) {
                return $product["id"];
            }

            // or check it from variation
            foreach ($product['data']['variants'] as $variant) {
                if ($variant['sku'] == $sku) {
                    return $product["id"];
                }
            }
        }
  
        return false;
    }

    
    public static function get_dublicated_products($sku)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        $dublicated_products = [];
        foreach ($products as $product) {
            if ($product["sku"] == $sku) {
                $dublicated_products[] = $product;
            }
        }
        return $dublicated_products;
    }

    # get dublicated count
    public static function get_dublicated_count($sku)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        $dublicated_count = 0;
        foreach ($products as $product) {
            if ($product["sku"] == $sku) {
                $dublicated_count++;
            }
        }
        return $dublicated_count;
    }

    public static function get_all_products()
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        return $products;
    }

    # get product variation id by sku
    public static function get_product_variation_id($sku)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        foreach ($products as $product) {
            if ($product["sku"] == $sku) {
                return $product["variation_id"];
            }
        }
        return false;
    }

    # get product by title
    public static function get_product_by_title($title)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        foreach ($products as $product) {
            if ($product["data"]["title"] == $title) {
                return $product;
            }
        }
        return false;
    }

    # add product data to json file
    public static function add_product($id, $variation_id ,$sku,$row = [])
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        $products[] = ["id" => $id, "sku" => $sku, "variation_id" => $variation_id,'data' => $row];
        file_put_contents(__DIR__ . "/../products.json", json_encode($products));
    }

    # get product data by id
    public static function get_product_sku($id)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        foreach ($products as $product) {
            if ($product["id"] == $id) {
                return $product["sku"];
            }
        }
        return false;
    }

    # delete product
    public static function delete_product($id)
    {
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        foreach ($products as $key => $product) {
            if ($product["id"] == $id) {
                unset($products[$key]);
            }
        }
        file_put_contents(__DIR__ . "/../products.json", json_encode($products));
    }

    # reset data
    public static function reset_data(){
        file_put_contents(__DIR__ . "/../products.json", json_encode([]));
    }

 static function update_product_data()
    {
        echo "Product data getting updated" . PHP_EOL;

        self::reset_data();

        $page = 1;

        $continue = true;
        $since_id = 0;

        $product_data = [];

        while ($continue) {

            echo "Page: " . $page . " downloaded" .PHP_EOL;

            $url = STORE_DOMAIN . "admin/api/" . API_VERSION . "/products.json?limit=250&since_id=" . $since_id;

            $data = http_get_request($url);

            $data = json_decode($data, true);

            if (isset($data["products"]) && count($data["products"])) {
                foreach ($data["products"] as $row) {
                    $product_data[] = ["id" => $row["id"], "sku" => $row["variants"][0]["sku"], "variation_id" => $row["variants"][0]["id"],'data'=>$row];
                    $since_id = $row["id"];
                }
            }

            if (!isset($data["products"]) || count($data["products"]) == 0) {
                $continue = false;
            }

            $page++;

            sleep(1);
        }

        file_put_contents(__DIR__ . "/../products.json", json_encode($product_data));

        echo "Product data updated" . PHP_EOL;
    }


    static function product_count(){
        $products = json_decode(file_get_contents(__DIR__ . "/../products.json"), true);
        return count($products);
    }
}
