<?php
/**
 * Class Controller - Base Controller
 * Class cha cho tất cả các Controller trong hệ thống MVC
 */
class Controller {

    /**
     * Load Model
     * @param string $model - Tên file Model (không cần .php)
     * @return object|null - Instance của Model hoặc null nếu không tồn tại
     */
    public function model($model) {
        $modelPath = './web/Models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model;
        }

        echo "Model không tồn tại: " . $model;
        return null;
    }

    /**
     * Load View
     * @param string $view - Đường dẫn tới file View (VD: 'UserDashBoard/index.html')
     * @param array $data - Dữ liệu truyền vào View (optional)
     */
    public function view($view, $data = []) {
        // Kiểm tra nếu đã có extension (.html, .php) thì không thêm .php
        if (pathinfo($view, PATHINFO_EXTENSION)) {
            $viewPath = './web/Views/' . $view;
        } else {
            $viewPath = './web/Views/' . $view . '.php';
        }

        // Kiểm tra file tồn tại
        if (file_exists($viewPath)) {
            // Extract data thành biến để sử dụng trong View
            if (!empty($data)) {
                extract($data);
            }
            require_once $viewPath;
        } else {
            echo "View không tồn tại: " . $viewPath;
        }
    }
    
    /**
     * Trả về JSON response (dùng cho API)
     * @param array $data - Dữ liệu trả về
     * @param int $statusCode - HTTP status code
     */
    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
