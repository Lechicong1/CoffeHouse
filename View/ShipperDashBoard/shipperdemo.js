/* ============================================
   FILE: shipperdemo.js
   DESCRIPTION: JavaScript xử lý logic Dashboard Shipper
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Dữ liệu đơn hàng mẫu (Hard-coded cho demo)
let orders = [
    // ===== TRẠNG THÁI: CHỜ GIAO (PENDING) =====
    {
        id: 'ORD001',
        customerName: 'Trần Thị B',
        phone: '0912345678',
        address: '123 Nguyễn Huệ, Q.1, TP.HCM',
        items: [
            { name: 'Cà phê cốt dừa', quantity: 2 },
            { name: 'Trà đào cam sả', quantity: 1 }
        ],
        total: 145000,
        status: 'pending',
        orderTime: '10:30 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD003',
        customerName: 'Phạm Thị D',
        phone: '0901234567',
        address: '789 Trần Hưng Đạo, Q.5, TP.HCM',
        items: [
            { name: 'Cappuccino', quantity: 1 },
            { name: 'Croissant', quantity: 2 }
        ],
        total: 120000,
        status: 'pending',
        orderTime: '11:00 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD007',
        customerName: 'Đỗ Văn H',
        phone: '0956789012',
        address: '222 Hai Bà Trưng, Q.1, TP.HCM',
        items: [
            { name: 'Espresso', quantity: 2 },
            { name: 'Macchiato', quantity: 1 },
            { name: 'Bánh Flan', quantity: 2 }
        ],
        total: 175000,
        status: 'pending',
        orderTime: '11:15 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD008',
        customerName: 'Bùi Thị I',
        phone: '0967890123',
        address: '333 Cách Mạng Tháng 8, Q.10, TP.HCM',
        items: [
            { name: 'Trà sữa ô long', quantity: 3 },
            { name: 'Trà xanh matcha', quantity: 1 }
        ],
        total: 165000,
        status: 'pending',
        orderTime: '11:45 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD009',
        customerName: 'Ngô Văn K',
        phone: '0978901234',
        address: '444 Lý Thường Kiệt, Q.Tân Bình, TP.HCM',
        items: [
            { name: 'Cà phê đen đá', quantity: 1 },
            { name: 'Bạc xỉu', quantity: 2 },
            { name: 'Bánh mì thịt', quantity: 1 }
        ],
        total: 135000,
        status: 'pending',
        orderTime: '12:00 PM',
        orderDate: '28/11/2025'
    },

    // ===== TRẠNG THÁI: ĐANG GIAO (DELIVERING) =====
    {
        id: 'ORD002',
        customerName: 'Lê Văn C',
        phone: '0987654321',
        address: '456 Lê Lợi, Q.3, TP.HCM',
        items: [
            { name: 'Bạc xỉu', quantity: 3 },
            { name: 'Bánh mì pate', quantity: 2 }
        ],
        total: 185000,
        status: 'delivering',
        orderTime: '09:15 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD005',
        customerName: 'Hoàng Thị F',
        phone: '0934567890',
        address: '555 Nguyễn Thị Minh Khai, Q.1, TP.HCM',
        items: [
            { name: 'Latte', quantity: 2 },
            { name: 'Tiramisu', quantity: 1 }
        ],
        total: 210000,
        status: 'delivering',
        orderTime: '10:00 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD010',
        customerName: 'Trịnh Thị L',
        phone: '0989012345',
        address: '666 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM',
        items: [
            { name: 'Caramel Macchiato', quantity: 1 },
            { name: 'Mocha', quantity: 2 },
            { name: 'Cheesecake', quantity: 1 }
        ],
        total: 245000,
        status: 'delivering',
        orderTime: '09:45 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD011',
        customerName: 'Mai Văn M',
        phone: '0990123456',
        address: '777 Xô Viết Nghệ Tĩnh, Q.Bình Thạnh, TP.HCM',
        items: [
            { name: 'Trà chanh', quantity: 2 },
            { name: 'Nước ép cam', quantity: 1 },
            { name: 'Sandwich', quantity: 2 }
        ],
        total: 195000,
        status: 'delivering',
        orderTime: '10:20 AM',
        orderDate: '28/11/2025'
    },

    // ===== TRẠNG THÁI: ĐÃ HOÀN THÀNH (COMPLETED) =====
    {
        id: 'ORD004',
        customerName: 'Nguyễn Văn E',
        phone: '0923456789',
        address: '321 Võ Văn Tần, Q.3, TP.HCM',
        items: [
            { name: 'Trà sữa trân châu', quantity: 2 },
            { name: 'Trà đào cam sả', quantity: 1 }
        ],
        total: 155000,
        status: 'completed',
        orderTime: '08:45 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD006',
        customerName: 'Vũ Văn G',
        phone: '0945678901',
        address: '888 Phan Xích Long, Q.Phú Nhuận, TP.HCM',
        items: [
            { name: 'Americano', quantity: 1 },
            { name: 'Bánh croissant', quantity: 1 }
        ],
        total: 95000,
        status: 'completed',
        orderTime: '07:30 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD012',
        customerName: 'Đặng Thị N',
        phone: '0901234568',
        address: '999 Cộng Hòa, Q.Tân Bình, TP.HCM',
        items: [
            { name: 'Cà phê sữa đá', quantity: 3 },
            { name: 'Bánh bông lan', quantity: 2 }
        ],
        total: 165000,
        status: 'completed',
        orderTime: '08:00 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD013',
        customerName: 'Phan Văn O',
        phone: '0912345679',
        address: '111 Hoàng Văn Thụ, Q.Phú Nhuận, TP.HCM',
        items: [
            { name: 'Trà sữa thái', quantity: 2 },
            { name: 'Pudding', quantity: 3 }
        ],
        total: 185000,
        status: 'completed',
        orderTime: '07:15 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD014',
        customerName: 'Lý Thị P',
        phone: '0923456780',
        address: '222 Lạc Long Quân, Q.11, TP.HCM',
        items: [
            { name: 'Freeze Chocolate', quantity: 1 },
            { name: 'Freeze Caramel', quantity: 1 },
            { name: 'Muffin', quantity: 2 }
        ],
        total: 205000,
        status: 'completed',
        orderTime: '06:45 AM',
        orderDate: '28/11/2025'
    },
    {
        id: 'ORD015',
        customerName: 'Trương Văn Q',
        phone: '0934567891',
        address: '333 Nguyễn Văn Cừ, Q.5, TP.HCM',
        items: [
            { name: 'Cà phê phin', quantity: 2 },
            { name: 'Trà gừng', quantity: 1 },
            { name: 'Bánh tráng nướng', quantity: 1 }
        ],
        total: 125000,
        status: 'completed',
        orderTime: '08:30 AM',
        orderDate: '28/11/2025'
    }
];

// Biến toàn cục
let currentFilter = 'all';
let selectedOrder = null;
let pendingAction = null;

// Chờ DOM load xong
document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
});

/**
 * Khởi tạo dashboard
 */
