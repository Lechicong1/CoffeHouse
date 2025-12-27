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

// ========== KHỞI TẠO KHI TRANG LOAD ==========
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Product Detail JS loaded (UI only mode)');
    initializeProductDetail();
    initializeSizeButtons();
    initializeQuantityControls();
    initializeBuyNowButton();
    initializeTabs();
});

// ========== KHỞI TẠO THÔNG TIN SẢN PHẨM ==========
function initializeProductDetail() {
    // Tự động chọn size đầu tiên
    const firstSizeBtn = document.querySelector('.size-btn');
    if (firstSizeBtn) {
        firstSizeBtn.classList.add('active');
        currentProduct.sizeId = firstSizeBtn.dataset.productSizeId;
        currentProduct.sizeName = firstSizeBtn.dataset.size;
        currentProduct.price = parseFloat(firstSizeBtn.dataset.price);

        // Cập nhật hidden input
        document.getElementById('selected-product-size-id').value = currentProduct.sizeId;

        console.log('✅ Auto-selected first size:', currentProduct);
    }
}

// ========== XỬ LÝ CHỌN SIZE ==========
function initializeSizeButtons() {
    const sizeBtns = document.querySelectorAll('.size-btn');
    console.log('🔘 Found', sizeBtns.length, 'size buttons');

    sizeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🖱️ Size button clicked:', this.dataset.size);

            // Bỏ active khỏi tất cả các nút
            sizeBtns.forEach(b => b.classList.remove('active'));

            // Thêm active cho nút được chọn
            this.classList.add('active');

            // Cập nhật thông tin size
            currentProduct.sizeId = this.dataset.productSizeId;
            currentProduct.sizeName = this.dataset.size;
            currentProduct.price = parseFloat(this.dataset.price);

            // Cập nhật hidden input để gửi lên server
            document.getElementById('selected-product-size-id').value = currentProduct.sizeId;

            console.log('✅ Updated size:', currentProduct);

            // Cập nhật hiển thị giá
            updatePriceDisplay();
        });
    });
}

// ========== CẬP NHẬT HIỂN THỊ GIÁ ==========
function updatePriceDisplay() {
    const priceElement = document.getElementById('product-price');
    if (priceElement && currentProduct.price) {
        priceElement.textContent = formatCurrency(currentProduct.price) + 'đ';
        priceElement.style.transition = 'transform 0.2s';
        priceElement.style.transform = 'scale(1.05)';
        setTimeout(() => {
            priceElement.style.transform = 'scale(1)';
        }, 200);
        console.log('💰 Price updated:', currentProduct.price);
    }
}

// ========== XỬ LÝ TĂNG/GIẢM SỐ LƯỢNG ==========
function initializeQuantityControls() {
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const quantityDisplay = document.getElementById('quantity');
    const hiddenQuantity = document.getElementById('selected-quantity');

    console.log('🔢 Quantity controls initialized');

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentProduct.quantity > 1) {
                currentProduct.quantity--;
                quantityDisplay.textContent = currentProduct.quantity;
                hiddenQuantity.value = currentProduct.quantity;
                console.log('➖ Quantity decreased:', currentProduct.quantity);
            }
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentProduct.quantity < 99) {
                currentProduct.quantity++;
                quantityDisplay.textContent = currentProduct.quantity;
                hiddenQuantity.value = currentProduct.quantity;
                console.log('➕ Quantity increased:', currentProduct.quantity);
            }
        });
    }
}

// ========== XỬ LÝ NÚT MUA NGAY ==========
function initializeBuyNowButton() {
    const buyNowBtn = document.getElementById('buy-now');
    const buyNowFlag = document.getElementById('buy-now-flag');
    const form = document.getElementById('add-to-cart-form');

    if (buyNowBtn && form) {
        buyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('⚡ Buy now clicked');

            // Set flag để controller biết là mua ngay
            buyNowFlag.value = '1';

            // Submit form
            form.submit();
        });
    }
}

// ========== XỬ LÝ TABS ==========
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Bỏ active khỏi tất cả
            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Thêm active cho tab được chọn
            this.classList.add('active');
            const targetContent = document.getElementById(`tab-${targetTab}`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// ========== HELPER FUNCTIONS ==========
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount);
}
