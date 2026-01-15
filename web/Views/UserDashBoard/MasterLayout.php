<?php
/**
 * filepath: /home/cong/lampp/htdocs/COFFEE_PHP/web/Views/UserDashBoard/MasterLayout.php
 * Master Layout - User Dashboard (Minimalist White Design)
 * Tích hợp Header, Footer, và Dynamic Content trong một file
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy thông tin user từ session
$currentUser = $_SESSION['user'] ?? null;

// Xác định trang hiện tại từ section
$currentSection = $data['section'] ?? 'home';
$currentPage = $data['currentPage'] ?? $currentSection;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $data['description'] ?? 'Coffee House - Quán cà phê phong cách tối giản, hiện đại' ?>">
    <title><?= $data['title'] ?? 'Coffee House - Trải nghiệm cà phê đặc biệt' ?></title>
    <base href="http://localhost/COFFEE_PHP/">
    <link rel="stylesheet" href="Public/Css/user-style.css">
    <?php if (isset($data['additionalCSS'])): ?>
        <?php foreach ($data['additionalCSS'] as $css): ?>
            <link rel="stylesheet" href="<?= $css . '?v=' . time() ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- ========== HEADER & NAVIGATION ========== -->
    <header>
        <nav>
            <a href="/COFFEE_PHP/User/index" class="logo">COFFEE HOUSE</a>

            <ul class="nav-menu">
                <li><a href="/COFFEE_PHP/User/index" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Trang chủ</a></li>
                <li><a href="/COFFEE_PHP/User/index#about" class="<?= $currentPage === 'about-section' ? 'active' : '' ?>">Giới thiệu</a></li>
                <li><a href="/COFFEE_PHP/User/menu" class="<?= $currentPage === 'menu' ? 'active' : '' ?>">Thực đơn</a></li>
                <li><a href="/COFFEE_PHP/User/index#location" class="<?= $currentPage === 'location' ? 'active' : '' ?>">Địa chỉ</a></li>

                <?php if ($currentUser && isset($currentUser['type']) && $currentUser['type'] === 'customer'): ?>
                    <li><a href="/COFFEE_PHP/OrderController/GetData" class="<?= $currentPage === 'myOrders' ? 'active' : '' ?>">📋 Đơn hàng</a></li>
                <?php endif; ?>
            </ul>

            <?php if ($currentUser && isset($currentUser['type']) && $currentUser['type'] === 'customer'): ?>
                <div class="user-profile">
                    <span class="user-greeting">Xin chào, <?= htmlspecialchars($currentUser['fullname'] ?? $currentUser['username']) ?></span>
                    <a href="/COFFEE_PHP/Auth/logout" class="user-logout">Đăng xuất</a>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/COFFEE_PHP/Auth/login" class="btn-login">Đăng nhập</a>
                    <a href="/COFFEE_PHP/Auth/showSignup" class="btn-register">Đăng ký</a>
                </div>
            <?php endif; ?>

            <div class="cart-icon">
                <a href="/COFFEE_PHP/CartController" style="text-decoration: none; color: inherit;">
                    🛒
                </a>
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- ========== NOTIFICATIONS ========== -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="position: fixed; top: 80px; right: 20px; z-index: 9999; background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); animation: slideIn 0.3s ease;">
            ✓ <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error" style="position: fixed; top: 80px; right: 20px; z-index: 9999; background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); animation: slideIn 0.3s ease;">
            ✗ <?= htmlspecialchars($_SESSION['error_message']) ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- ========== DYNAMIC CONTENT - View con được include vào đây ========== -->
    <main class="main-content">
        <?php 
            // Include view con dựa vào tham số 'page' từ Controller
            if (isset($data['page'])) {
                $pageFile = __DIR__ . '/Pages/' . $data['page'] . '.php';
                
                if (file_exists($pageFile)) {
                    include_once $pageFile;
                } else {
                    echo '<div class="error-message" style="text-align: center; padding: 100px 20px;">';
                    echo '<h2>⚠️ Không tìm thấy trang</h2>';
                    echo '<p>Trang "' . htmlspecialchars($data['page']) . '" không tồn tại.</p>';
                    echo '</div>';
                }
            } else {
                echo '<div class="error-message" style="text-align: center; padding: 100px 20px;">';
                echo '<h2>⚠️ Lỗi</h2>';
                echo '<p>Không có trang nào được chỉ định.</p>';
                echo '</div>';
            }
        ?>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>COFFEE HOUSE</h3>
                    <p>Mang đến trải nghiệm cà phê tuyệt vời nhất với không gian hiện đại và dịch vụ tận tâm.</p>
                    <div class="social-links">
                        <a href="#" class="social-icon">f</a>
                        <a href="#" class="social-icon">📷</a>
                        <a href="#" class="social-icon">T</a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>LIÊN KẾT</h3>
                    <a href="/COFFEE_PHP/User/index">Trang chủ</a>
                    <a href="/COFFEE_PHP/User/index#about">Giới thiệu</a>
                    <a href="/COFFEE_PHP/User/menu">Thực đơn</a>
                    <a href="/COFFEE_PHP/User/index#location">Địa chỉ</a>
                </div>
                
                <div class="footer-section">
                    <h3>CHÍNH SÁCH</h3>
                    <a href="#">Chính sách bảo mật</a>
                    <a href="#">Điều khoản sử dụng</a>
                    <a href="#">Chính sách đổi trả</a>
                    <a href="#">Hướng dẫn đặt hàng</a>
                </div>
                
                <div class="footer-section">
                    <h3>LIÊN HỆ</h3>
                    <p>📍 138/58 Tân Triều</p>
                    <p>📞 0862137882</p>
                    <p>✉️ info@coffeehouse.vn</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Coffee House. All Rights Reserved. Made with ❤️ in Vietnam</p>
            </div>
        </div>
    </footer>

    <!-- ========== JAVASCRIPT - CHỈ DÙNG KHI CẦN ========== -->
    <?php if (isset($data['additionalJS'])): ?>
        <?php foreach ($data['additionalJS'] as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
