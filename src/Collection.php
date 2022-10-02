<?php
namespace Lib;
#Custom collection class
class Collection{

    /*
     * Create custom collection
     * https://shopify.dev/api/admin-rest/2022-07/resources/customcollection#post-customcollections
     *
     */
    static function collection_create($data)
    {
        $data = ["custom_collection" => $data];
        $url = STORE_DOMAIN . "admin/api/" . API_VERSION . "/custom_collections.json";
        $response = json_decode(http_request($url, $data, "POST"));
        if (isset($response->custom_collection)) {
            return $response->custom_collection;
        }
        return false;
    }

    /*
     * Retrieve custom collections
     * https://shopify.dev/api/admin-rest/2022-07/resources/customcollection#get-customcollections
     *
     */
    static function get_all_collections(){
        $collections = [];
        $continue = true;
        $since_id = 0;
        $limit = 10;
        while ($continue) {
            $url = STORE_DOMAIN . "admin/api/" . API_VERSION . "/custom_collections.json?limit=".$limit."&since_id=" . $since_id;
            $response = json_decode(http_request($url, [], "GET"));
            var_dump($response);
            if (isset($response->custom_collections)) {
                $collections = array_merge($collections, $response->custom_collections);
                if (count($response->custom_collections) < $limit) {
                    $continue = false;
                } else {
                    $since_id = $response->custom_collections[count($response->custom_collections) - 1]->id;
                }
            } else {
                $continue = false;
            }
        }
        return $collections;
    }

    /*
     * Retrieve single custom collection
     */
    static function get_collection($collection_id)
    {
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/custom_collections/" . $collection_id . ".json";
        $response = json_decode(http_request($url, [], "GET"));
        if (isset($response->custom_collection)) {
            return $response->custom_collection;
        }
        return false;
    }

    /*
     * Add product to custom collection
     */
    static function add_product_to_collection($collection_id, $product_id){
        $data = ["collect" => ["collection_id" => $collection_id, "product_id" => $product_id]];
        $url = STORE_DOMAIN . "/admin/api/" . API_VERSION . "/collects.json";
        $response = json_decode(http_request($url, $data, "POST"));
        if (isset($response->collect)) {
            return $response->collect;
        }
        return false;
    }

    /*
     * Assign product to custom collection
     */
    static function assign_product_to_collection($collection_title, $product_id)
    {
        $collection_id = CollectionDB::get_collection_id($collection_title);

        if ($collection_id){
            $collect = Collection::add_product_to_collection($collection_id,$product_id);
            if ($collect){
                return $collect;
            }
        }
        return false;
    }

}