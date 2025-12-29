<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderEntity.php';
include_once './Enums/status.enum.php';

use web\Entity\OrderEntity;

class ShipperRepository extends ConnectDatabase {
    
    /**
     * Lấy danh sách đơn hàng sẵn sàng giao
     * Status: READY
     * Order Type: ONLINE_DELIVERY (thường chỉ đơn online mới cần ship)
     */
    public function findReadyForDeliveryOrders() {
        $sql = "SELECT * FROM orders WHERE status IN ('" . OrderStatus::READY . "', '" . OrderStatus::SHIPPING . "', '" . OrderStatus::COMPLETED . "') ORDER BY created_at DESC";
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
     * @param string $status (SHIPPING, COMPLETED)
     */
    public function updateStatus($orderId, $status) {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $orderId);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Shipper nhận đơn -> Chuyển sang SHIPPING
     */
    public function startShipping($orderId) {
        return $this->updateStatus($orderId, OrderStatus::SHIPPING);
    }

    /**
     * Shipper giao thành công -> Chuyển sang COMPLETED
     * Đồng thời cập nhật payment_status thành PAID nếu là COD
     */
    public function completeDelivery($orderId) {
        // Cập nhật status = DELIVERED
        // Nếu cần cập nhật payment_status = PAID (với đơn COD), nên xử lý ở Service
        return $this->updateStatus($orderId, OrderStatus::COMPLETED);
    }
}
?>
