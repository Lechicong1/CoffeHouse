<?php
include_once './web/Entity/ProductSizeEntity.php';
use web\Entity\ProductSizeEntity;

class ProductSizeRepository extends ConnectDatabase {

    /**
     * Lấy tất cả kích thước sản phẩm
     */
    public function findAll() {
        $sql = "SELECT * FROM product_sizes ORDER BY size_name ASC";
        $result = mysqli_query($this->con, $sql);

        $productSizes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $productSizes[] = new ProductSizeEntity($row);
        }

        return $productSizes;
    }

    /**
     * Lấy kích thước sản phẩm theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM product_sizes WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new ProductSizeEntity($data) : null;
    }

    public function create($productSize) {
        $sql = "INSERT INTO product_sizes (product_id, size_name, price) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $productSize->product_id, $productSize->size_name, $productSize->price);
        mysqli_stmt_execute($stmt);

        return mysqli_insert_id($this->con);
    }

    public function update($productSize) {
        $sql = "UPDATE product_sizes SET product_id = ?, size_name = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "isii", $productSize->product_id, $productSize->size_name, $productSize->price, $productSize->id);
        
        return mysqli_stmt_execute($stmt);
    }
}
?>