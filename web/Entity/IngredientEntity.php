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

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->unit = $data['unit'] ?? null;
            $this->stock_quantity = $data['stock_quantity'] ?? 0;
        }
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->unit,
            'stock_quantity' => $this->stock_quantity
        ];
    }
}


?>
