<?php
/**
 * FILE: InventoryImportEntity.php
 * DESCRIPTION: Entity InventoryImport - Chứa properties từ bảng inventory_imports
 * TABLE: inventory_imports
 * AUTHOR: Coffee House System
 */
namespace web\Entity;

class InventoryImportEntity
{
    // Properties từ bảng inventory_imports
    public $id;
    public $ingredient_id;
    public $import_quantity;
    public $total_cost;
    public $import_date;
    public $note;

    // Properties từ JOIN với bảng ingredients
    public $ingredient_name;
    public $unit;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ingredient_id = $data['ingredient_id'] ?? null;
            $this->import_quantity = $data['import_quantity'] ?? 0;
            $this->total_cost = $data['total_cost'] ?? 0;
            $this->import_date = $data['import_date'] ?? null;
            $this->note = $data['note'] ?? null;
            $this->ingredient_name = $data['ingredient_name'] ?? null;
            $this->unit = $data['unit'] ?? '';
        }
    }
}
?>
