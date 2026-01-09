// Order List Page JavaScript

// Hàm xem chi tiết đơn hàng
function openOrderDetail(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const content = document.getElementById('orderDetailContent');
    // lưu orderId hiện tại để dùng khi submit AJAX cho note
    window.currentOrderDetailId = orderId;
    
    content.innerHTML = '<p style="text-align:center;padding:20px;">Đang tải...</p>';
    modal.style.display = 'flex';
    
    // Fetch order items - thêm timestamp để tránh cache
    fetch(`/COFFEE_PHP/StaffController/getOrderDetail?order_id=${orderId}&t=${Date.now()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<table class="items-table"><thead><tr><th>Sản phẩm</th><th>Size</th><th>SL</th><th>Giá</th><th>Thành tiền</th><th>Thao tác</th></tr></thead><tbody>';
                
                let total = 0;
                data.items.forEach(item => {
                    const subtotal = item.price_at_purchase * item.quantity;
                    total += subtotal;
                    
                    const imagePath = item.product_image ? `/COFFEE_PHP/${item.product_image}` : '/COFFEE_PHP/Public/Assets/default-product.jpg';
                    const itemNote = item.note ? item.note : '';
                    const noteDisplay = itemNote ? `<div style="font-size:0.85rem;color:#666;margin-top:5px;">📝 ${itemNote}</div>` : '';
                    
                    html += `<tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="${imagePath}" class="item-image" onerror="this.src='/COFFEE_PHP/Public/Assets/default-product.jpg'">
                                <div>
                                    <div>${item.product_name}</div>
                                    ${noteDisplay}
                                </div>
                            </div>
                        </td>
                        <td>${item.size_name}</td>
                        <td>${item.quantity}</td>
                        <td>${new Intl.NumberFormat('vi-VN').format(item.price_at_purchase)} ₫</td>
                        <td><strong>${new Intl.NumberFormat('vi-VN').format(subtotal)} ₫</strong></td>
                        <td>
                            <button class="action-btn" onclick="openEditItemNoteModal(${item.id}, '${itemNote.replace(/'/g, "\\'")}')">✏️</button>
                        </td>
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

// Hàm sửa ghi chú (nếu có lời gọi cũ, chuyển sang modal chỉnh sửa đơn)
function openEditNoteModal(orderId, currentNote) {
    // Mở modal chỉnh sửa đơn với note được truyền vào
    openEditOrderModal(orderId, 'AT_COUNTER', '', currentNote);
}

function closeEditNoteModal() {
    closeEditOrderModal();
}

// Mở modal chỉnh sửa đơn hàng (loại, bàn, ghi chú)
function openEditOrderModal(orderId, orderType, tableNumber, currentNote) {
    document.getElementById('edit-order-id').value = orderId;
    document.getElementById('edit-order-type').value = orderType || 'AT_COUNTER';
    document.getElementById('edit-note').value = currentNote || '';
    const tableSelect = document.getElementById('edit-table-number');
    if (tableSelect) tableSelect.value = tableNumber || '';
    // Show/hide table group based on order type
    toggleEditTableGroup(orderType || 'AT_COUNTER');
    document.getElementById('editOrderModal').style.display = 'flex';
}

function closeEditOrderModal() {
    document.getElementById('editOrderModal').style.display = 'none';
}

function toggleEditTableGroup(orderType) {
    const group = document.getElementById('edit-table-group');
    const tableSelect = document.getElementById('edit-table-number');
    if (!group) return;
    if (orderType === 'AT_COUNTER') {
        group.style.display = 'block';
    } else {
        // hide and clear
        group.style.display = 'none';
        if (tableSelect) tableSelect.value = '';
    }
}

// Gán sự kiện đổi loại trong modal
document.addEventListener('DOMContentLoaded', function() {
    const orderTypeEl = document.getElementById('edit-order-type');
    if (orderTypeEl) {
        orderTypeEl.addEventListener('change', function(e) {
            toggleEditTableGroup(e.target.value);
        });
    }
});

// Hàm sửa ghi chú cho từng item
function openEditItemNoteModal(itemId, currentNote) {
    document.getElementById('edit-item-id').value = itemId;
    document.getElementById('edit-item-note').value = currentNote;
    document.getElementById('editItemNoteModal').style.display = 'flex';
}

function closeEditItemNoteModal() {
    document.getElementById('editItemNoteModal').style.display = 'none';
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
        form.action = '/COFFEE_PHP/StaffController/updateOrderStatus';
        
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

// Close modal khi click outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Submit edit item note via AJAX and refresh order detail modal
function submitEditItemNote(e) {
    e.preventDefault();
    const form = document.getElementById('edit-item-note-form');
    const fd = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            closeEditItemNoteModal();
            // reload order detail modal content
            if (window.currentOrderDetailId) {
                openOrderDetail(window.currentOrderDetailId);
            }
            else {
                // fallback: reload page
                window.location.reload();
            }
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể cập nhật')); 
        }
    }).catch(err => {
        console.error(err);
        alert('Lỗi khi gửi yêu cầu');
    });
    return false;
}

