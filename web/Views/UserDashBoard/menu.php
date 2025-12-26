<!-- ===================================
     FILE: menu.php
     MÔ TẢ: Trang Menu đầy đủ (Dynamic)
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Thực đơn Coffee House - Cà phê, Trà sữa, Đồ ăn vặt">
    <title>Thực đơn - Coffee House</title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-menu-style.css">
</head>
<body>
    <!-- HEADER & NAVIGATION -->
    <header>
        <nav>
            <a href="/COFFEE_PHP/User/index" class="logo">COFFEE HOUSE</a>

            <ul class="nav-menu">
                <li><a href="/COFFEE_PHP/User/index">Trang chủ</a></li>
                <li><a href="/COFFEE_PHP/User/index#about">Giới thiệu</a></li>
                <li><a href="/COFFEE_PHP/User/menu" class="active">Thực đơn</a></li>
                <li><a href="/COFFEE_PHP/User/index#location">Địa chỉ</a></li>
                <li><a href="/COFFEE_PHP/User/about">Về chúng tôi</a></li>
            </ul>
            
            <div class="auth-buttons">
                <a href="/COFFEE_PHP/Auth/login" class="btn-login">Đăng nhập</a>
                <a href="/COFFEE_PHP/Auth/register" class="btn-register">Đăng ký</a>
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

    <!-- MENU HERO -->
    <section class="menu-hero">
        <div class="menu-hero-content">
            <h1>THỰC ĐƠN</h1>
            <p>Khám phá hương vị đặc biệt từ Coffee House</p>
        </div>
    </section>

    <!-- CATEGORIES SHOWCASE -->
    <section class="categories-section">
        <?php if (isset($categories) && !empty($categories)): ?>
            <?php
            $index = 0;
            foreach ($categories as $category):
                $isReverse = ($index % 2 != 0) ? 'reverse' : '';
                $index++;
            ?>
            <div class="category-showcase <?= $isReverse ?>" id="category-<?= $category->id ?>">
                <div class="category-content">
                    <div class="category-text">
                        <h2><?= strtoupper(htmlspecialchars($category->name)) ?></h2>
                        <p><?= htmlspecialchars($category->description) ?></p>
                        <button class="btn-discover" onclick="window.location.href='/COFFEE_PHP/User/categoryProducts?id=<?= $category->id ?>'">
                            KHÁM PHÁ THÊM
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--text-light);">
                <h3>Hiện tại chưa có danh mục nào</h3>
                <p>Vui lòng quay lại sau để khám phá thực đơn của chúng tôi!</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- FOOTER -->
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
                    <a href="User/index">Trang chủ</a>
                    <a href="User/index#about">Giới thiệu</a>
                    <a href="User/menu">Thực đơn</a>
                    <a href="User/index#location">Địa chỉ</a>
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
                    <p>📍 123 Nguyễn Huệ, Q.1, TP.HCM</p>
                    <p>📞 1900 8888</p>
                    <p>✉️ info@coffeehouse.vn</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Coffee House. All Rights Reserved. Made with ❤️ in Vietnam</p>
            </div>
        </div>
    </footer>
</body>
</html>
