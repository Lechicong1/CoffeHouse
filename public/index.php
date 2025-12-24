<?php
// Autoloader cho các class theo namespace
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../' . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use Config\Router;

$router = new Router();

// Home routes
$router->get("/", "HomeController@index");
$router->get("/index.php", "HomeController@index");

// Auth routes
$router->get("/login", "AuthController@showLoginPage");
$router->post("/api/login", "AuthController@handleLogin");
$router->get("/logout", "AuthController@logout");
$router->get("/api/check-auth", "AuthController@checkAuth");

// User routes (cần authentication)
$router->get("/users", "UserController@index");
$router->get("/users/{id}", "UserController@detail");

$router->dispatch();
