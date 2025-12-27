<?php
/**
 * FILE: VoucherRedemptionEntity.php
 * MÔ TẢ: Entity `VoucherRedemption` - chứa thuộc tính tương ứng bảng `voucher_redemptions`
 * BẢNG: voucher_redemptions
 * TÁC GIẢ: Hệ thống Coffee House
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
    public $created_at; // DateTime object or null


    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->customer_id = isset($data['customer_id']) ? (int)$data['customer_id'] : null;
            $this->voucher_id = isset($data['voucher_id']) ? (int)$data['voucher_id'] : null;
            $this->order_id = isset($data['order_id']) ? (int)$data['order_id'] : null;
            $this->points_used = isset($data['points_used']) ? (int)$data['points_used'] : 0;
            $this->discount_amount = isset($data['discount_amount']) ? (float)$data['discount_amount'] : 0.0;
            $this->created_at = null;
            if (!empty($data['created_at'])) {
                $this->created_at = new \DateTime($data['created_at']);
            }
        }
    }

    /**
     * Tạo entity từ hàng DB (alias)
     */
    public static function fromRow(array $row) {
        return new self($row);
    }

    /**
     * Chuyển sang mảng phù hợp để trả JSON hoặc lưu/hiển thị
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'voucher_id' => $this->voucher_id,
            'order_id' => $this->order_id,
            'points_used' => $this->points_used,
            'discount_amount' => $this->discount_amount,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
?>
