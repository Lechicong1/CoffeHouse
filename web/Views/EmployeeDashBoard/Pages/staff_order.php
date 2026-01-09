<!-- Link CSS riêng cho POS -->
<link rel="stylesheet" href="Public/Css/pos-style.css">

<!-- Small POS layout tweaks for compact customer+voucher and voucher highlight -->
<style>
    .customer-details { display:flex; flex-wrap:wrap; gap:8px; align-items:flex-start; }
    .customer-details .input-box { flex: 1 1 180px; min-width:160px; }
    #pos-selected-customer, #pos-selected-voucher { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .voucher-card { border:1px solid #eee; padding:14px; border-radius:10px; display:flex; justify-content:space-between; align-items:center; cursor:pointer; min-height:84px; background: #fff; }
    .voucher-card:hover { background:#f7fff2; }
    .voucher-card.selected { background:#e6f9d9; border-color:#b6da9f; box-shadow:0 0 0 4px rgba(182,218,159,0.12); }
    .voucher-card .v-left { flex:1; padding-right:12px; }
    .voucher-card .v-name { font-weight:700; font-size:1rem; margin-bottom:6px; }
    .voucher-card .v-meta { font-size:0.9rem; color:#444; }
    .voucher-card .v-note { font-size:0.85rem; color:#666; margin-top:6px; }
    .voucher-card .v-actions { margin-left:8px; }
    .voucher-card.disabled { opacity:0.55; }
</style>

<!-- Truyền dữ liệu menu từ PHP sang JavaScript -->
<script>
    // Dữ liệu menu được load từ server (MVC pattern)
    const SERVER_MENU_DATA = <?php echo json_encode($data['menuItems'] ?? []); ?>;
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

        <!-- Categories -->
        <div class="categories">
            <div class="category-card active" data-category="coffee">
                <div class="category-status">Sẵn sàng</div>
                <div class="category-content">
                    <h3>Cà Phê</h3>

                </div>
                <i class="fas fa-coffee fa-5x category-img" style="color: rgba(255,255,255,0.2);"></i>
            </div>
            <div class="category-card" data-category="tea">
                <div class="category-status">Sẵn sàng</div>
                <div class="category-content">
                    <h3>Trà Sữa</h3>
                </div>
                <i class="fas fa-leaf fa-5x category-img" style="color: #eee;"></i>
            </div>
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
                <input type="text" id="pos-customer-name" value="Khách Lẻ" placeholder="Tên Khách" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem;">
            </div>
            <div id="table-box" style="flex: 1;">
                <input type="text" id="table-number" placeholder="Bàn số" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem;">
            </div>
            <div id="order-id-box" style="flex: 1; display: none;">
                <input type="text" id="order-id" readonly placeholder="Mã Đơn" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem; background: #f5f5f5;">
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

<!-- Logic JS riêng cho POS -->
<script src="Public/Js/pos-logic.js"></script>
<script src="Public/Js/pos-customer.js"></script>
<script src="Public/Js/pos-voucher.js"></script>

<!-- Customer Modal -->
<div id="posCustomerModal" class="modal" style="display:none; position: fixed; z-index: 1200; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4);">
    <div class="modal-content" style="max-width:420px; margin: 80px auto; background: #fff; border-radius:8px; overflow:hidden;">
        <div style="padding:12px 16px; border-bottom:1px solid #eee; display:flex; align-items:center; justify-content:space-between;">
            <h3 style="margin:0; font-size:1.1rem;">Tìm / Thêm Khách</h3>
            <button style="background:none;border:none;font-size:1.4rem;cursor:pointer;" onclick="closePosCustomerModal()">&times;</button>
        </div>
        <div style="padding:16px;">
            <!-- Form tìm kiếm khách hàng -->
            <form id="search-customer-form" method="POST" action="/COFFEE_PHP/StaffController/searchCustomer">
                <label>Số điện thoại</label>
                <input type="text" name="phone" id="posPhone" placeholder="Nhập số điện thoại" style="width:100%;padding:8px;margin-bottom:8px;" required>
                <button type="button" id="posFindBtn" class="btn btn-primary" onclick="posFindCustomer()">Tìm Khách</button>
            </form>
            <div id="posCustomerMessage" style="margin-top:8px;color:#444;font-size:0.95rem;"></div>
            
            <hr style="margin: 16px 0;">
            
            <!-- Form tạo/cập nhật khách hàng -->
            <form id="upsert-customer-form" method="POST" action="/COFFEE_PHP/StaffController/upsertCustomer">
                <label>Số điện thoại</label>
                <input type="text" name="phone" id="posPhoneUpsert" placeholder="Nhập số điện thoại" style="width:100%;padding:8px;margin-bottom:8px;" required>
                <label>Tên (tùy chọn)</label>
                <input type="text" name="fullname" id="posFullName" placeholder="Khách lẻ" style="width:100%;padding:8px;margin-bottom:8px;">
                <label>Email (tùy chọn)</label>
                <input type="email" name="email" id="posEmail" placeholder="example@email.com" style="width:100%;padding:8px;margin-bottom:12px;">
                <input type="hidden" name="pointsToAdd" value="0">
                <div style="display:flex; gap:8px;">
                    <button type="button" id="posCreateBtn" class="btn btn-success" onclick="posCreateOrUseCustomer()">Tạo / Dùng</button>
                    <button type="button" class="btn" onclick="closePosCustomerModal()">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>
