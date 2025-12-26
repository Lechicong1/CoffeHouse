<?php
/**
 * FILE: IngredientEntity.php
 * DESCRIPTION: Entity Ingredient - Chứa properties từ bảng ingredients
 * TABLE: ingredients
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class IngredientEntity
{
    // Properties từ bảng ingredients
    public $id;
    public $name;
    public $unit;
    public $stock_quantity;
    public $updated_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->unit = $data['unit'] ?? null;
            $this->stock_quantity = $data['stock_quantity'] ?? 0;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }
}
?>
