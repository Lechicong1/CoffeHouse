<!-- ===================================
     FILE: cart.php
     MÔ TẢ: Trang giỏ hàng
     =================================== -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Coffee House</title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <style>
        .cart-section {
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 0 20px;
        }

        .cart-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        .cart-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
            align-items: start;
        }

        .cart-items {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .cart-item-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-info h3 {
            margin: 0 0 8px 0;
            font-size: 1.1rem;
        }

        .cart-item-size {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .cart-item-price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
        }

        .quantity-controls {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .quantity-controls form {
            display: inline;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .quantity-value {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
        }

        .remove-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .remove-btn:hover {
            background: #cc0000;
        }

        .cart-summary {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 100px;
        }

        .cart-summary h3 {
            margin: 0 0 20px 0;
            font-size: 1.3rem;
            text-transform: uppercase;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row.total {
            border-bottom: none;
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-top: 20px;
        }

        .checkout-btn {
            width: 100%;
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            margin-top: 20px;
        }

        .checkout-btn:hover {
            background: #8fb97e;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(182, 218, 159, 0.4);
        }

        .empty-cart {
            text-align: center;
            padding: 100px 20px;
        }

        .empty-cart h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .empty-cart p {
            color: var(--text-light);
            margin-bottom: 30px;
        }

        .continue-shopping {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .continue-shopping:hover {
            background: #8fb97e;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }

            .cart-item-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                justify-content: space-between;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <?php
    $currentPage = 'cart';
    include __DIR__ . '/header.php';
    ?>

    <section class="cart-section">
        <h1 class="cart-title">Giỏ hàng của bạn</h1>

        <?php if (empty($cartItems)): ?>
            <!-- Giỏ hàng trống -->
            <div class="empty-cart">
                <h2>🛒 Giỏ hàng trống</h2>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá thực đơn của chúng tôi!</p>
                <a href="/COFFEE_PHP/User/menu" class="continue-shopping">Xem thực đơn</a>
            </div>
        <?php else: ?>
            <!-- Giỏ hàng có sản phẩm -->
            <div class="cart-container">
                <!-- Danh sách sản phẩm -->
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item">
                            <div class="cart-item-image">
                                <?php if (!empty($item->image_url)): ?>
                                    <img src="/COFFEE_PHP/<?= htmlspecialchars($item->image_url) ?>" alt="<?= htmlspecialchars($item->product_name) ?>">
                                <?php else: ?>
                                    <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=200" alt="<?= htmlspecialchars($item->product_name) ?>">
                                <?php endif; ?>
                            </div>

                            <div class="cart-item-info">
                                <h3><?= htmlspecialchars($item->product_name) ?></h3>
                                <div class="cart-item-size">Size: <?= htmlspecialchars($item->size_name) ?></div>
                                <div class="cart-item-price"><?= number_format($item->price, 0, ',', '.') ?>đ</div>
                            </div>

                            <div class="cart-item-actions">
                                <!-- Điều chỉnh số lượng -->
                                <div class="quantity-controls">
                                    <!-- Giảm số lượng -->
                                    <form method="POST" action="/COFFEE_PHP/CartController/updateCartItemForm" style="display: inline;">
                                        <input type="hidden" name="cart_item_id" value="<?= $item->id ?>">
                                        <input type="hidden" name="quantity" value="<?= $item->quantity - 1 ?>">
                                        <button type="submit" class="quantity-btn">-</button>
                                    </form>

                                    <span class="quantity-value"><?= $item->quantity ?></span>

                                    <!-- Tăng số lượng -->
                                    <form method="POST" action="/COFFEE_PHP/CartController/updateCartItemForm" style="display: inline;">
                                        <input type="hidden" name="cart_item_id" value="<?= $item->id ?>">
                                        <input type="hidden" name="quantity" value="<?= $item->quantity + 1 ?>">
                                        <button type="submit" class="quantity-btn">+</button>
                                    </form>
                                </div>

                                <!-- Xóa sản phẩm -->
                                <form method="POST" action="/COFFEE_PHP/CartController/removeItemForm" style="display: inline;">
                                    <input type="hidden" name="cart_item_id" value="<?= $item->id ?>">
                                    <button type="submit" class="remove-btn" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Tóm tắt giỏ hàng -->
                <div class="cart-summary">
                    <h3>Tóm tắt đơn hàng</h3>

                    <div class="summary-row">
                        <span>Số lượng sản phẩm:</span>
                        <strong><?= $count ?></strong>
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <strong><?= number_format($total, 0, ',', '.') ?>đ</strong>
                    </div>

                    <div class="summary-row">
                        <span>Phí giao hàng:</span>
                        <strong>Miễn phí</strong>
                    </div>

                    <div class="summary-row total">
                        <span>Tổng cộng:</span>
                        <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                    </div>

                    <a href="/COFFEE_PHP/User/checkout">
                        <button class="checkout-btn">Thanh toán</button>
                    </a>

                    <a href="/COFFEE_PHP/User/menu" class="continue-shopping" style="display: block; text-align: center; margin-top: 15px; color: var(--primary-color); text-decoration: none;">
                        ← Tiếp tục mua hàng
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>

