
<?php
$category = $data['category'] ?? null;
$products = $data['products'] ?? [];
?>

<section class="category-hero" id="category-hero">
    <div class="category-hero-content">
        <h1><?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?></h1>
        <p><?= isset($category) ? htmlspecialchars($category->description) : 'Khám phá các sản phẩm tuyệt vời' ?></p>
    </div>
</section>

<section class="products-container">


    <div class="products-header">
        <h2 style="color: var(--primary-color); margin-bottom: 10px;"><?= isset($category) ? strtoupper(htmlspecialchars($category->name)) : 'SẢN PHẨM' ?></h2>
        <p class="products-count"><?= count($products) ?> sản phẩm</p>
    </div>

    <div class="menu-grid">
        <?php if (!empty($products)):
            foreach ($products as $product):
                $minPrice = !empty($product->sizes) ? min(array_column($product->sizes, 'price')) : null;
        ?>
            <div class="menu-card">
                <a href="/COFFEE_PHP/User/productDetail?id=<?= $product->id ?>" style="text-decoration: none; color: inherit;">
                    <div class="menu-card-image">
                        <img src="/COFFEE_PHP/<?= htmlspecialchars($product->image_url ?: 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=500') ?>" alt="<?= htmlspecialchars($product->name) ?>">
                    </div>
                    <div class="menu-card-content">
                        <h3><?= strtoupper(htmlspecialchars($product->name)) ?></h3>
                        <p><?= htmlspecialchars(mb_strimwidth($product->description, 0, 80, "...")) ?></p>
                        <div class="menu-card-footer">
                            <span class="price"><?= $minPrice ? 'Từ ' . number_format($minPrice, 0, ',', '.') . 'đ' : 'Liên hệ' ?></span>
                            <button class="btn-add">Xem chi tiết</button>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: var(--text-light);">
                <h3>Chưa có sản phẩm nào trong danh mục này</h3>
                <p>Vui lòng quay lại sau hoặc khám phá các danh mục khác!</p>
                <a href="/COFFEE_PHP/User/menu" class="btn btn-primary" style="display: inline-block; margin-top: 20px;">Quay lại thực đơn</a>
            </div>
        <?php endif; ?>
    </div>
</section>
