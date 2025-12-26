<?php
// Test file để debug lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Testing UserController...<br>";

// Include các file cần thiết
include_once './Config/ConnectDatabase.php';
include_once './Config/Controller.php';
include_once './Config/Service.php';
include_once './web/Controllers/UserController.php';

echo "Files included successfully!<br>";

try {
    $controller = new UserController();
    echo "UserController created successfully!<br>";
    
    // Test method index
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Method index() executed!<br>";
    echo "<hr>";
    echo $output;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
}
?>

