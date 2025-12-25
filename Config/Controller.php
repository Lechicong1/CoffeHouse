<?php
/**
 * Class Controller - Base Controller
 * Class cha cho tất cả các Controller trong hệ thống MVC
 */
class Controller {

    public function model($model) {
        $modelPath = './web/Models/' . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            return new $model;
        }

        echo "Model không tồn tại: " . $model;
        return null;
    }


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
    

//    public function json($data, $statusCode = 200) {
//        http_response_code($statusCode);
//        header('Content-Type: application/json; charset=utf-8');
//        echo json_encode($data, JSON_UNESCAPED_UNICODE);
//        exit;
//    }
}
