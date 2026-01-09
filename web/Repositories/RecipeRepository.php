<?php
include_once './web/Entity/RecipeEntity.php';

class RecipeRepository extends ConnectDatabase
{
    public function getByProductId(int $productId): array
    {
        $sql = "SELECT r.*, 
                       i.name as ingredient_name, 
                       i.unit as ingredient_unit
                FROM recipes r
                LEFT JOIN ingredients i ON r.ingredient_id = i.id
                WHERE r.product_id = ?
                ORDER BY r.id ASC";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            return [];
        }
        
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $recipes = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $recipes[] = $this->mapRowToEntity($row);
        }

        mysqli_stmt_close($stmt);
        return $recipes;
    }

    public function findById(int $id): ?RecipeEntity
    {
        $sql = "SELECT r.*, 
                       i.name as ingredient_name, 
                       i.unit as ingredient_unit
                FROM recipes r
                LEFT JOIN ingredients i ON r.ingredient_id = i.id
                WHERE r.id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);
        return $row ? $this->mapRowToEntity($row) : null;
    }

    public function create(RecipeEntity $recipe): int{
        $sql = "INSERT INTO recipes (product_id, ingredient_id, base_amount) 
                VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            error_log("RecipeRepository.create() - Prepare failed: " . mysqli_error($this->con));
            return -1;
        }
        
        mysqli_stmt_bind_param($stmt, "iid", 
            $recipe->productId,
            $recipe->ingredientId,
            $recipe->baseAmount
        );

        $success = mysqli_stmt_execute($stmt);
        
        if (!$success) {
            error_log("RecipeRepository.create() - Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        $affectedRows = $success ? mysqli_stmt_affected_rows($stmt) : -1;
    
        if ($success) {
            $recipe->id = mysqli_insert_id($this->con);
        }
        
        mysqli_stmt_close($stmt);
        return $affectedRows;
    }

    public function update(RecipeEntity $recipe): bool
    {
        if (!$recipe->id) {
            return false;
        }

        $sql = "UPDATE recipes 
                SET product_id = ?, ingredient_id = ?, base_amount = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "iidi",
            $recipe->productId,
            $recipe->ingredientId,
            $recipe->baseAmount,
            $recipe->id
        );

        $success = mysqli_stmt_execute($stmt);
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        
        mysqli_stmt_close($stmt);
        return $success && $affectedRows > 0;
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM recipes WHERE id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        $affectedRows = mysqli_stmt_affected_rows($stmt);
        
        mysqli_stmt_close($stmt);
        return $success && $affectedRows > 0;
    }

    public function exists(int $productId, int $ingredientId, int $excludeId = 0): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM recipes 
                WHERE product_id = ? AND ingredient_id = ? AND id != ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "iii", $productId, $ingredientId, $excludeId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        mysqli_stmt_close($stmt);
        return ($row['cnt'] ?? 0) > 0;
    }

    public function saveRecipeForProduct(int $productId, array $recipes): bool
    {
        mysqli_begin_transaction($this->con);
        
        try {
            $deleted = $this->deleteByProductId($productId);
            error_log("RecipeRepository.saveRecipeForProduct - Delete result: " . ($deleted ? 'true' : 'false'));
            
            foreach ($recipes as $recipe) {
                $recipe->productId = $productId;
                $result = $this->create($recipe);
                error_log("RecipeRepository.saveRecipeForProduct - Create result: $result for ingredient_id: " . $recipe->ingredientId);
                if ($result <= 0) {
                    throw new Exception("Failed to create recipe for ingredient " . $recipe->ingredientId);
                }
            }
            
            mysqli_commit($this->con);
            return true;
            
        } catch (Exception $e) {
            error_log("RecipeRepository.saveRecipeForProduct - Error: " . $e->getMessage());
            mysqli_rollback($this->con);
            return false;
        }
    }

    private function mapRowToEntity(array $row): RecipeEntity
    {
        return new RecipeEntity($row);
    }
}
?>
