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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLogin();
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $remember = !empty($_POST['remember']);
        $userType = trim($_POST['userType'] ?? 'customer');

        $authService = $this->service('AuthService');
        $result = $authService->authenticateUser($username, $password, $userType);

        if (!$result['success']) {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
            return;
        }

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

        if ($remember) {
            setcookie('remember_username', $user->username, time() + 60*60*24*30, '/');
        }

        $redirect = $authService->getRedirectUrl($userType, $user->roleName ?? '');
        echo "<script>alert('{$result['message']}'); window.location.href = '{$redirect}';</script>";
    }

    public function logout() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        session_destroy();

        if (isset($_COOKIE['remember_username'])) {
            setcookie('remember_username', '', time() - 3600, '/');
        }

        echo "<script>alert('Đã đăng xuất thành công!'); window.location.href = '/COFFEE_PHP/Auth/showLogin';</script>";
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showSignup();
            return;
        }

        $data = [
            'fullname' => trim($_POST['fullname'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'confirmPassword' => trim($_POST['confirmPassword'] ?? '')
        ];

        $authService = $this->service('AuthService');
        $result = $authService->registerCustomer($data);

        if ($result['success']) {
            echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href = '/COFFEE_PHP/Auth/showLogin';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }
}
