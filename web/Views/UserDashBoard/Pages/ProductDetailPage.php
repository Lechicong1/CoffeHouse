<!-- ===================================
     FILE: product-detail.php
     MÔ TẢ: Trang chi tiết sản phẩm (Dynamic)
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($data['product']) ? htmlspecialchars($data['product']->description) : 'Chi tiết sản phẩm' ?>">
    <title><?= isset($data['product']) ? htmlspecialchars($data['product']->name) . ' - Coffee House' : ($data['title'] ?? 'Chi tiết sản phẩm - Coffee House') ?></title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-product-detail.css">
</head>
<body>
    <?php
    $currentPage = 'menu';
    include __DIR__ . '/../header.php';
    $product = $data['product'] ?? null;
    $category = $data['category'] ?? null;
    $relatedProducts = $data['relatedProducts'] ?? [];
    ?>

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
                <form method="POST" action="/COFFEE_PHP/Cart/ins" id="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?= $product->id ?>">
                    <!-- Tên trường phải khớp với CartController::ins -> txtProductSizeId, txtQuantity -->
                    <input type="hidden" name="txtProductSizeId" id="selected-product-size-id" value="<?= !empty($product->sizes) ? $product->sizes[0]->id : '' ?>">
                    <input type="hidden" name="txtQuantity" id="selected-quantity" value="1">
                    <input type="hidden" name="buy_now" id="buy-now-flag" value="0">
                    <!-- Hidden button flag để controller kiểm tra (btnThemGioHang) - cần thiết khi JS gọi form.submit() -->
                    <input type="hidden" name="btnThemGioHang" id="btn-them-gio-hang-hidden" value="1">

                    <div class="product-options">
                        <?php if (!empty($product->sizes)): ?>
                        <!-- Size -->
                        <div class="option-group">
                            <label>Kích thước <span style="color: red;">*</span></label>
                            <div class="size-options">
                                <?php foreach ($product->sizes as $index => $size): ?>
                                    <button type="button" class="size-btn <?= $index === 0 ? 'active' : '' ?>"
                                            data-product-size-id="<?= $size->id ?>"
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
                            <button type="button" class="quantity-btn" id="decrease-qty">-</button>
                            <span class="quantity-value" id="quantity">1</span>
                            <button type="button" class="quantity-btn" id="increase-qty">+</button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="product-actions">
                        <button type="submit" name="btnThemGioHang" class="btn-add-cart" id="add-to-cart">
                            🛒 Thêm vào giỏ hàng
                        </button>
                        <button type="button" class="btn-buy-now" id="buy-now">
                            ⚡ Đặt hàng ngay
                        </button>
                    </div>
                </form>

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

    <?php include __DIR__ . '/../footer.php'; ?>

    <!-- JavaScript -->
    <script src="/COFFEE_PHP/Public/Js/user-product-detail.js"></script>
</body>
</html>
