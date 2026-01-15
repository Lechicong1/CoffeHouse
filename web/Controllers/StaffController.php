<?php
/**
 * Staff Controller - Xử lý POS (Point of Sale)
 * Theo mô hình MVC chuẩn - KHÔNG SỬ DỤNG API JSON
 */

require_once './web/Services/ProductService.php';
require_once './web/Services/OrderService.php';
require_once './web/Services/CustomerService.php';
require_once './web/Services/CategoryService.php';
require_once __DIR__ . '/../../Config/ExcelHelper.php';

class StaffController extends Controller {
    private $productService;
    private $orderService;
    private $customerService;
    private $categoryService;

    public function __construct() {
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
        $this->customerService = new CustomerService();
        $this->categoryService = new CategoryService();
    }

    /**
     * Cập nhật chi tiết đơn hàng (order_type, table_number, note)
     * URL: http://localhost/COFFEE_PHP/StaffController/updateOrderDetails
     */
    function updateOrderDetails() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/orders');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        try {
            $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
            $orderType = isset($_POST['order_type']) ? trim($_POST['order_type']) : 'AT_COUNTER';
            $tableNumber = isset($_POST['table_number']) ? trim($_POST['table_number']) : null;
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';

            if (!$orderId) {
                throw new Exception('Thiếu thông tin');
            }

            $result = $this->orderService->updateOrderDetails($orderId, $orderType, $tableNumber, $note);

            if ($result['success']) {
                echo "<script>
                    alert('" . addslashes($result['message']) . "');
                    window.location.href = '/COFFEE_PHP/StaffController/orders';
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
            // Lấy categories từ database
            $categories = $this->categoryService->getAllCategories();
            
            // Chuyển đổi categories sang array
            $categoriesArray = [];
            foreach ($categories as $cat) {
                $categoriesArray[] = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'description' => $cat->description
                ];
            }

