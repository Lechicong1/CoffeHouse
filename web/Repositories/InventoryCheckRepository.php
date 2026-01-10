<?php
/**
 * InventoryCheckRepository - Xử lý truy vấn database cho bảng inventory_checks
 */
include_once './web/Entity/InventoryCheckEntity.php';

class InventoryCheckRepository extends ConnectDatabase {

    /**
     * Lấy danh sách nguyên liệu kèm thông tin kiểm kho trong ngày
     * @param string $date Ngày kiểm tra (format: Y-m-d)
     * @return array Mảng chứa thông tin nguyên liệu và kiểm kho (nếu có)
     */
    public function getInventoryCheckByDate($date) {
        error_log("=== DEBUG getInventoryCheckByDate ===");
        error_log("Date param: " . $date);

        $sql = "SELECT 
                    i.id AS i_id, 
                    i.name AS i_name, 
                    i.unit AS i_unit, 
                    i.stock_quantity AS i_stock, 
                    c.id AS c_id, 
                    c.theoryQuantity, 
                    c.actualQuantity, 
                    c.difference, 
                    c.note, 
                    c.checked_at,
                    DATE(c.checked_at) AS check_date
                FROM ingredients i 
                LEFT JOIN inventory_checks c 
                    ON i.name = c.ingredient AND DATE(c.checked_at) = ? 
                ORDER BY i.name";
        
        error_log("SQL: " . $sql);

        $stmt = mysqli_prepare($this->con, $sql);

        if (!$stmt) {
            error_log("ERROR PREPARE: " . mysqli_error($this->con));
            return [];
        }

        mysqli_stmt_bind_param($stmt, "s", $date);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            error_log("ERROR GET RESULT: " . mysqli_error($this->con));
            return [];
        }

        $data = [];
        $rowCount = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            $rowCount++;
            error_log("Row $rowCount: ingredient={$row['i_name']}, c_id={$row['c_id']}, check_date={$row['check_date']}");
            // Xây dựng đối tượng ingredient (luôn có)
            $ingredient = [
                'id' => $row['i_id'],
                'name' => $row['i_name'],
                'unit' => $row['i_unit'],
                'stock_quantity' => $row['i_stock']
            ];
            
            // Xây dựng đối tượng check (có thể null nếu chưa kiểm kho)
            $check = null;
            // Kiểm tra c_id: phải khác null VÀ không phải empty string
            if (!empty($row['c_id']) && $row['c_id'] !== null) {
                $check = [
                    'id' => $row['c_id'],
                    'ingredient' => $row['i_name'],
                    'theoryQuantity' => $row['theoryQuantity'],
                    'actualQuantity' => $row['actualQuantity'],
                    'difference' => $row['difference'],
                    'note' => $row['note'],
                    'checked_at' => $row['checked_at']
                ];
                error_log("  -> Has check: actualQty={$row['actualQuantity']}, diff={$row['difference']}");
            } else {
                error_log("  -> No check (c_id is NULL or empty)");
            }
            
