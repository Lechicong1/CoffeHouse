<?php
/**
 * FILE: Review.php
 * DESCRIPTION: Model quản lý đánh giá sản phẩm
 */

require_once __DIR__ . '/../Config/Database.php';

class Review {
    private $conn;
    private $table_name = "reviews";

    // Properties
    public $id;
    public $user_id;
    public $product_id;
    public $order_id;
    public $rating; // 1-5 stars
    public $comment;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả reviews của một sản phẩm
     */
    public function getByProduct($productId) {
        $query = "SELECT r.*, u.username, u.avatar_url
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.user_id = u.id
                  WHERE r.product_id = :product_id
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy reviews của một user
     */
    public function getByUser($userId) {
        $query = "SELECT r.*, p.name as product_name, p.image_url
                  FROM " . $this->table_name . " r
                  LEFT JOIN products p ON r.product_id = p.id
                  WHERE r.user_id = :user_id
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy review theo order và product
     */
    public function getByOrderProduct($orderId, $productId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE order_id = :order_id AND product_id = :product_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Tính điểm trung bình của sản phẩm
     */
    public function getAverageRating($productId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews
                  FROM " . $this->table_name . " 
                  WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return ['avg_rating' => 0, 'total_reviews' => 0];
    }

    /**
     * Tạo review mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, product_id, order_id, rating, comment, created_at) 
                  VALUES (:user_id, :product_id, :order_id, :rating, :comment, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':order_id', $this->order_id);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật review
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET rating = :rating,
                      comment = :comment
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':rating', $this->rating);
        $stmt->bindParam(':comment', $this->comment);
        
        return $stmt->execute();
    }

    /**
     * Xóa review
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
