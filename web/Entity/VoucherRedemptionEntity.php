<?php
/**
 * FILE: VoucherRedemptionEntity.php
 * DESCRIPTION: Entity VoucherRedemption - Chứa properties từ bảng voucher_redemptions
 * TABLE: voucher_redemptions
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class VoucherRedemptionEntity{
    // Properties từ bảng voucher_redemptions
    public $id;
    public $customer_id;
    public $voucher_id;
    public $order_id;
    public $points_used;
    public $discount_amount;
    public $created_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->customer_id = $data['customer_id'] ?? null;
            $this->voucher_id = $data['voucher_id'] ?? null;
            $this->order_id = $data['order_id'] ?? null;
            $this->points_used = $data['points_used'] ?? 0;
            $this->discount_amount = $data['discount_amount'] ?? 0;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
