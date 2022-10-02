<?php

namespace Lib;

use Lib\Collection;

# store collection id and title data in json file for later use
class CollectionDB
{
    static function get_collection_id($title)
    {
        if ($collection = self::search_collection_by("title", $title)) {
            return $collection["id"];
        }

        # if collection is not found, create it via API
        $collection = Collection::collection_create(["title" => $title]);

        self::add_collection($collection->id, $collection->title);

        return $collection->id;
    }

    static function search_collection_by($key, $value)
    {
        /*
         * Example array
         * [
         *  ["id" => 123, "title" => "test"],
         *  ["id" => 456, "title" => "test2"]
         * ]
         */

        $collections = json_decode(file_get_contents(__DIR__ . "/../collections.json"), true);
        foreach ($collections as $collection) {
            if ($collection[$key] == $value) {
                return $collection;
            }
        }
        return false;
    }

    static function get_collection_title($id)
    {
        if ($collection = self::search_collection_by("id", $id)) {
            return $collection->title;
        }

        $collection = Collection::get_collection($id);

        self::add_collection($collection->id, $collection->title);

        return $collection->title;

    }

    static function add_collection($id, $title)
    {
        $collections = json_decode(file_get_contents(__DIR__ . "/../collections.json"), true);
        $collections[] = ["id" => $id, "title" => $title];
        file_put_contents("collections.json", json_encode($collections));
    }

    /*
     * Update collection data
     */
    static function update_collection_data(){
        $collections = Collection::get_all_collections();
        $collection_data = [];
        foreach ($collections as $collection) {
            $collection_data[] = ["id" => $collection->id, "title" => $collection->title];
        }
        file_put_contents("collections.json", json_encode($collection_data));
    }
}