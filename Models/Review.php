<?php

class Review {
    public $id;
    public $user_id;
    public $product_id;
    public $order_id;
    public $rating; // 1-5 stars
    public $comment;
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
            $this->order_id = $data['order_id'] ?? null;
            $this->rating = $data['rating'] ?? 5;
            $this->comment = $data['comment'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
