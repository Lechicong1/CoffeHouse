<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderItemEntity.php';

use web\Entity\OrderItemEntity;

class OrderItemRepository extends ConnectDatabase {
    
    public function create(OrderItemEntity $item) {
        $sql = "INSERT INTO order_items (order_id, product_id, product_size_id, quantity, price_at_purchase, note) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        
        // Định nghĩa kiểu dữ liệu: order_id(i), product_id(i), product_size_id(i), quantity(i), price(i), note(s)
        $types = "iiiiss";
        
        mysqli_stmt_bind_param($stmt, $types, 
            $item->order_id,
            $item->product_id,
            $item->product_size_id,
            $item->quantity,
            $item->price_at_purchase,
            $item->note
        );

        return mysqli_stmt_execute($stmt);
    }
}
?>
