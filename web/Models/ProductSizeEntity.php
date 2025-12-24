<?php
/**
 * FILE: ProductSizeEntity.php
 * DESCRIPTION: Entity ProductSize - Chứa properties từ bảng product_sizes
 * TABLE: product_sizes
 * AUTHOR: Coffee House System
 */
namespace web\Models;
class ProductSizeEntity {
    // Properties từ bảng product_sizes
    public $id;
    public $product_id;
    public $size_name;
    public $price_adjustment;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->product_id = $data['product_id'] ?? null;
            $this->size_name = $data['size_name'] ?? null;
            $this->price_adjustment = $data['price_adjustment'] ?? 0;
        }
    }
}
?>
