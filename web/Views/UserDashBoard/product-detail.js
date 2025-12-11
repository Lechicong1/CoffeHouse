/* ===================================
   FILE: product-detail.js
   MÔ TẢ: JavaScript cho trang chi tiết sản phẩm
   =================================== */

// ==================== IMAGE GALLERY ====================
const thumbnails = document.querySelectorAll('.thumbnail');
const mainImage = document.getElementById('main-product-image');

thumbnails.forEach(thumbnail => {
    thumbnail.addEventListener('click', () => {
        // Xóa active class từ tất cả thumbnails
        thumbnails.forEach(t => t.classList.remove('active'));
        
        // Thêm active class cho thumbnail được click
        thumbnail.classList.add('active');
        
        // Đổi hình ảnh chính
        const newImage = thumbnail.getAttribute('data-image');
        mainImage.src = newImage;
        mainImage.style.animation = 'fadeIn 0.3s ease';
    });
});

// ==================== SIZE SELECTION ====================
const sizeButtons = document.querySelectorAll('.size-btn');
const productPrice = document.getElementById('product-price');
let basePrice = 50000; // Giá mặc định size M
let selectedSize = 'M';

sizeButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Xóa active class
        sizeButtons.forEach(btn => btn.classList.remove('active'));
        
        // Thêm active class
        button.classList.add('active');
        
        // Cập nhật giá
        basePrice = parseInt(button.getAttribute('data-price'));
        selectedSize = button.getAttribute('data-size');
        updateTotalPrice();
    });
});

// ==================== TOPPING SELECTION ====================
const toppingButtons = document.querySelectorAll('.topping-btn');
let selectedToppings = [];

toppingButtons.forEach(button => {
    button.addEventListener('click', () => {
        const toppingName = button.getAttribute('data-topping');
        const toppingPrice = parseInt(button.getAttribute('data-price'));
        
        // Toggle active class
        button.classList.toggle('active');
        
        // Thêm hoặc xóa topping
        if (button.classList.contains('active')) {
            selectedToppings.push({
                name: toppingName,
                price: toppingPrice
            });
        } else {
            selectedToppings = selectedToppings.filter(t => t.name !== toppingName);
        }
        
        updateTotalPrice();
    });
});

// ==================== QUANTITY CONTROL ====================
const decreaseBtn = document.getElementById('decrease-qty');
const increaseBtn = document.getElementById('increase-qty');
const quantityDisplay = document.getElementById('quantity');
let quantity = 1;

decreaseBtn.addEventListener('click', () => {
    if (quantity > 1) {
        quantity--;
        quantityDisplay.textContent = quantity;
        updateTotalPrice();
    }
});

increaseBtn.addEventListener('click', () => {
    if (quantity < 99) {
        quantity++;
        quantityDisplay.textContent = quantity;
        updateTotalPrice();
    }
});

// ==================== UPDATE TOTAL PRICE ====================
function updateTotalPrice() {
    let toppingTotal = selectedToppings.reduce((sum, topping) => sum + topping.price, 0);
    let totalPrice = (basePrice + toppingTotal) * quantity;
    
    productPrice.textContent = formatPrice(totalPrice);
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price).replace('₫', 'đ');
}

// ==================== TAB SWITCHING ====================
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Xóa active class
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Thêm active class
        button.classList.add('active');
        
        // Hiển thị tab content tương ứng
        const tabId = button.getAttribute('data-tab');
        document.getElementById(`tab-${tabId}`).classList.add('active');
    });
});

// ==================== ADD TO CART ====================
const addToCartBtn = document.getElementById('add-to-cart');
const cartCount = document.querySelector('.cart-count');

addToCartBtn.addEventListener('click', () => {
    // Lấy thông tin sản phẩm
    const productName = document.getElementById('product-name').textContent;
    const productCategory = document.getElementById('product-cat-badge').textContent;
    
    // Tạo object sản phẩm
    const product = {
        name: productName,
        category: productCategory,
        size: selectedSize,
        toppings: selectedToppings.map(t => t.name),
        quantity: quantity,
        price: basePrice + selectedToppings.reduce((sum, t) => sum + t.price, 0),
        total: (basePrice + selectedToppings.reduce((sum, t) => sum + t.price, 0)) * quantity
    };
    
    // Lưu vào localStorage (giỏ hàng)
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.push(product);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Cập nhật số lượng giỏ hàng
    updateCartCount();
    
    // Hiển thị thông báo
    showNotification(`✓ Đã thêm ${quantity} ${productName} vào giỏ hàng!`);
    
    // Animation cho button
    addToCartBtn.textContent = '✓ Đã thêm';
    addToCartBtn.style.backgroundColor = '#9FC885';
    
    setTimeout(() => {
        addToCartBtn.innerHTML = '🛒 Thêm vào giỏ hàng';
        addToCartBtn.style.backgroundColor = '';
    }, 1500);
});

// ==================== BUY NOW ====================
const buyNowBtn = document.getElementById('buy-now');

buyNowBtn.addEventListener('click', () => {
    // Lấy thông tin sản phẩm
    const productName = document.getElementById('product-name').textContent;
    
    // Thêm vào giỏ hàng trước
    const product = {
        name: productName,
        category: document.getElementById('product-cat-badge').textContent,
        size: selectedSize,
        toppings: selectedToppings.map(t => t.name),
        quantity: quantity,
        price: basePrice + selectedToppings.reduce((sum, t) => sum + t.price, 0),
        total: (basePrice + selectedToppings.reduce((sum, t) => sum + t.price, 0)) * quantity
    };
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.push(product);
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Chuyển đến trang thanh toán
    showNotification('⚡ Đang chuyển đến trang thanh toán...');
    
    setTimeout(() => {
        // TODO: Chuyển đến trang checkout
        window.location.href = 'checkout.html';
    }, 1000);
});

// ==================== UPDATE CART COUNT ====================
function updateCartCount() {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    let totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    
    // Animation
    cartCount.style.transform = 'scale(1.3)';
    setTimeout(() => {
        cartCount.style.transform = 'scale(1)';
    }, 300);
}

// ==================== NOTIFICATION ====================
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background-color: #B6DA9F;
        color: #2C2C2C;
        padding: 15px 25px;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        font-weight: 600;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ==================== LOAD PRODUCT DATA FROM URL ====================
// Lấy ID sản phẩm từ URL (nếu có)
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

if (productId) {
    // TODO: Fetch product data từ API
    // Hiện tại dùng data tĩnh
    console.log('Loading product ID:', productId);
}

// Cập nhật cart count khi load trang
updateCartCount();

// ==================== CONSOLE LOG ====================
console.log('📦 Product Detail Page loaded!');
console.log('🛒 Selected Size:', selectedSize);
console.log('🍰 Selected Toppings:', selectedToppings);
