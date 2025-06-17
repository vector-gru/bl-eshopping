<?php
class OrderHelper {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function generateOrderNumber() {
        do {
            // Format: BL-YYYYMMDD-XXXX (BL for BL E-Shopping, date, 4 random alphanumeric)
            $date = date('Ymd');
            $random = $this->generateRandomString(4);
            $orderNumber = "BL-{$date}-{$random}";
            
            // Check if this order number already exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM orders WHERE order_number = ?");
            $stmt->execute([$orderNumber]);
            $exists = $stmt->fetchColumn();
            
        } while ($exists > 0);
        
        return $orderNumber;
    }
    
    private function generateRandomString($length = 4) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $randomString;
    }
}
?> 