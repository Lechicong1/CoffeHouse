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
            <a href="/COFFEE_PHP/User/cart" style="text-decoration: none; color: inherit;">
                🛒
                <span class="cart-count" id="cart-count">0</span>
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

<!-- Thông báo (Success/Error) -->
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

<style>
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.alert {
    animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s;
}

@keyframes fadeOut {
    to {
        opacity: 0;
        transform: translateX(100%);
    }
}
</style>

<script>
// Tự động ẩn thông báo sau 3 giây
setTimeout(() => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.style.display = 'none';
    });
}, 3000);
</script>
