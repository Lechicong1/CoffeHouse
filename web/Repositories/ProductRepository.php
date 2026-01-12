<?php
include_once './web/Entity/ProductEntity.php';
include_once './web/Entity/ProductSizeEntity.php';
use web\Entity\ProductEntity;
use web\Entity\ProductSizeEntity;

class ProductRepository extends ConnectDatabase {

    /**
     * Lấy tất cả sản phẩm
     */
    public function findAll() {
        $sql = "SELECT * FROM products ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }

    /**
     * Lấy tất cả sản phẩm đang active
     */
    public function findAllActive() {
        $sql = "SELECT * FROM products WHERE is_active = 1 ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);

        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }

    /**
     * Lấy sản phẩm theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new ProductEntity($data) : null;
    }

    public function create($product) {
        $sql = "INSERT INTO products (category_id, name, description, image_url, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "isssi", $product->category_id, $product->name, $product->description, $product->image_url, $product->is_active);
        mysqli_stmt_execute($stmt);

        return mysqli_insert_id($this->con);
    }

    /**
     * Cập nhật thông tin sản phẩm
     * @param ProductEntity $product
     * @return bool
     */
    public function update($product) {
        $sql = "UPDATE products SET category_id = ?, name = ?, description = ?, image_url = ?, is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "isssii", $product->category_id, $product->name, $product->description, $product->image_url, $product->is_active, $product->id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa sản phẩm theo ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Tìm sản phẩm theo tên
     * @param string $name
     * @param int|null $excludeId ID sản phẩm cần loại trừ (khi update)
     * @return ProductEntity|null
     */
    public function findByName($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM products WHERE name = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $name, $excludeId);
        } else {
            $sql = "SELECT * FROM products WHERE name = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new ProductEntity($data) : null;
    }

    /**
     * Tìm sản phẩm theo danh mục
     * @param int $categoryId
     * @return array
     */
    public function findByCategoryId($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = ? ORDER BY name ASC";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }

    /**
     * Lấy sản phẩm active theo danh mục
     */
    public function findActiveByCategoryId($categoryId) {
        $sql = "SELECT * FROM products WHERE category_id = ? AND is_active = 1 ORDER BY name ASC";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }

    /**
     * Tìm kiếm sản phẩm theo tên hoặc mô tả
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM products WHERE name LIKE ? OR description LIKE ? ORDER BY name ASC";
        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%" . $keyword . "%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }

    /**
     * Thêm size cho sản phẩm
     */
    public function addSize($productSize) {
        $sql = "INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "isd", $productSize->product_id, $productSize->size_name, $productSize->price);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy danh sách size của sản phẩm
     */
    public function getSizesByProductId($productId) {
        $sql = "SELECT * FROM product_sizes WHERE product_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $sizes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $sizes[] = new ProductSizeEntity($row);
        }
        return $sizes;
    }

    /**
     * Cập nhật size
     */
    public function updateSize($productSize) {
        $sql = "UPDATE product_sizes SET size_name = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sdi", $productSize->size_name, $productSize->price, $productSize->id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa size theo ID
     */
    public function deleteSize($sizeId) {
        $sql = "DELETE FROM product_sizes WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $sizeId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa tất cả size của sản phẩm
     */
    public function deleteSizesByProductId($productId) {
        $sql = "DELETE FROM product_sizes WHERE product_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        return mysqli_stmt_execute($stmt);
    }


    /**
     * Lấy sản phẩm liên quan (cùng danh mục, loại trừ sản phẩm hiện tại)
     */
    public function findRelatedProducts($categoryId, $excludeId, $limit = 4) {
        $sql = "SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY RAND() LIMIT ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $categoryId, $excludeId, $limit);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = new ProductEntity($row);
        }

        return $products;
    }
}

?>