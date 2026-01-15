<!-- Link CSS riêng cho POS -->
<link rel="stylesheet" href="Public/Css/pos-style.css">
<link rel="stylesheet" href="Public/Css/voucher-page.css">


<!-- Truyền dữ liệu menu từ PHP sang JavaScript -->
<script>
    // Dữ liệu menu được load từ server (MVC pattern)
    const SERVER_MENU_DATA = <?php echo json_encode($data['menuItems'] ?? []); ?>;
    const SERVER_CATEGORIES = <?php echo json_encode($data['categories'] ?? []); ?>;
    const STAFF_ID = <?php echo json_encode($data['staffId'] ?? null); ?>;
    
    // Customer search result (nếu có)
    <?php if (isset($_SESSION['pos_customer_search'])): ?>
        const CUSTOMER_SEARCH_RESULT = <?php echo json_encode($_SESSION['pos_customer_search']); ?>;
        <?php unset($_SESSION['pos_customer_search']); ?>
    <?php else: ?>
        const CUSTOMER_SEARCH_RESULT = null;
    <?php endif; ?>
</script>

<div class="pos-wrapper">
    <!-- LEFT SIDE: MENU -->
    <div class="menu-section">
        <!-- Header (Optional inside POS) -->
        

        <!-- Search -->
        <div class="search-container">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Tìm kiếm món...">
                <i class="fas fa-sliders-h filter-icon"></i>
            </div>
        </div>

        <!-- Categories với navigation -->
        <div class="categories-wrapper" style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
            <button id="cat-prev" class="cat-nav-btn" style="padding: 8px 12px; background: #f0f0f0; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; min-width: 40px;" disabled>◀</button>
            <div class="categories" id="categories-container" style="display: flex; gap: 10px; overflow: hidden; flex: 1;">
                <!-- Categories sẽ được inject bởi JavaScript -->
            </div>
            <button id="cat-next" class="cat-nav-btn" style="padding: 8px 12px; background: #f0f0f0; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; min-width: 40px;">▶</button>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid" id="menu-grid">
            <!-- Items injected by JS -->
        </div>
    </div>

    <!-- RIGHT SIDE: ORDER -->
    <div class="order-section" style="overflow-y: auto; max-height: calc(100vh - 140px);">
        
        <div class="order-toggle">
            <button class="toggle-btn active" id="btn-dine-in" onclick="setOrderType('AT_COUNTER')">Tại Bàn</button>
            <button class="toggle-btn" id="btn-take-away" onclick="setOrderType('TAKEAWAY')">Mang Về</button>
        </div>

        <!-- Hàng 1: Tên khách và Bàn số -->
        <div style="display: flex; gap: 10px; margin-bottom: 10px;">
            <div style="flex: 1;">
                <input type="text" id="pos-customer-name" value="Khách Lẻ" placeholder="Tên Khách" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem;" readonly>
            </div>
            <div id="table-box" style="flex: 1;">
                <select id="pos-table-select" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem; background: white; cursor: pointer;">
                    <option value="">-- Chọn Bàn --</option>

                    <?php
                    $totalTables = 30;

                    // Dùng vòng for để in ra các option
                    for ($i = 1; $i <= $totalTables; $i++):
                        ?>
                        <option value="<?= $i ?>" <?= ($i == 1) ? 'selected' : '' ?>>
                            Bàn <?= $i ?>
                        </option>
                    <?php endfor; ?>

                </select>
            </div>
        </div>

        <!-- Hàng 2: Buttons Chọn khách và Voucher -->
        <div style="display: flex; gap: 10px; margin-bottom: 15px;">
            <div style="flex: 1;">
                <button type="button" id="open-customer-modal" class="btn" style="width: 100%; padding: 8px; font-size: 0.85rem; border: 1px solid #064528; background: white; color: #064528; border-radius: 8px; cursor: pointer;">Chọn / Tìm Khách</button>
                <div id="pos-selected-customer" style="margin-top: 5px; color: #444; font-size: 0.85rem; text-align: center;">Khách lẻ</div>
            </div>
            <div style="flex: 1;">
                <button type="button" id="open-voucher-modal" class="btn" style="width: 100%; padding: 8px; font-size: 0.85rem; border: 1px solid #064528; background: white; color: #064528; border-radius: 8px; cursor: pointer;">Áp Voucher / Điểm</button>
                <div id="pos-selected-voucher" style="margin-top: 5px; color: #444; font-size: 0.85rem; text-align: center;">Không có voucher</div>
            </div>
        </div>

        <div class="order-list" id="order-list" style="max-height: 300px; overflow-y: auto;">
            <!-- Order Items injected by JS -->
        </div>

        <div class="payment-summary">
            <div class="summary-row">
                <span>Tạm tính</span>
                <span id="subtotal-price">0 ₫</span>
            </div>
            <div class="summary-row" id="discount-row" style="display: none; color: #28a745;">
                <span>Giảm (<span id="discount-voucher-name"></span>)</span>
                <span id="discount-price">0 ₫</span>
            </div>
            <div class="summary-row total">
                <span>Tổng cộng</span>
                <span id="total-price">0 ₫</span>
            </div>
        </div>

        <!-- Form POST để submit đơn hàng (MVC không dùng JSON API) -->
        <form id="order-form" method="POST" action="/COFFEE_PHP/StaffController/createOrder" style="display: none;">
            <input type="hidden" name="order_type" id="form-order-type">
            <input type="hidden" name="payment_method" id="form-payment-method">
            <input type="hidden" name="total_amount" id="form-total-amount">
            <input type="hidden" name="customer_id" id="form-customer-id">
            <input type="hidden" name="cart_items" id="form-cart-items">
            <input type="hidden" name="note" id="form-note">
            <input type="hidden" name="voucher_id" id="form-voucher-id">
            <input type="hidden" name="table_number" id="form-table-number">
        </form>

        <button class="place-order-btn" onclick="openPaymentModal()">
            <div class="btn-icon">
                <i class="fas fa-arrow-right"></i>
                <span>Thanh Toán</span>
            </div>
            <span id="btn-total">0 ₫</span>
        </button>
    </div>
