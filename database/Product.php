<?php

namespace database;

// Used to fetch product data
class Product
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return null;
        $this->db = $db;
    }

    // fetch product data using getData Method
    public function getData($table = 'product', $user_id = null){
        if ($table == 'cart' || $table == 'wishlist') {
            if (!isset($_SESSION['user_id'])) {
                return array(); // Return empty array if user is not logged in
            }
            $user_id = $_SESSION['user_id'];
            $result = $this->db->con->query("SELECT * FROM {$table} WHERE user_id = {$user_id}");
        } else {
            // For products, only get active ones
            $result = $this->db->con->query("SELECT p.*, 
                (SELECT COUNT(*) FROM product_images WHERE item_id = p.item_id) as image_count,
                (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$table} p 
                WHERE p.is_active = 1 
                ORDER BY p.item_register DESC");
        }

        $resultArray = array();

        // fetch product data one by one
        while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $resultArray[] = $item;
        }

        return $resultArray;
    }

    // get product using item id
    public function getProduct($item_id = null, $table = 'product'){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT p.*, 
                (SELECT COUNT(*) FROM product_images WHERE item_id = p.item_id) as image_count,
                (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
                FROM {$table} p 
                WHERE p.item_id = {$item_id}");

            $resultArray = array();

            // fetch product data one by one
            while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $resultArray[] = $item;
            }

            return $resultArray;
        }
    }

    // Get all product images
    public function getProductImages($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT * FROM product_images WHERE item_id = {$item_id} ORDER BY is_primary DESC, sort_order ASC");
            
            $images = array();
            while ($image = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $images[] = $image;
            }
            
            return $images;
        }
        return array();
    }

    // Get primary product image
    public function getPrimaryImage($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT image_path FROM product_images 
                WHERE item_id = {$item_id} AND is_primary = 1 
                LIMIT 1");
            
            if ($image = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                return $image['image_path'];
            }
        }
        return null;
    }

    // Calculate savings amount
    public function getSavingsAmount($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT old_price, item_price FROM product WHERE item_id = {$item_id}");
            if ($product = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                if ($product['old_price'] && $product['old_price'] > $product['item_price']) {
                    return $product['old_price'] - $product['item_price'];
                }
            }
        }
        return 0;
    }

    // Calculate savings percentage
    public function getSavingsPercentage($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT old_price, item_price FROM product WHERE item_id = {$item_id}");
            if ($product = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                if ($product['old_price'] && $product['old_price'] > $product['item_price']) {
                    return round((($product['old_price'] - $product['item_price']) / $product['old_price']) * 100, 0);
                }
            }
        }
        return 0;
    }

    // Format price with currency
    public function formatPrice($price, $currency){
        $formatted_price = number_format($price, 2);
        return $currency === 'USD' ? '$' . $formatted_price : $formatted_price . ' XAF';
    }

    // Check if product is in stock
    public function isInStock($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT stock_quantity FROM product WHERE item_id = {$item_id}");
            if ($product = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                return $product['stock_quantity'] > 0;
            }
        }
        return false;
    }

    // Get stock quantity
    public function getStockQuantity($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT stock_quantity FROM product WHERE item_id = {$item_id}");
            if ($product = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                return (int)$product['stock_quantity'];
            }
        }
        return 0;
    }

    // Update stock quantity
    public function updateStock($item_id, $quantity){
        if (isset($item_id) && isset($quantity)){
            $current_stock = $this->getStockQuantity($item_id);
            $new_stock = max(0, $current_stock + $quantity); // Prevent negative stock
            
            $this->db->con->query("UPDATE product SET stock_quantity = {$new_stock} WHERE item_id = {$item_id}");
            return true;
        }
        return false;
    }
}