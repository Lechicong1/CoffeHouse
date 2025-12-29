/* ============================================
   FILE: shipper-logic.js
   DESCRIPTION: JavaScript xử lý logic Dashboard Shipper
   FRAMEWORK: Vanilla JS (No Dependencies)
   ============================================ */

// Biến toàn cục
let currentFilter = 'all';
let selectedOrder = null;
let pendingAction = null;

// Chờ DOM load xong
document.addEventListener('DOMContentLoaded', function () {
    initDashboard();
});

/**
 * Khởi tạo dashboard
 */
function initDashboard() {
    // Các hàm khởi tạo khác nếu cần
    // Lưu ý: Dữ liệu orders được render từ PHP (Server-side rendering)
    // nên không cần renderOrders() ở đây trừ khi dùng AJAX
}

/**
 * Lọc đơn hàng (Client-side filtering cho đơn giản)
 */
function filterOrders(filter) {
    currentFilter = filter;

    // Cập nhật UI nút filter
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

    // Lọc các card
    const cards = document.querySelectorAll('.order-card');
    let hasVisible = false;

    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        // Mapping status từ PHP sang filter
        // PHP: READY_FOR_DELIVERY -> pending
        // PHP: SHIPPING -> delivering
        // PHP: DELIVERED -> completed

        let shouldShow = false;
        if (filter === 'all') {
            shouldShow = true;
        } else if (filter === 'pending' && status === 'READY_FOR_DELIVERY') {
            shouldShow = true;
        } else if (filter === 'delivering' && status === 'SHIPPING') {
            shouldShow = true;
        } else if (filter === 'completed' && status === 'DELIVERED') {
            shouldShow = true;
        }

        if (shouldShow) {
            card.classList.remove('hidden');
            hasVisible = true;
        } else {
            card.classList.add('hidden');
        }
    });

    // Hiển thị empty state nếu không có đơn nào
    const emptyState = document.getElementById('emptyState');
    if (!hasVisible) {
        emptyState.style.display = 'block';
    } else {
        emptyState.style.display = 'none';
    }
}

/**
 * Xử lý giao hàng (Submit form ẩn)
 */
function handleDeliverOrder(orderId) {
    showModal(
        'Xác nhận giao hàng',
        `Bạn có chắc chắn muốn bắt đầu giao đơn hàng <strong>${orderId}</strong>?`,
        () => {
            document.getElementById('form-start-shipping-' + orderId).submit();
        }
    );
}

/**
 * Xử lý hoàn thành đơn hàng (Submit form ẩn)
 */
function handleCompleteOrder(orderId) {
    showModal(
        'Xác nhận hoàn thành',
        `Bạn có chắc chắn đơn hàng <strong>${orderId}</strong> đã được giao thành công?`,
        () => {
            document.getElementById('form-complete-delivery-' + orderId).submit();
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

    // Xóa event listener cũ bằng cách clone
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

    // Thêm event listener mới
    newConfirmBtn.addEventListener('click', onConfirm);

    modal.classList.add('show');
}

/**
 * Đóng modal
 */
function closeModal() {
    const modal = document.getElementById('confirmModal');
    modal.classList.remove('show');
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
        window.location.href = '/COFFEE_PHP/Auth/logout';
    }
}

// Đóng modal khi click bên ngoài
window.onclick = function (event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) {
        closeModal();
    }
}
