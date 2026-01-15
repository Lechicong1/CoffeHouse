/**
 * FILE: ingredients-page.js
 * JavaScript cho trang Quản lý Nguyên liệu
 * Xử lý Modal và các tương tác UI
 */

// ========== MODAL QUẢN LÝ NGUYÊN LIỆU ==========

/**
 * Mở modal thêm/sửa nguyên liệu
 * @param {string} mode - 'add' hoặc 'edit'
 * @param {object} data - Dữ liệu nguyên liệu (khi edit)
 */
function openIngredientModal(mode, data = null) {
    const modal = document.getElementById('ingredientModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('ingredientForm');
    const submitBtn = document.getElementById('submitBtn');

    if (mode === 'add') {
        // Mode: Thêm mới
        modalTitle.textContent = 'Thêm nguyên liệu mới';
        form.action = '?url=Ingredient/ins';
        submitBtn.name = 'btnThem';
        submitBtn.innerHTML = '<span>✅</span> Thêm mới';

        // Reset form
        form.reset();
        document.getElementById('ingredientId').value = '';
        document.getElementById('formAction').value = 'create';

    } else if (mode === 'edit' && data) {
        // Mode: Chỉnh sửa
        modalTitle.textContent = 'Chỉnh sửa nguyên liệu';
        form.action = '?url=Ingredient/upd';
        submitBtn.name = 'btnCapnhat';
        submitBtn.innerHTML = '<span>✅</span> Cập nhật';

        // Điền dữ liệu vào form (chỉ có tên và đơn vị)
        document.getElementById('ingredientId').value = data.id;
        document.getElementById('name').value = data.name;
        document.getElementById('unit').value = data.unit;
        
        // Populate expiry date if exists
        if (data.expiry_date) {
            document.getElementById('expiryDate').value = data.expiry_date;
        } else {
            document.getElementById('expiryDate').value = '';
        }

        // Không cần điền stock_quantity vì không có input này nữa
        document.getElementById('formAction').value = 'update';
    }

    // Hiển thị modal
    modal.classList.add('active');
    modal.style.display = 'flex';
}

/**
 * Đóng modal nguyên liệu
 */
function closeIngredientModal() {
    const modal = document.getElementById('ingredientModal');
    modal.classList.remove('active');

    setTimeout(() => {
        modal.style.display = 'none';
        document.getElementById('ingredientForm').reset();
    }, 300);
}

// ========== EVENT LISTENERS ==========

document.addEventListener('DOMContentLoaded', function() {
    // Đóng modal khi click vào nền overlay
    const ingredientModal = document.getElementById('ingredientModal');

    if (ingredientModal) {
        ingredientModal.addEventListener('click', function(e) {
            if (e.target === ingredientModal) {
                closeIngredientModal();
            }
        });
    }

    // Đóng modal khi nhấn ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeIngredientModal();
        }
    });

    // Validation form - CHỈ validate tên và đơn vị
    const ingredientForm = document.getElementById('ingredientForm');
    if (ingredientForm) {
        ingredientForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const unit = document.getElementById('unit').value.trim();

            if (name.length < 2) {
                e.preventDefault();
                alert('Tên nguyên liệu phải có ít nhất 2 ký tự!');
                return false;
            }

            if (unit.length < 1) {
                e.preventDefault();
                alert('Đơn vị không được để trống!');
                return false;
            }

            // BỎ validation cho stock_quantity
        });
    }
});

// ========== UTILITY FUNCTIONS ==========

/**
 * Format số với dấu phân cách hàng nghìn
 * @param {number} num - Số cần format
 * @returns {string}
 */
function formatNumber(num) {
    return parseFloat(num).toLocaleString('vi-VN');
}

/**
 * Xác nhận xóa
 * @param {string} name - Tên nguyên liệu
 * @returns {boolean}
 */
function confirmDelete(name) {
    return confirm(`Bạn có chắc chắn muốn xóa nguyên liệu "${name}" không?\n\nHành động này không thể hoàn tác!`);
}

// ========== AUTO-COMPLETE ĐƠN VỊ ==========

// Danh sách đơn vị phổ biến
const commonUnits = ['kg', 'g', 'lít', 'ml', 'gói', 'hộp', 'chai', 'lon', 'túi', 'thùng'];

// Thêm datalist cho input đơn vị
document.addEventListener('DOMContentLoaded', function() {
    const unitInput = document.getElementById('unit');
    if (unitInput && !document.getElementById('unitDatalist')) {
        const datalist = document.createElement('datalist');
        datalist.id = 'unitDatalist';

        commonUnits.forEach(unit => {
            const option = document.createElement('option');
            option.value = unit;
            datalist.appendChild(option);
        });

        document.body.appendChild(datalist);
        unitInput.setAttribute('list', 'unitDatalist');
    }
});

console.log('✅ Ingredients Page JavaScript loaded successfully!');

// ========== TABLE DISPLAY LOGIC (NEW) ==========

document.addEventListener('DOMContentLoaded', function() {
    processIngredientTable();
});

/**
 * Xử lý hiển thị bảng nguyên liệu (Stock status only)
 */
function processIngredientTable() {
    // Process Stock Status
    const stockCells = document.querySelectorAll('.col-stock');
    stockCells.forEach(cell => {
        const qty = parseFloat(cell.getAttribute('data-qty'));
        let label = '';
        let badgeClass = '';

        if (qty <= 0) {
            label = 'Hết hàng';
            badgeClass = 'badge-status-out';
        } else if (qty < 10) {
            label = 'Sắp hết';
            badgeClass = 'badge-status-low';
        } else {
            label = 'Còn hàng';
            badgeClass = 'badge-status-ok';
        }

        cell.innerHTML = `<span class="badge ${badgeClass}">${label}</span>`;
    });
}
