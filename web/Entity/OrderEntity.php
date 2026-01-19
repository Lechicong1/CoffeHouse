<?php
/**
 * FILE: OrderEntity.php
 * DESCRIPTION: Entity Order - Chứa properties từ bảng orders
 * TABLE: orders
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class OrderEntity {
    // Properties từ bảng orders
    public $id;
    public $order_code;
    public $staff_id;
    public $barista_id;
    public $customer_id;
    public $order_type; // AT_COUNTER, ONLINE_DELIVERY
    public $status; // PENDING, COMPLETED, CANCELLED
    public $payment_status; // UNPAID, PAID, REFUNDED
    public $payment_method; // CASH, BANKING, COD
    public $total_amount;
    public $shipping_address;
    public $receiver_name;
    public $receiver_phone;
    public $shipping_fee;
    public $note;
    public $table_number;
    public $created_at;
    public $updated_at;
    
    public $customer_name;
    public $customer_phone;
    // Thêm thuộc tính để tránh lỗi dynamic property PHP 8.2+
    public $items = [];

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->order_code = $data['order_code'] ?? null;
            $this->staff_id = $data['staff_id'] ?? null;
            $this->barista_id = $data['barista_id'] ?? null;
            $this->customer_id = $data['customer_id'] ?? null;
            $this->order_type = $data['order_type'] ?? 'AT_COUNTER';
            $this->status = $data['status'] ?? 'PENDING';
            $this->payment_status = $data['payment_status'] ?? 'UNPAID';
            $this->payment_method = $data['payment_method'] ?? 'CASH';
            $this->total_amount = $data['total_amount'] ?? 0;
            $this->shipping_address = $data['shipping_address'] ?? null;
            $this->receiver_name = $data['receiver_name'] ?? null;
            $this->receiver_phone = $data['receiver_phone'] ?? null;
            $this->shipping_fee = $data['shipping_fee'] ?? 0;
            $this->note = $data['note'] ?? null;
            $this->table_number = $data['table_number'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
            $this->customer_name = $data['customer_name'] ?? null;
            $this->customer_phone = $data['customer_phone'] ?? null;
        }
    }
}
?>
