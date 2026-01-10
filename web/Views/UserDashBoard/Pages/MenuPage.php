<!-- ===================================
     FILE: MenuPage.php
     MÔ TẢ: Trang Menu đầy đủ (Dynamic)
     Nội dung chính - Được include vào MasterLayout
     =================================== -->

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
