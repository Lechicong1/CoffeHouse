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
        // JOIN 2 cấp: Items -> Sizes -> Products
        $sql = "SELECT 
                    oi.id,
                    oi.order_id,
                    oi.product_size_id,
                    oi.quantity,
                    oi.price_at_purchase,
                    oi.note,
                    ps.size_name,
                    
                    -- Lấy tên/ảnh từ bảng products (p) thông qua bảng sizes (ps)
                    p.id as product_id,
                    p.name as product_name,
                    p.image_url as product_image

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

    /**
     * Cập nhật ghi chú cho một order item
     * @param int $itemId
     * @param string $note
     * @return bool
     */
    public function updateItemNote($itemId, $note) {
        $sql = "UPDATE order_items SET note = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $note, $itemId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy thông tin chi tiết một order item
     * @param int $itemId
     * @return array|null
     */
    public function findItemById($itemId) {
        $sql = "SELECT 
                    oi.id,
                    oi.order_id,
                    oi.product_size_id,
                    oi.quantity,
                    oi.price_at_purchase,
                    oi.note,
                    ps.size_name,
                    p.id as product_id,
                    p.name as product_name,
                    p.image_url as product_image
                FROM order_items oi
                INNER JOIN product_sizes ps ON oi.product_size_id = ps.id
                INNER JOIN products p ON ps.product_id = p.id
                WHERE oi.id = ?
                LIMIT 1";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $itemId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}
?>
