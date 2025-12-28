<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'Thanh Toán'; ?></title>
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/user-style.css">
    <link rel="stylesheet" href="/COFFEE_PHP/Public/Css/checkout-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include_once './web/Views/UserDashBoard/header.php'; ?>

    <main class="checkout-container">
        <div class="checkout-wrapper">
            <h1 class="checkout-title">THANH TOÁN</h1>

            <div class="checkout-content">
                <!-- Thông tin giao hàng -->
                <div class="checkout-section">
                    <div class="section-header">
                        <i class="fas fa-shipping-fast"></i>
                        <h2>Thông Tin Giao Hàng</h2>
                    </div>

                    <!-- Form submit trực tiếp như Employee -->
                    <form id="checkoutForm" method="POST" action="/COFFEE_PHP/Checkout/placeOrder" class="checkout-form">
                        <!-- Hidden field để truyền total amount -->
                        <input type="hidden" name="txtTotalAmount" value="<?php echo $data['total']; ?>">

                        <!-- Voucher (optional) -->
                        <div class="form-group">
                            <label for="customerVoucher">Mã Voucher (nếu có)</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input type="text" id="customerVoucher" name="txtVoucherCode" placeholder="Nhập mã voucher hoặc chọn" style="flex:1;padding:8px;">
                                <button type="button" id="openVoucherListBtn" class="btn">Chọn voucher</button>
                            </div>
                            <input type="hidden" id="appliedVoucherId" name="applied_voucher_id" value="">
                            <div id="checkoutVoucherMsg" style="margin-top:8px;color:#0a6; font-size:0.95rem"></div>
                        </div>

                        <div class="form-group">
                            <label for="customerName">Họ và tên <span class="required">*</span></label>
                            <input type="text"
                                   id="customerName"
                                   name="txtCustomerName"
                                   value="<?php echo htmlspecialchars($data['customer']->full_name ?? ''); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="customerPhone">Số điện thoại <span class="required">*</span></label>
                            <input type="tel"
                                   id="customerPhone"
                                   name="txtCustomerPhone"
                                   value="<?php echo htmlspecialchars($data['customer']->phone ?? ''); ?>"
                                   pattern="[0-9]{10}"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="shippingAddress">Địa chỉ giao hàng <span class="required">*</span></label>
                            <textarea id="shippingAddress"
                                      name="txtShippingAddress"
                                      rows="3"
                                      required><?php echo htmlspecialchars($data['customerAddress'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="note">Ghi chú</label>
                            <textarea id="note"
                                      name="txtNote"
                                      rows="3"
                                      placeholder="Ghi chú thêm cho đơn hàng..."></textarea>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="section-header">
                            <i class="fas fa-credit-card"></i>
                            <h2>Phương Thức Thanh Toán</h2>
                        </div>

                        <div class="payment-methods">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="CASH" checked>
                                <div class="payment-card">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Tiền Mặt</span>
                                    <p>Thanh toán khi nhận hàng</p>
                                </div>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="BANK_TRANSFER">
                                <div class="payment-card">
                                    <i class="fas fa-university"></i>
                                    <span>Chuyển Khoản</span>
                                    <p>Quét mã QR để thanh toán</p>
                                </div>
                            </label>
                        </div>

                        <!-- Button submit - ĐÃ SỬA: Xóa action ở div -->
                        <div class="form-actions" style="margin-top: 30px;">
                            <button type="submit" name="btnDatHang" class="btn-place-order">
                                <i class="fas fa-check-circle"></i>
                                Đặt Hàng
                            </button>

                            <a href="/COFFEE_PHP/Cart/index" class="btn-back-cart">
                                <i class="fas fa-arrow-left"></i>
                                Quay lại giỏ hàng
                            </a>
                        </div>

                        <!-- QR Code Section - Hiển thị khi chọn chuyển khoản -->
                        <div id="qrSection" class="qr-section" style="display: none;">
                            <div class="qr-container">
                                <h3>Quét Mã QR Để Thanh Toán</h3>
                                <div class="qr-code">
                                    <img id="qrImage" src="" alt="QR Code VietQR" onerror="this.style.display='none'">
                                </div>

                                <div class="bank-info">
                                    <h4>Thông Tin Chuyển Khoản</h4>
                                    <div class="bank-detail">
                                        <i class="fas fa-university"></i>
                                        <div>
                                            <strong>Ngân hàng:</strong>
                                            <span>MBBank</span>
                                        </div>
                                    </div>
                                    <div class="bank-detail">
                                        <i class="fas fa-credit-card"></i>
                                        <div>
                                            <strong>Số tài khoản:</strong>
                                            <span>88221020056868</span>
                                        </div>
                                    </div>
                                    <div class="bank-detail">
                                        <i class="fas fa-user"></i>
                                        <div>
                                            <strong>Chủ tài khoản:</strong>
                                            <span>COFFEE HOUSE</span>
                                        </div>
                                    </div>
                                    <div class="bank-detail highlight">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <div>
                                            <strong>Số tiền:</strong>
                                            <span class="amount-value"><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
                                        </div>
                                    </div>
                                    <div class="bank-detail">
                                        <i class="fas fa-comment-dots"></i>
                                        <div>
                                            <strong>Nội dung:</strong>
                                            <span id="qrDescription">Thanh toan don hang</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="qr-note">
                                    <i class="fas fa-info-circle"></i>
                                    <p><strong>Lưu ý:</strong> Vui lòng chuyển khoản đúng nội dung để đơn hàng được xử lý nhanh chóng</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Tóm tắt đơn hàng -->
                <div class="order-summary">
                    <h2>Đơn Hàng Của Bạn</h2>

                    <div class="summary-items">
                        <?php if (!empty($data['cartItems'])): ?>
                            <?php foreach ($data['cartItems'] as $item): ?>
                                <div class="summary-item">
                                    <div class="item-info">
                                        <span class="item-name"><?php echo htmlspecialchars($item->product_name); ?></span>
                                        <span class="item-size"><?php echo htmlspecialchars($item->size_name); ?></span>
                                        <span class="item-quantity">x<?php echo $item->quantity; ?></span>
                                    </div>
                                    <div class="item-price">
                                        <?php echo number_format($item->price * $item->quantity, 0, ',', '.'); ?>đ
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="summary-total">
                        <div class="total-row subtotal">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="total-row shipping">
                            <span>Phí giao hàng:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Tổng cộng:</span>
                            <span id="grandTotal"><?php echo number_format($data['total'], 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include_once './web/Views/UserDashBoard/footer.php'; ?>

    <!-- Loading khi submit form -->
    <script>
        // Dữ liệu PHP truyền sang JavaScript
        const TOTAL_AMOUNT = <?php echo $data['total']; ?>;
        const ORDER_CODE = 'ORD' + Date.now();
        const CUSTOMER_ID = <?php echo isset($data['customer']->id) ? (int)$data['customer']->id : 'null'; ?>;

        document.getElementById('checkoutForm').addEventListener('submit', function() {
            // Hiển thị loading khi submit
            document.querySelector('.btn-place-order').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            document.querySelector('.btn-place-order').disabled = true;
        });

        // Hiển thị QR Code khi chọn phương thức chuyển khoản
        const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
        const qrSection = document.getElementById('qrSection');
        const qrImage = document.getElementById('qrImage');
        const qrDescription = document.getElementById('qrDescription');

        paymentOptions.forEach(option => {
            option.addEventListener('change', function() {
                if (this.value === 'BANK_TRANSFER') {
                    // Hiển thị section QR
                    qrSection.style.display = 'block';

                    // Tạo URL VietQR - Format đúng theo API
                    const bankId = 'MB';
                    const accountNo = '88221020056868';
                    const accountName = 'COFFEE HOUSE';
                    const description = 'Thanh toan don hang ' + ORDER_CODE;

                    // URL VietQR đúng format
                    const vietQRUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact2.png?amount=${TOTAL_AMOUNT}&addInfo=${encodeURIComponent(description)}&accountName=${encodeURIComponent(accountName)}`;

                    console.log('VietQR URL:', vietQRUrl); // Debug

                    // Set QR image
                    qrImage.src = vietQRUrl;
                    qrImage.style.display = 'block'; // Đảm bảo hiển thị
                    qrDescription.textContent = description;

                    // Scroll to QR section
                    setTimeout(() => {
                        qrSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                } else {
                    // Ẩn section QR
                    qrSection.style.display = 'none';
                }
            });
        });

        
    </script>
    <script src="/COFFEE_PHP/Public/Js/voucher-web.js"></script>
</body>
</html>
