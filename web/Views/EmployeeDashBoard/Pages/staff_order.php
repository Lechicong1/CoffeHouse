<!-- Link CSS riêng cho POS -->
<link rel="stylesheet" href="Public/Css/pos-style.css">

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
                    <span>50 món</span>
                </div>
                <i class="fas fa-coffee fa-5x category-img" style="color: rgba(255,255,255,0.2);"></i>
            </div>
            <div class="category-card" data-category="tea">
                <div class="category-status">Sẵn sàng</div>
                <div class="category-content">
                    <h3>Trà</h3>
                    <span>20 món</span>
                </div>
                <i class="fas fa-leaf fa-5x category-img" style="color: #eee;"></i>
            </div>
            <div class="category-card warning" data-category="snack">
                <div class="category-status">Sắp hết <i class="fas fa-info-circle"></i></div>
                <div class="category-content">
                    <h3>Bánh</h3>
                    <span>10 món</span>
                </div>
                <i class="fas fa-cookie-bite fa-5x category-img" style="color: #eee;"></i>
            </div>
        </div>

        <!-- Menu Grid -->
        <div class="menu-grid" id="menu-grid">
            <!-- Items injected by JS -->
        </div>
    </div>

    <!-- RIGHT SIDE: ORDER -->
    <div class="order-section">
        <div class="order-header">
            <div class="order-header-left">
                <button class="back-btn"><i class="fas fa-chevron-right"></i></button>
                <div class="receipt-info">
                    <h3>Hóa Đơn</h3>
                    <span>#27362</span>
                </div>
            </div>
            <button class="menu-dots"><i class="fas fa-bars"></i></button>
        </div>

        <div class="order-toggle">
            <button class="toggle-btn active" id="btn-dine-in" onclick="setOrderType('dine-in')">Tại Bàn</button>
            <button class="toggle-btn" id="btn-take-away" onclick="setOrderType('take-away')">Mang Về</button>
        </div>

        <div class="customer-details">
            <div class="input-box">
                <label>Tên Khách</label>
                <input type="text" value="Khách Lẻ">
            </div>
            <div class="input-box">
                <label>Bàn Số</label>
                <select>
                    <option>Bàn 1</option>
                    <option>Bàn 2</option>
                    <option>Bàn 3</option>
                </select>
            </div>
            <div class="input-box" id="order-id-group" style="display: none;">
                <label>Mã Đơn</label>
                <input type="text" id="order-id" readonly>
            </div>
        </div>

        <div class="order-list" id="order-list">
            <!-- Order Items injected by JS -->
        </div>

        <div class="payment-summary">
            <div class="summary-row">
                <span>Tạm tính</span>
                <span id="subtotal-price">0 ₫</span>
            </div>
            <div class="summary-row total">
                <span>Tổng cộng</span>
                <span id="total-price">0 ₫</span>
            </div>
        </div>

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
            <div class="payment-option" id="pay-cash" onclick="selectPayment('cash')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                <i class="fas fa-money-bill-wave fa-2x" style="margin-bottom: 10px;"></i>
                <p>Tiền Mặt</p>
            </div>
            <div class="payment-option" id="pay-card" onclick="selectPayment('card')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                <i class="fas fa-qrcode fa-2x" style="margin-bottom: 10px;"></i>
                <p>Thẻ / QR</p>
            </div>
        </div>

        <button class="confirm-btn" onclick="processPayment()" style="background: var(--primary-green); color: white; border: none; padding: 15px 40px; border-radius: 30px; font-size: 1rem; cursor: pointer; width: 100%;">Xác Nhận</button>
    </div>
</div>

<!-- Logic JS riêng cho POS -->
<script src="Public/Js/pos-logic.js"></script>