function initDashboard() {
    updateStats();
    renderOrders();
}

/**
 * Cập nhật thống kê
 */
function updateStats() {
    const totalOrders = orders.length;
    const deliveryOrders = orders.filter(o => o.status === 'delivering').length;
    const completedOrders = orders.filter(o => o.status === 'completed').length;

    document.getElementById('totalOrders').textContent = totalOrders;
    document.getElementById('deliveryOrders').textContent = deliveryOrders;
    document.getElementById('completedOrders').textContent = completedOrders;
}

/**
 * Render danh sách đơn hàng
 */
function renderOrders() {
    const container = document.getElementById('ordersContainer');
    
    // Lọc đơn hàng theo filter
    let filteredOrders = orders;
    if (currentFilter !== 'all') {
        filteredOrders = orders.filter(order => order.status === currentFilter);
    }

    // Kiểm tra nếu không có đơn hàng
    if (filteredOrders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div class="empty-text">Không có đơn hàng nào</div>
            </div>
        `;
        return;
    }

    // Render các đơn hàng
    container.innerHTML = filteredOrders.map(order => createOrderCard(order)).join('');
}

/**
 * Tạo HTML card cho đơn hàng
 */
function createOrderCard(order) {
    const statusClass = `status-${order.status}`;
    const statusText = getStatusText(order.status);
    
    // Tạo danh sách items
    const itemsHTML = order.items.map(item => `
        <div class="item-row">
            <span class="item-name">${item.name}</span>
            <span class="item-quantity">x${item.quantity}</span>
        </div>
    `).join('');

    // Tạo nút hành động dựa trên trạng thái
    let actionsHTML = '';
    if (order.status === 'pending') {
        actionsHTML = `
            <button class="btn-action btn-deliver" onclick="handleDeliverOrder('${order.id}')">
                🚚 Giao hàng
            </button>
        `;
    } else if (order.status === 'delivering') {
        actionsHTML = `
            <button class="btn-action btn-complete" onclick="handleCompleteOrder('${order.id}')">
                ✅ Hoàn thành
            </button>
        `;
    } else {
        actionsHTML = `
            <button class="btn-action btn-complete" disabled>
                ✅ Đã hoàn thành
            </button>
        `;
    }

    return `
        <div class="order-card" data-status="${order.status}">
            <div class="order-header">
                <div class="order-id">
                    <strong>${order.id}</strong>
                </div>
                <div class="order-status ${statusClass}">
                    ${statusText}
                </div>
            </div>
            <div class="order-body">
                <div class="order-info">
                    <div class="info-label">Khách hàng</div>
                    <div class="info-value">${order.customerName}</div>
                </div>
                <div class="order-info">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value">${order.phone}</div>
                </div>
                <div class="order-info">
                    <div class="info-label">Địa chỉ</div>
                    <div class="info-value">${order.address}</div>
                </div>
                <div class="order-info">
                    <div class="info-label">Thời gian</div>
                    <div class="info-value">${order.orderTime} - ${order.orderDate}</div>
                </div>
            </div>
            <div class="order-items">
                <div class="items-title">Chi tiết đơn hàng</div>
                <div class="items-list">
                    ${itemsHTML}
                </div>
            </div>
            <div class="order-footer">
                <div class="order-total">
                    Tổng: ${formatCurrency(order.total)}
                </div>
                <div class="order-actions">
                    ${actionsHTML}
                </div>
            </div>
        </div>
    `;
}

/**
 * Lấy text hiển thị của trạng thái
 */
function getStatusText(status) {
    const statusMap = {
        'pending': 'Chờ giao',
        'delivering': 'Đang giao',
        'completed': 'Đã hoàn thành'
    };
    return statusMap[status] || status;
}

/**
 * Format số tiền
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

/**
 * Lọc đơn hàng
 */
function filterOrders(filter) {
    currentFilter = filter;
    
    // Cập nhật UI nút filter
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
    
    // Render lại danh sách
    renderOrders();
}

/**
 * Xử lý giao hàng
 */
function handleDeliverOrder(orderId) {
    selectedOrder = orders.find(o => o.id === orderId);
    pendingAction = 'deliver';
    
    showModal(
        'Xác nhận giao hàng',
        `Bạn có chắc chắn muốn bắt đầu giao đơn hàng <strong>${orderId}</strong>?`,
        () => {
            // Cập nhật trạng thái
            selectedOrder.status = 'delivering';
            
            // Cập nhật UI
            updateStats();
            renderOrders();
            
            // Hiển thị thông báo
            showAlert(`Đã bắt đầu giao đơn hàng ${orderId}`, 'info');
            
            closeModal();
        }
    );
}

/**
 * Xử lý hoàn thành đơn hàng
 */
function handleCompleteOrder(orderId) {
    selectedOrder = orders.find(o => o.id === orderId);
    pendingAction = 'complete';
    
    showModal(
        'Xác nhận hoàn thành',
        `Bạn có chắc chắn đơn hàng <strong>${orderId}</strong> đã được giao thành công?`,
        () => {
            // Cập nhật trạng thái
            selectedOrder.status = 'completed';
            
            // Cập nhật UI
            updateStats();
            renderOrders();
            
            // Hiển thị thông báo
            showAlert(`Đơn hàng ${orderId} đã hoàn thành!`, 'success');
            
            closeModal();
        }
    );
}

/**
 * Hiển thị modal xác nhận
 */
function showModal(title, message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    
    modalTitle.textContent = title;
    modalMessage.innerHTML = message;
    
    // Xóa event listener cũ
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    // Thêm event listener mới
    document.getElementById('confirmBtn').addEventListener('click', onConfirm);
    
    modal.classList.add('show');
}

/**
 * Đóng modal
 */
function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('show');
    selectedOrder = null;
    pendingAction = null;
}

/**
 * Hiển thị thông báo
 */
function showAlert(message, type) {
    const alertBox = document.getElementById('alertBox');
    alertBox.textContent = message;
    alertBox.className = `alert alert-${type} show`;
    
    // Tự động ẩn sau 4 giây
    setTimeout(() => {
        alertBox.classList.remove('show');
    }, 4000);
}

/**
 * Xử lý đăng xuất
 */
function handleLogout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        showAlert('Đang đăng xuất...', 'info');
        setTimeout(() => {
            window.location.href = '../Auth/Login/login.html';
        }, 1500);
    }
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) {
        closeModal();
    }
}
