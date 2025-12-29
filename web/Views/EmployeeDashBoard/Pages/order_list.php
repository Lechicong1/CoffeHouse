<!-- Link CSS riêng cho Order List -->
<link rel="stylesheet" href="Public/Css/staff-layout.css">

<style>
    .order-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .order-header h2 {
        font-size: 1.8rem;
        color: #064528;
        margin: 0;
    }

    /* Filter Bar */
    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 10px 20px;
        border: 2px solid #ddd;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        color: #666;
    }

    .filter-btn.active {
        background: #064528;
        color: white;
        border-color: #064528;
    }

    .filter-btn:hover {
        border-color: #064528;
        color: #064528;
    }

    .search-box {
        display: flex;
        gap: 10px;
    }

    .search-box input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 25px;
        font-size: 1rem;
    }

    .search-box button {
        padding: 12px 30px;
        background: #064528;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
    }

    /* Order Table */
    .order-table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .order-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-table thead {
        background: #064528;
        color: white;
    }

    .order-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .order-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .order-table tbody tr:hover {
        background: #f9f9f9;
    }

    .order-table td {
        padding: 15px;
        vertical-align: middle;
    }

    .order-code {
        color: #064528;
        font-weight: 700;
        cursor: pointer;
        text-decoration: underline;
    }

    .order-code:hover {
        color: #0a5c36;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .customer-name {
        font-weight: 600;
        color: #333;
    }

    .customer-phone {
        font-size: 0.85rem;
        color: #666;
    }

    .order-type-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 3px;
    }

    .badge-counter {
        background: #e3f2fd;
        color: #1976d2;
    }

    .badge-takeaway {
        background: #fff3e0;
        color: #f57c00;
    }

    .total-amount {
        font-weight: 700;
        color: #064528;
        font-size: 1.1rem;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .payment-paid {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .payment-unpaid {
        background: #f5f5f5;
        color: #666;
    }

    .payment-refunded {
        background: #ffebee;
        color: #c62828;
    }

    .note-cell {
        max-width: 200px;
    }

    .note-text {
        color: #666;
        font-style: italic;
        font-size: 0.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .note-edit-btn {
        color: #064528;
        cursor: pointer;
        font-size: 0.85rem;
        text-decoration: underline;
        margin-top: 3px;
        display: inline-block;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .status-processing {
        background: #fff8e1;
        color: #f57f17;
    }

    .status-delivering {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-completed {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-cancelled {
        background: #ffebee;
        color: #c62828;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-print {
        background: #e3f2fd;
        color: #1976d2;
    }

    .btn-complete {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .btn-cancel {
        background: #ffebee;
        color: #c62828;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Modals */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 15px;
        max-width: 600px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 5px 30px rgba(0,0,0,0.3);
        animation: modalFadeIn 0.3s;
    }

    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .modal-header h3 {
        margin: 0;
        color: #064528;
        font-size: 1.4rem;
    }

    .close-modal {
        font-size: 1.8rem;
        color: #999;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .close-modal:hover {
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-size: 1rem;
        font-family: inherit;
        resize: vertical;
        min-height: 100px;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .modal-btn {
        padding: 12px 25px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .btn-save {
        background: #064528;
        color: white;
    }

    .btn-cancel-modal {
        background: #f0f0f0;
        color: #666;
    }

    .modal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Order Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .items-table th {
        background: #f5f5f5;
        padding: 10px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
    }

    .items-table td {
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
    }

    .item-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
    }

    .alert-text {
        color: #c62828;
        font-weight: 600;
        margin-top: 15px;
        padding: 12px;
        background: #ffebee;
        border-radius: 8px;
    }
</style>

<?php
// Lấy dữ liệu từ Controller
$orders = $data['orders'] ?? [];
$currentFilter = $data['currentFilter'] ?? [];
$currentStatus = $currentFilter['status'] ?? '';
$currentSearch = $currentFilter['search'] ?? '';
?>

<div class="order-container">
    <!-- Header -->
    <div class="order-header">
        <h2>📋 Quản Lý Đơn Hàng</h2>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-buttons">
            <a href="/COFFEE_PHP/Staff/orders" class="filter-btn <?php echo empty($currentStatus) ? 'active' : ''; ?>">
                Tất cả
            </a>
            <a href="/COFFEE_PHP/Staff/orders?status=PROCESSING" class="filter-btn <?php echo $currentStatus === 'PROCESSING' ? 'active' : ''; ?>">
                ⏳ Đang pha chế
            </a>
            <a href="/COFFEE_PHP/Staff/orders?status=COMPLETED" class="filter-btn <?php echo $currentStatus === 'COMPLETED' ? 'active' : ''; ?>">
                ✅ Hoàn thành
            </a>
            <a href="/COFFEE_PHP/Staff/orders?status=CANCELLED" class="filter-btn <?php echo $currentStatus === 'CANCELLED' ? 'active' : ''; ?>">
                ❌ Đã hủy
            </a>
        </div>

        <form method="GET" action="/COFFEE_PHP/Staff/orders" class="search-box">
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($currentStatus); ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Tìm theo mã đơn hoặc SĐT..." value="<?php echo htmlspecialchars($currentSearch); ?>">
            <button type="submit">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Order Table -->
    <div class="order-table-container">
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Ghi chú</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                            Không có đơn hàng nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <!-- Mã đơn -->
                            <td>
                                <span class="order-code" onclick="openOrderDetail(<?php echo $order['id']; ?>)">
                                    #<?php echo htmlspecialchars($order['order_code']); ?>
                                </span>
                            </td>

                            <!-- Khách hàng -->
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name">
                                        <?php echo htmlspecialchars($order['customer_name'] ?? $order['receiver_name'] ?? 'Khách lẻ'); ?>
                                    </span>
                                    <span class="customer-phone">
                                        <?php echo htmlspecialchars($order['customer_phone'] ?? $order['receiver_phone'] ?? ''); ?>
                                    </span>
                                    <span class="order-type-badge <?php echo $order['order_type'] === 'AT_COUNTER' ? 'badge-counter' : 'badge-takeaway'; ?>">
                                        <?php echo $order['order_type'] === 'AT_COUNTER' ? 'Tại quầy' : 'Mang về'; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Tổng tiền -->
                            <td>
                                <span class="total-amount">
                                    <?php echo number_format($order['total_amount'], 0, ',', '.'); ?> ₫
                                </span>
                            </td>

                            <!-- Thanh toán -->
                            <td>
                                <?php
                                $paymentClass = 'payment-unpaid';
                                $paymentText = 'Chưa thanh toán';
                                $paymentIcon = '⏳';

                                if ($order['payment_status'] === 'PAID') {
                                    $paymentClass = 'payment-paid';
                                    $paymentText = 'Đã thanh toán';
                                    $paymentIcon = '✅';
                                } elseif ($order['payment_status'] === 'REFUNDED') {
                                    $paymentClass = 'payment-refunded';
                                    $paymentText = 'Đã hoàn tiền';
                                    $paymentIcon = '↩️';
                                }
                                ?>
                                <span class="payment-badge <?php echo $paymentClass; ?>">
                                    <?php echo $paymentIcon; ?> <?php echo $paymentText; ?>
                                </span>
                            </td>

                            <!-- Ghi chú -->
                            <td class="note-cell">
                                <div class="note-text">
                                    <?php echo !empty($order['note']) ? htmlspecialchars($order['note']) : '<span style="color:#ccc;">Không có ghi chú</span>'; ?>
                                </div>
                                <?php if ($order['status'] === 'PROCESSING'): ?>
                                    <span class="note-edit-btn" onclick="openEditNoteModal(<?php echo $order['id']; ?>, '<?php echo addslashes($order['note'] ?? ''); ?>')">
                                        Sửa
                                    </span>
                                <?php endif; ?>
                            </td>

                            <!-- Trạng thái -->
                            <td>
                                <?php
                                $statusClass = 'status-processing';
                                $statusText = 'Đang pha chế';
                                $statusIcon = '⏳';

                                switch ($order['status']) {
                                    case 'COMPLETED':
                                        $statusClass = 'status-completed';
                                        $statusText = 'Hoàn thành';
                                        $statusIcon = '✅';
                                        break;
                                    case 'CANCELLED':
                                        $statusClass = 'status-cancelled';
                                        $statusText = 'Đã hủy';
                                        $statusIcon = '❌';
                                        if ($order['payment_status'] === 'REFUNDED') {
                                            $statusText .= ' (Đã hoàn tiền)';
                                        }
                                        break;
                                    case 'DELIVERING':
                                        $statusClass = 'status-delivering';
                                        $statusText = 'Đang giao';
                                        $statusIcon = '🚚';
                                        break;
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusIcon; ?> <?php echo $statusText; ?>
                                </span>
                            </td>

                            <!-- Hành động -->
                            <td>
                                <div class="action-btns">
                                    <!-- In hóa đơn -->
                                    <button class="action-btn btn-print" onclick="printOrder(<?php echo $order['id']; ?>)">
                                        🖨️ In
                                    </button>

                                    <!-- Hoàn thành (chỉ hiện khi PROCESSING) -->
                                    <?php if ($order['status'] === 'PROCESSING'): ?>
                                        <button class="action-btn btn-complete" onclick="updateStatus(<?php echo $order['id']; ?>, 'COMPLETED')">
                                            ✅ Xong
                                        </button>

                                        <!-- Hủy đơn -->
                                        <button class="action-btn btn-cancel" onclick="openCancelModal(<?php echo $order['id']; ?>, '<?php echo addslashes($order['order_code']); ?>', '<?php echo $order['payment_status']; ?>')">
                                            ❌ Hủy
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Chi tiết đơn hàng -->
<div id="orderDetailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Chi tiết đơn hàng</h3>
            <button class="close-modal" onclick="closeOrderDetailModal()">&times;</button>
        </div>
        <div id="orderDetailContent">
            <!-- Content loaded by JS -->
        </div>
    </div>
</div>

<!-- Modal Sửa ghi chú -->
<div id="editNoteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sửa ghi chú đơn hàng</h3>
            <button class="close-modal" onclick="closeEditNoteModal()">&times;</button>
        </div>
        <form method="POST" action="/COFFEE_PHP/Staff/updateOrderNote">
            <input type="hidden" name="order_id" id="edit-order-id">
            <div class="form-group">
                <label>Ghi chú:</label>
                <textarea name="note" id="edit-note" placeholder="Nhập ghi chú..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-btn btn-cancel-modal" onclick="closeEditNoteModal()">Hủy</button>
                <button type="submit" class="modal-btn btn-save">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xác nhận hủy -->
<div id="cancelOrderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>⚠️ Xác nhận hủy đơn</h3>
            <button class="close-modal" onclick="closeCancelModal()">&times;</button>
        </div>
        <p>Bạn có chắc muốn hủy đơn <strong id="cancel-order-code"></strong> không?</p>
        <p id="refund-warning" class="alert-text" style="display: none;">
            <strong>Lưu ý:</strong> Đơn hàng này đã thanh toán. Hệ thống sẽ ghi nhận HOÀN TIỀN cho khách.
        </p>
        <form method="POST" action="/COFFEE_PHP/Staff/updateOrderStatus">
            <input type="hidden" name="order_id" id="cancel-order-id">
            <input type="hidden" name="status" value="CANCELLED">
            <div class="modal-actions">
                <button type="button" class="modal-btn btn-cancel-modal" onclick="closeCancelModal()">Không</button>
                <button type="submit" class="modal-btn btn-save" style="background: #c62828;">Xác nhận hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
// Hàm xem chi tiết đơn hàng
function openOrderDetail(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const content = document.getElementById('orderDetailContent');
    
    content.innerHTML = '<p style="text-align:center;padding:20px;">Đang tải...</p>';
    modal.style.display = 'flex';
    
    // Fetch order items
    fetch(`/COFFEE_PHP/Staff/getOrderDetail?order_id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<table class="items-table"><thead><tr><th>Sản phẩm</th><th>Size</th><th>SL</th><th>Giá</th><th>Thành tiền</th></tr></thead><tbody>';
                
                let total = 0;
                data.items.forEach(item => {
                    const subtotal = item.price_at_purchase * item.quantity;
                    total += subtotal;
                    
                    html += `<tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="${item.product_image || 'Public/Assets/default-product.jpg'}" class="item-image">
                                <span>${item.product_name}</span>
                            </div>
                        </td>
                        <td>${item.size_name}</td>
                        <td>${item.quantity}</td>
                        <td>${new Intl.NumberFormat('vi-VN').format(item.price_at_purchase)} ₫</td>
                        <td><strong>${new Intl.NumberFormat('vi-VN').format(subtotal)} ₫</strong></td>
                    </tr>`;
                });
                
                html += `</tbody><tfoot><tr><td colspan="4" style="text-align:right;font-weight:bold;">Tổng cộng:</td><td style="font-weight:bold;color:#064528;font-size:1.2rem;">${new Intl.NumberFormat('vi-VN').format(total)} ₫</td></tr></tfoot></table>`;
                
                content.innerHTML = html;
            } else {
                content.innerHTML = `<p style="color:#c62828;text-align:center;">${data.message}</p>`;
            }
        })
        .catch(error => {
            content.innerHTML = '<p style="color:#c62828;text-align:center;">Lỗi khi tải dữ liệu</p>';
        });
}

function closeOrderDetailModal() {
    document.getElementById('orderDetailModal').style.display = 'none';
}

// Hàm sửa ghi chú
function openEditNoteModal(orderId, currentNote) {
    document.getElementById('edit-order-id').value = orderId;
    document.getElementById('edit-note').value = currentNote;
    document.getElementById('editNoteModal').style.display = 'flex';
}

function closeEditNoteModal() {
    document.getElementById('editNoteModal').style.display = 'none';
}

// Hàm hủy đơn
function openCancelModal(orderId, orderCode, paymentStatus) {
    document.getElementById('cancel-order-id').value = orderId;
    document.getElementById('cancel-order-code').textContent = '#' + orderCode;
    
    // Hiện cảnh báo hoàn tiền nếu đã thanh toán
    const refundWarning = document.getElementById('refund-warning');
    if (paymentStatus === 'PAID') {
        refundWarning.style.display = 'block';
    } else {
        refundWarning.style.display = 'none';
    }
    
    document.getElementById('cancelOrderModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelOrderModal').style.display = 'none';
}

// Hàm cập nhật trạng thái
function updateStatus(orderId, status) {
    if (confirm('Xác nhận đánh dấu đơn hàng hoàn thành?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/COFFEE_PHP/Staff/updateOrderStatus';
        
        const orderIdInput = document.createElement('input');
        orderIdInput.type = 'hidden';
        orderIdInput.name = 'order_id';
        orderIdInput.value = orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(orderIdInput);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Hàm in hóa đơn
function printOrder(orderId) {
    alert('Chức năng in hóa đơn #' + orderId + ' (Sẽ được phát triển)');
    // TODO: Implement print functionality
}

// Close modal khi click outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}
</script>
