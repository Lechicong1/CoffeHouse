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

        <div class="auth-buttons">
            <a href="/COFFEE_PHP/Auth/login" class="btn-login">Đăng nhập</a>
            <a href="/COFFEE_PHP/Auth/showSignup" class="btn-register">Đăng ký</a>
        </div>

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