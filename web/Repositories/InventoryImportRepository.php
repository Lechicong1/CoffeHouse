<?php
include_once './web/Entity/InventoryImportEntity.php';
use web\Entity\InventoryImportEntity;

class InventoryImportRepository extends ConnectDatabase {

    /**
     * Lấy tất cả phiếu nhập kho
     */
    public function findAll() {
        $sql = "SELECT * FROM inventory_imports ORDER BY import_date DESC";
        $result = mysqli_query($this->con, $sql);

        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = new InventoryImportEntity($row);
        }

        return $imports;
    }

    /**
     * Lấy phiếu nhập theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM inventory_imports WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new InventoryImportEntity($data) : null;
    }

    /**
     * Tạo phiếu nhập mới
     */
    public function create($import) {
        $sql = "INSERT INTO inventory_imports (ingredient_id, import_quantity, total_cost, import_date, note) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iddss", $import->ingredient_id, $import->import_quantity, $import->total_cost, $import->import_date, $import->note);
        mysqli_stmt_execute($stmt);

        return mysqli_insert_id($this->con);
    }

    /**
     * Cập nhật phiếu nhập
     */
    public function update($import) {
        $sql = "UPDATE inventory_imports SET ingredient_id = ?, import_quantity = ?, total_cost = ?, import_date = ?, note = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iddssi", $import->ingredient_id, $import->import_quantity, $import->total_cost, $import->import_date, $import->note, $import->id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa phiếu nhập
     */
    public function delete($id) {
        $sql = "DELETE FROM inventory_imports WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Tìm kiếm phiếu nhập theo tên nguyên liệu hoặc ghi chú
     */
    public function search($keyword) {
        $sql = "SELECT i.* FROM inventory_imports i 
                JOIN ingredients ing ON i.ingredient_id = ing.id 
                WHERE i.note LIKE ? OR ing.name LIKE ? 
                ORDER BY i.import_date DESC";
        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%" . $keyword . "%";
        mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = new InventoryImportEntity($row);
        }

        return $imports;
    }
    
    /**
     * Lấy danh sách nhập kho theo nguyên liệu
     */
    public function findByIngredientId($ingredientId) {
        $sql = "SELECT * FROM inventory_imports WHERE ingredient_id = ? ORDER BY import_date DESC";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $ingredientId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = new InventoryImportEntity($row);
        }

        return $imports;
    }

    /**
     * Lấy phiếu nhập theo khoảng thời gian (cho Report)
     */
    public function findByDateRange($fromDate, $toDate) {
        $sql = "SELECT ii.*, ing.name as ingredient_name, ing.unit 
                FROM inventory_imports ii 
                JOIN ingredients ing ON ii.ingredient_id = ing.id 
                WHERE ii.import_date BETWEEN ? AND ? 
                ORDER BY ii.import_date DESC";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $fromDate, $toDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = $row;
        }

        return $imports;
    }
}
?>
