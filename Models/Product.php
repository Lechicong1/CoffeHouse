<?php
/**
 * FILE: Product.php
 * DESCRIPTION: Model quản lý sản phẩm
 */

require_once __DIR__ . '/../Config/Database.php';

class Product {
    private $conn;
    private $table_name = "products";

    // Properties
    public $id;
    public $category_id;
    public $name;
    public $description;
    public $base_price;
    public $image_url;
    public $is_active;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả sản phẩm
     */
    public function getAll() {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.is_active = 1
                  ORDER BY p.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function getById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->category_id = $row['category_id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->base_price = $row['base_price'];
            $this->image_url = $row['image_url'];
            $this->is_active = $row['is_active'];
            return $row;
        }
        return false;
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getByCategory($categoryId) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE category_id = :category_id AND is_active = 1
                  ORDER BY name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Tìm kiếm sản phẩm
     */
    public function search($keyword) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p
                  LEFT JOIN categories c ON p.category_id = c.id
                  WHERE p.is_active = 1 
                  AND (p.name LIKE :keyword OR p.description LIKE :keyword)
                  ORDER BY p.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Tạo sản phẩm mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (category_id, name, description, base_price, image_url, is_active) 
                  VALUES (:category_id, :name, :description, :base_price, :image_url, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':base_price', $this->base_price);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':is_active', $this->is_active);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET category_id = :category_id,
                      name = :name,
                      description = :description,
                      base_price = :base_price,
                      image_url = :image_url,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':base_price', $this->base_price);
        $stmt->bindParam(':image_url', $this->image_url);
        $stmt->bindParam(':is_active', $this->is_active);
        
        return $stmt->execute();
    }

    /**
     * Xóa sản phẩm (soft delete)
     */
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET is_active = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
