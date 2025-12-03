<?php
/**
 * FILE: Order.php
 * DESCRIPTION: Model quản lý đơn hàng
 */

require_once __DIR__ . '/../Config/Database.php';

class Order {
    private $conn;
    private $table_name = "orders";

    // Properties
    public $id;
    public $user_id;
    public $staff_id;
    public $shipper_id;
    public $promotion_id;
    public $order_type; // ENUM: 'dine_in', 'takeaway', 'delivery'
    public $status; // ENUM: 'pending', 'confirmed', 'preparing', 'ready', 'delivering', 'completed', 'cancelled'
    public $payment_status; // ENUM: 'unpaid', 'paid', 'refunded'
    public $total_amount;
    public $shipping_address;
    public $note;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả đơn hàng
     */
    public function getAll() {
        $query = "SELECT o.*, u.username as customer_name 
                  FROM " . $this->table_name . " o
                  LEFT JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy đơn hàng theo ID
     */
    public function getById($id) {
        $query = "SELECT o.*, 
                         u.username as customer_name,
                         s.username as staff_name,
                         sh.username as shipper_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN users u ON o.user_id = u.id
                  LEFT JOIN users s ON o.staff_id = s.id
                  LEFT JOIN users sh ON o.shipper_id = sh.id
                  WHERE o.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Lấy đơn hàng theo user
     */
    public function getByUser($userId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy đơn hàng theo shipper
     */
    public function getByShipper($shipperId) {
        $query = "SELECT o.*, u.username as customer_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN users u ON o.user_id = u.id
                  WHERE o.shipper_id = :shipper_id 
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shipper_id', $shipperId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy đơn hàng theo trạng thái
     */
    public function getByStatus($status) {
        $query = "SELECT o.*, u.username as customer_name
                  FROM " . $this->table_name . " o
                  LEFT JOIN users u ON o.user_id = u.id
                  WHERE o.status = :status 
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Tạo đơn hàng mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, staff_id, shipper_id, promotion_id, order_type, status, 
                   payment_status, total_amount, shipping_address, note, created_at) 
                  VALUES (:user_id, :staff_id, :shipper_id, :promotion_id, :order_type, :status,
                          :payment_status, :total_amount, :shipping_address, :note, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':staff_id', $this->staff_id);
        $stmt->bindParam(':shipper_id', $this->shipper_id);
        $stmt->bindParam(':promotion_id', $this->promotion_id);
        $stmt->bindParam(':order_type', $this->order_type);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':shipping_address', $this->shipping_address);
        $stmt->bindParam(':note', $this->note);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật đơn hàng
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET staff_id = :staff_id,
                      shipper_id = :shipper_id,
                      status = :status,
                      payment_status = :payment_status,
                      total_amount = :total_amount,
                      shipping_address = :shipping_address,
                      note = :note,
                      updated_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':staff_id', $this->staff_id);
        $stmt->bindParam(':shipper_id', $this->shipper_id);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':shipping_address', $this->shipping_address);
        $stmt->bindParam(':note', $this->note);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($orderId, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    /**
     * Gán shipper cho đơn hàng
     */
    public function assignShipper($orderId, $shipperId) {
        $query = "UPDATE " . $this->table_name . " 
                  SET shipper_id = :shipper_id, updated_at = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->bindParam(':shipper_id', $shipperId);
        
        return $stmt->execute();
    }

    /**
     * Hủy đơn hàng
     */
    public function cancel($orderId) {
        return $this->updateStatus($orderId, 'cancelled');
    }
}
?>
