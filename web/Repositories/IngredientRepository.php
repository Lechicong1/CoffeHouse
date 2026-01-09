<?php
include_once './web/Entity/IngredientEntity.php';

class IngredientRepository extends ConnectDatabase {

    /**
     * Lấy tất cả nguyên liệu
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM ingredients ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);

        $ingredients = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ingredients[] = new IngredientEntity($row);
        }

        return $ingredients;
    }

    /**
     * Lấy nguyên liệu theo ID
     * @param int $id
     * @return IngredientEntity|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM ingredients WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new IngredientEntity($data) : null;
    }

    /**
     * Lấy nguyên liệu theo tên
     * @param string $name
     * @param int|null $excludeId ID nguyên liệu cần loại trừ (khi update)
     * @return IngredientEntity|null
     */
    public function findByName($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM ingredients WHERE name = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $name, $excludeId);
        } else {
            $sql = "SELECT * FROM ingredients WHERE name = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $name);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new IngredientEntity($data) : null;
    }

    /**
     * Tìm kiếm nguyên liệu
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM ingredients 
                WHERE name LIKE ? 
                OR unit LIKE ?
                ORDER BY name";

        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $ingredients = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ingredients[] = new IngredientEntity($row);
        }

        return $ingredients;
    }

    /**
     * Tạo nguyên liệu mới
     * @param IngredientEntity $ingredient
     * @return bool
     */
    public function create($ingredient) {
        $sql = "INSERT INTO ingredients (name, unit, stock_quantity, expiry_date, is_active) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsi",
            $ingredient->name,
            $ingredient->unit,
            $ingredient->stock_quantity,
            $ingredient->expiry_date,
            $ingredient->is_active
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật nguyên liệu
     * @param IngredientEntity $ingredient
     * @return bool
     */
    public function update($ingredient) {
        $sql = "UPDATE ingredients 
                SET name = ?, unit = ?, stock_quantity = ?, expiry_date = ?, is_active = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ssdsii",
            $ingredient->name,
            $ingredient->unit,
            $ingredient->stock_quantity,
            $ingredient->expiry_date,
            $ingredient->is_active,
            $ingredient->id
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa nguyên liệu
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM ingredients WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số nguyên liệu
     * @return int
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM ingredients";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }
    /**
     * Vô hiệu hóa các nguyên liệu hết hạn
     * @return int Số dòng bị ảnh hưởng
     */
    public function deactivateExpiredIngredients() {
        $sql = "UPDATE ingredients 
                SET is_active = 0 
                WHERE is_active = 1 
                AND expiry_date IS NOT NULL 
                AND expiry_date < CURDATE()";

        mysqli_query($this->con, $sql);
        return mysqli_affected_rows($this->con);
    }

    /**
     * Cập nhật trạng thái kích hoạt cho một nguyên liệu
     * @param int $id
     * @param int $status (0 or 1)
     * @return bool
     */
    public function updateStatus($id, $status) {
        $sql = "UPDATE ingredients SET is_active = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $status, $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy danh sách nguyên liệu đang hoạt động
     * @return array
     */
    public function findActive() {
        $sql = "SELECT * FROM ingredients WHERE is_active = 1 ORDER BY name ASC";
        $result = mysqli_query($this->con, $sql);

        $ingredients = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $ingredients[] = new IngredientEntity($row);
        }

        return $ingredients;
    }

    /**
     * Lấy nguyên liệu theo tên (trả về array thay vì Entity)
     * @param string $name
     * @return array|null
     */
    public function getByName($name) {
        $sql = "SELECT * FROM ingredients WHERE name = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}
?>