            // Render view với dữ liệu
            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'staff_order',
                'section' => 'pos',
                'menuItems' => $menuData,
                'categories' => $categoriesArray,
                'staffId' => $staffId
            ]);

        } catch (Exception $e) {
            echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "');</script>";
            $this->view('EmployeeDashBoard/MasterLayoutStaff', [
                'page' => 'staff_order',
                'section' => 'pos',
                'menuItems' => [],
                'categories' => [],
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
        // Delegate full POS order creation to service (service will parse/validate)
        $postData = $_POST;
        $postData['staff_id'] = $staffId;

        $result = $this->orderService->createOrderFromPOS($postData);

        if ($result['success']) {
            echo "<script>
                alert('Đặt hàng thành công! Mã đơn: {$result['order_code']}');
                window.location.href = '/COFFEE_PHP/StaffController/GetData';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Lỗi: " . addslashes($result['message']) . "');
                window.history.back();
            </script>";
            exit;
        }
    }

    function searchCustomerPos() {
        header("Content-Type: text/plain; charset=utf-8");
        
        $phone = trim($_POST['phone'] ?? '');
        if (empty($phone)) {
            echo "ERROR|Vui lòng nhập số điện thoại";
            exit;
        }

        try {
            $customer = $this->customerService->getCustomerByPhone($phone);
            
            if ($customer) {
                echo "OK|{$customer->id}|{$customer->full_name}|{$customer->phone}|{$customer->points}";
            } else {
                echo "ERROR|Không tìm thấy khách hàng với SĐT: {$phone}";
            }
        } catch (Exception $e) {
            echo "ERROR|" . $e->getMessage();
        }
        exit;
    }

    function createCustomerPos() {
        header("Content-Type: text/plain; charset=utf-8");
        
        $phone = trim($_POST['phone'] ?? '');
        $fullname = trim($_POST['fullname'] ?? 'Khách lẻ');
        $email = trim($_POST['email'] ?? '');

        if (empty($phone)) {
            echo "ERROR|Vui lòng nhập số điện thoại";
            exit;
        }

        try {
            $result = $this->customerService->posCreateCustomer([
                'phone' => $phone,
                'fullname' => $fullname,
                'email' => $email
            ]);

            if ($result['success'] && $result['customer']) {
                $c = $result['customer'];
                echo "OK|{$c->id}|{$c->full_name}|{$c->phone}|{$c->points}";
            } else {
                echo "ERROR|" . ($result['message'] ?? 'Không thể tạo khách hàng');
            }
        } catch (Exception $e) {
            echo "ERROR|" . $e->getMessage();
        }
        exit;
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
     * Xuất Excel danh sách đơn hàng
     */
    public function xuatexcel() {
        if(isset($_POST['btnXuatexcel'])){
            try {
                // Lấy filter từ POST
                $filters = [];
                if (isset($_POST['status']) && !empty($_POST['status'])) {
                    $filters['status'] = $_POST['status'];
                }
                if (isset($_POST['search']) && !empty($_POST['search'])) {
                    $filters['search'] = trim($_POST['search']);
                }
                
                // Chỉ lấy đơn hàng tại quầy
                $filters['order_type'] = ['AT_COUNTER', 'TAKEAWAY'];

                // Lấy danh sách đơn hàng
                $orders = $this->orderService->getOrders($filters);

                // Chuyển đổi array sang format Excel
                $data = array_map(function($order) {
                    // Format status
                    $statusText = '';
                    switch($order['status']) {
                        case 'PENDING': $statusText = 'Chờ xác nhận'; break;
                        case 'PREPARING': $statusText = 'Đang chuẩn bị'; break;
                        case 'READY': $statusText = 'Sẵn sàng'; break;
                        case 'SHIPPING': $statusText = 'Đang giao'; break;
                        case 'COMPLETED': $statusText = 'Hoàn thành'; break;
                        case 'CANCELLED': $statusText = 'Đã hủy'; break;
                        default: $statusText = $order['status'];
                    }

                    // Format payment status
                    $paymentText = '';
                    switch($order['payment_status']) {
                        case 'PAID': $paymentText = 'Đã thanh toán'; break;
                        case 'UNPAID': $paymentText = 'Chưa thanh toán'; break;
                        case 'REFUNDED': $paymentText = 'Đã hoàn tiền'; break;
                        default: $paymentText = $order['payment_status'];
                    }

                    // Format order type
                    $orderTypeText = $order['order_type'] === 'AT_COUNTER' ? 'Tại quầy' : 'Mang về';

                    // Format payment method
                    $paymentMethodText = $order['payment_method'] === 'CASH' ? 'Tiền mặt' : 'Chuyển khoản';

                    return [
                        'id' => $order['id'],
                        'order_code' => $order['order_code'] ?? '-',
                        'customer_name' => $order['customer_name'] ?? 'Khách lẻ',
                        'customer_phone' => $order['customer_phone'] ?? '-',
                        'table_number' => $order['table_number'] ?? '-',
                        'order_type' => $orderTypeText,
                        'total_amount' => number_format($order['total_amount'], 0, ',', '.') . ' ₫',
                        'payment_method' => $paymentMethodText,
                        'payment_status' => $paymentText,
                        'status' => $statusText,
                        'note' => $order['note'] ?? '-',
                        'created_at' => date('d/m/Y H:i', strtotime($order['created_at']))
                    ];
                }, $orders);

                // Định nghĩa cấu trúc cột cho Excel
                $headers = [
                    'id' => 'ID',
                    'order_code' => 'Mã Đơn',
                    'customer_name' => 'Khách Hàng',
                    'customer_phone' => 'SĐT',
                    'table_number' => 'Bàn Số',
                    'order_type' => 'Loại Đơn',
                    'total_amount' => 'Tổng Tiền',
                    'payment_method' => 'Phương Thức TT',
                    'payment_status' => 'Trạng Thái TT',
                    'status' => 'Trạng Thái',
                    'note' => 'Ghi Chú',
                    'created_at' => 'Ngày Tạo'
                ];

                // Gọi hàm xuất Excel từ Helper
                ExcelHelper::exportToExcel($data, $headers, 'DanhSachDonHang');

            } catch (Exception $e) {
                echo "<script>
                    alert('Lỗi xuất Excel: " . addslashes($e->getMessage()) . "');
                    window.history.back();
                </script>";
            }
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

            // Nếu request AJAX (fetch), trả về JSON để client xử lý và giữ modal mở
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit;
            }

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
     * Cập nhật ghi chú cho từng item trong đơn hàng (POST)
     * URL: http://localhost/COFFEE_PHP/StaffController/updateOrderItemNote
     */
    function updateOrderItemNote() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/StaffController/orders');
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        try {
            $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
            $note = isset($_POST['note']) ? trim($_POST['note']) : '';

            if (!$itemId) {
                throw new Exception('Thiếu thông tin');
            }

            $result = $this->orderService->updateOrderItemNote($itemId, $note);

            // Nếu AJAX thì trả về JSON (để client reload nội dung modal)
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($result);
                exit;
            }

            if ($result['success']) {
                echo "<script>
                    alert('" . $result['message'] . "');
                    window.location.href = '/COFFEE_PHP/StaffController/orders';
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

        // Delegate to service
        $items = $this->orderService->getOrderItems($orderId);
        echo json_encode(['success' => true, 'items' => $items]);
        exit;
    }

    /**
     * Lấy dữ liệu đầy đủ cho hóa đơn (order + items)
     * URL: http://localhost/COFFEE_PHP/StaffController/getOrderInvoiceData
     */
    function getOrderInvoiceData() {
        // Enforce GET method for this endpoint (consistent with other StaffController handlers)
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Location: /COFFEE_PHP/StaffController/GetData');
            exit;
        }

        // Keep response consistent with other StaffController endpoints (plain text responses)
        header('Content-Type: text/plain; charset=utf-8');

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

        if (!$orderId) {
            echo "ERROR|Thiếu order_id";
            exit;
        }

        try {
            // Delegate to service to get order + items
            $res = $this->orderService->getOrderInvoiceData($orderId);
            // Return payload as JSON string but with plain/text content-type for compatibility
            echo json_encode($res);
        } catch (Exception $e) {
            echo 'ERROR|' . $e->getMessage();
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