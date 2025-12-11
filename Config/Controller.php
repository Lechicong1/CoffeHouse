<?php
namespace Config;

class Controller {
    protected function view($name, $data = []) {
        if (!empty($data)) {
            extract($data, EXTR_SKIP); // EXTR_SKIP: không ghi đè biến đã tồn tại
        }
        
        // Kiểm tra nếu đã có extension (.html, .php) thì không thêm .php
        if (pathinfo($name, PATHINFO_EXTENSION)) {
            $viewPath = __DIR__ . "/../web/Views/$name";
        } else {
            $viewPath = __DIR__ . "/../web/Views/$name.php";
        }
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: $viewPath");
        }
        
        require $viewPath;
    }
    
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
