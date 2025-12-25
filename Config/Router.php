<?php
/**
 * Class Router - Điều hướng request trong MVC
 * Xử lý URL và gọi Controller -> Action tương ứng
 */
class Router {
    protected $controller = "HomeController";  // Controller mặc định
    protected $action = "index";                // Action mặc định
    protected $params = [];                     // Mảng parameters

    /**
     * Constructor - Khởi tạo và xử lý routing
     */
    public function __construct() {
        $arr = $this->processURL();

        // ===== XỬ LÝ CONTROLLER =====
        if ($arr != null) {
            // Tự động thêm "Controller" vào tên nếu chưa có
            $controllerName = $arr[0];
            if (!str_ends_with($controllerName, 'Controller')) {
                $controllerName .= 'Controller';
            }
            
            // Kiểm tra file Controller có tồn tại không
            if (file_exists('./web/Controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($arr[0]);
            }
        }

        // Include Controller file
        require_once './web/Controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // ===== XỬ LÝ ACTION (METHOD) =====
        if (isset($arr[1])) {
            // Kiểm tra method có tồn tại trong Controller không
            if (method_exists($this->controller, $arr[1])) {
                $this->action = $arr[1];
                unset($arr[1]);
            }
        }

        // ===== XỬ LÝ PARAMETERS =====
        $this->params = $arr ? array_values($arr) : [];

        // ===== GỌI CONTROLLER -> ACTION VỚI PARAMS =====
        // Tương đương: $controller->action($param1, $param2, ...)
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    /**
     * Xử lý URL từ $_GET['url']
     * @return array|null - Mảng các phần tử URL hoặc null
     *
     * VD: URL = "User/profile/123"
     *     => ['User', 'profile', '123']
     */
    private function processURL() {
        if (isset($_GET['url'])) {
            // Loại bỏ khoảng trắng đầu/cuối và dấu /
            $url = trim($_GET['url'], '/');

            // Lọc URL để bảo mật (xóa các ký tự nguy hiểm)
            $url = filter_var($url, FILTER_SANITIZE_URL);

            // Tách URL thành mảng theo dấu /
            return explode('/', $url);
        }

        return null;
    }
}
