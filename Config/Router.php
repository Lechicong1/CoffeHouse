<?php
namespace Config;

class Router {
    private $routes = [];

    public function get($path, $action) {
        $this->addRoute('GET', $path, $action);
    }
    
    public function post($path, $action) {
        $this->addRoute('POST', $path, $action);
    }
    
    public function put($path, $action) {
        $this->addRoute('PUT', $path, $action);
    }
    
    public function delete($path, $action) {
        $this->addRoute('DELETE', $path, $action);
    }
    
    private function addRoute($method, $path, $action) {
        $this->routes[$method][$path] = $action;
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Kiểm tra nếu có query parameter 'url'
        if (isset($_GET['url'])) {
            $path = '/' . trim($_GET['url'], '/');
        } else {
            $uri = strtok($_SERVER['REQUEST_URI'], '?');
            
            // Loại bỏ /COFFEE_PHP/public khỏi URI để lấy path thực
            $scriptName = dirname($_SERVER['SCRIPT_NAME']);
            $path = str_replace($scriptName, '', $uri);
            $path = $path ?: '/';
        }
        
        // Sanitize path - chống path traversal
        $path = preg_replace('#/+#', '/', $path); // Loại bỏ // duplicate slashes
        
        // Xử lý route với parameters {id}
        foreach ($this->routes[$method] ?? [] as $route => $action) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route);
            $pattern = '#^' . $pattern . '$#';
            
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Bỏ full match
                
                list($controller, $methodName) = explode("@", $action);
                $controllerClass = "web\\Controllers\\$controller";
                
                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Internal server error']);
                    return;
                }
                
                $controllerObj = new $controllerClass;
                
                if (!method_exists($controllerObj, $methodName)) {
                    http_response_code(500);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'Internal server error']);
                    return;
                }
                
                call_user_func_array([$controllerObj, $methodName], $matches);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Route not found']);
    }
}
