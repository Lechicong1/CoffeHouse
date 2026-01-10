/**
 * FILE: user-product-detail.js
 * MÔ TẢ: JavaScript cho trang chi tiết sản phẩm
 * CHỨC NĂNG: Chỉ làm hiệu ứng UI - chọn size, tăng/giảm số lượng, cập nhật hidden inputs
 * LƯU Ý: KHÔNG gọi API, form sẽ submit trực tiếp đến Controller
 */

// State quản lý sản phẩm hiện tại (chỉ để hiển thị UI)
let currentProduct = {
    sizeId: null,
    sizeName: null,
    price: 0,
    quantity: 1
};

// ========== USER PRODUCT DETAIL - MINIMAL JS ==========
// Chỉ xử lý UI cơ bản - Form submit thuần túy qua PHP

document.addEventListener('DOMContentLoaded', function() {
    initializeSizeSelection();
    initializeQuantityControls();
    initializeBuyNow();
    initializeTabs();
});

// ========== CHỌN SIZE (Cập nhật hidden input) ==========
function initializeSizeSelection() {
    const sizeBtns = document.querySelectorAll('.size-btn');
    const hiddenSizeInput = document.getElementById('selected-product-size-id');
    const priceDisplay = document.getElementById('product-price');

    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // Bỏ active khỏi tất cả
            sizeBtns.forEach(b => b.classList.remove('active'));

            // Thêm active cho button được chọn
            this.classList.add('active');

            // Cập nhật hidden input để submit lên server
            hiddenSizeInput.value = this.dataset.productSizeId;

            // Cập nhật hiển thị giá
            if (priceDisplay) {
                const price = parseFloat(this.dataset.price);
                priceDisplay.textContent = new Intl.NumberFormat('vi-VN').format(price) + 'đ';
            }
        });
    });
}

// ========== TĂNG/GIẢM SỐ LƯỢNG ==========
function initializeQuantityControls() {
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const quantityDisplay = document.getElementById('quantity');
    const hiddenQuantity = document.getElementById('selected-quantity');

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            let qty = parseInt(quantityDisplay.textContent);
            if (qty > 1) {
                qty--;
                quantityDisplay.textContent = qty;
                hiddenQuantity.value = qty;
            }
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            let qty = parseInt(quantityDisplay.textContent);
            if (qty < 99) {
                qty++;
                quantityDisplay.textContent = qty;
                hiddenQuantity.value = qty;
            }
        });
    }
}

// ========== MUA NGAY (Set flag và submit form) ==========
function initializeBuyNow() {
    const buyNowBtn = document.getElementById('buy-now');
    const buyNowFlag = document.getElementById('buy-now-flag');
    const form = document.getElementById('add-to-cart-form');

    if (buyNowBtn && form) {
        buyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Set flag để controller biết là mua ngay
            buyNowFlag.value = '1';

            // Submit form - controller sẽ xử lý và redirect
            form.submit();
        });
    }
}

// ========== TABS (Chỉ chuyển tab UI) ==========
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Bỏ active
            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Thêm active
            this.classList.add('active');
            const targetContent = document.getElementById(`tab-${targetTab}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}
