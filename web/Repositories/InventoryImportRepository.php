<?php
include_once './web/Entity/InventoryImportEntity.php';
use web\Entity\InventoryImportEntity;

class InventoryImportRepository extends ConnectDatabase {


    public function findAll() {
        $sql = "SELECT ip.*, i.name as ingredient_name, i.unit 
                FROM inventory_imports ip
                JOIN ingredients i ON ip.ingredient_id = i.id
                ORDER BY ip.import_date DESC";
        $result = mysqli_query($this->con, $sql);

        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = new InventoryImportEntity($row);
        }

        return $imports;
    }


    public function findById($id) {
        $sql = "SELECT * FROM inventory_imports WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new InventoryImportEntity($data) : null;
    }


    public function create($import) {
        $sql = "INSERT INTO inventory_imports (ingredient_id, import_quantity, total_cost, import_date, note) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iddss", $import->ingredient_id, $import->import_quantity, $import->total_cost, $import->import_date, $import->note);
        mysqli_stmt_execute($stmt);

        return mysqli_insert_id($this->con);
    }


    public function update($import) {
        $sql = "UPDATE inventory_imports SET ingredient_id = ?, import_quantity = ?, total_cost = ?, import_date = ?, note = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iddssi", $import->ingredient_id, $import->import_quantity, $import->total_cost, $import->import_date, $import->note, $import->id);
        
        return mysqli_stmt_execute($stmt);
    }


    public function delete($id) {
        $sql = "DELETE FROM inventory_imports WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }


    public function search($keyword1,$keyword2) {
        $sql = "SELECT ip.*, i.name as ingredient_name, i.unit 
                FROM inventory_imports ip 
                JOIN ingredients i ON ip.ingredient_id = i.id 
                WHERE i.name LIKE ? and i.import_quantity= ? 
                ORDER BY ip.import_date DESC";
        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%" . $keyword1 . "%";
        $searchTerm1 = $keyword2;
        mysqli_stmt_bind_param($stmt, "sd", $searchTerm,$searchTerm1);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $imports = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $imports[] = new InventoryImportEntity($row);
        }

        return $imports;
    }
    
//    /**
//     * Lấy danh sách nhập kho theo nguyên liệu
//     */
//    public function findByIngredientId($ingredientId) {
//        $sql = "SELECT * FROM inventory_imports WHERE ingredient_id = ? ORDER BY import_date DESC";
//        $stmt = mysqli_prepare($this->con, $sql);
//        mysqli_stmt_bind_param($stmt, "i", $ingredientId);
//        mysqli_stmt_execute($stmt);
//
//        $result = mysqli_stmt_get_result($stmt);
//        $imports = [];
//        while ($row = mysqli_fetch_assoc($result)) {
//            $imports[] = new InventoryImportEntity($row);
//        }
//
//        return $imports;
//    }

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
