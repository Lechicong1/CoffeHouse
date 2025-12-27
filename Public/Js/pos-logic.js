// ===================================
// FILE: pos-logic.js
// DESCRIPTION: Logic for POS Interface (Adapted from staff.js)
// ===================================

// --- DATA ---
let menuItems = []; // Fetched from API

// --- STATE ---
let cart = [];
let currentOrderType = 'dine-in'; 
let selectedPaymentMethod = null;

// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    fetchMenu(); // Fetch menu from server
    updateDate();
    setupEventListeners();
    updateCartUI(); // Render initial cart
});

// --- FUNCTIONS ---

async function fetchMenu() {
    try {
        const response = await fetch('/COFFEE_PHP/Staff/getMenu');
        if (!response.ok) throw new Error('Failed to fetch menu');
        menuItems = await response.json();
        renderMenu('coffee'); // Render default category after fetch
    } catch (error) {
        console.error('Error fetching menu:', error);
        alert('Không thể tải danh sách món ăn. Vui lòng thử lại.');
    }
}

function updateDate() {
    const dateElement = document.getElementById('current-date');
    if(dateElement) {
        const options = { weekday: 'long', day: 'numeric', month: 'long' };
        const today = new Date();
        dateElement.textContent = today.toLocaleDateString('vi-VN', options);
    }
}

function setupEventListeners() {
    // Category filtering
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', () => {
            // Remove active class from all
            categoryCards.forEach(c => c.classList.remove('active'));
            // Add active class to clicked
            card.classList.add('active');
            // Render menu
            const category = card.getAttribute('data-category');
            renderMenu(category);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('search-input');
    if(searchInput) {
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredItems = menuItems.filter(item => 
                item.name.toLowerCase().includes(searchTerm)
            );
            renderMenuGrid(filteredItems);
        });
    }
}

function renderMenu(category) {
    let itemsToRender = menuItems;
    if (category !== 'all') {
        itemsToRender = menuItems.filter(item => item.category === category);
    }
    renderMenuGrid(itemsToRender);
}

function renderMenuGrid(items) {
    const grid = document.getElementById('menu-grid');
    if(!grid) return;
    
    grid.innerHTML = '';

    items.forEach(item => {
        const itemEl = document.createElement('div');
        itemEl.className = 'menu-item';
        itemEl.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="menu-item-info">
                <h4>${item.name}</h4>
                <span class="price">${formatCurrency(item.price)}</span>
            </div>
            <button class="add-btn" onclick="addToCart(${item.id})">
                <i class="fas fa-plus"></i>
            </button>
        `;
        grid.appendChild(itemEl);
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function addToCart(itemId) {
    const item = menuItems.find(i => i.id === itemId);
    const existingItem = cart.find(i => i.id === itemId);

    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({ ...item, qty: 1, notes: "" });
    }
    updateCartUI();
}

function removeFromCart(itemId) {
    const index = cart.findIndex(i => i.id === itemId);
    if (index > -1) {
        cart.splice(index, 1);
    }
    updateCartUI();
}

function updateQty(itemId, change) {
    const item = cart.find(i => i.id === itemId);
    if (item) {
        item.qty += change;
        if (item.qty <= 0) {
            removeFromCart(itemId);
        } else {
            updateCartUI();
        }
    }
}

function updateCartUI() {
    const list = document.getElementById('order-list');
    if(!list) return;
    list.innerHTML = '';

    if (cart.length === 0) {
        list.innerHTML = '<div style="text-align: center; color: #999; margin-top: 50px;">Chưa có món nào</div>';
    } else {
        cart.forEach(item => {
            const itemEl = document.createElement('div');
            itemEl.className = 'order-item';
            
            // Calculate item total
            const itemTotal = item.price * item.qty;

            itemEl.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="item-info">
                    <h4>${item.name}</h4>
                    <div class="price">${formatCurrency(itemTotal)} <span style="font-weight:normal; color:#888; font-size:0.8rem;">(${formatCurrency(item.price)} x ${item.qty})</span></div>
                    ${item.notes ? `<div class="notes"><i class="fas fa-file-alt"></i> ${item.notes}</div>` : ''}
                </div>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                    <span>${item.qty}</span>
                    <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                </div>
            `;
            list.appendChild(itemEl);
        });
        // Auto-scroll to bottom
        list.scrollTop = list.scrollHeight;
    }
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const total = subtotal; // No tax

    const subtotalEl = document.getElementById('subtotal-price');
    const totalEl = document.getElementById('total-price');
    const btnTotalEl = document.getElementById('btn-total');
    const modalTotalEl = document.getElementById('modal-total');

    if(subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
    if(totalEl) totalEl.textContent = formatCurrency(total);
    if(btnTotalEl) btnTotalEl.textContent = formatCurrency(total);
    if(modalTotalEl) modalTotalEl.textContent = formatCurrency(total);
}

