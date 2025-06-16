<?php

namespace database;

class Wishlist
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return null;
        $this->db = $db;
    }

    // insert into wishlist table
    public function insertIntoWishlist($params = null, $table = "wishlist"){
        if ($this->db->con != null){
            if ($params != null){
                // get table columns
                $columns = implode(',', array_keys($params));
                $values = implode(',' , array_values($params));

                // create sql query
                $query_string = sprintf("INSERT INTO %s(%s) VALUES(%s)", $table, $columns, $values);

                // execute query
                $result = $this->db->con->query($query_string);
                return $result;
            }
        }
    }

    // to get user_id and item_id and insert into wishlist table
    public function addToWishlist($userid, $itemid){
        if (isset($userid) && isset($itemid)){
            $params = array(
                "user_id" => $userid,
                "item_id" => $itemid
            );

            // insert data into wishlist
            $result = $this->insertIntoWishlist($params);
            return $result;
        }
        return false;
    }

    // delete wishlist item using item id
    public function deleteWishlist($item_id = null, $table = 'wishlist'){
        if($item_id != null){
            $result = $this->db->con->query("DELETE FROM {$table} WHERE item_id={$item_id}");
            if($result){
                header("Location:" . $_SERVER['PHP_SELF']);
            }
            return $result;
        }
    }

    // get item_id of wishlist items
    public function getWishlistId($wishlistArray = null, $key = "item_id"){
        if ($wishlistArray != null){
            $wishlist_id = array_map(function ($value) use($key){
                return $value[$key];
            }, $wishlistArray);
            return $wishlist_id;
        }
    }

    // move item from wishlist to cart
    public function moveToCart($item_id = null){
        if ($item_id != null){
            $query = "INSERT INTO cart SELECT * FROM wishlist WHERE item_id={$item_id};";
            $query .= "DELETE FROM wishlist WHERE item_id={$item_id};";

            // execute multiple query
            $result = $this->db->con->multi_query($query);

            if($result){
                header("Location:" . $_SERVER['PHP_SELF']);
            }
            return $result;
        }
    }
} 