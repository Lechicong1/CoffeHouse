/**
 * Admin Dashboard - JavaScript Module
 * Refactored để hỗ trợ cấu trúc view động với PHP sections
 */

// ==================== GLOBAL STATE ====================
const AdminApp = {
    currentSection: window.CURRENT_SECTION || 'dashboard',
    apiBaseUrl: '/web/Controllers/',
    
    init() {
        this.initSidebar();
        this.initModals();
        this.initForms();
        this.initTables();
        this.initSettings();
    }
};

// ==================== SIDEBAR ====================
AdminApp.initSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('sidebarToggle');

    // Toggle Sidebar
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar?.classList.toggle('collapsed');
            mainContent?.classList.toggle('collapsed');
            
            // Save state to localStorage
            const isCollapsed = sidebar?.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
    }

    // Restore sidebar state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        sidebar?.classList.add('collapsed');
        mainContent?.classList.add('collapsed');
    }

    // Highlight active menu item based on current section
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        const target = item.getAttribute('data-target');
        if (target === AdminApp.currentSection) {
            item.classList.add('active');
        }
    });
};

// ==================== MODALS ====================
AdminApp.initModals = function() {
    const modalOverlay = document.getElementById('modalOverlay');
    const confirmModal = document.getElementById('confirmModal');
    const closeModalBtns = document.querySelectorAll('.close-modal');
    const closeConfirmBtns = document.querySelectorAll('.close-confirm');

    // Close main modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modalOverlay?.classList.add('hidden');
        });
    });

    // Close confirm modal
    closeConfirmBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            confirmModal?.classList.add('hidden');
        });
    });

    // Close on click outside
    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.add('hidden');
        }
    });

    confirmModal?.addEventListener('click', (e) => {
        if (e.target === confirmModal) {
            confirmModal.classList.add('hidden');
        }
    });

    // ESC key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modalOverlay?.classList.add('hidden');
            confirmModal?.classList.add('hidden');
        }
    });
};

// ==================== MODAL FUNCTIONS ====================
function openModal(type, action = 'create', data = null) {
    const modal = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const formContent = document.getElementById('formContent');
    
    if (!modal || !title || !formContent) return;

    // Cấu hình form theo loại
    const formConfigs = {
        order: {
            create: { title: '🛍️ Tạo đơn hàng mới', fields: getOrderFields() },
            edit: { title: '✏️ Chỉnh sửa đơn hàng', fields: getOrderFields(data) }
        },
        product: {
            create: { title: '☕ Thêm sản phẩm mới', fields: getProductFields() },
            edit: { title: '✏️ Chỉnh sửa sản phẩm', fields: getProductFields(data) }
        },
        customer: {
            create: { title: '👥 Thêm khách hàng', fields: getCustomerFields() },
            edit: { title: '✏️ Chỉnh sửa khách hàng', fields: getCustomerFields(data) }
        },
        employee: {
            create: { title: '👔 Thêm nhân viên', fields: getEmployeeFields() },
            edit: { title: '✏️ Chỉnh sửa nhân viên', fields: getEmployeeFields(data) }
        }
    };

    const config = formConfigs[type]?.[action];
    if (!config) {
        console.error('Invalid modal configuration');
        return;
    }

    title.textContent = config.title;
    formContent.innerHTML = config.fields;
    
    modal.classList.remove('hidden');
    
    // Focus first input
    const firstInput = formContent.querySelector('input, select, textarea');
    firstInput?.focus();
}

function openConfirmModal(message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    const messageEl = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmAction');
    
    if (!modal || !messageEl || !confirmBtn) return;

    messageEl.textContent = message;
    modal.classList.remove('hidden');

    // Remove old listeners
    const newBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);

    // Add new listener
    newBtn.addEventListener('click', () => {
        onConfirm();
        modal.classList.add('hidden');
    });
}

// ==================== FORM FIELDS ====================
function getOrderFields(data = null) {
    return `
        <div class="form-group">
            <label>Khách hàng <span class="required">*</span></label>
            <select class="form-control" name="customer_id" required>
                <option value="">-- Chọn khách hàng --</option>
                <option value="1">Nguyễn Văn A</option>
                <option value="2">Trần Thị B</option>
            </select>
        </div>
        <div class="form-group">
            <label>Sản phẩm <span class="required">*</span></label>
            <select class="form-control" name="product_id" required>
                <option value="">-- Chọn món --</option>
                <option value="1">Cà phê sữa đá</option>
                <option value="2">Trà đào cam sả</option>
            </select>
        </div>
        <div class="form-group">
            <label>Số lượng <span class="required">*</span></label>
            <input type="number" class="form-control" name="quantity" min="1" value="1" required>
        </div>
        <div class="form-group">
            <label>Ghi chú</label>
            <textarea class="form-control" name="note" rows="3"></textarea>
        </div>
    `;
}

