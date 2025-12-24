<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/partials/modal.php
 * Modal Component - Modal tái sử dụng cho các form CRUD
 */
?>
<div id="modalOverlay" class="modal-overlay hidden">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Tiêu đề Modal</h3>
            <button class="close-modal" aria-label="Đóng modal">&times;</button>
        </div>
        
        <div class="modal-body">
            <form id="modalForm" method="POST">
                <!-- Nội dung form sẽ được inject động bằng JavaScript -->
                <div id="formContent">
                    <!-- Dynamic form fields will be inserted here -->
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary close-modal">Hủy</button>
            <button type="submit" class="btn-primary" id="modalSubmit">Lưu lại</button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal-overlay hidden">
    <div class="modal-box modal-small">
        <div class="modal-header">
            <h3 id="confirmTitle">Xác nhận</h3>
            <button class="close-confirm" aria-label="Đóng">&times;</button>
        </div>
        
        <div class="modal-body">
            <p id="confirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-secondary close-confirm">Hủy</button>
            <button type="button" class="btn-danger" id="confirmAction">Xác nhận</button>
        </div>
    </div>
</div>
