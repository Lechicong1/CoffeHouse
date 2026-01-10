<section class="cart-section">
    <h1 class="cart-title">Giỏ hàng của bạn</h1>

    <?php if (empty($data['cartItems'])): ?>
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
                <?php foreach ($data['cartItems'] as $item): ?>
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
                                <form method="POST" action="/COFFEE_PHP/Cart/upd" style="display: inline;">
                                    <input type="hidden" name="txtCartItemId" value="<?= $item->id ?>">
                                    <input type="hidden" name="txtQuantity" value="<?= $item->quantity - 1 ?>">
                                    <button type="submit" name="btnCapnhat" class="quantity-btn" <?= $item->quantity <= 1 ? 'disabled' : '' ?>>-</button>
                                </form>

                                <span class="quantity-value"><?= $item->quantity ?></span>

                                <!-- Tăng số lượng -->
                                <form method="POST" action="/COFFEE_PHP/Cart/upd" style="display: inline;">
                                    <input type="hidden" name="txtCartItemId" value="<?= $item->id ?>">
                                    <input type="hidden" name="txtQuantity" value="<?= $item->quantity + 1 ?>">
                                    <button type="submit" name="btnCapnhat" class="quantity-btn">+</button>
                                </form>
                            </div>

                            <!-- Xóa sản phẩm -->
                            <form method="POST" action="/COFFEE_PHP/Cart/del" style="display: inline;">
                                <input type="hidden" name="txtCartItemId" value="<?= $item->id ?>">
                                <button type="submit" name="btnXoa" class="remove-btn" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</button>
                            </form>
                        </div>

                        <div class="cart-item-subtotal">
                            <?= number_format($item->price * $item->quantity, 0, ',', '.') ?>đ
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Tóm tắt giỏ hàng -->
            <div class="cart-summary">
                <div class="cart-summary-header">
                    <h3>Tóm tắt đơn hàng</h3>
                    <!-- Form xóa tất cả sản phẩm - di chuyển lên đây -->
                    <form method="POST" action="/COFFEE_PHP/Cart/clear" style="display: inline;">
                        <button type="submit" name="btnXoaTatCa" class="clear-cart-btn-small" onclick="return confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')">
                            🗑️ Xóa tất cả
                        </button>
                    </form>
                </div>

                <div class="summary-row">
                    <span>Số lượng sản phẩm:</span>
                    <strong><?= $data['count'] ?></strong>
                </div>

                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <strong><?= number_format($data['total'], 0, ',', '.') ?>đ</strong>
                </div>

                <div class="summary-row">
                    <span>Phí giao hàng:</span>
                    <strong>Miễn phí</strong>
                </div>

                <div class="summary-row total">
                    <span>Tổng cộng:</span>
                    <span><?= number_format($data['total'], 0, ',', '.') ?>đ</span>
                </div>

                <!-- Form checkout toàn bộ giỏ hàng -->
                <form method="POST" action="/COFFEE_PHP/Checkout/GetData" style="margin-top: 20px;">
                    <button type="submit" class="checkout-btn">💳 Thanh toán</button>
                </form>

                <a href="/COFFEE_PHP/User/menu" class="continue-shopping-link">
                    ← Tiếp tục mua hàng
                </a>
            </div>
        </div>
    <?php endif; ?>
</section>
