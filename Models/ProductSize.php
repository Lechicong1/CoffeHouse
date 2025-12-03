<?php
/**
 * FILE: ProductSize.php
 * DESCRIPTION: Model quản lý kích cỡ sản phẩm và giá theo size
 */

require_once __DIR__ . '/../Config/Database.php';

class ProductSize {
    private $conn;
    private $table_name = "product_sizes";

    // Properties
    public $id;
    public $product_id;
    public $size_name;
    public $price_adjustment;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả size của một sản phẩm
     */
    public function getByProduct($productId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE product_id = :product_id 
                  ORDER BY price_adjustment ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy size theo ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->product_id = $row['product_id'];
            $this->size_name = $row['size_name'];
            $this->price_adjustment = $row['price_adjustment'];
            return $row;
        }
        return false;
    }

    /**
     * Tạo size mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_id, size_name, price_adjustment) 
                  VALUES (:product_id, :size_name, :price_adjustment)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':product_id', $this->product_id);
        $stmt->bindParam(':size_name', $this->size_name);
        $stmt->bindParam(':price_adjustment', $this->price_adjustment);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật size
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET size_name = :size_name,
                      price_adjustment = :price_adjustment
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':size_name', $this->size_name);
        $stmt->bindParam(':price_adjustment', $this->price_adjustment);
        
        return $stmt->execute();
    }

    /**
     * Xóa size
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
