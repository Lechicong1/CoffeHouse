<?php
// Debug file - Bật tất cả lỗi
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "=== DEBUG USER CONTROLLER ===<br><br>";

// Test từng bước
try {
    echo "1. Include ConnectDatabase...<br>";
    include_once './Config/ConnectDatabase.php';
    echo "✓ ConnectDatabase OK<br><br>";
    
    echo "2. Include Controller...<br>";
    include_once './Config/Controller.php';
    echo "✓ Controller OK<br><br>";
    
    echo "3. Include Service...<br>";
    include_once './Config/Service.php';
    echo "✓ Service OK<br><br>";
    
    echo "4. Include ProductService...<br>";
    include_once './web/Services/ProductService.php';
    echo "✓ ProductService OK<br><br>";
    
    echo "5. Include CategoryService...<br>";
    include_once './web/Services/CategoryService.php';
    echo "✓ CategoryService OK<br><br>";
    
    echo "6. Include UserController...<br>";
    include_once './web/Controllers/UserController.php';
    echo "✓ UserController OK<br><br>";
    
    echo "7. Create UserController instance...<br>";
    $controller = new UserController();
    echo "✓ UserController created!<br><br>";
    
    echo "8. Call index() method...<br>";
    ob_start();
    $controller->index();
    $content = ob_get_clean();
    echo "✓ index() executed!<br><br>";
    
    echo "9. Output length: " . strlen($content) . " bytes<br><br>";
    
    echo "<hr>";
    echo "<h3>TRANG CHỦ:</h3>";
    echo $content;
    
} catch (Exception $e) {
    echo "<div style='color:red; padding:20px; background:#ffe0e0; border:2px solid red;'>";
    echo "<h2>LỖI:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
} catch (Error $e) {
    echo "<div style='color:red; padding:20px; background:#ffe0e0; border:2px solid red;'>";
    echo "<h2>FATAL ERROR:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
?>

