
<section class="hero" id="home">
    <div class="hero-slider">
        <div class="hero-slide active">
            <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=1200" alt="Coffee Shop Interior">
            <div class="hero-content">
                <h1>CHÀO MỪNG ĐẾN COFFEE HOUSE</h1>
                <p>Trải nghiệm hương vị cà phê đặc biệt trong không gian tối giản, hiện đại</p>
                <a href="?url=UserController/menu" class="btn btn-primary">Xem thực đơn</a>
                <a href="?url=UserController/about" class="btn btn-secondary">Tìm hiểu thêm</a>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section class="about" id="about">
    <div class="about-image">
        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800" alt="About Coffee House">
    </div>

    <div class="about-content">
        <h2>VỀ COFFEE HOUSE</h2>
        <p>Coffee House được thành lập với niềm đam mê mang đến những trải nghiệm cà phê tuyệt vời nhất cho khách hàng.</p>
        <p>Với không gian thiết kế theo phong cách tối giản hiện đại, tông màu trắng chủ đạo kết hợp điểm nhấn xanh matcha nhẹ nhàng.</p>
        <p><strong>Cam kết:</strong><br>✓ 100% cà phê nguyên chất<br>✓ Nguyên liệu tươi ngon<br>✓ Không gian sạch sẽ<br>✓ Phục vụ tận tâm</p>
        <a href="?url=UserController/about" class="btn btn-primary">Xem chi tiết</a>
    </div>
</section>

<!-- MENU SECTION -->
<section class="menu" id="menu">
    <h2>THỰC ĐƠN NỔI BẬT</h2>
    <p style="text-align: center; color: #666; margin-bottom: 40px;">Khám phá những món đồ uống được yêu thích nhất</p>

    <div class="menu-grid">
        <?php if (isset($products) && !empty($products)):
            foreach (array_slice($products, 0, 6) as $product):
                $minPrice = !empty($product->sizes) ? min(array_column($product->sizes, 'price')) : null;
        ?>
            <div class="menu-card">
                <!-- Link đến trang chi tiết sản phẩm -->
                <a href="?url=UserController/productDetail&id=<?= $product->id ?>" style="text-decoration: none; color: inherit;">
                    <div class="menu-card-image">
                        <img src="<?= htmlspecialchars($product->image_url ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </div>
                    <div class="menu-card-content">
                        <h3><?= strtoupper(htmlspecialchars($product->name)) ?></h3>
                        <p><?= htmlspecialchars(mb_strimwidth($product->description, 0, 80, "...")) ?></p>
                        <div class="menu-card-footer">
                            <span class="price"><?= $minPrice ? 'Từ ' . number_format($minPrice, 0, ',', '.') . 'đ' : 'Liên hệ' ?></span>
                            <span class="btn-add">Đặt món →</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">
                <p>Hiện tại chưa có sản phẩm nào. Vui lòng quay lại sau!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- View All Button -->
    <div style="text-align: center; margin-top: 50px;">
        <a href="?url=UserController/menu" class="btn btn-primary" style="display: inline-block; padding: 15px 50px;">Xem tất cả thực đơn →</a>
    </div>
</section>

<!-- LOCATION SECTION -->
<section class="location" id="location">
    <h2>TÌM CHÚNG TÔI</h2>

    <div class="location-grid">
        <div class="location-info">
            <?php
            $locations = [
                ['icon' => '📍', 'title' => 'ĐỊA CHỈ', 'text' => '123 Đường Nguyễn Huệ, Quận 1<br>TP. Hồ Chí Minh, Việt Nam'],
                ['icon' => '📞', 'title' => 'ĐIỆN THOẠI', 'text' => 'Hotline: 1900 8888<br>Mobile: 0901 234 567'],
                ['icon' => '⏰', 'title' => 'GIỜ MỞ CỬA', 'text' => 'T2 - T6: 7:00 - 22:00<br>T7 - CN: 8:00 - 23:00'],
                ['icon' => '✉️', 'title' => 'EMAIL', 'text' => 'info@coffeehouse.vn<br>support@coffeehouse.vn']
            ];
            foreach ($locations as $loc):
            ?>
            <div class="location-item">
                <div class="location-icon"><?= $loc['icon'] ?></div>
                <div class="location-text">
                    <h3><?= $loc['title'] ?></h3>
                    <p><?= $loc['text'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4958724619744!2d106.70204431533431!3d10.776543992320892!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f4b3330bcc9%3A0xb46eb6d3d302f7f4!2zTmd1eeG7hW4gSHXhu4UsIFF1YW4gMQ!5e0!3m2!1svi!2s!4v1234567890123!5m2!1svi!2s"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>
