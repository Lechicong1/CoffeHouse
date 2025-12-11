<?php
namespace Config;

class Controller {
    protected function view($name, $data = []) {
        extract($data);
        $viewPath = __DIR__ . "/../web/Views/$name.php";
        
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
