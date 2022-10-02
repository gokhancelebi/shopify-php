<?php

namespace Lib;

# for more informations : https://shopify.dev/api/admin-rest
class Product
{
    /*
     * Create product
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#post-products
     *
     */
    static function product_create($data)
    {
        $data = ["product" => $data];
        $url = STORE_DOMAIN . "admin/api/" . API_VERSION . "/products.json";
        $response = json_decode(http_request($url, $data, "POST"));
        if (isset($response->product)) {
            return $response->product;
        }
        return false;
    }

    /*
     * Retrieve products
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#get-products
     *
     */
    static function products($data)
    {
        $data = ["products" => $data];
        $url = STORE_DOMAIN . "admin/api/" . API_VERSION . "/products.json";
        $response = json_decode(http_request($url, $data, "GET"));
        if (isset($response->products)) {
            return $response->products;
        }
        return false;
    }

    /*
     * Retrieve single product
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#get-products-product-id
     *
     */
    static function get_product($product_id)
    {
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/products/" . $product_id . ".json";
        $response = json_decode(http_request($url, [], "GET"));
        if (isset($response->product)) {
            return $response->product;
        }
        return false;
    }

    /*
     * Retrieve count of products
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#get-products-count
     *
     */
    static function product_count()
    {
        $url = STORE_DOMAIN . '/admin/api/' . API_VERSION . '/products/count.json';
        $response = json_decode(http_request($url, [], "GET"));
        if (isset($response->count)) {
            return $response->count;
        }
        return false;
    }

    /*
     * Update product
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#put-products-product-id
     *
     */
    static function product_update($product_id, $data)
    {
        $data = ["product" => $data];
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/products/" . $product_id . ".json";
        $response = json_decode(http_request($url, $data, "PUT"),true);
        return $response;
        if (isset($response->product)) {
            return $response->product;
        }
        return false;
    }

    /*
     * Update Price of product
     */
    static function product_update_price($product_id, $price)
    {
        $product = ProductDB::get_product_by_product_id($product_id);

        $data = ["product" => ["id" => $product_id,  "variants" => [["id" => $product["variation_id"], "price" => $price]]]];

        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/products/" . $product_id . ".json";
        $response = json_decode(http_request($url, $data, "PUT"));
        if (isset($response->product)) {
            return $response->product;
        }
        return false;
    }

    /*
     * Delete product
     * https://shopify.dev/api/admin-rest/2022-07/resources/product#delete-products-product-id
     */
    static function product_delete($product_id)
    {
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/products/" . $product_id . ".json";
        http_request($url, [], "DELETE");
        ProductDB::delete_product($product_id);
        return true;
    }

    /*
     * get product inventory level
     * https://shopify.dev/api/admin-rest/2022-07/resources/inventorylevel#get-inventory_levels
     */
    static function product_inventory_item_id($product_id, $variant_id)
    {
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/products/" . $product_id . '/variants/' . $variant_id . ".json";
        $response = json_decode(http_request($url, [], "GET"));
//        if (isset($response->inventory_item_id)) {
//            return $response->inventory_item_id;
//        }
        return $response;
    }

    static function product_inventory_level($inventory_item_id)
    {
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/inventory_levels.json?inventory_item_ids=" . $inventory_item_id;
        $response = json_decode(http_request($url, [], "GET"));
//        if (isset($response->inventory_levels)) {
//           return $response->inventory_levels;
//        }
        return $response;
    }

    static function inventory_level_set($inventory_item_id, $location_id, $available)
    {
        $data = [
            "inventory_item_id" => $inventory_item_id,
            "location_id" => $location_id,
            "available" => $available
        ];

        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/inventory_levels/set.json";
        $response = json_decode(http_request($url, $data, "POST"));
        if (isset($response->inventory_level)) {
            return $response->inventory_level;
        }
        return false;
    }

    static function update_stock($product_id, $variant_id, $available)
    {
        $inventory_item_id = self::product_inventory_item_id($product_id, $variant_id)->variant->inventory_item_id;
        $location_id = self::product_inventory_level($inventory_item_id)->inventory_levels[0]->location_id;
        $response = self::inventory_level_set($inventory_item_id, $location_id, $available);
        return $response;
    }
}