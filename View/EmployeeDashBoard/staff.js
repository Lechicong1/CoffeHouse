// ===================================
// FILE: staff.js
// DESCRIPTION: Logic for Staff Dashboard (Updated for Clone)
// ===================================

// --- HARDCODED DATA ---
const menuItems = [
    { id: 1, name: "Espresso", price: 4.20, category: "coffee", image: "https://images.unsplash.com/photo-1510707577719-ae7c14805e3a?w=200&q=80" },
    { id: 2, name: "Cappuccino", price: 3.30, category: "coffee", image: "https://images.unsplash.com/photo-1572442388796-11668a67e53d?w=200&q=80" },
    { id: 3, name: "Latte", price: 4.00, category: "coffee", image: "https://images.unsplash.com/photo-1561882468-48983335ce23?w=200&q=80" },
    { id: 4, name: "Americano", price: 4.00, category: "coffee", image: "https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=200&q=80" },
    { id: 5, name: "Mocha", price: 4.00, category: "coffee", image: "https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=200&q=80" },
    { id: 6, name: "Iced Coffee", price: 3.80, category: "coffee", image: "https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?w=200&q=80" },
    { id: 7, name: "Cold Brew", price: 4.00, category: "coffee", image: "https://images.unsplash.com/photo-1461023058943-48dbf1399f98?w=200&q=80" },
    { id: 8, name: "Flat White", price: 3.80, category: "coffee", image: "https://images.unsplash.com/photo-1577968897966-3d4325b36b61?w=200&q=80" },
    { id: 9, name: "Green Tea", price: 3.00, category: "tea", image: "https://images.unsplash.com/photo-1627435601361-ec25f5b1d0e5?w=200&q=80" },
    { id: 10, name: "Black Tea", price: 3.00, category: "tea", image: "https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=200&q=80" },
    { id: 11, name: "Milk Tea", price: 4.50, category: "tea", image: "https://images.unsplash.com/photo-1558160074-4d7d8bdf4256?w=200&q=80" },
    { id: 12, name: "Croissant", price: 2.50, category: "snack", image: "https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=200&q=80" },
    { id: 13, name: "Bagel", price: 3.00, category: "snack", image: "https://images.unsplash.com/photo-1585478684894-a9e549539665?w=200&q=80" },
    { id: 14, name: "Cheesecake", price: 5.00, category: "snack", image: "https://images.unsplash.com/photo-1524351199678-941a58a3df50?w=200&q=80" },
    { id: 15, name: "Muffin", price: 3.50, category: "snack", image: "https://images.unsplash.com/photo-1558401391-7899b4bd5bbf?w=200&q=80" }
];

// --- STATE ---
let cart = [
    // Pre-populate with the item from the image for demo
    { id: 4, name: "Americano", price: 4.00, category: "coffee", image: "https://images.unsplash.com/photo-1551033406-611cf9a28f67?w=200&q=80", qty: 2, notes: "Less Sugar" }
];
let currentOrderType = 'dine-in'; 
let selectedPaymentMethod = null;

// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    renderMenu('coffee'); // Default to coffee
    updateDate();
    setupEventListeners();
    updateCartUI(); // Render initial cart
});

// --- FUNCTIONS ---

function updateDate() {
    const dateElement = document.getElementById('current-date');
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    const today = new Date();
    dateElement.textContent = today.toLocaleDateString('en-US', options);
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
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredItems = menuItems.filter(item => 
            item.name.toLowerCase().includes(searchTerm)
        );
        renderMenuGrid(filteredItems);
    });
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
    grid.innerHTML = '';

    items.forEach(item => {
        const itemEl = document.createElement('div');
        itemEl.className = 'menu-item';
        itemEl.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="menu-item-info">
                <h4>${item.name}</h4>
                <span class="price">$${item.price.toFixed(1)}</span>
            </div>
            <button class="add-btn" onclick="addToCart(${item.id})">
                <i class="fas fa-plus"></i>
            </button>
        `;
        grid.appendChild(itemEl);
    });
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
    list.innerHTML = '';

    if (cart.length === 0) {
        list.innerHTML = '<div style="text-align: center; color: #999; margin-top: 50px;">No items in order</div>';
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
                    <div class="price">$${itemTotal.toFixed(1)} <span style="font-weight:normal; color:#888; font-size:0.8rem;">($${item.price} x ${item.qty})</span></div>
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
    }
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const tax = subtotal * 0.10; // 10% tax
    const total = subtotal + tax;

    document.getElementById('subtotal-price').textContent = `$${subtotal.toFixed(1)}`;
    document.getElementById('tax-price').textContent = `$${tax.toFixed(1)}`;
    document.getElementById('total-price').textContent = `$${total.toFixed(1)}`;
    document.getElementById('btn-total').textContent = `$${total.toFixed(1)}`;
    document.getElementById('modal-total').textContent = `$${total.toFixed(1)}`;
}

function setOrderType(type) {
    currentOrderType = type;
    
    // Update Buttons
    document.getElementById('btn-dine-in').classList.toggle('active', type === 'dine-in');
    document.getElementById('btn-take-away').classList.toggle('active', type === 'take-away');

    // Update Inputs
    const tableGroup = document.getElementById('table-input-group');
    const orderIdGroup = document.getElementById('order-id-group');
    const orderIdInput = document.getElementById('order-id');

    if (type === 'dine-in') {
        tableGroup.style.display = 'block';
        orderIdGroup.style.display = 'none';
    } else {
        tableGroup.style.display = 'none';
        orderIdGroup.style.display = 'block';
        if (!orderIdInput.value) {
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
        alert("Please add items to the order first.");
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

function processPayment() {
    if (!selectedPaymentMethod) {
        alert("Please select a payment method.");
        return;
    }

    const total = document.getElementById('total-price').textContent;
    alert(`Payment of ${total} via ${selectedPaymentMethod.toUpperCase()} successful!\nOrder placed.`);

    // Reset Order
    cart = [];
    updateCartUI();
    closePaymentModal();
}

// Close modal if clicked outside
window.onclick = function(event) {
    const modal = document.getElementById('payment-modal');
    if (event.target == modal) {
        closePaymentModal();
    }
}
