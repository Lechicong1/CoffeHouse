<?php

class CartItem {
    public $id;
    public $user_id;
    public $product_id;
    public $size_id;
    public $quantity;
    public $created_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->product_id = $data['product_id'] ?? null;
            $this->size_id = $data['size_id'] ?? null;
            $this->quantity = $data['quantity'] ?? 1;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