function setOrderType(type) {
    currentOrderType = type;
    
    // Update Buttons
    const btnDineIn = document.getElementById('btn-dine-in');
    const btnTakeAway = document.getElementById('btn-take-away');
    
    if(btnDineIn) btnDineIn.classList.toggle('active', type === 'dine-in');
    if(btnTakeAway) btnTakeAway.classList.toggle('active', type === 'take-away');

    // Update Inputs
    const tableGroup = document.getElementById('table-input-group');
    const orderIdGroup = document.getElementById('order-id-group');
    const orderIdInput = document.getElementById('order-id');

    if (type === 'dine-in') {
        if(tableGroup) tableGroup.style.display = 'block';
        if(orderIdGroup) orderIdGroup.style.display = 'none';
    } else {
        if(tableGroup) tableGroup.style.display = 'none';
        if(orderIdGroup) orderIdGroup.style.display = 'block';
        if (orderIdInput && !orderIdInput.value) {
            orderIdInput.value = generateOrderId();
        }
    }
}

function generateOrderId() {
    return 'ORD-' + Math.floor(1000 + Math.random() * 9000);
}

// --- PAYMENT MODAL ---
function openPaymentModal() {
    if (cart.length === 0) {
        alert("Vui lòng chọn món trước khi thanh toán.");
        return;
    }
    document.getElementById('payment-modal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('payment-modal').style.display = 'none';
    selectedPaymentMethod = null;
    updatePaymentSelection();
}

function selectPayment(method) {
    selectedPaymentMethod = method;
    updatePaymentSelection();
}

function updatePaymentSelection() {
    const cashBtn = document.getElementById('pay-cash');
    const cardBtn = document.getElementById('pay-card');
    
    cashBtn.style.borderColor = selectedPaymentMethod === 'cash' ? 'var(--primary-green)' : '#eee';
    cashBtn.style.backgroundColor = selectedPaymentMethod === 'cash' ? '#f0f9eb' : 'white';
    
    cardBtn.style.borderColor = selectedPaymentMethod === 'card' ? 'var(--primary-green)' : '#eee';
    cardBtn.style.backgroundColor = selectedPaymentMethod === 'card' ? '#f0f9eb' : 'white';
}

async function processPayment() {
    if (!selectedPaymentMethod) {
        alert("Vui lòng chọn phương thức thanh toán.");
        return;
    }

    const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const orderCode = currentOrderType === 'take-away' 
        ? document.getElementById('order-id').value 
        : 'DINEIN-' + Math.floor(Date.now() / 1000); // Simple unique code

    const orderData = {
        order_code: orderCode,
        order_type: currentOrderType === 'take-away' ? 'TAKE_AWAY' : 'AT_COUNTER',
        payment_method: selectedPaymentMethod === 'cash' ? 'CASH' : 'BANKING',
        total_amount: totalAmount,
        items: cart,
        note: '', 
        customer_id: null 
    };

    try {
        const response = await fetch('/COFFEE_PHP/Staff/createOrder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();

        if (result.success) {
            alert(`Thanh toán thành công! Mã đơn: ${result.order_id}`);
            cart = [];
            updateCartUI();
            closePaymentModal();
        } else {
            alert('Lỗi thanh toán: ' + result.message);
        }
    } catch (error) {
        console.error('Error processing payment:', error);
        alert('Lỗi kết nối server.');
    }
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('payment-modal');
    if (event.target == modal) {
        closePaymentModal();
    }
}

function addToCart(id) {
    const item = menuItems.find(i => i.id === id);
    const existingItem = cart.find(i => i.id === id);

    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.push({ ...item, qty: 1, notes: '' });
    }
    updateCartUI();
}

function updateCartUI() {
    const cartList = document.getElementById('order-list');
    if(!cartList) return;

    cartList.innerHTML = '';
    let subtotal = 0;

    cart.forEach(item => {
        subtotal += item.price * item.qty;
        const cartItem = document.createElement('div');
        cartItem.className = 'order-item';
        cartItem.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="item-info">
                <h4>${item.name}</h4>
                <span class="price">${formatCurrency(item.price)}</span>
                <div class="notes">${item.notes || 'Thêm ghi chú...'} <i class="fas fa-pen" style="font-size: 0.7rem; cursor: pointer;"></i></div>
            </div>
            <div class="qty-controls">
                <button class="qty-btn" onclick="changeQty(${item.id}, -1)">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn" onclick="changeQty(${item.id}, 1)">+</button>
            </div>
        `;
        cartList.appendChild(cartItem);
    });

    // Update totals
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + tax;

    document.getElementById('subtotal-price').textContent = formatCurrency(subtotal);
    document.getElementById('tax-price').textContent = formatCurrency(tax);
    document.getElementById('total-price').textContent = formatCurrency(total);
}

function changeQty(id, change) {
    const itemIndex = cart.findIndex(i => i.id === id);
    if (itemIndex > -1) {
        cart[itemIndex].qty += change;
        if (cart[itemIndex].qty <= 0) {
            cart.splice(itemIndex, 1);
        }
        updateCartUI();
    }
}

function setOrderType(type) {
    currentOrderType = type;
    document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
    if(type === 'dine-in') {
        document.getElementById('btn-dine-in').classList.add('active');
    } else {
        document.getElementById('btn-take-away').classList.add('active');
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

// Expose functions to global scope for onclick events
window.addToCart = addToCart;
window.changeQty = changeQty;
window.setOrderType = setOrderType;
