<?php
/**
 * AuthService - Xử lý logic nghiệp vụ về xác thực
 */
class AuthService {

    private $userRepo;

    public function __construct() {
        // Include Repository cần thiết
        require_once './web/Repositories/UserRepository.php';
        $this->userRepo = new UserRepository();
        
        // Khởi động session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Đăng nhập user
     * @return array ['success' => bool, 'message' => string, 'user' => UserEntity|null]
     */
    public function login($username, $password, $remember = false) {
        try {
            // Tìm user theo username
            $user = $this->userRepo->findByUsername($username);

            // Kiểm tra user có tồn tại không
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
                ];
            }

            // Verify password
            if (!password_verify($password, $user->password_hash)) {
                return [
                    'success' => false,
                    'message' => 'Tên đăng nhập hoặc mật khẩu không đúng'
                ];
            }

            // Lưu thông tin user vào session
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            $_SESSION['full_name'] = $user->full_name;
            $_SESSION['logged_in_at'] = time();

            // Regenerate session ID để tránh session fixation attack
            session_regenerate_id(true);

            // Xử lý "Remember Me" (optional - dùng cookie)
            if ($remember) {
                $this->setRememberMeCookie($user->id);
            }

            return [
                'success' => true,
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Đăng xuất user
     */
    public function logout() {
        // Xóa cookie remember me (nếu có)
        $this->clearRememberMeCookie();

        // Xóa toàn bộ session
        $_SESSION = [];

        // Xóa session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
    }

    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    public function isLoggedIn() {
        // Kiểm tra session
        if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
            // Optional: Kiểm tra timeout (30 phút không hoạt động)
            $timeout = 1800; // 30 minutes
            if (isset($_SESSION['logged_in_at']) && (time() - $_SESSION['logged_in_at']) > $timeout) {
                $this->logout();
                return false;
            }

            // Update last activity time
            $_SESSION['logged_in_at'] = time();
            return true;
        }

        // Kiểm tra cookie "Remember Me"
        if (isset($_COOKIE['remember_token'])) {
            return $this->loginFromRememberToken();
        }

        return false;
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            $userId = $_SESSION['user_id'];
            return $this->userRepo->findById($userId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Kiểm tra quyền của user
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Kiểm tra user có một trong các role
     */
    public function hasAnyRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
    }

    /**
     * Set cookie Remember Me (lưu 30 ngày)
     */
    private function setRememberMeCookie($userId) {
        $token = bin2hex(random_bytes(32)); // Generate random token
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days

        // Lưu token vào cookie
        setcookie('remember_token', $token, $expiry, '/', '', false, true); // httponly = true

        // TODO: Lưu token vào database với user_id để verify sau
        // (Cần tạo bảng remember_tokens: id, user_id, token, expires_at)
    }

    /**
     * Xóa cookie Remember Me
     */
    private function clearRememberMeCookie() {
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
            unset($_COOKIE['remember_token']);
        }
    }

    /**
     * Login từ remember token (chưa implement đầy đủ)
     */
    private function loginFromRememberToken() {
        // TODO: Verify token từ database
        // Hiện tại return false
        return false;
    }
}
