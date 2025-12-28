<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderEntity.php';

use web\Entity\OrderEntity;

class OrderRepository extends ConnectDatabase {
    
    public function create(OrderEntity $order) {
        $sql = "INSERT INTO orders (order_code, staff_id, customer_id, order_type, status, payment_status, payment_method, total_amount, receiver_name, receiver_phone, shipping_address, note) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);
        
        // Định nghĩa kiểu dữ liệu cho các tham số (s: string, i: integer, d: double, b: blob)
        // order_code(s), staff_id(i), customer_id(i), order_type(s), status(s), 
        // payment_status(s), payment_method(s), total_amount(i), receiver_name(s), 
        // receiver_phone(s), shipping_address(s), note(s)
        $types = "siissssissss";
        
        mysqli_stmt_bind_param($stmt, $types, 
            $order->order_code,
            $order->staff_id,
            $order->customer_id,
            $order->order_type,
            $order->status,
            $order->payment_status,
            $order->payment_method,
            $order->total_amount,
            $order->receiver_name,
            $order->receiver_phone,
            $order->shipping_address,
            $order->note
        );

        if (mysqli_stmt_execute($stmt)) {
            return mysqli_insert_id($this->con);
        }
        return false;
    }

    /**
     * Lấy đơn hàng theo ID
     */
    public function findById($id) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new OrderEntity($data) : null;
    }
}
?>
