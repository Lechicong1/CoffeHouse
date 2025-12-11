<?php
/**
 * FILE: OrderItemEntity.php
 * DESCRIPTION: Entity OrderItem - Chứa properties từ bảng order_items
 * TABLE: order_items
 * AUTHOR: Coffee House System
 */
namespace web\Models;
class OrderItemEntity {
    // Properties từ bảng order_items
    public $id;
    public $order_id;
    public $product_id;
    public $size_id;
    public $quantity;
    public $price_at_purchase;
    public $note;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->order_id = $data['order_id'] ?? null;
            $this->product_id = $data['product_id'] ?? null;
            $this->size_id = $data['size_id'] ?? null;
            $this->quantity = $data['quantity'] ?? 1;
            $this->price_at_purchase = $data['price_at_purchase'] ?? null;
            $this->note = $data['note'] ?? null;
        }
    }
}
?>
