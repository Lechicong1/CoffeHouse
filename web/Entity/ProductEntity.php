<?php
/**
 * FILE: ProductEntity.php
 * DESCRIPTION: Entity Product - Chứa properties từ bảng products
 * TABLE: products
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class ProductEntity {
    // Properties từ bảng products
    public $id;
    public $category_id;
    public $name;
    public $description;
    public $image_url;
    public $is_active;
    public $created_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->category_id = $data['category_id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
            $this->is_active = $data['is_active'] ?? true;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
