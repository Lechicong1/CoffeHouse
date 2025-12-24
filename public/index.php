<?php
/**
 * Entry Point - Điểm vào chính của ứng dụng
 * File này sẽ được Apache/Nginx gọi đầu tiên
 */

// Bật hiển thị lỗi (chỉ dùng khi Development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include các file Config cần thiết
require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Config/Controller.php';
require_once __DIR__ . '/../Config/Router.php';

// Khởi tạo Router - Router sẽ tự động xử lý URL và gọi Controller
$app = new Router();
