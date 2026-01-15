<!-- Link CSS cho Order List -->
<link rel="stylesheet" href="Public/Css/staff-layout.css">
<link rel="stylesheet" href="Public/Css/order-list.css">

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
        <h2>Quản Lý Đơn Hàng</h2>
        <div style="display:flex;gap:8px;align-items:center;">
            <button type="button" class="filter-btn" onclick="window.location.reload();" style="background: #fff; color: #064528; border-color: #064528;">Làm mới</button>
            <form method="POST" action="/COFFEE_PHP/StaffController/xuatexcel" style="margin: 0;">
            <input type="hidden" name="status" value="<?php echo isset($data['currentFilter']['status']) ? $data['currentFilter']['status'] : ''; ?>">
            <input type="hidden" name="search" value="<?php echo isset($data['currentFilter']['search']) ? $data['currentFilter']['search'] : ''; ?>">
            <button type="submit" name="btnXuatexcel" class="filter-btn" style="background: #2e7d32; color: white; border-color: #2e7d32;">
                Xuất Excel
            </button>
            </form>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-buttons">
            <a href="/COFFEE_PHP/StaffController/orders" class="filter-btn <?php echo empty($currentStatus) ? 'active' : ''; ?>">
                Tất cả
            </a>
            <a href="/COFFEE_PHP/StaffController/orders?status=PROCESSING" class="filter-btn <?php echo $currentStatus === 'PROCESSING' ? 'active' : ''; ?>">
                Đang pha chế
            </a>
            <a href="/COFFEE_PHP/StaffController/orders?status=READY" class="filter-btn <?php echo $currentStatus === 'READY' ? 'active' : ''; ?>">
                Pha chế xong
            </a>
            <a href="/COFFEE_PHP/StaffController/orders?status=COMPLETED" class="filter-btn <?php echo $currentStatus === 'COMPLETED' ? 'active' : ''; ?>">
                Hoàn thành
            </a>
            <a href="/COFFEE_PHP/StaffController/orders?status=CANCELLED" class="filter-btn <?php echo $currentStatus === 'CANCELLED' ? 'active' : ''; ?>">
                Đã hủy
            </a>
        </div>

        <form method="GET" action="/COFFEE_PHP/StaffController/orders" class="search-box">
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($currentStatus); ?>">
            <?php endif; ?>
            <input type="text" name="search" placeholder="Tìm theo mã đơn hoặc SĐT..." value="<?php echo htmlspecialchars($currentSearch); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>

    <!-- Order Table -->
    <div class="order-table-container">
        <table class="order-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Loại đơn</th>
                    <th>Số bàn</th>
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
                        <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                            Không có đơn hàng nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if (!in_array(($order['order_type'] ?? ''), ['AT_COUNTER', 'TAKEAWAY'])) continue; ?>
                        <tr>
                            <!-- Mã đơn -->
                            <td>
                                <span style="font-weight: 700; color: #064528;">
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
                                </div>
                            </td>

                            <!-- Loại đơn -->
                            <td>
                                <span class="order-type-badge <?php echo $order['order_type'] === 'AT_COUNTER' ? 'badge-counter' : 'badge-takeaway'; ?>">
                                    <?php echo $order['order_type'] === 'AT_COUNTER' ? 'Tại quầy' : 'Mang về'; ?>
                                </span>
                            </td>

                            <!-- Số bàn -->
                            <td>
                                <?php if ($order['order_type'] === 'AT_COUNTER' && !empty($order['table_number'])): ?>
                                    <span style="font-weight: 600; color: #064528; display: inline-flex; align-items: center; gap: 4px;">
                                        Bàn <?php echo htmlspecialchars($order['table_number']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.85rem;">--</span>
                                <?php endif; ?>
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

                                if ($order['payment_status'] === 'PAID') {
                                    $paymentClass = 'payment-paid';
                                    $paymentText = 'Đã thanh toán';
                                } elseif ($order['payment_status'] === 'REFUNDED') {
                                    $paymentClass = 'payment-refunded';
                                    $paymentText = 'Đã hoàn tiền';
                                }
                                ?>
                                <span class="payment-badge <?php echo $paymentClass; ?>">
                                    <?php echo $paymentText; ?>
                                </span>
                            </td>

                            <!-- Ghi chú -->
                            <td class="note-cell">
                                <div class="note-text">
                                    <?php echo !empty($order['note']) ? htmlspecialchars($order['note']) : '<span style="color:#ccc;">Không có ghi chú</span>'; ?>
                                </div>
                                <!-- Note edit moved to Edit Order modal -->
                            </td>

                            <!-- Trạng thái -->
                            <td>
                                <?php
                                $statusClass = 'status-pending';
                                $statusText = 'Chờ xác nhận';

                                switch ($order['status']) {
                                    case 'PENDING':
                                        $statusClass = 'status-pending';
                                        $statusText = 'Chờ xác nhận';
                                        break;
                                    case 'PREPARING':
                                        $statusClass = 'status-preparing';
                                        $statusText = 'Đang pha chế';
                                        break;
                                    case 'READY':
                                        $statusClass = 'status-ready';
                                        $statusText = 'Pha chế xong';
                                        break;
                                    case 'SHIPPING':
                                        $statusClass = 'status-shipping';
                                        $statusText = 'Đang giao';
                                        break;
                                    case 'COMPLETED':
                                        $statusClass = 'status-completed';
                                        $statusText = 'Hoàn thành';
                                        break;
                                    case 'CANCELLED':
                                        $statusClass = 'status-cancelled';
                                        $statusText = 'Đã hủy';
                                        
                                        break;
                                }
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>

                            <!-- Hành động -->
                            <td>
                                <div class="action-btns">
                                    <!-- Nút Xem chi tiết -->
                                    <button class="action-btn" style="background: #e3f2fd; color: #1976d2;" onclick="openOrderDetail(<?php echo $order['id']; ?>)">
                                        Xem
                                    </button>

                                    <?php if ($order['status'] === 'PENDING'): ?>
                                        <button class="action-btn btn-edit" onclick="openEditOrderModal(<?php echo $order['id']; ?>, '<?php echo $order['order_type']; ?>', '<?php echo htmlspecialchars($order['table_number'] ?? '', ENT_QUOTES); ?>', '<?php echo addslashes($order['note'] ?? ''); ?>')">
                                            Sửa
                                        </button>

                                        <button class="action-btn btn-cancel" onclick="openCancelModal(<?php echo $order['id']; ?>, '<?php echo htmlspecialchars($order['order_code'], ENT_QUOTES); ?>', '<?php echo $order['payment_status']; ?>')">
                                            Hủy
                                        </button>
                                    <?php endif; ?>

                                    <!-- In hóa đơn - chỉ hiển thị với PENDING và COMPLETED -->
                                    <?php if ($order['status'] === 'PENDING' || $order['status'] === 'COMPLETED'): ?>
                                        <button class="action-btn btn-print" onclick="printOrder(<?php echo $order['id']; ?>)">
                                            In
                                        </button>
                                    <?php endif; ?>

                                    <!-- Chỉ cho phép hoàn thành khi READY -->
                                    <?php if ($order['status'] === 'READY'): ?>
                                        <button class="action-btn btn-complete" onclick="updateStatus(<?php echo $order['id']; ?>, 'COMPLETED')">
                                            Hoàn thành
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

<!-- Modal Chỉnh sửa đơn hàng (số bàn / loại đơn / ghi chú) -->
<div id="editOrderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Chỉnh sửa đơn hàng</h3>
            <button class="close-modal" onclick="closeEditOrderModal()">&times;</button>
        </div>
        <form method="POST" action="/COFFEE_PHP/StaffController/updateOrderDetails">
            <input type="hidden" name="order_id" id="edit-order-id">
            <div class="form-group">
                <label>Loại đơn:</label>
                <select name="order_type" id="edit-order-type" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
                    <option value="AT_COUNTER">Tại quầy</option>
                    <option value="TAKEAWAY">Mang về</option>
                </select>
            </div>

            <div class="form-group" id="edit-table-group">
                <label>Số bàn:</label>
                <select name="table_number" id="edit-table-number" style="padding:8px;border:1px solid #ddd;border-radius:6px;">
                    <option value="">-- Không chọn --</option>
                    <?php for ($i=1;$i<=20;$i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo 'Bàn ' . $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Ghi chú:</label>
                <textarea name="note" id="edit-note" placeholder="Nhập ghi chú..." style="min-height:80px;"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-btn btn-cancel-modal" onclick="closeEditOrderModal()">Hủy</button>
                <button type="submit" class="modal-btn btn-save">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Xác nhận hủy -->
<div id="cancelOrderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Xác nhận hủy đơn</h3>
            <button class="close-modal" onclick="closeCancelModal()">&times;</button>
        </div>
        <p>Bạn có chắc muốn hủy đơn <strong id="cancel-order-code"></strong> không?</p>
        <p id="refund-warning" class="alert-text" style="display: none;">
            <strong>Lưu ý:</strong> Đơn hàng này đã thanh toán. Hệ thống sẽ ghi nhận HOÀN TIỀN cho khách.
        </p>
        <form method="POST" action="/COFFEE_PHP/StaffController/updateOrderStatus">
            <input type="hidden" name="order_id" id="cancel-order-id">
            <input type="hidden" name="status" value="CANCELLED">
            <div class="modal-actions">
                <button type="button" class="modal-btn btn-cancel-modal" onclick="closeCancelModal()">Không</button>
                <button type="submit" class="modal-btn btn-save" style="background: #c62828;">Xác nhận hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Link JavaScript cho Order List -->
<script src="Public/Js/order-list.js"></script>

<!-- Link JavaScript cho Order List -->
<script src="Public/Js/order-list.js"></script>

<!-- Modal Hóa Đơn -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-content">
        <div class="invoice-body" id="invoiceBody">
            <!-- Content loaded by JS -->
        </div>
        <div class="invoice-actions">
            <button class="invoice-btn invoice-btn-close" onclick="closeInvoice()" style="width: 100%;">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal Sửa ghi chú cho Item -->
<div id="editItemNoteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Sửa ghi chú món ăn</h3>
            <button class="close-modal" onclick="closeEditItemNoteModal()">&times;</button>
        </div>
        <form id="edit-item-note-form" method="POST" action="/COFFEE_PHP/StaffController/updateOrderItemNote" onsubmit="submitEditItemNote(event)">
            <input type="hidden" name="item_id" id="edit-item-id">
            <div class="form-group">
                <label>Ghi chú món:</label>
                <textarea name="note" id="edit-item-note" placeholder="Ví dụ: Ít đá, nhiều đường..." style="width:100%;min-height:100px;padding:10px;border:2px solid #ddd;border-radius:8px;font-size:0.9rem;"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="modal-btn btn-cancel-modal" onclick="closeEditItemNoteModal()">Hủy</button>
                <button type="submit" class="modal-btn btn-save">Lưu</button>
            </div>
        </form>
    </div>
</div>
