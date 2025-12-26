<?php
/**
 * filepath: /home/cong/lampp/htdocs/COFFEE_PHP/web/Views/AdminDashBoard/MasterLayout.php
 * Master Layout - Admin Dashboard (Minimalist White Design)
 * Tích hợp tất cả: Sidebar, Header, Content, Modal trong một file
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
    <base href="http://localhost/COFFEE_PHP/">
    <link rel="stylesheet" href="web/Views/AdminDashBoard/admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar" id="sidebar">
            <!-- Brand -->
            <div class="sidebar-header">
                <span class="brand-icon">☕</span>
                <span class="brand-name">COFFEE HOUSE</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="menu-list">
                    <li class="menu-item <?= $currentSection === 'dashboard' ? 'active' : '' ?>" data-target="dashboard">
                        <a href="/COFFEE_PHP/admin/dashboard" class="menu-link">
                            <span class="menu-icon">📊</span>
                            <span class="menu-text">DASHBOARD</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'products' ? 'active' : '' ?>" data-target="products">
                        <a href="/COFFEE_PHP/admin/products" class="menu-link">
                            <span class="menu-icon">🛒</span>
                            <span class="menu-text">SẢN PHẨM</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'orders' ? 'active' : '' ?>" data-target="orders">
                        <a href="/COFFEE_PHP/admin/orders" class="menu-link">
                            <span class="menu-icon">📦</span>
                            <span class="menu-text">ĐƠN HÀNG</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'employees' ? 'active' : '' ?>" data-target="employees">
                        <a href="/COFFEE_PHP/admin/employees" class="menu-link">
                            <span class="menu-icon">👥</span>
                            <span class="menu-text">NHÂN VIÊN</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'customers' ? 'active' : '' ?>" data-target="customers">
                        <a href="/COFFEE_PHP/admin/customers" class="menu-link">
                            <span class="menu-icon">👤</span>
                            <span class="menu-text">KHÁCH HÀNG</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'promotions' ? 'active' : '' ?>" data-target="promotions">
                        <a href="/COFFEE_PHP/admin/promotions" class="menu-link">
                            <span class="menu-icon">🎁</span>
                            <span class="menu-text">KHUYẾN MÃI</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'reports' ? 'active' : '' ?>" data-target="reports">
                        <a href="/COFFEE_PHP/admin/reports" class="menu-link">
                            <span class="menu-icon">📈</span>
                            <span class="menu-text">BÁO CÁO</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $currentSection === 'settings' ? 'active' : '' ?>" data-target="settings">
                        <a href="/COFFEE_PHP/admin/settings" class="menu-link">
                            <span class="menu-icon">⚙️</span>
                            <span class="menu-text">CÀI ĐẶT</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Logout -->
            <div class="sidebar-footer">
                <a href="/COFFEE_PHP/logout" class="menu-link logout-link">
                    <span class="menu-icon">🚪</span>
                    <span class="menu-text">ĐĂNG XUẤT</span>
                </a>
            </div>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content" id="mainContent">
            <!-- ========== HEADER ========== -->
            <header class="header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <span class="toggle-icon">☰</span>
                    </button>
                    <h1 class="page-title"><?= strtoupper($currentSection) ?></h1>
                </div>

                <div class="header-right">
                    <!-- Search Bar -->
                    <div class="search-box">
                        <input type="text" placeholder="Tìm kiếm..." class="search-input">
                        <span class="search-icon">🔍</span>
                    </div>

                    <!-- Notifications -->
                    <button class="header-btn notification-btn">
                        <span class="btn-icon">🔔</span>
                        <span class="badge">3</span>
                    </button>

                    <!-- User Profile -->
                    <div class="user-profile">
                        <div class="user-avatar">
                            <span><?= substr($adminName, 0, 1) ?></span>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?= htmlspecialchars($adminName) ?></span>
                            <span class="user-role"><?= htmlspecialchars($adminRole) ?></span>
                        </div>
                    </div>
                </div>
            </header>

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
        </main>

        <!-- ========== MODAL - Generic Modal for Forms ========== -->
        <div id="modalOverlay" class="modal-overlay hidden">
            <div class="modal-container">
                <div class="modal-header">
                    <h2 id="modalTitle" class="modal-title">TIÊU ĐỀ MODAL</h2>
                    <button class="close-modal">✕</button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Dynamic content will be injected here by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary close-modal">HỦY</button>
                    <button class="btn btn-primary" id="modalSubmitBtn">LƯU</button>
                </div>
            </div>
        </div>

        <!-- ========== CONFIRM MODAL - For Delete Confirmations ========== -->
        <div id="confirmModal" class="modal-overlay hidden">
            <div class="modal-container modal-small">
                <div class="modal-header">
                    <h2 class="modal-title">XÁC NHẬN</h2>
                    <button class="close-confirm">✕</button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary close-confirm">HỦY</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn">XÓA</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="web/Views/AdminDashBoard/admin-script.js"></script>
    <script>
        // Truyền section hiện tại vào JavaScript
        window.CURRENT_SECTION = '<?= $currentSection ?>';
    </script>
</body>
</html>

