<?php
/**
 * FILE: IngredientEntity.php
 * DESCRIPTION: Entity Ingredient - Chứa properties từ bảng ingredients
 * TABLE: ingredients
 * AUTHOR: Coffee House System
 */
class IngredientEntity
{
    // Properties từ bảng ingredients
    public $id;
    public $name;
    public $unit;
    public $stock_quantity;
    public $expiry_date; // Hạn sử dụng
    public $is_active;   // Trạng thái: 1=Active, 0=Inactive

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
            $this->expiry_date = $data['expiry_date'] ?? null;
            $this->is_active = isset($data['is_active']) ? (int)$data['is_active'] : 1;
        }
    }

    /**
     * Chuyển entity thành array
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'stock_quantity' => $this->stock_quantity,
            'expiry_date' => $this->expiry_date,
            'is_active' => $this->is_active
        ];
    }
}


?>
