<!-- ===================================
     FILE: product-detail.php
     MÔ TẢ: Trang chi tiết sản phẩm (Dynamic)
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($product) ? htmlspecialchars($product->description) : 'Chi tiết sản phẩm' ?>">
    <title><?= isset($product) ? htmlspecialchars($product->name) : 'Chi tiết sản phẩm' ?> - Coffee House</title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-product-detail.css">
</head>
<body>
    <!-- HEADER & NAVIGATION -->
    <header>
        <nav>
            <a href="/COFFEE_PHP/User/index" class="logo">COFFEE HOUSE</a>

            <ul class="nav-menu">
                <li><a href="/COFFEE_PHP/User/index">Trang chủ</a></li>
                <li><a href="/COFFEE_PHP/User/index#about">Giới thiệu</a></li>
                <li><a href="/COFFEE_PHP/User/menu">Thực đơn</a></li>
                <li><a href="/COFFEE_PHP/User/index#location">Địa chỉ</a></li>
                <li><a href="/COFFEE_PHP/User/about">Về chúng tôi</a></li>
            </ul>
            
            <div class="auth-buttons">
                <a href="Auth/login" class="btn-login">Đăng nhập</a>
                <a href="Auth/register" class="btn-register">Đăng ký</a>
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

    <?php if (isset($product)): ?>
    <!-- PRODUCT DETAIL SECTION -->
    <section class="product-detail-section">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/COFFEE_PHP/User/index">Trang chủ</a>
            <span>›</span>
            <a href="/COFFEE_PHP/User/menu">Thực đơn</a>
            <span>›</span>
            <a href="/COFFEE_PHP/User/categoryProducts?id=<?= $product->category_id ?>"><?= isset($category) ? htmlspecialchars($category->name) : 'Danh mục' ?></a>
            <span>›</span>
            <strong><?= htmlspecialchars($product->name) ?></strong>
        </div>

        <div class="product-container">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image">
                    <?php if (!empty($product->image_url)): ?>
                        <img src="/COFFEE_PHP/<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    <?php else: ?>
                        <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800" alt="<?= htmlspecialchars($product->name) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info">
                <span class="product-category"><?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?></span>
                <h1><?= htmlspecialchars($product->name) ?></h1>

                <div class="product-rating">
                    <div class="stars">★★★★★</div>
                    <span class="rating-count">(4.9 - 128 đánh giá)</span>
                </div>

                <div class="product-price" id="product-price">
                    <?php
                    if (!empty($product->sizes)) {
                        $prices = array_column($product->sizes, 'price');
                        $minPrice = min($prices);
                        $maxPrice = max($prices);
                        if ($minPrice == $maxPrice) {
                            echo number_format($minPrice, 0, ',', '.') . 'đ';
                        } else {
                            echo number_format($minPrice, 0, ',', '.') . 'đ - ' . number_format($maxPrice, 0, ',', '.') . 'đ';
                        }
                    } else {
                        echo 'Liên hệ';
                    }
                    ?>
                </div>

                <div class="product-description">
                    <p><?= nl2br(htmlspecialchars($product->description)) ?></p>
                </div>

                <!-- Product Options -->
                <div class="product-options">
                    <?php if (!empty($product->sizes)): ?>
                    <!-- Size -->
                    <div class="option-group">
                        <label>Kích thước</label>
                        <div class="size-options">
                            <?php foreach ($product->sizes as $index => $size): ?>
                                <button class="size-btn <?= $index === 0 ? 'active' : '' ?>"
                                        data-size="<?= htmlspecialchars($size->size_name) ?>"
                                        data-price="<?= $size->price ?>">
                                    Size <?= htmlspecialchars($size->size_name) ?> - <?= number_format($size->price, 0, ',', '.') ?>đ
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Quantity -->
                <div class="quantity-selector">
                    <label>Số lượng</label>
                    <div class="quantity-controls">
                        <button class="quantity-btn" id="decrease-qty">-</button>
                        <span class="quantity-value" id="quantity">1</span>
                        <button class="quantity-btn" id="increase-qty">+</button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="product-actions">
                    <button class="btn-add-cart" id="add-to-cart">
                        🛒 Thêm vào giỏ hàng
                    </button>
                    <button class="btn-buy-now" id="buy-now">
                        ⚡ Đặt hàng ngay
                    </button>
                </div>

                <!-- Product Meta -->
                <div class="product-meta">
                    <div class="meta-item">
                        <div class="meta-icon">📦</div>
                        <div class="meta-text">
                            <strong>Giao hàng</strong>
                            <span>15-30 phút</span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">✓</div>
                        <div class="meta-text">
                            <strong>Chất lượng</strong>
                            <span>100% nguyên chất</span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">🔥</div>
                        <div class="meta-text">
                            <strong>Nhiệt độ</strong>
                            <span>Nóng / Đá</span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-icon">💚</div>
                        <div class="meta-text">
                            <strong>Đánh giá</strong>
                            <span>4.9/5 ★</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="description">Mô tả</button>
                <button class="tab-btn" data-tab="reviews">Đánh giá</button>
            </div>

            <!-- Tab: Description -->
            <div class="tab-content active" id="tab-description">
                <h3>Mô tả chi tiết</h3>
                <p style="line-height: 1.8; color: var(--text-light);">
                    <?= nl2br(htmlspecialchars($product->description)) ?>
                </p>
            </div>

            <!-- Tab: Reviews -->
            <div class="tab-content" id="tab-reviews">
                <h3>Đánh giá từ khách hàng</h3>
                <p style="color: var(--text-light); padding: 20px 0;">
                    Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!
                </p>
            </div>
        </div>

        <?php if (isset($relatedProducts) && !empty($relatedProducts)): ?>
        <!-- Related Products -->
        <div class="related-products">
            <h2>SẢN PHẨM LIÊN QUAN</h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $relProduct):
                    $minPrice = null;
                    if (!empty($relProduct->sizes)) {
                        $prices = array_column($relProduct->sizes, 'price');
                        $minPrice = min($prices);
                    }
                ?>
                <div class="product-card">
                    <a href="/COFFEE_PHP/User/productDetail?id=<?= $relProduct->id ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-image">
                            <?php if (!empty($relProduct->image_url)): ?>
                                <img src="/COFFEE_PHP/<?= htmlspecialchars($relProduct->image_url) ?>" alt="<?= htmlspecialchars($relProduct->name) ?>">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500" alt="<?= htmlspecialchars($relProduct->name) ?>">
                            <?php endif; ?>
                        </div>
                        <div class="product-content">
                            <h3><?= strtoupper(htmlspecialchars($relProduct->name)) ?></h3>
                            <div class="product-footer">
                                <span class="price">
                                    <?php if ($minPrice): ?>
                                        Từ <?= number_format($minPrice, 0, ',', '.') ?>đ
                                    <?php else: ?>
                                        Liên hệ
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <?php else: ?>
    <section style="padding: 100px 20px; text-align: center;">
        <h2>Không tìm thấy sản phẩm</h2>
        <p style="color: var(--text-light); margin: 20px 0;">Sản phẩm bạn đang tìm không tồn tại hoặc đã bị xóa.</p>
        <a href="/COFFEE_PHP/User/menu" class="btn btn-primary">Quay lại thực đơn</a>
    </section>
    <?php endif; ?>

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

    <script>
        // Simple quantity control
        document.getElementById('decrease-qty')?.addEventListener('click', function() {
            const qtyEl = document.getElementById('quantity');
            let qty = parseInt(qtyEl.textContent);
            if (qty > 1) {
                qtyEl.textContent = qty - 1;
            }
        });

        document.getElementById('increase-qty')?.addEventListener('click', function() {
            const qtyEl = document.getElementById('quantity');
            let qty = parseInt(qtyEl.textContent);
            qtyEl.textContent = qty + 1;
        });

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            });
        });

        // Size selection
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>
