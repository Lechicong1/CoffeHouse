<?php

class Category {
    public $id;
    public $name;
    public $description;
    public $image_url;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
        }
    }
}
?>
