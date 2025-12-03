<?php
/**
 * FILE: Payment.php
 * DESCRIPTION: Model quản lý thanh toán
 */

require_once __DIR__ . '/../Config/Database.php';

class Payment {
    private $conn;
    private $table_name = "payments";

    // Properties
    public $id;
    public $order_id;
    public $payment_method; // ENUM: 'cash', 'card', 'momo', 'bank_transfer'
    public $amount;
    public $status; // ENUM: 'pending', 'completed', 'failed', 'refunded'
    public $transaction_code;
    public $paid_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy payment theo order ID
     */
    public function getByOrder($orderId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE order_id = :order_id 
                  ORDER BY paid_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy payment theo ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Tạo payment mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (order_id, payment_method, amount, status, transaction_code, paid_at) 
                  VALUES (:order_id, :payment_method, :amount, :status, :transaction_code, :paid_at)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':payment_method', $this->payment_method);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':transaction_code', $this->transaction_code);
        $stmt->bindParam(':paid_at', $this->paid_at);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật trạng thái payment
     */
    public function updateStatus($paymentId, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $paymentId);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    /**
     * Xác nhận thanh toán
     */
    public function confirmPayment($paymentId, $transactionCode = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'completed', 
                      transaction_code = :transaction_code,
                      paid_at = NOW()
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $paymentId);
        $stmt->bindParam(':transaction_code', $transactionCode);
        
        return $stmt->execute();
    }

    /**
     * Hoàn tiền
     */
    public function refund($paymentId) {
        return $this->updateStatus($paymentId, 'refunded');
    }
}
?>