</div>

<!-- PAYMENT MODAL -->
<div class="modal" id="payment-modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closePaymentModal()" style="float: right; cursor: pointer; font-size: 1.5rem;">&times;</span>
        <h2 style="margin-bottom: 20px;">Phương Thức Thanh Toán</h2>
        <p style="margin-bottom: 30px;">Tổng Tiền: <span id="modal-total" style="font-weight: bold; color: var(--primary-green);">0 ₫</span></p>
        
        <div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 30px;">
            <div class="payment-option" id="pay-cash" onclick="selectPayment('CASH')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                <i class="fas fa-money-bill-wave fa-2x" style="margin-bottom: 10px;"></i>
                <p>Tiền Mặt</p>
            </div>
            <div class="payment-option" id="pay-card" onclick="selectPayment('BANKING')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                <i class="fas fa-qrcode fa-2x" style="margin-bottom: 10px;"></i>
                <p>Thẻ / QR</p>
            </div>
        </div>

        <button class="confirm-btn" onclick="processPayment()" style="background: var(--primary-green); color: white; border: none; padding: 15px 40px; border-radius: 30px; font-size: 1rem; cursor: pointer; width: 100%;">Xác Nhận</button>
    </div>
</div>

<!-- SIZE SELECTION MODAL -->
<div class="modal" id="size-modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); align-items: center; justify-content: center;">
    <div class="modal-content" style="background-color: #fefefe; margin: auto; padding: 20px; border: 1px solid #888; width: 300px; border-radius: 15px; text-align: center;">
        <span class="close-modal" onclick="closeSizeModal()" style="float: right; cursor: pointer; font-size: 1.5rem;">&times;</span>
        <h3 id="size-modal-title" style="margin-bottom: 20px;">Chọn Size</h3>
        <div id="size-options" style="display: flex; flex-direction: column; gap: 10px;">
            <!-- Size options injected by JS -->
        </div>
    </div>
</div>


<?php include __DIR__ . '/poscreatecustomer_v.php'; ?>

<!-- Logic JS riêng cho POS (cache-bust để luôn load phiên bản mới) -->
<script src="Public/Js/pos-logic.js?v=<?php echo time(); ?>"></script>
<script src="Public/Js/pos-customer.js?v=<?php echo time(); ?>"></script>
<script src="Public/Js/voucher-utils.js"></script>
<script src="Public/Js/pos-voucher.js?v=<?php echo time(); ?>"></script>

