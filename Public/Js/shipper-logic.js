let currentFilter = 'all';

function filterOrders(filter) {
    currentFilter = filter;

    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

    const cards = document.querySelectorAll('.order-card');
    let hasVisible = false;

    cards.forEach(card => {
        const status = card.getAttribute('data-status');
        let shouldShow = filter === 'all' ||
            (filter === 'pending' && status === 'READY') ||
            (filter === 'delivering' && status === 'SHIPPING') ||
            (filter === 'completed' && status === 'COMPLETED');

        if (shouldShow) {
            card.classList.remove('hidden');
            hasVisible = true;
        } else {
            card.classList.add('hidden');
        }
    });

    document.getElementById('emptyState').style.display = hasVisible ? 'none' : 'block';
}

function handleDeliverOrder(orderId) {
    showModal('Xác nhận giao hàng', `Bạn có chắc chắn muốn bắt đầu giao đơn hàng <strong>${orderId}</strong>?`, 
        () => document.getElementById('form-start-shipping-' + orderId).submit());
}

function handleCompleteOrder(orderId) {
    showModal('Xác nhận hoàn thành', `Bạn có chắc chắn đơn hàng <strong>${orderId}</strong> đã được giao thành công?`, 
        () => document.getElementById('form-complete-delivery-' + orderId).submit());
}

function showModal(title, message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').innerHTML = message;

    const confirmBtn = document.getElementById('confirmBtn');
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    newConfirmBtn.addEventListener('click', onConfirm);

    modal.classList.add('show');
}

function closeModal() {
    document.getElementById('confirmModal').classList.remove('show');
}

function showAlert(message, type) {
    const alertBox = document.getElementById('alertBox');
    alertBox.textContent = message;
    alertBox.className = `alert alert-${type} show`;
    setTimeout(() => alertBox.classList.remove('show'), 4000);
}

function handleLogout() {
    if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
        window.location.href = '/COFFEE_PHP/Auth/logout';
    }
}

window.onclick = function (event) {
    const modal = document.getElementById('confirmModal');
    if (event.target === modal) closeModal();
};

