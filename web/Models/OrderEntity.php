<?php
/**
 * FILE: OrderEntity.php
 * DESCRIPTION: Entity Order - Chứa properties từ bảng orders
 * TABLE: orders
 * AUTHOR: Coffee House System
 */
namespace web\Models;
class OrderEntity {
    // Properties từ bảng orders
    public $id;
    public $user_id;
    public $staff_id;
    public $shipper_id;
    public $promotion_id;
    public $order_type; // online, at_counter
    public $status; // pending, confirmed, preparing, ready_pickup, shipping, delivered, cancelled
    public $payment_status; // unpaid, paid, refunded
    public $total_amount;
    public $shipping_address;
    public $note;
    public $created_at;
    public $updated_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->staff_id = $data['staff_id'] ?? null;
            $this->shipper_id = $data['shipper_id'] ?? null;
            $this->promotion_id = $data['promotion_id'] ?? null;
            $this->order_type = $data['order_type'] ?? 'online';
            $this->status = $data['status'] ?? 'pending';
            $this->payment_status = $data['payment_status'] ?? 'unpaid';
            $this->total_amount = $data['total_amount'] ?? null;
            $this->shipping_address = $data['shipping_address'] ?? null;
            $this->note = $data['note'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }
}
?>
