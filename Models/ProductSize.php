<?php

class ProductSize {
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
