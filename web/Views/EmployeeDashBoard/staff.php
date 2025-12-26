<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Green Grounds Coffee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===================================
           FILE: staff.css
           DESCRIPTION: Stylesheet for Staff Dashboard (Clone of Reference Image)
           =================================== */

        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        :root {
            --bg-outer: #6B7C6B;             /* Sage Green Background */
            --bg-app: #F3F3E9;               /* Cream/Beige App Background */
            --primary-green: #064528;        /* Deep Forest Green */
            --primary-text: #1A1A1A;
            --secondary-text: #888888;
            --card-bg: #FFFFFF;
            --border-color: #064528;         /* Green Border for cards */
            --accent-red: #FF6B6B;
            --border-radius-lg: 30px;
            --border-radius-md: 20px;
            --border-radius-sm: 12px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-outer);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            overflow: hidden;
        }

        .app-container {
            background-color: var(--bg-app);
            width: 100%;
            height: 100%;
            max-width: 1600px;
            border-radius: var(--border-radius-lg);
            display: grid;
            grid-template-columns: 2.2fr 1fr; /* Menu takes more space */
            grid-template-rows: minmax(0, 1fr);
            gap: 30px;
            padding: 30px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* ================= LEFT SIDE: MENU SECTION ================= */
        .menu-section {
            display: flex;
            flex-direction: column;
            gap: 25px;
            height: 100%;
            overflow: hidden;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            flex-direction: column;
        }

        .brand h1 {
            font-size: 1.5rem;
            line-height: 1.2;
            color: var(--primary-green);
            font-weight: 800;
            text-transform: uppercase;
        }

        .date-info {
            font-size: 0.9rem;
            color: var(--primary-text);
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .report-btn {
            background: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .icon-btn {
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            box-shadow: var(--shadow);
            font-size: 1.1rem;
        }

        .badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: red;
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: white;
            padding: 5px 15px 5px 4px;
            border-radius: 30px;
            box-shadow: var(--shadow);

        }

        .user-profile img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info h4 {
            font-size: 0.9rem;
            margin: 0;
        }

        .user-info span {
            font-size: 0.75rem;
            color: var(--secondary-text);
        }

        /* Search Bar */
        .search-container {
            position: relative;
        }

        .search-bar {
            width: 100%;
            padding: 15px 20px;
            border-radius: 15px;
            border: 1px solid #ddd;
            background: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 1rem;
            font-family: inherit;
        }

        .filter-icon {
            color: var(--primary-green);
            cursor: pointer;
        }

        /* Categories */
        .categories {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .category-card {
            background: white;
            border-radius: var(--border-radius-md);
            padding: 20px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .category-card.active {
            background-color: var(--primary-green);
            color: white;
        }

        .category-card.active h3, 
        .category-card.active span {
            color: white;
        }

        .category-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            border: 1px solid #ddd;
            width: fit-content;
        }

        .category-card.active .category-status {
            border-color: rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
        }

        .category-card.warning .category-status {
            background: var(--accent-red);
            color: white;
            border: none;
        }

        .category-content {
            z-index: 2;
        }

        .category-content h3 {
            font-size: 1.4rem;
            margin-bottom: 5px;
        }

        .category-content span {
            font-size: 0.9rem;
            color: var(--secondary-text);
        }

        .category-img {
            position: absolute;
            right: -10px;
            bottom: -10px;
            width: 100px;
            opacity: 0.8;
            transform: rotate(-10deg);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            overflow-y: auto;
            padding-right: 5px;
            padding-bottom: 20px;
            flex: 1;
            min-height: 0;
        }

        .menu-item {
            background: white;
            border: 1px solid var(--primary-green);
            border-radius: var(--border-radius-md);
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            transition: transform 0.2s;
        }

        .menu-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .menu-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 15px; /* Slightly rounded image */
            margin-bottom: 15px;
        }

        .menu-item-info {
            width: 100%;
            text-align: left;
        }

        .menu-item h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: var(--primary-text);
        }

        .menu-item .price {
            font-weight: 700;
            color: var(--secondary-text);
            font-size: 0.95rem;
        }

        .add-btn {
            position: absolute;
            bottom: 15px;
            right: 15px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 1px solid var(--primary-text);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .add-btn:hover {
            background: var(--primary-text);
            color: white;
        }

        /* ================= RIGHT SIDE: ORDER SECTION ================= */
        .order-section {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 25px;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .order-header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            background: var(--primary-green);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .receipt-info h3 {
            font-size: 1rem;
            margin: 0;
        }

        .receipt-info span {
            font-size: 0.8rem;
            color: var(--secondary-text);
        }

        .menu-dots {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            cursor: pointer;
        }

        /* Order Type Toggle */
        .order-toggle {
            display: flex;
            border: 1px solid #ddd;
            border-radius: 30px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .toggle-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--secondary-text);
            cursor: pointer;
            transition: all 0.3s;
        }

        .toggle-btn.active {
            background: var(--primary-green);
            color: white;
        }

        /* Customer Inputs */
        .customer-details {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .input-box {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 25px;
            padding: 10px 15px;
            position: relative;
        }

        .input-box label {
            display: block;
            font-size: 0.7rem;
            color: var(--secondary-text);
            margin-bottom: 2px;
        }

        .input-box input, .input-box select {
            width: 100%;
            border: none;
            outline: none;
            font-weight: 600;
            font-size: 0.9rem;
            background: transparent;
        }

        /* Order List */
        .order-list {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f5f5f5;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item img {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            object-fit: cover;
            margin-right: 15px;
        }

        .item-info {
            flex: 1;
        }

        .item-info h4 {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .item-info .price {
            font-weight: 600;
            color: var(--primary-text);
        }

        .item-info .notes {
            font-size: 0.75rem;
            color: var(--secondary-text);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f5f5f5;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .qty-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            width: 20px;
            display: flex;
            justify-content: center;
        }

        /* Payment Summary */
        .payment-summary {
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: var(--secondary-text);
            font-weight: 500;
        }

        .summary-row.total {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary-text);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #ddd;
        }

        /* Place Order Button */
        .place-order-btn {
            background: var(--primary-green);
            color: white;
            border: none;
            width: 100%;
            padding: 18px 25px;
            border-radius: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.3s;
        }

        .place-order-btn:hover {
            background: #0a5c36;
        }

        .btn-icon {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ccc;
        }

        /* Modal (Keep existing but style it better) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 30px;
            width: 450px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- LEFT SIDE: MENU -->
        <div class="menu-section">
            <!-- Header -->
            <div class="header">
                <div class="brand">
                    <h1>Green<br>Grounds<br>Coffee</h1>
                </div>
                <div class="date-info" id="current-date">
                    Thursday, 23 June
                </div>
                <div class="header-actions">
                    <button class="report-btn">
                        <i class="fas fa-file-alt"></i> Report
                    </button>
                    <button class="icon-btn">
                        <i class="fas fa-bell"></i>
                        <span class="badge">1</span>
                    </button>
                    <div class="user-profile">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80" alt="User">
                        <div class="user-info">
                            <h4>Samantha W</h4>
                            <span>Cashier</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="search-container">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" id="search-input" placeholder="Search">
                    <i class="fas fa-sliders-h filter-icon"></i>
                </div>
            </div>

            <!-- Categories -->
            <div class="categories">
                <div class="category-card active" data-category="coffee">
                    <div class="category-status">Available</div>
                    <div class="category-content">
                        <h3>Coffee</h3>
                        <span>50 items</span>
                    </div>
                    <!-- Decorative Image/Icon -->
                    <i class="fas fa-coffee fa-5x category-img" style="color: rgba(255,255,255,0.2);"></i>
                </div>
                <div class="category-card" data-category="tea">
                    <div class="category-status">Available</div>
                    <div class="category-content">
                        <h3>Tea</h3>
                        <span>20 items</span>
                    </div>
                    <i class="fas fa-leaf fa-5x category-img" style="color: #eee;"></i>
                </div>
                <div class="category-card warning" data-category="snack">
                    <div class="category-status">Need to re-stock <i class="fas fa-info-circle"></i></div>
                    <div class="category-content">
                        <h3>Snack</h3>
                        <span>10 items</span>
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
                        <h3>Purchase Receipt</h3>
                        <span>#27362</span>
                    </div>
                </div>
                <button class="menu-dots"><i class="fas fa-bars"></i></button>
            </div>

            <div class="order-toggle">
                <button class="toggle-btn active" id="btn-dine-in" onclick="setOrderType('dine-in')">Dine In</button>
                <button class="toggle-btn" id="btn-take-away" onclick="setOrderType('take-away')">Take Away</button>
            </div>

            <div class="customer-details">
                <div class="input-box">
                    <label>Customer name</label>
                    <input type="text" id="customer-name" value="Muadz">
                </div>
                <div class="input-box" id="table-input-group">
                    <label>Table</label>
                    <select id="table-select">
                        <option value="B12">B12 - Indoor</option>
                        <option value="T1">T1 - Outdoor</option>
                        <option value="T2">T2 - Indoor</option>
                    </select>
                </div>
                <div class="input-box" id="order-id-group" style="display: none;">
                    <label>Order ID</label>
                    <input type="text" id="order-id" readonly>
                </div>
            </div>

            <div class="order-list" id="order-list">
                <!-- Order Items injected by JS -->
            </div>

            <div class="payment-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal-price">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Tax</span>
                    <span id="tax-price">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="total-price">$0.00</span>
                </div>
            </div>

            <button class="place-order-btn" onclick="openPaymentModal()">
                <div class="btn-icon">
                    <i class="fas fa-arrow-right"></i>
                    <span>Place Order</span>
                </div>
                <span id="btn-total">$0.00</span>
            </button>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div class="modal" id="payment-modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closePaymentModal()" style="float: right; cursor: pointer; font-size: 1.5rem;">&times;</span>
            <h2 style="margin-bottom: 20px;">Payment Method</h2>
            <p style="margin-bottom: 30px;">Total Amount: <span id="modal-total" style="font-weight: bold; color: var(--primary-green);">$0.00</span></p>
            
            <div style="display: flex; gap: 20px; justify-content: center; margin-bottom: 30px;">
                <div class="payment-option" id="pay-cash" onclick="selectPayment('cash')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                    <i class="fas fa-money-bill-wave fa-2x" style="margin-bottom: 10px;"></i>
                    <p>Cash</p>
                </div>
                <div class="payment-option" id="pay-card" onclick="selectPayment('card')" style="border: 2px solid #eee; padding: 20px; border-radius: 15px; cursor: pointer; flex: 1;">
                    <i class="fas fa-qrcode fa-2x" style="margin-bottom: 10px;"></i>
                    <p>Card / QR</p>
                </div>
            </div>

            <button class="confirm-btn" onclick="processPayment()" style="background: var(--primary-green); color: white; border: none; padding: 15px 40px; border-radius: 30px; font-size: 1rem; cursor: pointer; width: 100%;">Confirm Payment</button>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>