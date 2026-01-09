/**
 * Report Page JavaScript
 * Xử lý logic hiển thị modal và gọi API lấy chi tiết
 */

// ==================================================
// MODAL FUNCTIONS - Quản lý hiển thị Modal
// ==================================================

/**
 * Mở modal chi tiết nhân viên & lương
 */
function openEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    modal.style.display = 'block';
    
    // Gọi API lấy dữ liệu
    loadEmployeeDetails();
}

/**
 * Đóng modal chi tiết nhân viên
 */
function closeEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    modal.style.display = 'none';
}

/**
 * Mở modal chi tiết nhập nguyên liệu
 */
function openInventoryModal() {
    const modal = document.getElementById('inventoryModal');
    modal.style.display = 'block';
    
    // Gọi API lấy dữ liệu
    loadInventoryDetails();
}

/**
 * Đóng modal chi tiết nhập nguyên liệu
 */
function closeInventoryModal() {
    const modal = document.getElementById('inventoryModal');
    modal.style.display = 'none';
}

/**
 * Đóng danh sách nhân viên
 */
function closeEmployeeList() {
    const section = document.getElementById('employee-list-section');
    section.style.display = 'none';
}

// Đóng modal khi click bên ngoài
window.onclick = function(event) {
    const employeeModal = document.getElementById('employeeModal');
    const inventoryModal = document.getElementById('inventoryModal');
    
    if (event.target == employeeModal) {
        closeEmployeeModal();
    }
    if (event.target == inventoryModal) {
        closeInventoryModal();
    }
}

// ==================================================
// API CALLS - Gọi API lấy dữ liệu
// ==================================================

/**
 * Lấy chi tiết nhân viên & lương
 */
function loadEmployeeDetails() {
    const tbody = document.getElementById('employee-modal-tbody');
    
    // Hiển thị loading
    tbody.innerHTML = `
        <tr>
            <td colspan="4" style="text-align: center; padding: 20px;">
                <div class="loading-spinner">⏳ Đang tải dữ liệu...</div>
            </td>
        </tr>
    `;
    
    // Gọi API
    fetch('?url=Report/GetEmployeeDetails')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayEmployeeData(data.data);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #e74c3c;">
                            ❌ ${data.message || 'Có lỗi xảy ra khi tải dữ liệu'}
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #e74c3c;">
                        ❌ Lỗi kết nối: ${error.message}
                    </td>
                </tr>
            `;
        });
}

/**
 * Hiển thị dữ liệu nhân viên vào bảng
 */
function displayEmployeeData(employees) {
    const tbody = document.getElementById('employee-modal-tbody');
    
    if (employees.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #95a5a6;">
                    📭 Không có dữ liệu nhân viên
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    let totalSalary = 0;
    
    employees.forEach((emp, index) => {
        totalSalary += parseFloat(emp.salary || 0);
        
        // Map roleName sang tiếng Việt
        const roleMap = {
            'ORDER': 'Nhân viên Order',
            'BARTENDER': 'Nhân viên Pha chế',
            'SHIPPER': 'Nhân viên Giao hàng'
        };
        const roleDisplay = roleMap[emp.roleName] || emp.roleName;
        
        html += `
            <tr>
                <td><strong>#${emp.id}</strong></td>
                <td>${escapeHtml(emp.name)}</td>
                <td><span style="background: #B6DA9F; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">${roleDisplay}</span></td>
                <td style="font-weight: 700; color: #27ae60;">${formatMoney(emp.salary)} VNĐ</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    
    // Cập nhật tổng lương
    document.getElementById('total-salary-modal').textContent = formatMoney(totalSalary) + ' VNĐ';
}

/**
 * Lấy chi tiết nhập nguyên liệu
 */
function loadInventoryDetails() {
    const tbody = document.getElementById('inventory-modal-tbody');
    
    // Hiển thị loading
    tbody.innerHTML = `
        <tr>
            <td colspan="6" style="text-align: center; padding: 20px;">
                <div class="loading-spinner">⏳ Đang tải dữ liệu...</div>
            </td>
        </tr>
    `;
    
    // Lấy thời gian từ biến global (đã set trong view)
    const fromDate = reportDateRange.fromDate;
    const toDate = reportDateRange.toDate;
    
    // Gọi API
    fetch(`?url=Report/GetInventoryDetails&from_date=${fromDate}&to_date=${toDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInventoryData(data.data);
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #e74c3c;">
                            ❌ ${data.message || 'Có lỗi xảy ra khi tải dữ liệu'}
                        </td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #e74c3c;">
                        ❌ Lỗi kết nối: ${error.message}
                    </td>
                </tr>
            `;
        });
}

/**
 * Hiển thị dữ liệu phiếu nhập vào bảng
 */
function displayInventoryData(imports) {
    const tbody = document.getElementById('inventory-modal-tbody');
    
    if (imports.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #95a5a6;">
                    📭 Không có phiếu nhập nào trong khoảng thời gian này
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    let totalCost = 0;
    
    imports.forEach((item, index) => {
        totalCost += parseFloat(item.total_cost || 0);
        
        html += `
            <tr>
                <td><strong>#${item.id}</strong></td>
                <td>${escapeHtml(item.ingredient_name || 'N/A')}</td>
                <td style="font-weight: 600; color: #3498db;">${formatNumber(item.import_quantity)}</td>
                <td>${escapeHtml(item.unit || '')}</td>
                <td style="font-weight: 700; color: #e67e22;">${formatMoney(item.total_cost)} VNĐ</td>
                <td>${formatDate(item.import_date)}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    
    // Cập nhật tổng chi phí
    document.getElementById('total-inventory-modal').textContent = formatMoney(totalCost) + ' VNĐ';
}

// ==================================================
// UTILITY FUNCTIONS - Các hàm tiện ích
// ==================================================

/**
 * Format số tiền với dấu phẩy
 */
function formatMoney(value) {
    return parseFloat(value || 0).toLocaleString('vi-VN');
}

/**
 * Format số lượng
 */
function formatNumber(value) {
    return parseFloat(value || 0).toLocaleString('vi-VN');
}

/**
 * Format ngày tháng
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}/${month}/${year}`;
}

/**
 * Escape HTML để tránh XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==================================================
// INITIALIZATION
// ==================================================

console.log('✅ Report page script loaded successfully');

