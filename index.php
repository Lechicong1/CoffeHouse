<?php
/**
 * INDEX.PHP - Entry point chính của ứng dụng (Root level)
 */

// BẬT HIỂN THỊ LỖI ĐỂ DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bật output buffering và session
ob_start();
session_start();

// Include các file Core/Config
include_once './Config/ConnectDatabase.php';
include_once './Config/Controller.php';
include_once './Config/Router.php';
include './Config/Service.php';

include_once './web/brigde.php';

try {
    // Khởi tạo ứng dụng
    $myapp = new Router();
} catch (Exception $e) {
    echo "<h1>Lỗi hệ thống:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Kết thúc output buffering
ob_end_flush();
?>
