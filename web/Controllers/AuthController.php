<?php

class AuthController extends Controller {

    /**
     * Hiển thị trang login
     */
    public function showLogin() {
        // Hiển thị form login
        include './web/Views/Auth/login_v.php';
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function showSignup() {
        // Hiển thị form đăng ký
        include './web/Views/Auth/signup_v.php';
    }

    /**
     * Xử lý đăng nhập (POST)
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $remember = isset($_POST['remember']) ? true : false;

            // Gọi AuthService để xác thực (userType từ form: 'customer' hoặc 'employee')
            $userType = isset($_POST['userType']) ? trim($_POST['userType']) : 'customer';
            $authService = $this->service('AuthService');
            $result = $authService->authenticateUser($username, $password, $userType);

            // Nếu xác thực thất bại
            if (!$result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
                return;
            }

            // Đăng nhập thành công - tạo session
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $user = $result['user'];
            $userType = $result['userType'];

            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'roleName' => $user->roleName ?? null,
                'type' => $userType,
                'fullname' => $userType === 'employee' ? $user->fullname : $user->full_name
            ];

            // Lưu cookie nếu chọn remember
            if ($remember) {
                setcookie('remember_username', $user->username, time() + 60*60*24*30, '/');
            }

            // Lấy redirect URL từ service
            $roleName = isset($user->roleName) ? $user->roleName : '';
            $redirect = $authService->getRedirectUrl($userType, $roleName);

            echo "<script>
                alert('{$result['message']}');
                window.location.href = '{$redirect}';
            </script>";

        } else {
            // Không phải POST, redirect về trang login
            $this->showLogin();
        }
    }

    /**
     * Đăng xuất - Xóa hoàn toàn session
     */
    public function logout() {
        // Khởi động session nếu chưa được khởi động
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        // Bước 1: Xóa tất cả biến session
        $_SESSION = array();

        // Bước 2: Xóa session cookie trên browser
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Bước 3: Destroy session trên server
        session_destroy();

        // Bước 4: Xóa cookie "Remember me" nếu có
        if (isset($_COOKIE['remember_username'])) {
            setcookie('remember_username', '', time() - 3600, '/');
        }

        echo "<script>
            alert('Đã đăng xuất thành công!');
            window.location.href = '/COFFEE_PHP/Auth/showLogin';
        </script>";
    }

    /**
     * Xử lý đăng ký tài khoản (POST)
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'fullname' => isset($_POST['fullname']) ? trim($_POST['fullname']) : '',
                'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
                'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
                'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
                'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
                'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
                'confirmPassword' => isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : ''
            ];

            // Gọi AuthService để xử lý đăng ký
            $authService = $this->service('AuthService');
            $result = $authService->registerCustomer($data);

            if ($result['success']) {
                echo "<script>
                    alert('Đăng ký thành công! Vui lòng đăng nhập.');
                    window.location.href = '/COFFEE_PHP/Auth/showLogin';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }

        } else {
            // Không phải POST, redirect về trang đăng ký
            $this->showSignup();
        }
    }
}
