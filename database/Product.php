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
            
            if ($table == 'cart') {
                // Join cart with product data to get complete information
                $result = $this->db->con->query("
                    SELECT c.*, p.item_name, p.item_price, p.old_price, p.item_brand, p.currency,
                        (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
                    FROM cart c 
                    JOIN product p ON c.item_id = p.item_id 
                    WHERE c.user_id = {$user_id}
                ");
            } else {
                // For wishlist, just get the basic data
                $result = $this->db->con->query("SELECT * FROM {$table} WHERE user_id = {$user_id}");
            }
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

    // Get products by category
    public function getProductsByCategory($category, $limit = null){
        $category_column = '';
        switch($category) {
            case 'top_sale':
                $category_column = 'is_top_sale';
                break;
            case 'special_price':
                $category_column = 'is_special_price';
                break;
            case 'new_arrival':
                $category_column = 'is_new_arrival';
                break;
            default:
                return array();
        }
        
        $limit_clause = $limit ? "LIMIT {$limit}" : "";
        
        $result = $this->db->con->query("SELECT p.*, 
            (SELECT COUNT(*) FROM product_images WHERE item_id = p.item_id) as image_count,
            (SELECT image_path FROM product_images WHERE item_id = p.item_id AND is_primary = 1 LIMIT 1) as primary_image
            FROM product p 
            WHERE p.is_active = 1 AND p.{$category_column} = 1
            ORDER BY p.item_register DESC {$limit_clause}");
        
        $resultArray = array();
        while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $resultArray[] = $item;
        }
        
        return $resultArray;
    }

    // Get product sizes
    public function getProductSizes($item_id){
        if (isset($item_id)){
            $result = $this->db->con->query("SELECT * FROM product_sizes WHERE item_id = {$item_id} ORDER BY sort_order ASC");
            
            $sizes = array();
            while ($size = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $sizes[] = $size;
            }
            
            return $sizes;
        }
        return array();
    }

    // Add product size
    public function addProductSize($item_id, $size_name, $size_value, $sort_order = 0){
        if (isset($item_id) && isset($size_name) && isset($size_value)){
            $size_name = mysqli_real_escape_string($this->db->con, $size_name);
            $size_value = mysqli_real_escape_string($this->db->con, $size_value);
            $sort_order = (int)$sort_order;
            
            $this->db->con->query("INSERT INTO product_sizes (item_id, size_name, size_value, sort_order) 
                VALUES ({$item_id}, '{$size_name}', '{$size_value}', {$sort_order})");
            return true;
        }
        return false;
    }

    // Delete product size
    public function deleteProductSize($size_id){
        if (isset($size_id)){
            $this->db->con->query("DELETE FROM product_sizes WHERE id = {$size_id}");
            return true;
        }
        return false;
    }

    // Delete all product sizes
    public function deleteAllProductSizes($item_id){
        if (isset($item_id)){
            $this->db->con->query("DELETE FROM product_sizes WHERE item_id = {$item_id}");
            return true;
        }
        return false;
    }
}