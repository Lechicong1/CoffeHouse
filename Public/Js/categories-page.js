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
        document.getElementById('categoryForm').reset();
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
});

