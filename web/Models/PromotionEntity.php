<?php
/**
 * FILE: PromotionEntity.php
 * DESCRIPTION: Entity Promotion - Chứa properties từ bảng promotions
 * TABLE: promotions
 * AUTHOR: Coffee House System
 */
namespace web\Models;
class PromotionEntity {
    // Properties từ bảng promotions
    public $id;
    public $code;
    public $name;
    public $type; // coupon, voucher, auto_apply
    public $discount_type; // percentage, fixed_amount
    public $discount_value;
    public $min_order_value;
    public $max_discount_amount;
    public $usage_limit; // Tổng số mã được phát hành
    public $used_count; // Số mã đã được sử dụng
    public $user_limit; // Số lần 1 user được dùng mã này
    public $start_at;
    public $end_at;
    public $scope; // all, specific_branch, specific_product
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
            $this->type = $data['type'] ?? null;
            $this->discount_type = $data['discount_type'] ?? 'percentage';
            $this->discount_value = $data['discount_value'] ?? null;
            $this->min_order_value = $data['min_order_value'] ?? 0;
            $this->max_discount_amount = $data['max_discount_amount'] ?? null;
            $this->usage_limit = $data['usage_limit'] ?? null;
            $this->used_count = $data['used_count'] ?? 0;
            $this->user_limit = $data['user_limit'] ?? 1;
            $this->start_at = $data['start_at'] ?? null;
            $this->end_at = $data['end_at'] ?? null;
            $this->scope = $data['scope'] ?? 'all';
            $this->is_active = $data['is_active'] ?? true;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
