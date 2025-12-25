<?php
/**
 * Order Controller - Quản lý đơn hàng
 */

require_once __DIR__ . '/../../Config/Controller.php';
require_once __DIR__ . '/../../Config/Database.php';

class OrderController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hiển thị danh sách đơn hàng (GET)
     */
    public function Get_data()
    {
        $status = $_GET['status'] ?? 'all';
        $keyword = $_GET['search'] ?? '';

        try {
            // Lấy danh sách đơn hàng
            if (!empty($keyword)) {
                $orders = $this->searchOrders($keyword);
            } elseif ($status !== 'all') {
                $orders = $this->getOrdersByStatus($status);
            } else {
                $orders = $this->getAllOrders();
            }

            // Lấy thống kê
            $stats = $this->getStatistics();
            
        } catch (Exception $e) {
            $orders = [];
            $stats = ['total' => 0, 'pending' => 0, 'processing' => 0, 'completed' => 0, 'cancelled' => 0];
            $errorMessage = $e->getMessage();
        }

        // Gọi MasterLayout với view con Orders_v
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Orders_v',
            'section' => 'orders',
            'orders' => $orders,
            'stats' => $stats,
            'status' => $status,
            'keyword' => $keyword,
            'successMessage' => $_GET['msg'] ?? null,
            'errorMessage' => $errorMessage ?? null
        ]);
    }

    /**
     * Lấy tất cả đơn hàng
     */
    private function getAllOrders()
    {
        $query = "SELECT o.*, u.fullname as customer_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy đơn hàng theo trạng thái
     */
    private function getOrdersByStatus($status)
    {
        $query = "SELECT o.*, u.fullname as customer_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.status = :status 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Tìm kiếm đơn hàng
     */
    private function searchOrders($keyword)
    {
        $query = "SELECT o.*, u.fullname as customer_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  WHERE o.order_code LIKE :keyword OR u.fullname LIKE :keyword 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy thống kê đơn hàng
     */
    private function getStatistics()
    {
        $stats = ['total' => 0, 'pending' => 0, 'processing' => 0, 'completed' => 0, 'cancelled' => 0];
        
        $query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($results as $row) {
            $stats[$row->status] = $row->count;
            $stats['total'] += $row->count;
        }
        
        return $stats;
    }

    /**
     * Cập nhật trạng thái đơn hàng (POST)
     */
    public function updateStatus()
    {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $query = "UPDATE orders SET status = :status WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['txtId']);
                $stmt->bindParam(':status', $_POST['ddlStatus']);
                $stmt->execute();
                
                header("Location: ?url=Order/Get_data&msg=" . urlencode("✅ Cập nhật trạng thái thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Order/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }

    /**
     * Xóa đơn hàng (POST)
     */
    public function del()
    {
        if (isset($_POST['btnXoa'])) {
            try {
                $query = "DELETE FROM orders WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['txtId']);
                $stmt->execute();
                
                header("Location: ?url=Order/Get_data&msg=" . urlencode("✅ Xóa đơn hàng thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Order/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
}

