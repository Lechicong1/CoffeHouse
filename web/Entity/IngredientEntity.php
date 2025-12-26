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
            'stock_quantity' => $this->stock_quantity
        ];
    }

    /**
     * Lấy trạng thái tồn kho
     * @return string
     */
    public function getStockStatus() {
        if ($this->stock_quantity <= 0) {
            return 'Hết hàng';
        } elseif ($this->stock_quantity < 10) {
            return 'Sắp hết';
        } else {
            return 'Còn hàng';
        }
    }

    /**
     * Lấy class CSS cho trạng thái
     * @return string
     */
    public function getStockStatusClass() {
        if ($this->stock_quantity <= 0) {
            return 'status-out';
        } elseif ($this->stock_quantity < 10) {
            return 'status-low';
        } else {
            return 'status-ok';
        }
    }

    /**
     * Format số lượng với đơn vị
     * @return string
     */
    public function getFormattedQuantity() {
        return number_format($this->stock_quantity, 0, ',', '.') . ' ' . $this->unit;
    }
}
?>
