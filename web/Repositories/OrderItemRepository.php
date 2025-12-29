<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderItemEntity.php';

use web\Entity\OrderItemEntity;

class OrderItemRepository extends ConnectDatabase {
    
    public function create(OrderItemEntity $item) {
        $sql = "INSERT INTO order_items (order_id, product_size_id, quantity, price_at_purchase, note) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        
        // Định nghĩa kiểu dữ liệu: order_id(i), product_size_id(i), quantity(i), price(d), note(s)
        $types = "iiiis";

        mysqli_stmt_bind_param($stmt, $types, 
            $item->order_id,
            $item->product_size_id,
            $item->quantity,
            $item->price_at_purchase,
            $item->note
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy danh sách items của đơn hàng với thông tin sản phẩm
     * @param int $orderId
     * @return array
     */
    public function findByOrderId($orderId) {
        $sql = "SELECT 
                    oi.*, 
                    ps.size_name, 
                    p.name as product_name,
                    p.image as product_image
                FROM order_items oi
                INNER JOIN product_sizes ps ON oi.product_size_id = ps.id
                INNER JOIN products p ON ps.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id ASC";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
        
        return $items;
    }
}
?>
