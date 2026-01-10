<!-- Bộ lọc & Thống kê -->
<?php
    $totalOrders = count($orders ?? []);
    $deliveringOrders = 0;
    $completedOrders = 0;
    
    if (!empty($orders)) {
        foreach ($orders as $o) {
            if ($o->status == 'SHIPPING') {
                $deliveringOrders++;
            } elseif ($o->status == 'COMPLETED') {
                $completedOrders++;
            }
        }
    }
?>
<div class="stats-section">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
            <div class="stat-value" id="totalOrders"><?php echo $totalOrders; ?></div>
            <div class="stat-label">Tổng đơn hàng</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🚚</div>
        <div class="stat-info">
            <div class="stat-value" id="deliveryOrders"><?php echo $deliveringOrders; ?></div>
            <div class="stat-label">Đang giao</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
            <div class="stat-value" id="completedOrders"><?php echo $completedOrders; ?></div>
            <div class="stat-label">Đã hoàn thành</div>
        </div>
    </div>
</div>

<!-- Bộ lọc đơn hàng -->
<div class="filter-section">
    <h2 class="section-title">Danh sách đơn hàng</h2>
    <div class="filter-buttons">
        <button class="filter-btn active" data-filter="all" onclick="filterOrders('all')">
            Tất cả
        </button>
        <button class="filter-btn" data-filter="pending" onclick="filterOrders('pending')">
            Chờ giao
        </button>
        <button class="filter-btn" data-filter="delivering" onclick="filterOrders('delivering')">
            Đang giao
        </button>
        <button class="filter-btn" data-filter="completed" onclick="filterOrders('completed')">
            Đã hoàn thành
        </button>
    </div>
</div>

<!-- Bảng đơn hàng -->
<div class="orders-section">
    <div id="ordersContainer" class="orders-container">
        <?php if (empty($orders)): ?>
            <div class="empty-state" id="emptyState">
                <div class="empty-icon">📦</div>
                <div class="empty-text">Không có đơn hàng nào</div>
            </div>
        <?php else: ?>
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-icon">📦</div>
                <div class="empty-text">Không có đơn hàng nào</div>
            </div>
            <?php foreach ($orders as $order): ?>
                <?php 
                    $statusClass = '';
                    $statusText = '';
                    if ($order->status == 'READY') {
                        $statusClass = 'status-pending';
                        $statusText = 'Chờ giao';
                    } elseif ($order->status == 'SHIPPING') {
                        $statusClass = 'status-delivering';
                        $statusText = 'Đang giao';
                    } elseif ($order->status == 'COMPLETED') {
                        $statusClass = 'status-completed';
                        $statusText = 'Đã hoàn thành';
                    }
                ?>
                <div class="order-card" data-status="<?php echo $order->status; ?>">
                    <div class="order-header">
                        <div class="order-id">
                            <strong><?php echo $order->order_code; ?></strong>
                        </div>
                        <div class="order-status <?php echo $statusClass; ?>">
                            <?php echo $statusText; ?>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="order-info">
                            <div class="info-label">Người nhận</div>
                            <div class="info-value"><?php echo $order->customer_name; ?></div>
                        </div>
                        <div class="order-info">
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value"><?php echo $order->receiver_phone; ?></div>
                        </div>
                        <div class="order-info">
                            <div class="info-label">Địa chỉ</div>
                            <div class="info-value"><?php echo $order->shipping_address; ?></div>
                        </div>
                        <div class="order-info">
                            <div class="info-label">Thời gian</div>
                            <div class="info-value"><?php echo $order->created_at; ?></div>
                        </div>
                    </div>
                    
                    <!-- Note: Cần load items của order nếu muốn hiển thị chi tiết -->
                    
                    <div class="order-footer">
                        <div class="order-total">
                            Tổng: <?php echo number_format($order->total_amount); ?> đ
                        </div>
                        <div class="order-actions">
                            <?php if ($order->status == 'READY'): ?>
                                <form id="form-start-shipping-<?php echo $order->id; ?>" action="index.php?url=Shipper/startShipping" method="POST" style="display: none;">
                                    <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                </form>
                                <button class="btn-action btn-deliver" onclick="handleDeliverOrder('<?php echo $order->id; ?>')">
                                    🚚 Giao hàng
                                </button>
                            <?php elseif ($order->status == 'SHIPPING'): ?>
                                <form id="form-complete-delivery-<?php echo $order->id; ?>" action="index.php?url=Shipper/completeDelivery" method="POST" style="display: none;">
                                    <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                </form>
                                <button class="btn-action btn-complete" onclick="handleCompleteOrder('<?php echo $order->id; ?>')">
                                    ✅ Hoàn thành
                                </button>
                            <?php else: ?>
                                <button class="btn-action btn-complete" disabled>
                                    ✅ Đã hoàn thành
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal xác nhận -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Xác nhận</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="modalMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal()">Hủy</button>
            <button class="btn-confirm" id="confirmBtn">Xác nhận</button>
        </div>
    </div>
</div>