// ===== INVOICE FUNCTIONS =====

// Hiển thị hóa đơn
function showInvoice(orderId) {
    fetch(`/COFFEE_PHP/StaffController/getOrderInvoiceData?order_id=${orderId}&t=${Date.now()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const html = generateInvoiceHTML(data.order, data.items);
                document.getElementById('invoiceBody').innerHTML = html;
                document.getElementById('invoiceModal').style.display = 'flex';
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .catch(error => {
            alert('Lỗi khi tải hóa đơn');
            console.error(error);
        });
}

// Tạo HTML cho hóa đơn
function generateInvoiceHTML(order, items) {
    const orderType = order.order_type === 'AT_COUNTER' ? 'TẠI QUẦY' : 'MANG VỀ / TAKE AWAY';
    const tableInfo = order.table_number ? `BÀN SỐ: ${order.table_number}` : orderType;
    
    // Calculate totals
    let subtotal = 0;
    items.forEach(item => {
        subtotal += parseFloat(item.price_at_purchase) * parseInt(item.quantity);
    });
    
    const discount = parseFloat(order.discount_amount || 0);
    const shipping = parseFloat(order.shipping_fee || 0);
    const total = parseFloat(order.total_amount);
    
    let html = `
        <div class="invoice-header">
            <div class="invoice-shop-name">QUÁN CÀ PHÊ</div>
            <div class="invoice-title">HÓA ĐƠN</div>
            <div class="invoice-info">Mã đơn: ${order.order_code}</div>
            <div class="invoice-info">Ngày: ${order.created_at}</div>
            <div class="invoice-table-pos">${tableInfo}</div>
        </div>
        
        <div class="invoice-items">`;
    
    items.forEach(item => {
        const lineTotal = parseFloat(item.price_at_purchase) * parseInt(item.quantity);
        html += `
            <div class="invoice-item">
                <div class="invoice-item-name">${item.product_name}${item.size_name ? ' (' + item.size_name + ')' : ''}</div>
                <div class="invoice-item-detail">
                    <span>${item.quantity} x ${new Intl.NumberFormat('vi-VN').format(item.price_at_purchase)} ₫</span>
                    <strong>${new Intl.NumberFormat('vi-VN').format(lineTotal)} ₫</strong>
                </div>`;
        
        if (item.note) {
            html += `<div class="invoice-item-note">📝 ${item.note}</div>`;
        }
        
        html += `</div>`;
    });
    
    html += `
        </div>
        
        <div class="invoice-summary">
            <div class="invoice-row">
                <span>Tổng tiền hàng:</span>
                <span>${new Intl.NumberFormat('vi-VN').format(subtotal)} ₫</span>
            </div>`;
    
    if (discount > 0) {
        html += `
            <div class="invoice-row">
                <span>Giảm giá:</span>
                <span style="color:#c62828;">-${new Intl.NumberFormat('vi-VN').format(discount)} ₫</span>
            </div>`;
    }
    
    if (shipping > 0) {
        html += `
            <div class="invoice-row">
                <span>Phí ship:</span>
                <span>${new Intl.NumberFormat('vi-VN').format(shipping)} ₫</span>
            </div>`;
    }
    
    html += `
            <div class="invoice-row total">
                <span>TỔNG THANH TOÁN:</span>
                <span>${new Intl.NumberFormat('vi-VN').format(total)} ₫</span>
            </div>
        </div>
        
        <div class="invoice-footer">
            Cảm ơn quý khách - Hẹn gặp lại!
        </div>`;
    
    return html;
}

// Đóng modal hóa đơn
function closeInvoice() {
    document.getElementById('invoiceModal').style.display = 'none';
}

// Sửa lại hàm printOrder để hiển thị modal invoice
function printOrder(orderId) {
    showInvoice(orderId);
}

// ===== END INVOICE FUNCTIONS =====
