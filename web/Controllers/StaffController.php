<?php
include_once './web/Services/ProductService.php';
include_once './web/Services/OrderService.php';
include_once './web/Services/CustomerService.php';

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
     * Default Action: Redirect to POS or show Dashboard
     * URL: http://localhost/COFFEE_PHP/Staff
     */
    function GetData() {
        $this->pos();
    }

    /**
     * API: Get Menu for POS
     * URL: http://localhost/COFFEE_PHP/Staff/getMenu
     */
    function getMenu() {
        header('Content-Type: application/json');
        try {
            $menu = $this->productService->getMenuForPOS();
            echo json_encode($menu);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Lấy voucher đủ điều kiện cho POS
     * POST { customer_id, bill_total }
     */
    function getEligibleVouchers() {
        header('Content-Type: application/json');
        $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
        // Prefer 'sub_total' (pre-discount) when provided by client. Fall back to 'bill_total'.
        if (isset($_POST['sub_total'])) {
            $billTotal = (float)$_POST['sub_total'];
        } else {
            $billTotal = isset($_POST['bill_total']) ? (float)$_POST['bill_total'] : 0;
        }

        $voucherService = $this->service('VoucherService');
        $eligible = $voucherService->getEligibleVouchers($customerId, $billTotal);

        $out = [];
        foreach ($eligible as $v) $out[] = $v->toArray();

        echo json_encode(['success'=>true,'vouchers'=>$out]);
        exit;
    }

    /**
     * API: Lấy tất cả voucher (dành cho debug/hiển thị đầy đủ)
     * POST optional: none
     */
    function getAllVouchers() {
        header('Content-Type: application/json');
        $voucherService = $this->service('VoucherService');
        $all = $voucherService->getAllVouchers();
        $out = [];
        foreach ($all as $v) $out[] = $v->toArray();
        echo json_encode(['success'=>true,'vouchers'=>$out]);
        exit;
    }

    /**
     * API: Create Order
     * URL: http://localhost/COFFEE_PHP/Staff/createOrder
     */
    function createOrder() {
        header('Content-Type: application/json');
        
        // Nhận dữ liệu JSON từ fetch API
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid Data']);
            exit;
        }

        try {
            $result = $this->orderService->createOrder($data);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * POS Page (Point of Sale)
     * URL: http://localhost/COFFEE_PHP/Staff/pos
     */
    function pos() {
        // Load MasterLayoutStaff and pass 'staff_order' as the page content
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'staff_order',
            'section' => 'pos'
        ]);
    }

    /**
     * API: Tìm / Lấy customer theo phone (POS)
     * POST { phone: '090...' }
     */
    function searchCustomer() {
        header('Content-Type: application/json');
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        if (!$phone) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing phone']);
            exit;
        }
        $cust = $this->customerService->getCustomerByPhone($phone);
        if ($cust) {
            echo json_encode(['success' => true, 'customer' => $cust->toArray()]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not found']);
        }
        exit;
    }

    /**
     * API: POS upsert customer: nếu có thì trả về và có thể cộng điểm; nếu chưa thì tạo mới guest customer
     * POST { phone, fullname?, email?, pointsToAdd? }
     */
    function posUpsertCustomer() {
        header('Content-Type: application/json');
        $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
        $fullname = isset($_POST['fullname']) ? trim($_POST['fullname']) : 'Khách lẻ';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $pointsToAdd = isset($_POST['pointsToAdd']) ? (int)$_POST['pointsToAdd'] : 0;

        $result = $this->customerService->posUpsertCustomer([
            'phone' => $phone,
            'fullname' => $fullname,
            'email' => $email,
            'pointsToAdd' => $pointsToAdd
        ]);

        if ($result['success']) {
            $cust = $result['customer'];
            echo json_encode(['success' => true, 'customer' => $cust->toArray(), 'created' => $result['created']]);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Error']);
        }
        exit;
    }

    /**
     * Orders Management Page
     * URL: http://localhost/COFFEE_PHP/Staff/orders
     */
    function orders() {
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'orders',
            'section' => 'orders'
        ]);
    }

    /**
     * Customers Management Page
     * URL: http://localhost/COFFEE_PHP/Staff/customers
     */
    function customers() {
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'customers',
            'section' => 'customers'
        ]);
    }

    /**
     * Staff Profile Page
     * URL: http://localhost/COFFEE_PHP/Staff/profile
     */
    function profile() {
        $this->view('EmployeeDashBoard/MasterLayoutStaff', [
            'page' => 'profile',
            'section' => 'profile'
        ]);
    }
}