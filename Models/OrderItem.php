<?php
/**
 * FILE: OrderItem.php
 * DESCRIPTION: Model quản lý chi tiết đơn hàng (sản phẩm trong đơn)
 */

require_once __DIR__ . '/../Config/Database.php';

class OrderItem {
    private $conn;
    private $table_name = "order_items";

    // Properties
    public $id;
    public $order_id;
    public $product_id;
    public $size_id;
    public $quantity;
    public $price_at_purchase;
    public $note;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả items trong một đơn hàng
     */
    public function getByOrder($orderId) {
        $query = "SELECT oi.*, 
                         p.name as product_name,
                         p.image_url,
                         ps.size_name
                  FROM " . $this->table_name . " oi
                  LEFT JOIN products p ON oi.product_id = p.id
                  LEFT JOIN product_sizes ps ON oi.size_id = ps.id
                  WHERE oi.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Tạo order item mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (order_id, product_id, size_id, quantity, price_at_purchase, note) 
                  VALUES (:order_id, :product_id, :size_id, :quantity, :price_at_purchase, :note)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':size_id', $this->size_id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':price_at_purchase', $this->price_at_purchase);
        $stmt->bindParam(':note', $this->note);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật order item
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantity = :quantity,
                      note = :note
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':note', $this->note);
        
        return $stmt->execute();
    }

    /**
     * Xóa order item
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    /**
     * Xóa tất cả items của một đơn hàng
     */
    public function deleteByOrder($orderId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        return $stmt->execute();
    }
}
?>
