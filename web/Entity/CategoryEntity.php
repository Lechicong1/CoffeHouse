<?php
/**
 * FILE: CategoryEntity.php
 * DESCRIPTION: Entity Category - Chứa properties từ bảng categories
 * TABLE: categories
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class CategoryEntity {
    // Properties từ bảng categories
    public $id;
    public $name;
    public $description;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->description = $data['description'] ?? null;
        }
    }

    /**
     * Chuyển entity thành array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
?>
