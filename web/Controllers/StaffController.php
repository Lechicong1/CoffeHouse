<?php
/**
 * Staff Controller - Xử lý POS (Point of Sale)
 * Theo mô hình MVC chuẩn - KHÔNG SỬ DỤNG API JSON
 */

require_once './web/Services/ProductService.php';
require_once './web/Services/OrderService.php';
require_once './web/Services/CustomerService.php';

class StaffController extends Controller {
    private $productService;
    private $orderService;
    private $customerService;

    public function __construct() {
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
        $this->customerService = new CustomerService();
    }

    /**
     * Default Action: Hiển thị trang POS với dữ liệu menu
     * URL: http://localhost/COFFEE_PHP/StaffController/GetData
     */
    function GetData() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $staffId = $_SESSION['user']['id'] ?? null;

        try {
            // Lấy menu từ database
            $menuData = $this->productService->getMenuForPOS();

            // Render view với dữ liệu
            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'staff_order',
                'section' => 'pos',
                'menuItems' => $menuData,
                'staffId' => $staffId
            ]);

        } catch (Exception $e) {
            echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "');</script>";
            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'staff_order',
                'section' => 'pos',
                'menuItems' => [],
                'staffId' => $staffId
            ]);
        }
    }

    /**
     * POS Page (Alias cho GetData)
     * URL: http://localhost/COFFEE_PHP/StaffController/pos
     */
    function pos() {
        $this->GetData();
    }

    /**
     * Xử lý tạo đơn hàng từ POS (POST) - Giống CheckoutController/placeOrder
     * URL: http://localhost/COFFEE_PHP/StaffController/createOrder
     */
    function createOrder() {
        // Kiểm tra phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/Staff/GetData');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $staffId = $_SESSION['user']['id'] ?? null;

        try {
            // Validate dữ liệu
            $requiredFields = ['order_type', 'payment_method', 'total_amount'];
            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field])) {
                    throw new Exception("Thiếu thông tin: $field");
                }
            }

            // Validate items
            if (empty($_POST['cart_items'])) {
                throw new Exception("Giỏ hàng trống");
            }

            // Parse cart items từ JSON string
            $cartItems = json_decode($_POST['cart_items'], true);
            if (!$cartItems || !is_array($cartItems)) {
                throw new Exception("Dữ liệu giỏ hàng không hợp lệ");
            }

            // Chuẩn bị dữ liệu đơn hàng
            $orderData = [
                'staff_id' => $staffId,
                'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'order_type' => $_POST['order_type'],
                'payment_method' => $_POST['payment_method'],
                'total_amount' => (float)$_POST['total_amount'],
                'note' => trim($_POST['note'] ?? ''),
                'items' => $cartItems
            ];

            // Thêm thông tin voucher nếu có (GIỮ NGUYÊN LOGIC VOUCHER)
            if (!empty($_POST['voucher_id'])) {
                $orderData['voucher'] = [
                    'voucher_id' => (int)$_POST['voucher_id']
                ];
            }

            // Tạo đơn hàng thông qua Service
            $result = $this->orderService->createOrder($orderData);

            if ($result['success']) {
                echo "<script>
                    alert('Đặt hàng thành công! Mã đơn: {$result['order_code']}');
                    window.location.href = '/COFFEE_PHP/StaffController/GetData';
                </script>";
                exit;
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
            exit;
        }
    }

    /**
     * Tìm khách hàng theo số điện thoại (POST) - Không dùng JSON
     * URL: http://localhost/COFFEE_PHP/StaffController/searchCustomer
     */
    function searchCustomer() {
        // Kiểm tra phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/GetData');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        
        if (!$phone) {
            echo "<script>
                alert('Vui lòng nhập số điện thoại');
                window.history.back();
            </script>";
            exit;
        }

        try {
            $customer = $this->customerService->getCustomerByPhone($phone);
            
            if ($customer) {
                // Trả về trang với thông tin khách hàng
                $_SESSION['pos_customer_search'] = $customer->toArray();
                header('Location: /COFFEE_PHP/StaffController/GetData');
                exit;
            } else {
                echo "<script>
                    alert('Không tìm thấy khách hàng với số điện thoại: $phone');
                    window.history.back();
                </script>";
                exit;
            }

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
            exit;
        }
    }

    /**
     * Tạo hoặc cập nhật khách hàng (POST) - Không dùng JSON
     * URL: http://localhost/COFFEE_PHP/StaffController/upsertCustomer
     */
    function upsertCustomer() {
        // Kiểm tra phương thức POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/GetData');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        try {
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
            $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : 'Khách lẻ';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $pointsToAdd = isset($_POST['pointsToAdd']) ? (int)$_POST['pointsToAdd'] : 0;

            if (!$phone) {
                throw new Exception("Vui lòng nhập số điện thoại");
            }

            $result = $this->customerService->posUpsertCustomer([
                'phone' => $phone,
                'fullname' => $fullname,
                'email' => $email,
                'pointsToAdd' => $pointsToAdd
            ]);

            if ($result['success']) {
                $_SESSION['pos_customer_search'] = $result['customer']->toArray();
                header('Location: /COFFEE_PHP/StaffController/GetData');
                exit;
            } else {
                throw new Exception($result['message'] ?? 'Lỗi khi tạo/cập nhật khách hàng');
            }

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
            exit;
        }
    }

    /**
     * Orders Management Page
     * URL: http://localhost/COFFEE_PHP/StaffController/orders
     */
    function orders() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $staffId = $_SESSION['user']['id'] ?? null;

        try {
            // Lấy filter từ GET
            $filters = [];
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $filters['search'] = trim($_GET['search']);
            }
            
            // Chỉ hiển thị đơn hàng tại quầy (AT_COUNTER)
            $filters['order_type'] = 'AT_COUNTER';

            // Lấy danh sách đơn hàng
            $orders = $this->orderService->getOrders($filters);

            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'order_list',
                'section' => 'orders',
                'orders' => $orders,
                'currentFilter' => $filters
            ]);

        } catch (Exception $e) {
            echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "');</script>";
            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'order_list',
                'section' => 'orders',
                'orders' => [],
                'currentFilter' => []
            ]);
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng (POST)
     * URL: http://localhost/COFFEE_PHP/StaffController/updateOrderStatus
     */
    function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/orders');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        try {
            $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $newStatus = isset($_POST['status']) ? $_POST['status'] : '';

            if (!$orderId || !$newStatus) {
                throw new Exception('Thiếu thông tin');
            }

            $result = $this->orderService->updateOrderStatus($orderId, $newStatus);

            if ($result['success']) {
                echo "<script>
                    alert('" . $result['message'] . "');
                    window.location.href = '/COFFEE_PHP/Staff/orders';
                </script>";
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
        }
        exit;
    }

    /**
     * Cập nhật ghi chú đơn hàng (POST)
     * URL: http://localhost/COFFEE_PHP/StaffController/updateOrderNote
     */
    function updateOrderNote() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/orders');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        try {
            $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';

            if (!$orderId) {
                throw new Exception('Thiếu thông tin');
            }

            $result = $this->orderService->updateOrderNote($orderId, $note);

            if ($result['success']) {
                echo "<script>
                    alert('" . $result['message'] . "');
                    window.location.href = '/COFFEE_PHP/Staff/orders';
                </script>";
            } else {
                throw new Exception($result['message']);
            }

        } catch (Exception $e) {
            echo "<script>
                alert('Lỗi: " . addslashes($e->getMessage()) . "');
                window.history.back();
            </script>";
        }
        exit;
    }

    /**
     * Lấy chi tiết đơn hàng (GET)
     * URL: http://localhost/COFFEE_PHP/StaffController/getOrderDetail?order_id=1
     */
    function getOrderDetail() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu order_id']);
            exit;
        }

        try {
            $items = $this->orderService->getOrderItems($orderId);
            echo json_encode(['success' => true, 'items' => $items]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Customers Management Page
     * URL: http://localhost/COFFEE_PHP/StaffController/customers
     */
    function customers() {
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'customers',
            'section' => 'customers'
        ]);
    }

    /**
     * Staff Profile Page
     * URL: http://localhost/COFFEE_PHP/StaffController/profile
     */
    function profile() {
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'profile',
            'section' => 'profile'
        ]);
    }
}