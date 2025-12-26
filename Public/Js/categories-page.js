/**
 * Categories Page - JavaScript
 * File: categories-page.js
 * Xử lý modal thêm/sửa danh mục
 */

/**
 * MỞ MODAL THÊM/SỬA DANH MỤC
 */
function openCategoryModal(action, categoryData = null) {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('modalTitle');
    const submitBtn = form.querySelector('button[type="submit"]');

    if (!modal) {
        alert('Lỗi: Không tìm thấy modal!');
        return;
    }

    // Reset form
    form.reset();

    if (action === 'add') {
        // Chế độ thêm mới
        title.textContent = '➕ Thêm danh mục mới';
        form.action = '?url=Category/ins';

        submitBtn.name = 'btnThem';
        submitBtn.innerHTML = '<span>✅</span> Lưu lại';
    } else {
        // Chế độ sửa
        title.textContent = '✏️ Sửa thông tin danh mục';
        form.action = '?url=Category/upd';

        // Điền dữ liệu vào form
        document.getElementById('categoryId').value = categoryData.id;
        document.getElementById('categoryName').value = categoryData.name;
        document.getElementById('categoryDescription').value = categoryData.description || '';

        submitBtn.name = 'btnCapnhat';
        submitBtn.innerHTML = '<span>✅</span> Cập nhật';
    }

    // HIỂN THỊ MODAL
    modal.classList.add('active');
}

/**
 * ĐÓNG MODAL
 */
function closeCategoryModal() {
    const modal = document.getElementById('categoryModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            document.getElementById('categoryForm').reset();
        }, 300);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('categoryModal');

    if (modal) {
        // Click outside để đóng
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeCategoryModal();
            }
        });
    }

    // Nhấn ESC để đóng
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('categoryModal');
            if (modal && modal.classList.contains('active')) {
                closeCategoryModal();
            }
        }
    });

    // Hiển thị thông báo nếu có (được truyền từ window.CATEGORY_MESSAGES)
    if (window.CATEGORY_MESSAGES) {
        if (window.CATEGORY_MESSAGES.success) {
            showNotification(window.CATEGORY_MESSAGES.success, 'success');
        }
        if (window.CATEGORY_MESSAGES.error) {
            showNotification(window.CATEGORY_MESSAGES.error, 'error');
        }
    }
});

/**
 * Hiển thị thông báo (toast notification)
 */
function showNotification(message, type = 'success') {
    // Tạo element cho notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    // Thêm styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #b6da9f 0%, #9fc885 100%)' : 'linear-gradient(135deg, #f56565 0%, #e53e3e 100%)'};
        color: ${type === 'success' ? '#2c2c2c' : 'white'};
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        z-index: 10001;
        font-weight: 600;
        animation: slideInRight 0.3s ease;
    `;

    // Thêm vào body
    document.body.appendChild(notification);

    // Tự động xóa sau 3 giây
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// CSS Animation cho notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
