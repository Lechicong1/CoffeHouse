<?php
/**
 * FILE: CartItemEntity.php
 * DESCRIPTION: Entity CartItem - Chứa properties từ bảng cart_items
 * TABLE: cart_items
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class CartItemEntity {
    // Properties từ bảng cart_items
    public $id;
    public $customer_id;
    public $product_size_id;
    public $quantity;

    // Properties bổ sung từ JOIN
    public $product_id;
    public $product_name;
    public $image_url;
    public $size_name;
    public $price;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->customer_id = $data['customer_id'] ?? null;
            $this->product_size_id = $data['product_size_id'] ?? null;
            $this->quantity = $data['quantity'] ?? 1;
            $this->product_id = $data['product_id'] ?? null;
            $this->product_name = $data['product_name'] ?? null;
            $this->image_url = $data['image_url'] ?? null;
            $this->size_name = $data['size_name'] ?? null;
            $this->price = $data['price'] ?? 0;
        }
    }
}
?>
