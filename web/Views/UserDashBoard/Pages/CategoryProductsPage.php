<!-- ===================================
     FILE: CategoryProductsPage.php
     MÔ TẢ: Trang hiển thị sản phẩm theo danh mục (Dynamic)
     Nội dung chính - Được include vào MasterLayout
     =================================== -->

<?php
$category = $data['category'] ?? null;
$products = $data['products'] ?? [];
?>

<!-- CATEGORY HERO -->
<section class="category-hero" id="category-hero">
    <div class="category-hero-content">
        <h1><?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?></h1>
        <p><?= isset($category) ? htmlspecialchars($category->description) : 'Khám phá các sản phẩm tuyệt vời' ?></p>
    </div>
</section>

<!-- PRODUCTS SECTION -->
<section class="products-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/COFFEE_PHP/User/index">Trang chủ</a>
        <span>›</span>
        <a href="/COFFEE_PHP/User/menu">Thực đơn</a>
        <span>›</span>
        <strong><?= isset($category) ? htmlspecialchars($category->name) : 'Sản phẩm' ?></strong>
    </div>

    <!-- Products Header -->
    <div class="products-header">
        <div>
            <h2 style="color: var(--primary-color); margin-bottom: 10px;">
                <?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?>
            </h2>
            <p class="products-count">
                <?= isset($products) ? count($products) : 0 ?> sản phẩm
            </p>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="menu-grid">
        <?php if (isset($products) && !empty($products)): ?>
            <?php foreach ($products as $product):
                // Lấy giá nhỏ nhất từ các size
                $minPrice = null;
                if (!empty($product->sizes)) {
                    $prices = array_column($product->sizes, 'price');
                    $minPrice = min($prices);
                }
            ?>
            <div class="menu-card">
                <a href="/COFFEE_PHP/User/productDetail?id=<?= $product->id ?>" style="text-decoration: none; color: inherit;">
                    <div class="menu-card-image">
                        <?php if (!empty($product->image_url)): ?>
                            <img src="/COFFEE_PHP/<?= htmlspecialchars($product->image_url) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                        <?php else: ?>
                            <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500" alt="<?= htmlspecialchars($product->name) ?>">
                        <?php endif; ?>
                        <?php if (isset($product->created_at) && $product->created_at && strtotime($product->created_at) > strtotime('-7 days')): ?>
                            <span class="menu-badge">Mới</span>
                        <?php endif; ?>
                    </div>
                    <div class="menu-card-content">
                        <h3><?= strtoupper(htmlspecialchars($product->name)) ?></h3>
                        <p><?= htmlspecialchars(substr($product->description, 0, 80)) . (strlen($product->description) > 80 ? '...' : '') ?></p>
                        <div class="menu-card-footer">
                            <span class="price">
                                <?php if ($minPrice): ?>
                                    Từ <?= number_format($minPrice, 0, ',', '.') ?>đ
                                <?php else: ?>
                                    Liên hệ
                                <?php endif; ?>
                            </span>
                            <button class="btn-add">
                                Xem chi tiết
                            </button>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: var(--text-light);">
                <h3>Chưa có sản phẩm nào trong danh mục này</h3>
                <p>Vui lòng quay lại sau hoặc khám phá các danh mục khác!</p>
                <a href="/COFFEE_PHP/User/menu" class="btn btn-primary" style="display: inline-block; margin-top: 20px;">
                    Quay lại thực đơn
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
