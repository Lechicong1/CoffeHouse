<?php
/**
 * AuthController - Xử lý xác thực người dùng
 */
class AuthController extends Controller {

    private $authService;
    
    // Constants cho paths
    private const BASE_PATH = '/COFFEE_PHP/public/views';
    private const LOGIN_PATH = '/COFFEE_PHP/public/views/Auth/Login/login.html';

    public function __construct() {
        // Include Service cần thiết
        require_once './web/Services/AuthService.php';
        $this->authService = new AuthService();
    }

    /**
     * Hiển thị trang login
     */
    public function showLoginPage() {
        // Nếu đã login rồi, redirect về dashboard
        if ($this->authService->isLoggedIn()) {
            $this->redirectToDashboard();
            return;
        }

        // Hiển thị trang login
        header("Location: " . self::LOGIN_PATH);
        exit;
    }

    /**
     * Xử lý đăng nhập (API endpoint)
     */
    public function handleLogin() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        try {
            // Lấy dữ liệu từ JSON body
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Nếu không có JSON, lấy từ $_POST
            if (!$input) {
                $input = $_POST;
            }

            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            $remember = isset($input['remember']) && $input['remember'];

            // Validate
            if (empty($username) || empty($password)) {
                $this->json([
                    'success' => false,
                    'message' => 'Username và password không được để trống'
                ], 400);
                return;
            }

            // Thực hiện login
            $result = $this->authService->login($username, $password, $remember);

            if ($result['success']) {
                $this->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công',
                    'redirect' => $this->getDashboardUrl($result['user'])
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => $result['message']
                ], 401);
            }

        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout() {
        $this->authService->logout();
        
        // Redirect về trang login
        header("Location: " . self::LOGIN_PATH);
        exit;
    }

    /**
     * Check trạng thái đăng nhập (API)
     */
    public function checkAuth() {
        $user = $this->authService->getCurrentUser();
        
        if ($user) {
            $this->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name,
                    'role' => $user->role
                ]
            ]);
        } else {
            $this->json(['authenticated' => false], 401);
        }
    }

    /**
     * Lấy URL dashboard dựa vào role
     */
    private function getDashboardUrl($user) {
        // Map role to dashboard
        $dashboards = [
            'manager' => self::BASE_PATH . '/AdminDashBoard/admin.html',
            'staff' => self::BASE_PATH . '/EmployeeDashBoard/staff.html',
            'shipper' => self::BASE_PATH . '/ShipperDashBoard/shipperdemo.html',
            'customer' => self::BASE_PATH . '/UserDashBoard/index.html'
        ];
        
        return $dashboards[$user->role] ?? $dashboards['customer'];
    }

    /**
     * Redirect đến dashboard phù hợp
     */
    private function redirectToDashboard() {
        $user = $this->authService->getCurrentUser();
        $url = $this->getDashboardUrl($user);
        header("Location: $url");
        exit;
    }
}
