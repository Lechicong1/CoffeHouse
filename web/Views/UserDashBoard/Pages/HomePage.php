<!-- ===================================
     FILE: index.php
     MÔ TẢ: Trang chủ Coffee House - Minimalist White Style (Dynamic)
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Coffee House - Quán cà phê phong cách tối giản, hiện đại">
    <title><?php echo $data['title'] ?? 'Coffee House - Trải nghiệm cà phê đặc biệt'; ?></title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
</head>
<body>
    <?php
    $currentPage = 'index';
    include __DIR__ . '/../header.php';
    ?>

    <!-- HERO SECTION WITH SLIDER -->
    <section class="hero" id="home">
        <div class="hero-slider">
            <!-- Slide 1 -->
            <div class="hero-slide active">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1200" alt="Coffee Shop Interior">
                <div class="hero-content">
                    <h1>CHÀO MỪNG ĐẾN COFFEE HOUSE</h1>
                    <p>Trải nghiệm hương vị cà phê đặc biệt trong không gian tối giản, hiện đại</p>
                    <a href="#menu" class="btn btn-primary">Xem thực đơn</a>
                    <a href="#about" class="btn btn-secondary">Tìm hiểu thêm</a>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="hero-slide">
                <img src="https://images.unsplash.com/photo-1511920170033-f8396924c348?w=1200" alt="Coffee Drinks">
                <div class="hero-content">
                    <h1>ĐỒ UỐNG ĐẶC SẮC</h1>
                    <p>Từ cà phê truyền thống đến các loại trà hiện đại</p>
                    <a href="#menu" class="btn btn-primary">Đặt món ngay</a>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="hero-slide">
                <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=1200" alt="Cozy Cafe">
                <div class="hero-content">
                    <h1>KHÔNG GIAN THÂN THIỆN</h1>
                    <p>Nơi lý tưởng để làm việc, gặp gỡ và thư giãn</p>
                    <a href="#location" class="btn btn-primary">Tìm cửa hàng</a>
                </div>
            </div>
        </div>
        
        <!-- Slider Dots -->
        <div class="slider-controls">
            <span class="slider-dot active"></span>
            <span class="slider-dot"></span>
            <span class="slider-dot"></span>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about" id="about">
        <div class="about-image">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800" alt="About Coffee House">
        </div>
        
        <div class="about-content">
            <h2>VỀ COFFEE HOUSE</h2>
            <p>
                Coffee House được thành lập với niềm đam mê mang đến những trải nghiệm 
                cà phê tuyệt vời nhất cho khách hàng. Chúng tôi tin rằng mỗi tách cà phê 
                không chỉ là đồ uống, mà là một câu chuyện, một khoảnh khắc đáng nhớ.
            </p>
            <p>
                Với không gian thiết kế theo phong cách tối giản hiện đại, tông màu trắng 
                chủ đạo kết hợp điểm nhấn xanh matcha nhẹ nhàng, Coffee House tạo nên một 
                môi trường lý tưởng cho mọi hoạt động từ làm việc, gặp gỡ bạn bè đến thư giãn.
            </p>
            <p>
                <strong>Cam kết của chúng tôi:</strong><br>
                ✓ 100% cà phê nguyên chất, không pha trộn<br>
                ✓ Nguyên liệu tươi ngon, được chọn lọc kỹ càng<br>
                ✓ Không gian sạch sẽ, thoáng mát<br>
                ✓ Phục vụ tận tâm, chu đáo
            </p>
            <a href="about.php" class="btn btn-primary">Xem chi tiết</a>
        </div>
    </section>

    <!-- MENU SECTION -->
    <section class="menu" id="menu">
        <h2>THỰC ĐƠN NỔI BẬT</h2>
        <p style="text-align: center; color: var(--text-light); margin-bottom: 40px; font-size: 1.1rem;">
            Khám phá những món đồ uống và ăn vặt được yêu thích nhất tại Coffee House
        </p>
        
        <div class="menu-grid">
            <?php if (isset($products) && !empty($products)): ?>
                <?php
                // Giới hạn chỉ hiển thị 6 sản phẩm đầu tiên trên trang chủ
                $displayProducts = array_slice($products, 0, 6);
                foreach ($displayProducts as $product):
                    // Lấy giá nhỏ nhất từ các size
                    $minPrice = null;
                    if (!empty($product->sizes)) {
                        $prices = array_column($product->sizes, 'price');
                        $minPrice = min($prices);
                    }
                ?>
                <div class="menu-card" data-product-id="<?= $product->id ?>">
                    <a href="/COFFEE_PHP/User/productDetail?id=<?= $product->id ?>" style="text-decoration: none; color: inherit;">
                        <div class="menu-card-image">
                            <?php if (!empty($product->image_url)): ?>
                                <img src="/COFFEE_PHP/<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500" alt="<?= htmlspecialchars($product->name) ?>">
                            <?php endif; ?>
                            <?php if ($product->created_at && strtotime($product->created_at) > strtotime('-7 days')): ?>
                                <span class="menu-badge">Mới</span>
                            <?php endif; ?>
                        </div>
                        <div class="menu-card-content">
                            <h3><?= strtoupper(htmlspecialchars($product->name)) ?></h3>
                            <p><?= htmlspecialchars($product->description) ?></p>
                            <div class="menu-card-footer">
                                <span class="price">
                                    <?php if ($minPrice): ?>
                                        Từ <?= number_format($minPrice, 0, ',', '.') ?>đ
                                    <?php else: ?>
                                        Liên hệ
                                    <?php endif; ?>
                                </span>
                                <button class="btn-add" onclick="event.preventDefault(); event.stopPropagation(); window.location.href='/COFFEE_PHP/User/productDetail?id=<?= $product->id ?>'">Đặt món</button>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--text-light);">
                    <p>Hiện tại chưa có sản phẩm nào. Vui lòng quay lại sau!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- View All Button -->
        <div style="text-align: center; margin-top: 50px;">
            <a href="/COFFEE_PHP/User/menu" class="btn btn-primary" style="display: inline-block; padding: 15px 50px; font-size: 1.1rem;">
                Xem tất cả thực đơn →
            </a>
        </div>
    </section>

    <!-- LOCATION SECTION -->
    <section class="location" id="location">
        <h2>TÌM CHÚNG TÔI</h2>
        
        <div class="location-grid">
            <div class="location-info">
                <div class="location-item">
                    <div class="location-icon">📍</div>
                    <div class="location-text">
                        <h3>ĐỊA CHỈ</h3>
                        <p>123 Đường Nguyễn Huệ, Quận 1<br>Thành phố Hồ Chí Minh, Việt Nam</p>
                    </div>
                </div>
                
                <div class="location-item">
                    <div class="location-icon">📞</div>
                    <div class="location-text">
                        <h3>ĐIỆN THOẠI</h3>
                        <p>Hotline: 1900 8888<br>Mobile: 0901 234 567</p>
                    </div>
                </div>
                
                <div class="location-item">
                    <div class="location-icon">⏰</div>
                    <div class="location-text">
                        <h3>GIỜ MỞ CỬA</h3>
                        <p>Thứ 2 - Thứ 6: 7:00 - 22:00<br>Thứ 7 - Chủ nhật: 8:00 - 23:00</p>
                    </div>
                </div>
                
                <div class="location-item">
                    <div class="location-icon">✉️</div>
                    <div class="location-text">
                        <h3>EMAIL</h3>
                        <p>info@coffeehouse.vn<br>support@coffeehouse.vn</p>
                    </div>
                </div>
            </div>
            
            <div class="map-container">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4958724619744!2d106.70204431533431!3d10.776543992320892!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b3330bcc9%3A0xb46eb6d3d302f7f4!2zTmd1eeG7hW4gSHXhu4csIFF1YW4gMQ!5e0!3m2!1svi!2s!4v1234567890123!5m2!1svi!2s" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
