<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Controllers/AdminController.php
 * Admin Controller - Xử lý logic cho Admin Dashboard
 */

require_once __DIR__ . '/../../Config/Controller.php';
require_once __DIR__ . '/../../Config/Database.php';

class AdminController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hiển thị dashboard chính
     */
    public function index()
    {
        $_GET['section'] = 'dashboard';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang quản lý đơn hàng
     */
    public function orders()
    {
        $_GET['section'] = 'orders';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang quản lý sản phẩm
     */
    public function products()
    {
        $_GET['section'] = 'products';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang quản lý khách hàng
     */
    public function customers()
    {
        $_GET['section'] = 'customers';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang quản lý nhân viên
     */
    public function employees()
    {
        $_GET['section'] = 'employees';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang thống kê doanh thu
     */
    public function revenue()
    {
        $_GET['section'] = 'revenue';
        $this->renderAdminView();
    }

    /**
     * Hiển thị trang cài đặt
     */
    public function settings()
    {
        $_GET['section'] = 'settings';
        $this->renderAdminView();
    }

    /**
     * Render admin view với section tương ứng
     */
    private function renderAdminView()
    {
        // Kiểm tra quyền admin (uncomment khi đã có auth)
        // $this->checkAdminAuth();
        
        // Include file admin.php sẽ tự động load section tương ứng
        require_once __DIR__ . '/../Views/AdminDashBoard/admin.php';
    }

    /**
     * Kiểm tra xác thực admin
     */
    private function checkAdminAuth()
    {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'admin') {
            header('Location: /web/Views/Auth/Login/login.html');
            exit;
        }
    }

    // ==================== API METHODS ====================
    
    /**
     * API: Lấy danh sách đơn hàng
     */
    public function getOrders()
    {
        header('Content-Type: application/json');
        
        try {
            $status = $_GET['status'] ?? 'all';
            $page = $_GET['page'] ?? 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            // Query database (cần tạo OrderRepository)
            $query = "SELECT * FROM orders";
            if ($status !== 'all') {
                $query .= " WHERE status = :status";
            }
            $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            if ($status !== 'all') {
                $stmt->bindParam(':status', $status);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $orders,
                'page' => $page
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Lấy danh sách sản phẩm
     */
    public function getProducts()
    {
        header('Content-Type: application/json');
        
        try {
            $category = $_GET['category'] ?? 'all';
            $page = $_GET['page'] ?? 1;
            $limit = 20;
            $offset = ($page - 1) * $limit;

            $query = "SELECT * FROM products";
            if ($category !== 'all') {
                $query .= " WHERE category_id = :category";
            }
            $query .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($query);
            if ($category !== 'all') {
                $stmt->bindParam(':category', $category);
            }
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $products,
                'page' => $page
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Tạo đơn hàng mới
     */
    public function createOrder()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate data
            // Insert vào database
            // Return response

            echo json_encode([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công',
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $orderId = $data['order_id'] ?? null;
            $status = $data['status'] ?? null;

            if (!$orderId || !$status) {
                throw new Exception('Thiếu thông tin đơn hàng');
            }

            // Update database
            $query = "UPDATE orders SET status = :status WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $orderId);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Xóa đơn hàng
     */
    public function deleteOrder()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            $orderId = $_GET['id'] ?? null;
            
            if (!$orderId) {
                throw new Exception('Thiếu ID đơn hàng');
            }

            // Soft delete hoặc hard delete
            $query = "DELETE FROM orders WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $orderId);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Xóa đơn hàng thành công'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * API: Thống kê dashboard
     */
    public function getDashboardStats()
    {
        header('Content-Type: application/json');
        
        try {
            // Lấy thống kê từ database
            $stats = [
                'todayRevenue' => $this->getTodayRevenue(),
                'todayOrders' => $this->getTodayOrders(),
                'newCustomers' => $this->getNewCustomersToday(),
                'recentOrders' => $this->getRecentOrders(10)
            ];

            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Helper methods
    private function getTodayRevenue()
    {
        // Implement query
        return '5,200,000₫';
    }

    private function getTodayOrders()
    {
        // Implement query
        return 124;
    }

    private function getNewCustomersToday()
    {
        // Implement query
        return 18;
    }

    private function getRecentOrders($limit = 10)
    {
        // Implement query
        return [];
    }
}
