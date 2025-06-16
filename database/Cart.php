<?php

namespace database;

// php cart class
class Cart
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return null;
        $this->db = $db;
    }

    // insert into cart table
    public  function insertIntoCart($params = null, $table = "cart"){
        if ($this->db->con != null){
            if ($params != null){
                // "Insert into cart(user_id) values (0)"
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

    // to get user_id and item_id and insert into cart table
    public function addToCart($userid, $itemid){
        if (!isset($_SESSION['user_id'])) {
            return false; // Return false if user is not logged in
        }
        
        if (isset($userid) && isset($itemid)){
            // Verify that the user_id matches the logged-in user
            if ($userid != $_SESSION['user_id']) {
                return false;
            }
            
            // Check if item is already in cart for this user
            $query = "SELECT * FROM cart WHERE user_id = {$userid} AND item_id = {$itemid}";
            $result = $this->db->con->query($query);
            if ($result && mysqli_num_rows($result) > 0) {
                return true; // Item already in cart
            }
            
            $params = array(
                "user_id" => $userid,
                "item_id" => $itemid
            );

            // insert data into cart
            $result = $this->insertIntoCart($params);
            return $result;
        }
        return false;
    }

    // delete cart item using cart item id
    public function deleteCart($item_id = null, $table = 'cart'){
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

    // calculate sub total
    public function getSum($arr){
        if(isset($arr)){
            $sum = 0;
            foreach ($arr as $item){
                $sum += floatval($item[0]);
            }
            return sprintf('%.2f' , $sum);
        }
    }

    // get item_it of shopping cart list
    public function getCartId($cartArray = null, $key = "item_id"){
        if ($cartArray != null){
            $cart_id = array_map(function ($value) use($key){
                return $value[$key];
            }, $cartArray);
            return $cart_id;
        }
    }

    // Save for later
    public function saveForLater($item_id = null, $saveTable = "wishlist", $fromTable = "cart"){
        if ($item_id != null){
            $query = "INSERT INTO {$saveTable} SELECT * FROM {$fromTable} WHERE item_id={$item_id};";
            $query .= "DELETE FROM {$fromTable} WHERE item_id={$item_id};";
            //echo $query;

            // execute multiple query
            $result = $this->db->con->multi_query($query);

            if($result){
                header("Location:" . $_SERVER['PHP_SELF']);
            }
            return $result;
        }
    }
}