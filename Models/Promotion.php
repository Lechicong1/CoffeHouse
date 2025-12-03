<?php

class Promotion {
    public $id;
    public $code;
    public $name;
    public $discount_type; // ENUM: 'percentage', 'fixed_amount'
    public $discount_value;
    public $min_order_value;
    public $max_discount_amount;
    public $usage_limit;
    public $used_count;
    public $user_limit;
    public $start_at;
    public $end_at;
    public $scope; // ENUM: 'all', 'category', 'product'
    public $is_active;
    public $created_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->code = $data['code'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->discount_type = $data['discount_type'] ?? 'percentage';
            $this->discount_value = $data['discount_value'] ?? 0;
            $this->min_order_value = $data['min_order_value'] ?? 0;
            $this->max_discount_amount = $data['max_discount_amount'] ?? null;
            $this->usage_limit = $data['usage_limit'] ?? null;
            $this->used_count = $data['used_count'] ?? 0;
            $this->user_limit = $data['user_limit'] ?? null;
            $this->start_at = $data['start_at'] ?? null;
            $this->end_at = $data['end_at'] ?? null;
            $this->scope = $data['scope'] ?? 'all';
            $this->is_active = $data['is_active'] ?? 1;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
