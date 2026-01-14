/**
 * Employees Page - JavaScript
 * File: employees-page.js
 * Xử lý modal thêm/sửa nhân viên
 */

/**
 * MỞ MODAL THÊM/SỬA NHÂN VIÊN
 */
function openEmployeeModal(action, buttonElement = null) {
    const modal = document.getElementById('employeeModal');
    const form = document.getElementById('employeeForm');
    const title = document.getElementById('modalTitle');
    const passwordGroup = document.getElementById('passwordGroup');
    const usernameField = document.getElementById('username');
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!modal) {
        alert('Lỗi: Không tìm thấy modal!');
        return;
    }

    // Reset form
    form.reset();

    if (action === 'add') {
        // Chế độ thêm mới
        title.textContent = '➕ Thêm nhân viên mới';
        form.action = 'EmployeeController/ins';
        passwordGroup.style.display = 'block';
        usernameField.readOnly = false;
        document.getElementById('password').required = true;

        // Set ngày hiện tại cho trường create_at
        document.getElementById('createAt').value = new Date().toISOString().split('T')[0];

        submitBtn.name = 'btnThem';
        submitBtn.innerHTML = '<span>✅</span> Lưu lại';
    } else {
        // Chế độ sửa - Đọc dữ liệu từ data attributes
        if (!buttonElement || !buttonElement.dataset) {
            alert('Lỗi: Không tìm thấy dữ liệu nhân viên!');
            return;
        }

        const data = buttonElement.dataset;

        title.textContent = '✏️ Sửa thông tin nhân viên';
        form.action = 'EmployeeController/upd';

        // Điền dữ liệu vào form từ data attributes
        document.getElementById('employeeId').value = data.id;
        document.getElementById('username').value = data.username;
        document.getElementById('fullname').value = data.fullname;
        document.getElementById('email').value = data.email || '';
        document.getElementById('phonenumber').value = data.phone;
        document.getElementById('address').value = data.address || '';
        document.getElementById('roleId').value = data.role;
        document.getElementById('luong').value = data.salary;
        document.getElementById('createAt').value = data.createat || '';

        passwordGroup.style.display = 'none';
        usernameField.readOnly = true;
        document.getElementById('password').required = false;

        submitBtn.name = 'btnCapnhat';
        submitBtn.innerHTML = '<span>✅</span> Cập nhật';
    }

    // HIỂN THỊ MODAL
    modal.classList.add('active');
}

/**
 * ĐÓNG MODAL
 */
function closeEmployeeModal() {
    const modal = document.getElementById('employeeModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            document.getElementById('employeeForm').reset();
        }, 300);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('employeeModal');

    if (modal) {
        // Click outside để đóng
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeEmployeeModal();
            }
        });
    }

    // Nhấn ESC để đóng
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('employeeModal');
            if (modal && modal.classList.contains('active')) {
                closeEmployeeModal();
            }
        }
    });

    // Hiển thị thông báo nếu có (được truyền từ window.EMPLOYEE_MESSAGES)
    if (window.EMPLOYEE_MESSAGES) {
        if (window.EMPLOYEE_MESSAGES.success) {
            alert(window.EMPLOYEE_MESSAGES.success);
        }
        if (window.EMPLOYEE_MESSAGES.error) {
            alert(window.EMPLOYEE_MESSAGES.error);
        }
    }
});
