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
        if (!isset($_SESSION['user_id'])) {
            return false; // Return false if user is not logged in
        }
        
        if (isset($userid) && isset($itemid)){
            // Verify that the user_id matches the logged-in user
            if ($userid != $_SESSION['user_id']) {
                return false;
            }
            
            // Check if item is already in wishlist for this user
            $query = "SELECT * FROM wishlist WHERE user_id = {$userid} AND item_id = {$itemid}";
            $result = $this->db->con->query($query);
            if ($result && mysqli_num_rows($result) > 0) {
                return true; // Item already in wishlist
            }
            
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
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        if($item_id != null){
            $result = $this->db->con->query("DELETE FROM {$table} WHERE item_id={$item_id} AND user_id={$_SESSION['user_id']}");
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
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        if ($item_id != null){
            $query = "INSERT INTO cart (user_id, item_id) 
                     SELECT user_id, item_id FROM wishlist 
                     WHERE item_id={$item_id} AND user_id={$_SESSION['user_id']};";
            $query .= "DELETE FROM wishlist WHERE item_id={$item_id} AND user_id={$_SESSION['user_id']};";

            // execute multiple query
            $result = $this->db->con->multi_query($query);

            if($result){
                header("Location:" . $_SERVER['PHP_SELF']);
            }
            return $result;
        }
    }
} 