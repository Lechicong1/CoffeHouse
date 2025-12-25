<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/MasterLayout.php
 * Master Layout - View cha chứa các phần tái sử dụng
 * Các view con sẽ được include vào đây thông qua tham số $data['page']
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

// Lấy thông tin admin từ session
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';

// Xác định section hiện tại từ page name
$currentSection = $data['section'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House Admin - <?= ucfirst($currentSection) ?></title>
    <link rel="stylesheet" href="/web/Views/AdminDashBoard/admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- ========== SIDEBAR - Tái sử dụng cho tất cả trang ========== -->
        <aside class="sidebar-wrapper">
            <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content" id="mainContent">
            <!-- ========== HEADER - Tái sử dụng cho tất cả trang ========== -->
            <div class="header-wrapper">
                <?php include_once __DIR__ . '/partials/header.php'; ?>
            </div>

            <!-- ========== DYNAMIC CONTENT - View con được include vào đây ========== -->
            <div class="content-wrapper">
                <?php 
                    // Include view con dựa vào tham số 'page' từ Controller
                    if (isset($data['page'])) {
                        $pageFile = __DIR__ . '/Pages/' . $data['page'] . '.php';
                        
                        if (file_exists($pageFile)) {
                            include_once $pageFile;
                        } else {
                            echo '<div class="error-message">';
                            echo '<h2>⚠️ Không tìm thấy trang</h2>';
                            echo '<p>Trang "' . htmlspecialchars($data['page']) . '" không tồn tại.</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="error-message">';
                        echo '<h2>⚠️ Lỗi</h2>';
                        echo '<p>Không có trang nào được chỉ định.</p>';
                        echo '</div>';
                    }
                ?>
            </div>

            <!-- ========== MODAL - Tái sử dụng cho tất cả trang ========== -->
            <?php include_once __DIR__ . '/partials/modal.php'; ?>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="/web/Views/AdminDashBoard/admin-script.js"></script>
    <script>
        // Truyền section hiện tại vào JavaScript
        window.CURRENT_SECTION = '<?= $currentSection ?>';
    </script>
</body>
</html>

