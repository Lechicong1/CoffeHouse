<?php
include_once './Config/ConnectDatabase.php';
include_once './web/Entity/OrderEntity.php';

use web\Entity\OrderEntity;

class OrderRepository extends ConnectDatabase {
    
    public function create(OrderEntity $order) {
        
        $sql = "INSERT INTO orders (order_code, staff_id, customer_id, order_type, status, payment_status, payment_method, total_amount, receiver_name, receiver_phone, shipping_address, note, table_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->con, $sql);

        $types = "siissssssssss";
        
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
            $order->note,
            $order->table_number
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

    /**
     * Tìm đơn hàng theo order_code (kiểm tra trùng)
     */
    public function findByOrderCode($orderCode) {
        $sql = "SELECT * FROM orders WHERE order_code = ? LIMIT 1";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $orderCode);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new OrderEntity($data) : null;
    }

    /**
     * Lấy danh sách đơn hàng với filter và JOIN customer
     * @param array $filters ['status' => 'PROCESSING', 'search' => 'ORD123']
     * @return array
     */
    public function findAllWithFilters($filters = []) {
        $sql = "SELECT o.*, c.full_name as customer_name, c.phone as customer_phone, o.table_number 
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE 1=1";
        
        $params = [];
        $types = "";

        // Filter by status
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = ?";
            $params[] = $filters['status'];
            $types .= "s";
        }
        
        // Filter by order_type (support string or array for IN clause)
        if (!empty($filters['order_type'])) {
            if (is_array($filters['order_type'])) {
                $placeholders = implode(',', array_fill(0, count($filters['order_type']), '?'));
                $sql .= " AND o.order_type IN ($placeholders)";
                foreach ($filters['order_type'] as $ot) {
                    $params[] = $ot;
                    $types .= "s";
                }
            } else {
                $sql .= " AND o.order_type = ?";
                $params[] = $filters['order_type'];
                $types .= "s";
            }
        }

        // Search by order_code or phone
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_code LIKE ? OR c.phone LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }

        $sql .= " ORDER BY o.created_at DESC LIMIT 100";

        $stmt = mysqli_prepare($this->con, $sql);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row; // Return array thay vì Entity để có thêm customer info
        }

        return $orders;
    }

    /**
     * Cập nhật đơn hàng
     */
    public function update(OrderEntity $order) {
        $sql = "UPDATE orders SET 
                order_code = ?, 
                staff_id = ?, 
                customer_id = ?, 
                order_type = ?, 
                status = ?, 
                payment_status = ?, 
                payment_method = ?, 
                total_amount = ?, 
                receiver_name = ?, 
                receiver_phone = ?, 
                shipping_address = ?, 
                note = ?,
                table_number = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        
        mysqli_stmt_bind_param($stmt, "siissssdsssssi", 
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
            $order->note,
            $order->table_number,
            $order->id
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Lấy danh sách đơn hàng theo customer_id
     */
    public function findByCustomerId($customerId) {
        $sql = "SELECT o.*, c.full_name as customer_name, c.phone as customer_phone
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.customer_id = ?
                ORDER BY o.created_at DESC";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = new OrderEntity($row);
        }

        return $orders;
    }

    //Tấn
    public function findReadyForDeliveryOrders() {
        $sql = "SELECT o.*, c.full_name as customer_name, c.phone as customer_phone
                FROM orders o 
                LEFT JOIN customers c ON o.customer_id = c.id 
                WHERE o.status IN ('READY', 'SHIPPING', 'COMPLETED') 
                AND o.order_type = 'ONLINE_DELIVERY'
                ORDER BY o.created_at DESC";
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
     * Lấy tất cả đơn hàng cho Admin (đơn giản)
     * @return array
     */
    public function getAllOrdersForAdmin() {
        $sql = "SELECT order_code, order_type, status, payment_status, total_amount, receiver_name, receiver_phone 
                FROM orders 
                ORDER BY id DESC";
        $result = mysqli_query($this->con, $sql);

        $orders = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
        }
        return $orders;
    }

    /**
     * Tìm kiếm đơn hàng theo keyword (mã đơn, tên, SĐT người nhận)
     * @param string $keyword
     * @return array
     */
    public function searchOrdersForAdmin($keyword) {
        $keyword = '%' . mysqli_real_escape_string($this->con, $keyword) . '%';

        $sql = "SELECT order_code, order_type, status, payment_status, total_amount, receiver_name, receiver_phone 
                FROM orders 
                WHERE order_code LIKE ? OR receiver_name LIKE ? OR receiver_phone LIKE ?
                ORDER BY id DESC";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $keyword, $keyword, $keyword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $orders = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        return $orders;
    }
}
