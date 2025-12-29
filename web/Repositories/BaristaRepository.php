<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderEntity.php';
include_once './Enums/status.enum.php';

use web\Entity\OrderEntity;

class BaristaRepository extends ConnectDatabase {
    
    /**
     * Lấy danh sách đơn hàng cho Barista
     * Status: PENDING, PREPARING, READY
     */
    public function findBaristaOrders() {
        // Lấy các đơn hàng cần pha chế hoặc đã xong (chờ giao)
        $sql = "SELECT * FROM orders WHERE status IN ('" . OrderStatus::PENDING . "', '" . OrderStatus::PREPARING . "', '" . OrderStatus::READY . "') ORDER BY created_at DESC";
        $result = mysqli_query($this->con, $sql);
        
        $orders = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = new OrderEntity($row);
            }
        }
        return $orders;
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * @param int $orderId
     * @param string $status
     */
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
        return mysqli_stmt_execute($stmt);
    }
}
?>
