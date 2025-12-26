<?php
/**
 * CategoryRepository - Xử lý truy vấn database cho bảng categories
 */
include_once './web/Entity/CategoryEntity.php';

use web\Entity\CategoryEntity;

class CategoryRepository extends ConnectDatabase {

    /**
     * Lấy tất cả danh mục
     */
    public function findAll() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = new CategoryEntity($row);
        }

        return $categories;
    }

    /**
     * Lấy danh mục theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM categories WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CategoryEntity($data) : null;
    }

    /**
     * Lấy danh mục theo tên
     * @param string $name
     * @param int|null $excludeId ID danh mục cần loại trừ (khi update)
     * @return CategoryEntity|null
     */
    public function findByName($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM categories WHERE name = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $name, $excludeId);
        } else {
            $sql = "SELECT * FROM categories WHERE name = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CategoryEntity($data) : null;
    }

    /**
     * Tìm kiếm danh mục
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM categories 
                WHERE name LIKE ? 
                   OR description LIKE ?
                ORDER BY name ASC";
        
        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%{$keyword}%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = new CategoryEntity($row);
        }

        return $categories;
    }

    /**
     * Tạo danh mục mới
     * @param CategoryEntity $category
     * @return bool
     */
    public function create($category) {
        $sql = "INSERT INTO categories (name, description) 
                VALUES (?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", 
            $category->name,
            $category->description
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật danh mục
     * @param CategoryEntity $category
     * @return bool
     */
    public function update($category) {
        $sql = "UPDATE categories 
                SET name = ?, description = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", 
            $category->name,
            $category->description,
            $category->id
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa danh mục
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số danh mục
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM categories";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
}
?>
