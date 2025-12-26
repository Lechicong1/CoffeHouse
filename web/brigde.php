<?php
/**
 * BRIDGE.PHP - File cầu nối, chứa các hàm helper và constants
 * Tương tự như MVC/bridge.php cũ
 */

// ===== CONSTANTS - Định nghĩa các đường dẫn =====

// Base URL của project
define('BASE_URL', '/COFFEE_PHP/');
define('PUBLIC_URL', BASE_URL . 'public/');

// Đường dẫn tới các thư mục
define('VIEW_PATH', __DIR__ . '/Views/');
define('CONTROLLER_PATH', __DIR__ . '/Controllers/');
define('MODEL_PATH', __DIR__ . '/Entity/');

// Đường dẫn assets
define('CSS_URL', PUBLIC_URL . 'css/');
define('JS_URL', PUBLIC_URL . 'js/');
define('IMG_URL', PUBLIC_URL . 'images/');


// ===== HELPER FUNCTIONS - Các hàm tiện ích =====

/**
 * Redirect đến một URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Lấy base URL
 */
function base_url($path = '') {
    return BASE_URL . $path;
}

/**
 * Lấy URL của asset (CSS, JS, Image)
 */
function asset($path) {
    return PUBLIC_URL . $path;
}

/**
 * Escape HTML để tránh XSS
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Debug - In ra biến và dừng chương trình
 */
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Lấy thông tin user hiện tại từ session
 */
function currentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null
        ];
    }
    return null;
}

/**
 * Kiểm tra quyền truy cập theo role
 */
function checkRole($allowedRoles = []) {
    if (!isLoggedIn()) {
        redirect('?url=Auth/showLoginPage');
        return false;
    }

    $currentRole = $_SESSION['role'] ?? 'customer';

    if (!in_array($currentRole, $allowedRoles)) {
        http_response_code(403);
        echo "Bạn không có quyền truy cập trang này!";
        exit;
    }

    return true;
}

/**
 * Format tiền VNĐ
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Format ngày giờ
 */
function formatDate($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';

    $timestamp = strtotime($datetime);
    return date($format, $timestamp);
}

/**
 * Tạo URL cho routing
 */
function route($controller, $action = 'index', $params = []) {
    $url = "?url=$controller";

    if ($action !== 'index') {
        $url .= "/$action";
    }

    if (!empty($params)) {
        $url .= '/' . implode('/', $params);
    }

    return $url;
}

/**
 * Flash message - Lưu thông báo tạm thời
 */
function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Lấy và xóa flash message
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Upload file
 */
function uploadFile($file, $targetDir = 'uploads/') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Không có file được upload'];
    }

    // Tạo thư mục nếu chưa có
    $uploadPath = __DIR__ . '/../public/' . $targetDir;
    if (!file_exists($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    // Tạo tên file unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $targetFile = $uploadPath . $filename;

    // Kiểm tra loại file (chỉ cho phép ảnh)
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array(strtolower($extension), $allowedTypes)) {
        return ['success' => false, 'message' => 'Chỉ cho phép upload ảnh'];
    }

    // Upload
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return [
            'success' => true,
            'path' => $targetDir . $filename,
            'url' => PUBLIC_URL . $targetDir . $filename
        ];
    }

    return ['success' => false, 'message' => 'Upload thất bại'];
}


// ===== AUTO-INCLUDE CÁC FILE HELPER KHÁC (nếu có) =====

// Nếu bạn có các helper khác, include ở đây
// include_once __DIR__ . '/Helpers/StringHelper.php';
// include_once __DIR__ . '/Helpers/ValidationHelper.php';

// ===== LOAD CONTROLLERS =====
include_once __DIR__ . '/Controllers/AuthController.php';
include_once __DIR__ . '/Controllers/CategoryController.php';
include_once __DIR__ . '/Controllers/CustomerController.php';
include_once __DIR__ . '/Controllers/EmployeeController.php';
include_once __DIR__ . '/Controllers/IngredientController.php';
include_once __DIR__ . '/Controllers/VoucherController.php';

// ===== LOAD SERVICES =====
include_once __DIR__ . '/Services/AuthService.php';
include_once __DIR__ . '/Services/CategoryService.php';
include_once __DIR__ . '/Services/CustomerService.php';
include_once __DIR__ . '/Services/EmployeeService.php';
include_once __DIR__ . '/Services/IngredientService.php';
include_once __DIR__ . '/Services/VoucherService.php';
