<?php
include_once './web/Services/ProductService.php';
include_once './web/Services/OrderService.php';

class StaffController extends Controller {
    private $productService;
    private $orderService;

    public function __construct() {
        $this->productService = new ProductService();
        $this->orderService = new OrderService();
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
