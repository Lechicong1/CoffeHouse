<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/admin.php
 * Admin Dashboard - Layout chính với khả năng load dynamic sections
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin (uncomment khi đã có hệ thống auth)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'admin') {
//     header('Location: /web/Views/Auth/Login/login.html');
//     exit;
// }

// Lấy section hiện tại từ URL (mặc định là dashboard)
$currentSection = $_GET['section'] ?? 'dashboard';

// Danh sách các section được phép truy cập
$allowedSections = [
    'dashboard',
    'orders',
    'products',
    'customers',
    'employees',
    'revenue',
    'settings'
];

// Validate section
if (!in_array($currentSection, $allowedSections)) {
    $currentSection = 'dashboard';
}

// Lấy thông tin admin từ session
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House Admin - <?= ucfirst($currentSection) ?></title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Component (Tái sử dụng cho tất cả các trang) -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Header Component (Tái sử dụng cho tất cả các trang) -->
            <?php include __DIR__ . '/partials/header.php'; ?>

            <!-- Dynamic Content Wrapper -->
            <div class="content-wrapper">
                <?php 
                // Load section tương ứng động
                $sectionFile = __DIR__ . "/sections/{$currentSection}.php";
                
                if (file_exists($sectionFile)) {
                    include $sectionFile;
                } else {
                    // Fallback nếu không tìm thấy section
                    echo '<div class="error-message">';
                    echo '<h2>⚠️ Không tìm thấy trang</h2>';
                    echo '<p>Section "' . htmlspecialchars($currentSection) . '" không tồn tại.</p>';
                    echo '<a href="?section=dashboard" class="btn-primary">← Quay về Dashboard</a>';
                    echo '</div>';
                }
                ?>
            </div>

            <!-- Modal Component (Tái sử dụng cho tất cả các trang) -->
            <?php include __DIR__ . '/partials/modal.php'; ?>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="admin-script.js"></script>
    <script>
        // Truyền section hiện tại vào JavaScript
        window.CURRENT_SECTION = '<?= $currentSection ?>';
    </script>
</body>
</html>
