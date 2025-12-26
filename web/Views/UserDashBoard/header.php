<!-- ===================================
     FILE: header.php
     MÔ TẢ: Header chung cho tất cả trang UserDashBoard
     =================================== -->
<!-- HEADER & NAVIGATION -->
<header>
    <nav>
        <a href="/COFFEE_PHP/User/index" class="logo">COFFEE HOUSE</a>

        <ul class="nav-menu">
            <li><a href="/COFFEE_PHP/User/index" class="<?= ($currentPage ?? '') === 'index' ? 'active' : '' ?>">Trang chủ</a></li>
            <li><a href="/COFFEE_PHP/User/index#about" class="<?= ($currentPage ?? '') === 'about-section' ? 'active' : '' ?>">Giới thiệu</a></li>
            <li><a href="/COFFEE_PHP/User/menu" class="<?= ($currentPage ?? '') === 'menu' ? 'active' : '' ?>">Thực đơn</a></li>
            <li><a href="/COFFEE_PHP/User/index#location" class="<?= ($currentPage ?? '') === 'location' ? 'active' : '' ?>">Địa chỉ</a></li>
            <li><a href="/COFFEE_PHP/User/about" class="<?= ($currentPage ?? '') === 'about' ? 'active' : '' ?>">Về chúng tôi</a></li>
        </ul>

        <?php
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $currentUser = $_SESSION['user'] ?? null;

        if ($currentUser && isset($currentUser['type']) && $currentUser['type'] === 'customer') :
        ?>
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
            🛒
            <span class="cart-count">0</span>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>