            // Kết hợp ingredient và check
            $data[] = [
                'ingredient' => $ingredient,
                'check' => $check
            ];
        }
        
        error_log("Total rows: $rowCount, Data count: " . count($data));

        return $data;
    }

    /**
     * Kiểm tra xem nguyên liệu đã được kiểm kho trong ngày chưa
     * @param string $ingredientName Tên nguyên liệu
     * @return bool True nếu đã có, False nếu chưa
     */
    public function checkExistsTodayByIngredient($ingredientName) {
        $sql = "SELECT id FROM inventory_checks 
                WHERE ingredient = ? AND DATE(checked_at) = CURDATE()";

        $stmt = mysqli_prepare($this->con, $sql);

        if (!$stmt) {
            error_log("Prepare checkExistsTodayByIngredient failed: " . mysqli_error($this->con));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "s", $ingredientName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        return mysqli_num_rows($result) > 0;
    }

    /**
     * Lưu/Cập nhật thông tin kiểm kho
     * @param array $data
     * @return bool
     */
    public function saveInventoryCheck($data) {
        error_log("=== REPOSITORY saveInventoryCheck ===");
        error_log("Data received: " . json_encode($data));
        
        // Kiểm tra xem đã có kiểm kho cho nguyên liệu này trong ngày chưa
        $checkSql = "SELECT id FROM inventory_checks 
                     WHERE ingredient = ? AND DATE(checked_at) = CURDATE()";
        $stmt = mysqli_prepare($this->con, $checkSql);
        
        if (!$stmt) {
            error_log("Prepare check failed: " . mysqli_error($this->con));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "s", $data['ingredient']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Đã tồn tại -> UPDATE
            $row = mysqli_fetch_assoc($result);
            error_log("Record exists, updating ID: " . $row['id']);
            
            $updateSql = "UPDATE inventory_checks 
                         SET theoryQuantity = ?, 
                             actualQuantity = ?, 
                             difference = ?,
                             note = ?,
                             checked_at = CURRENT_TIMESTAMP
                         WHERE id = ?";
            $stmt = mysqli_prepare($this->con, $updateSql);
            
            if (!$stmt) {
                error_log("Prepare update failed: " . mysqli_error($this->con));
                return false;
            }
            
            // Lưu status vào note
            $note = $data['status'] ?? 'OK';
            mysqli_stmt_bind_param(
                $stmt, 
                "dddsi",
                $data['theoryQuantity'],
                $data['actualQuantity'], 
                $data['difference'],
                $note,
                $row['id']
            );
        } else {
            // Chưa tồn tại -> INSERT
            error_log("No record found, inserting new");
            
            $insertSql = "INSERT INTO inventory_checks 
                         (ingredient, theoryQuantity, actualQuantity, difference, note) 
                         VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->con, $insertSql);
            
            if (!$stmt) {
                error_log("Prepare insert failed: " . mysqli_error($this->con));
                return false;
            }
            
            // Lưu status vào note
            $note = $data['status'] ?? 'OK';
            mysqli_stmt_bind_param(
                $stmt, 
                "sddds",
                $data['ingredient'],
                $data['theoryQuantity'], 
                $data['actualQuantity'], 
                $data['difference'],
                $note
            );
        }
        
        $executeResult = mysqli_stmt_execute($stmt);
        
        if (!$executeResult) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
        } else {
            error_log("Execute SUCCESS! Affected rows: " . mysqli_stmt_affected_rows($stmt));
        }
        
        return $executeResult;
    }

    /**
     * Lưu mới một bản ghi kiểm kho
     * @param array $data
     * @return bool|int
     */
    public function create($data) {
        $sql = "INSERT INTO inventory_checks 
                (ingredient, theoryQuantity, actualQuantity, difference, note) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param(
            $stmt, 
            "sddds", 
            $data['ingredient'], 
            $data['theoryQuantity'], 
            $data['actualQuantity'], 
            $data['difference'], 
            $data['note']
        );
        
        if (mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->con);
        }
        
        return false;
    }

    /**
     * Cập nhật thông tin kiểm kho
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $sql = "UPDATE inventory_checks 
                SET ingredient = ?, 
                    theoryQuantity = ?, 
                    actualQuantity = ?, 
                    difference = ?, 
                    note = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param(
            $stmt, 
            "sdddsi", 
            $data['ingredient'], 
            $data['theoryQuantity'], 
            $data['actualQuantity'], 
            $data['difference'], 
            $data['note'],
            $id
        );
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa bản ghi kiểm kho
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM inventory_checks WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy báo cáo thất thoát theo tháng (tất cả tháng)
     * @return array
     */
    public function getInventoryCheckByMonth() {
        $sql = "SELECT 
                    ingredient, 
                    MONTH(checked_at) AS month, 
                    SUM(theoryQuantity) AS totalTheory, 
                    SUM(actualQuantity) AS totalActual, 
                    SUM(difference) AS totalDifference 
                FROM inventory_checks 
                GROUP BY ingredient, MONTH(checked_at) 
                ORDER BY MONTH(checked_at) DESC, ingredient";

        $result = mysqli_query($this->con, $sql);
        $data = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $entity = new InventoryCheckEntity();
                $entity->setIngredient($row['ingredient']);
                $entity->setMonth($row['month']);
                $entity->setTheoryQuantity($row['totalTheory']);
                $entity->setActualQuantity($row['totalActual']);
                $entity->setDifference($row['totalDifference']);
                $data[] = $entity;
            }
        }

        return $data;
    }

    /**
     * Lấy báo cáo thất thoát theo tháng cụ thể
     * @param int $month Tháng cần lọc (1-12)
     * @return array
     */
    public function getInventoryCheckBySpecificMonth($month) {
        $sql = "SELECT 
                    ingredient, 
                    MONTH(checked_at) AS month, 
                    SUM(theoryQuantity) AS totalTheory, 
                    SUM(actualQuantity) AS totalActual, 
                    SUM(difference) AS totalDifference 
                FROM inventory_checks 
                WHERE MONTH(checked_at) = ? 
                GROUP BY ingredient, MONTH(checked_at) 
                ORDER BY ingredient";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $month);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $entity = new InventoryCheckEntity();
            $entity->setIngredient($row['ingredient']);
            $entity->setMonth($row['month']);
            $entity->setTheoryQuantity($row['totalTheory']);
            $entity->setActualQuantity($row['totalActual']);
            $entity->setDifference($row['totalDifference']);
            $data[] = $entity;
        }

        return $data;
    }
}
