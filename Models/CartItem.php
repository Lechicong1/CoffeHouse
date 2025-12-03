<?php
/**
 * FILE: CartItem.php
 * DESCRIPTION: Model quản lý giỏ hàng
 */

require_once __DIR__ . '/../Config/Database.php';

class CartItem {
    private $conn;
    private $table_name = "cart_items";

    // Properties
    public $id;
    public $user_id;
    public $product_id;
    public $size_id;
    public $quantity;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy giỏ hàng của user
     */
    public function getByUser($userId) {
        $query = "SELECT ci.*, 
                         p.name as product_name,
                         p.base_price,
                         p.image_url,
                         ps.size_name,
                         ps.price_adjustment
                  FROM " . $this->table_name . " ci
                  LEFT JOIN products p ON ci.product_id = p.id
                  LEFT JOIN product_sizes ps ON ci.size_id = ps.id
                  WHERE ci.user_id = :user_id
                  ORDER BY ci.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Kiểm tra sản phẩm đã có trong giỏ hàng chưa
     */
    public function exists($userId, $productId, $sizeId) {
        $query = "SELECT id, quantity FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  AND product_id = :product_id 
                  AND size_id = :size_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':size_id', $sizeId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add() {
        // Kiểm tra sản phẩm đã có trong giỏ chưa
        $existing = $this->exists($this->user_id, $this->product_id, $this->size_id);
        
        if ($existing) {
            // Nếu đã có, tăng số lượng
            $query = "UPDATE " . $this->table_name . " 
                      SET quantity = quantity + :quantity 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $existing['id']);
            $stmt->bindParam(':quantity', $this->quantity);
            
            return $stmt->execute();
        } else {
            // Nếu chưa có, thêm mới
            $query = "INSERT INTO " . $this->table_name . " 
                      (user_id, product_id, size_id, quantity, created_at) 
                      VALUES (:user_id, :product_id, :size_id, :quantity, NOW())";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':product_id', $this->product_id);
            $stmt->bindParam(':size_id', $this->size_id);
            $stmt->bindParam(':quantity', $this->quantity);
            
            return $stmt->execute();
        }
    }

    /**
     * Cập nhật số lượng
     */
    public function updateQuantity($cartItemId, $quantity) {
        if ($quantity <= 0) {
            // Nếu số lượng <= 0, xóa khỏi giỏ hàng
            return $this->deleteById($cartItemId);
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET quantity = :quantity 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $cartItemId);
        $stmt->bindParam(':quantity', $quantity);
        
        return $stmt->execute();
    }

    /**
     * Xóa item khỏi giỏ hàng
     */
    public function deleteById($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Xóa toàn bộ giỏ hàng của user
     */
    public function clearCart($userId) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    /**
     * Đếm số lượng items trong giỏ hàng
     */
    public function countItems($userId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Tính tổng giá trị giỏ hàng
     */
    public function getTotalAmount($userId) {
        $query = "SELECT SUM((p.base_price + COALESCE(ps.price_adjustment, 0)) * ci.quantity) as total
                  FROM " . $this->table_name . " ci
                  LEFT JOIN products p ON ci.product_id = p.id
                  LEFT JOIN product_sizes ps ON ci.size_id = ps.id
                  WHERE ci.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }
}
?>