function getProductFields(data = null) {
    return `
        <div class="form-group">
            <label>Tên món <span class="required">*</span></label>
            <input type="text" class="form-control" name="name" value="${data?.name || ''}" required>
        </div>
        <div class="form-group">
            <label>Danh mục <span class="required">*</span></label>
            <select class="form-control" name="category" required>
                <option value="">-- Chọn danh mục --</option>
                <option value="coffee">Cà phê</option>
                <option value="tea">Trà</option>
                <option value="juice">Nước ép</option>
            </select>
        </div>
        <div class="form-group">
            <label>Giá <span class="required">*</span></label>
            <input type="number" class="form-control" name="price" value="${data?.price || ''}" required>
        </div>
        <div class="form-group">
            <label>Mô tả</label>
            <textarea class="form-control" name="description" rows="3">${data?.description || ''}</textarea>
        </div>
    `;
}

function getCustomerFields(data = null) {
    return `
        <div class="form-group">
            <label>Họ tên <span class="required">*</span></label>
            <input type="text" class="form-control" name="name" value="${data?.name || ''}" required>
        </div>
        <div class="form-group">
            <label>Số điện thoại <span class="required">*</span></label>
            <input type="tel" class="form-control" name="phone" value="${data?.phone || ''}" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" value="${data?.email || ''}">
        </div>
    `;
}

function getEmployeeFields(data = null) {
    return `
        <div class="form-group">
            <label>Họ tên <span class="required">*</span></label>
            <input type="text" class="form-control" name="name" value="${data?.name || ''}" required>
        </div>
        <div class="form-group">
            <label>Chức vụ <span class="required">*</span></label>
            <select class="form-control" name="position" required>
                <option value="">-- Chọn chức vụ --</option>
                <option value="barista">Pha chế</option>
                <option value="cashier">Thu ngân</option>
                <option value="manager">Quản lý</option>
            </select>
        </div>
        <div class="form-group">
            <label>Ca làm việc <span class="required">*</span></label>
            <select class="form-control" name="shift" required>
                <option value="morning">Sáng (6:00 - 14:00)</option>
                <option value="afternoon">Chiều (14:00 - 22:00)</option>
                <option value="full">Full (6:00 - 22:00)</option>
            </select>
        </div>
    `;
}

// ==================== CRUD OPERATIONS ====================
function viewOrder(orderId) {
    console.log('View order:', orderId);
    // Implement view logic
}

function editOrder(orderId) {
    openModal('order', 'edit', { id: orderId });
}

function deleteOrder(orderId) {
    openConfirmModal('Bạn có chắc chắn muốn xóa đơn hàng này?', () => {
        console.log('Delete order:', orderId);
        // Call API to delete
    });
}

function editProduct(productId) {
    openModal('product', 'edit', { id: productId });
}

function deleteProduct(productId) {
    openConfirmModal('Bạn có chắc chắn muốn xóa sản phẩm này?', () => {
        console.log('Delete product:', productId);
    });
}

function toggleProductStatus(productId) {
    console.log('Toggle product status:', productId);
}

function viewCustomer(customerId) {
    console.log('View customer:', customerId);
}

function editCustomer(customerId) {
    openModal('customer', 'edit', { id: customerId });
}

function deleteCustomer(customerId) {
    openConfirmModal('Bạn có chắc chắn muốn xóa khách hàng này?', () => {
        console.log('Delete customer:', customerId);
    });
}

function viewEmployee(employeeId) {
    console.log('View employee:', employeeId);
}

function editEmployee(employeeId) {
    openModal('employee', 'edit', { id: employeeId });
}

function deleteEmployee(employeeId) {
    openConfirmModal('Bạn có chắc chắn muốn xóa nhân viên này?', () => {
        console.log('Delete employee:', employeeId);
    });
}

// ==================== FORMS ====================
AdminApp.initForms = function() {
    const modalForm = document.getElementById('modalForm');
    
    if (modalForm) {
        modalForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(modalForm);
            const data = Object.fromEntries(formData);
            
            console.log('Form submitted:', data);
            
            // Call API here
            // await fetch(...)
            
            // Close modal on success
            document.getElementById('modalOverlay')?.classList.add('hidden');
            
            // Show success message
            showNotification('Lưu thành công!', 'success');
        });
    }
};

// ==================== TABLES ====================
AdminApp.initTables = function() {
    // Select all checkboxes
    const selectAllCheckboxes = document.querySelectorAll('.select-all');
    
    selectAllCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
            const table = e.target.closest('table');
            const itemCheckboxes = table?.querySelectorAll('.select-item');
            
            itemCheckboxes?.forEach(item => {
                item.checked = e.target.checked;
            });
        });
    });

    // Search functionality
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            console.log('Search:', searchTerm);
            // Implement search logic or API call
        });
    });

    // Filter functionality
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', (e) => {
            const filterValue = e.target.value;
            console.log('Filter:', filterValue);
            // Implement filter logic or API call
        });
    });
};

// ==================== SETTINGS ====================
AdminApp.initSettings = function() {
    // Settings tabs
    const tabBtns = document.querySelectorAll('.tab-btn');
    const settingsContents = document.querySelectorAll('.settings-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            
            // Update active tab
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show target content
            settingsContents.forEach(content => {
                if (content.id === targetTab) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        });
    });
};

// ==================== NOTIFICATIONS ====================
function showNotification(message, type = 'info') {
    // Simple notification (có thể dùng thư viện như Toastify)
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 8px;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ==================== INITIALIZE ====================
document.addEventListener('DOMContentLoaded', () => {
    AdminApp.init();
    console.log('Admin Dashboard initialized. Current section:', AdminApp.currentSection);
});
