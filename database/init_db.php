<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'eshop';

try {
    // Create connection without database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Database created successfully or already exists\n";
    
    // Connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create users table (must come first due to FK constraints)
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) NOT NULL AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) DEFAULT NULL,
        first_name VARCHAR(100) DEFAULT NULL,
        last_name VARCHAR(100) DEFAULT NULL,
        is_admin BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE (username),
        UNIQUE (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Users table created successfully\n";
    
    // Create product table with old_price field
    $sql = "CREATE TABLE IF NOT EXISTS product (
        item_id INT(11) NOT NULL AUTO_INCREMENT,
        item_brand VARCHAR(255) NOT NULL,
        item_name VARCHAR(255) NOT NULL,
        item_price DECIMAL(10,2) NOT NULL,
        old_price DECIMAL(10,2) DEFAULT NULL,
        item_image VARCHAR(255) DEFAULT NULL,
        currency ENUM('XAF', 'USD') DEFAULT 'XAF',
        item_description TEXT,
        stock_quantity INT(11) DEFAULT 0,
        is_top_sale BOOLEAN DEFAULT FALSE,
        is_special_price BOOLEAN DEFAULT FALSE,
        is_new_arrival BOOLEAN DEFAULT FALSE,
        is_fairly_used BOOLEAN DEFAULT FALSE,
        is_active BOOLEAN DEFAULT TRUE,
        item_register DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Product table created successfully\n";
    
    // Add old_price column if it doesn't exist (for existing databases)
    try {
        $conn->exec("ALTER TABLE product ADD COLUMN old_price DECIMAL(10,2) DEFAULT NULL AFTER item_price");
        echo "Added old_price column to product table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    // Add category columns if they don't exist
    try {
        $conn->exec("ALTER TABLE product ADD COLUMN is_top_sale BOOLEAN DEFAULT FALSE AFTER stock_quantity");
        echo "Added is_top_sale column to product table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    try {
        $conn->exec("ALTER TABLE product ADD COLUMN is_special_price BOOLEAN DEFAULT FALSE AFTER is_top_sale");
        echo "Added is_special_price column to product table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    try {
        $conn->exec("ALTER TABLE product ADD COLUMN is_new_arrival BOOLEAN DEFAULT FALSE AFTER is_special_price");
        echo "Added is_new_arrival column to product table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    try {
        $conn->exec("ALTER TABLE product ADD COLUMN is_fairly_used BOOLEAN DEFAULT FALSE AFTER is_new_arrival");
        echo "Added is_fairly_used column to product table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    // Create product_images table
    $sql = "CREATE TABLE IF NOT EXISTS product_images (
        id INT(11) NOT NULL AUTO_INCREMENT,
        item_id INT(11) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        image_name VARCHAR(255) NOT NULL,
        is_primary BOOLEAN DEFAULT FALSE,
        sort_order INT(11) DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (item_id) REFERENCES product (item_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Product images table created successfully\n";
    
    // Add new columns to product_images if they don't exist
    try {
        $conn->exec("ALTER TABLE product_images ADD COLUMN image_name VARCHAR(255) NOT NULL AFTER image_path");
        echo "Added image_name column to product_images table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    try {
        $conn->exec("ALTER TABLE product_images ADD COLUMN sort_order INT(11) DEFAULT 0 AFTER is_primary");
        echo "Added sort_order column to product_images table\n";
    } catch (PDOException $e) {
        // Column might already exist, ignore error
    }
    
    // Create cart table
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        item_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES product (item_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Cart table created successfully\n";
    
    // Create wishlist table
    $sql = "CREATE TABLE IF NOT EXISTS wishlist (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        item_id INT(11) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES product (item_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Wishlist table created successfully\n";
    
    // Create orders table
    $sql = "CREATE TABLE IF NOT EXISTS orders (
        id INT(11) NOT NULL AUTO_INCREMENT,
        order_number VARCHAR(20) NOT NULL UNIQUE,
        user_id INT(11) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        currency ENUM('XAF','USD') DEFAULT 'XAF',
        status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Orders table created successfully\n";
    
    // Create order_items table
    $sql = "CREATE TABLE IF NOT EXISTS order_items (
        id INT(11) NOT NULL AUTO_INCREMENT,
        order_id INT(11) NOT NULL,
        item_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES product (item_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Order items table created successfully\n";
    
    // Create product_sizes table
    $sql = "CREATE TABLE IF NOT EXISTS product_sizes (
        id INT(11) NOT NULL AUTO_INCREMENT,
        item_id INT(11) NOT NULL,
        size_name VARCHAR(100) NOT NULL,
        size_value VARCHAR(100) NOT NULL,
        sort_order INT(11) DEFAULT 0,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (item_id) REFERENCES product (item_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    $conn->exec($sql);
    echo "Product sizes table created successfully\n";
    
    echo "All tables created successfully!";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>