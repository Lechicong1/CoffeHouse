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

$router->get("/", "HomeController@index");

$router->dispatch();
