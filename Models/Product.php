<?php

class Product {
    public $id;
    public $category_id;
    public $name;
    public $description;
    public $base_price;
    public $image_url;
    public $is_active;


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
            $this->base_price = $data['base_price'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
            $this->is_active = $data['is_active'] ?? 1;
        }
    }
}
?>
