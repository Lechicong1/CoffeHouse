<?php
/**
 * INDEX.PHP - Entry point chính của ứng dụng (Root level)
 * File này có thể được dùng thay cho public/index.php
 */

// Bật output buffering và session
ob_start();
session_start();

// Include các file Core/Config (tương đương MVC/Core cũ)
include_once './Config/Database.php';      // Thay ./MVC/Core/connectDB.php
include_once './Config/Controller.php';    // Thay ./MVC/Core/controller.php
include_once './Config/Router.php';        // Thay ./MVC/Core/app.php

include_once './web/brigde.php';

// Khởi tạo ứng dụng (Router sẽ tự động xử lý routing)
$myapp = new Router();

// Kết thúc output buffering
ob_end_flush();
?>
