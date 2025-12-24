<?php
/**
 * Employees View - Giao diện quản lý nhân viên
 * VIEW THUẦN - Chỉ hiển thị dữ liệu từ Controller
 * Không xử lý logic, không kết nối database
 */

// Lấy dữ liệu từ GLOBALS (truyền từ AdminController)
$employees = $GLOBALS['employees'] ?? $data['employees'] ?? [];
$stats = $GLOBALS['stats'] ?? $data['stats'] ?? ['total' => 0, 'manager' => 0, 'barista' => 0, 'cashier' => 0, 'waiter' => 0, 'cleaner' => 0];
$keyword = $GLOBALS['keyword'] ?? $data['keyword'] ?? '';
$roleFilter = $GLOBALS['roleFilter'] ?? $data['roleFilter'] ?? 'all';
$successMessage = $GLOBALS['successMessage'] ?? $data['successMessage'] ?? null;
$errorMessage = $GLOBALS['errorMessage'] ?? $data['errorMessage'] ?? null;

// Định nghĩa danh sách vai trò
$roles = [
    1 => 'Quản lý',
    2 => 'Pha chế',
    3 => 'Thu ngân',
    4 => 'Phục vụ',
    5 => 'Vệ sinh'
];
?>

<style>
/* ========== MODAL - ẨN MẶC ĐỊNH ========== */
.modal {
    display: none !important;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(10px);
    z-index: 9999;
}

.modal.active {
    display: flex !important;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: #ffffff;
    padding: 0;
    border-radius: 24px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(60px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Modal Header */
.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px 48px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
}

.modal-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.modal-header h3 {
    color: white;
    font-size: 28px;
    font-weight: 900;
    text-transform: uppercase;
    margin: 0;
}

.btn-close-modal {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.15);
    color: white;
    font-size: 36px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-close-modal:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: rotate(90deg) scale(1.15);
}

/* Form */
#employeeForm {
    padding: 48px 56px;
    max-height: calc(90vh - 160px);
    overflow-y: auto;
    background: linear-gradient(to bottom, #ffffff 0%, #f8f9fc 100%);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 30px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    font-size: 14px;
    font-weight: 700;
    color: #2d3748;
    text-transform: uppercase;
}

.label-icon {
    font-size: 18px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    background: #ffffff;
    transition: all 0.3s;
    outline: none;
}

.form-group input::placeholder {
    color: #a0aec0;
    font-style: italic;
}

.form-group input:focus,
.form-group select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 5px rgba(102, 126, 234, 0.12);
    transform: translateY(-2px);
}

.form-group small {
    display: block;
    margin-top: 8px;
    font-size: 12px;
    color: #718096;
    font-style: italic;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    padding-top: 40px;
    margin-top: 40px;
    border-top: 3px solid #e2e8f0;
}

.btn-cancel,
.btn-submit {
    padding: 16px 40px;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 800;
    text-transform: uppercase;
    cursor: pointer;
    transition: all 0.3s;
    border: none;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-cancel {
    background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
    color: #4a5568;
}

.btn-cancel:hover {
    background: linear-gradient(135deg, #cbd5e0, #a0aec0);
    transform: translateY(-4px);
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
    }
    
    #employeeForm {
        padding: 28px;
    }
}

/* ========== NÚT SỬA VÀ XÓA TRONG BẢNG ========== */
.btn-edit,
.btn-delete {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 0 4px;
}

.btn-edit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.5);
}

.btn-delete {
    background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 101, 101, 0.3);
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 101, 101, 0.5);
    background: linear-gradient(135deg, #e53e3e 0%, #9b2c2c 100%);
}

/* ========== FILTER COMBOBOX ========== */
.filter-select {
    padding: 10px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
    background: white;
    cursor: pointer;
    outline: none;
    min-width: 180px;
}

.filter-select:focus {
    border-color: #667eea;
}
</style>

<section id="employees" class="content-section">
    <!-- Header Section -->
    <div class="section-header">
        <div class="header-title">
            <h2>👔 Quản lý Nhân viên</h2>
            <p class="subtitle">Tổng số: <strong><?= $stats['total'] ?></strong> nhân viên</p>
        </div>
        <div class="header-actions">
            <!-- Filter by Role -->
            <select class="filter-select" onchange="window.location.href='?section=employees&role=' + this.value">
                <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>Tất cả vai trò</option>
                <?php foreach ($roles as $id => $name): ?>
                    <option value="<?= $id ?>" <?= $roleFilter == $id ? 'selected' : '' ?>>
                        <?= $name ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openEmployeeModal('add')">
                ➕ Thêm nhân viên mới
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="GET" action="" class="search-form" style="display: flex; gap: 12px; max-width: 600px;">
            <input type="hidden" name="section" value="employees">
            <input type="text" name="search" class="search-input" placeholder="🔍 Tìm kiếm theo tên, email, số điện thoại..." value="<?= htmlspecialchars($keyword) ?>" style="flex: 1; padding: 12px 20px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px;">
            <button type="submit" class="btn-primary" style="padding: 12px 32px; white-space: nowrap; border-radius: 12px;">🔍 Tìm kiếm</button>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['manager'] ?></div>
            <div class="stat-label">Quản lý</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['barista'] ?></div>
            <div class="stat-label">Pha chế</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['cashier'] ?></div>
            <div class="stat-label">Thu ngân</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['waiter'] ?></div>
            <div class="stat-label">Phục vụ</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['cleaner'] ?></div>
            <div class="stat-label">Vệ sinh</div>
        </div>
    </div>

    <!-- Thông báo sẽ hiển thị qua JavaScript alert -->

    <!-- Employees Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Họ tên</th>
                    <th>Vai trò</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Lương</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                            📭 Không có nhân viên nào!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td>#<?= $employee->id ?></td>
                            <td><strong><?= htmlspecialchars($employee->username) ?></strong></td>
                            <td><?= htmlspecialchars($employee->fullname) ?></td>
                            <td>
                                <span class="badge badge-role-<?= $employee->roleId ?>">
                                    <?= $employee->getRoleName() ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($employee->email ?? '-') ?></td>
                            <td><?= htmlspecialchars($employee->phonenumber) ?></td>
                            <td style="font-weight: 600; color: #27ae60;">
                                <?= $employee->getFormattedSalary() ?>
                            </td>
                            <td style="color: #666;">
                                <?= date('d/m/Y', strtotime($employee->created_at)) ?>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="openEmployeeModal('edit', <?= htmlspecialchars(json_encode($employee->toArray())) ?>)" title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <a href="?section=employees&action=delete&id=<?= $employee->id ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                    <button class="btn-delete">🗑️ Xóa</button>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal Form Thêm/Sửa Nhân viên -->
<div id="employeeModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title-wrapper">
                <div class="modal-icon">👤</div>
                <h3 id="modalTitle">Thêm nhân viên mới</h3>
            </div>
            <button class="btn-close-modal" onclick="closeEmployeeModal()">×</button>
        </div>

        <form id="employeeForm" method="POST" action="?section=employees">
            <input type="hidden" id="employeeId" name="txtId">
            <input type="hidden" id="formAction" name="action" value="create">

            <!-- Row 1: Username + Password -->
            <div class="form-grid">
                <div class="form-group">
                    <label><span class="label-icon">👤</span> Username</label>
                    <input type="text" id="username" name="txtUsername" required placeholder="Nhập tên đăng nhập">
                </div>

                <div class="form-group" id="passwordGroup">
                    <label><span class="label-icon">🔒</span> Mật khẩu</label>
                    <input type="password" id="password" name="txtPassword" required placeholder="Tối thiểu 6 ký tự">
                    <small>Tối thiểu 6 ký tự</small>
                </div>
            </div>

            <!-- Row 2: Fullname + Email -->
            <div class="form-grid">
                <div class="form-group">
                    <label><span class="label-icon">📝</span> Họ tên đầy đủ</label>
                    <input type="text" id="fullname" name="txtFullname" required placeholder="Nguyễn Văn A">
                </div>

                <div class="form-group">
                    <label><span class="label-icon">✉️</span> Email</label>
                    <input type="email" id="email" name="txtEmail" placeholder="example@gmail.com">
                </div>
            </div>

            <!-- Row 3: Phone + Role -->
            <div class="form-grid">
                <div class="form-group">
                    <label><span class="label-icon">📞</span> Số điện thoại</label>
                    <input type="tel" id="phonenumber" name="txtPhonenumber" required pattern="[0-9]{10}" placeholder="0912345678">
                    <small>10 chữ số</small>
                </div>

                <div class="form-group">
                    <label><span class="label-icon">💼</span> Vai trò</label>
                    <select id="roleId" name="ddlRoleId" required>
                        <?php foreach ($roles as $id => $name): ?>
                            <option value="<?= $id ?>"><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Row 4: Salary + Address -->
            <div class="form-grid">
                <div class="form-group">
                    <label><span class="label-icon">💰</span> Lương (VNĐ)</label>
                    <input type="number" id="luong" name="txtLuong" required min="0" step="100000" placeholder="5000000">
                </div>

                <div class="form-group">
                    <label><span class="label-icon">📍</span> Địa chỉ</label>
                    <input type="text" id="address" name="txtAddress" placeholder="Nhập địa chỉ (không bắt buộc)">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeEmployeeModal()">
                    <span>❌</span> Hủy bỏ
                </button>
                <button type="submit" id="submitBtn" class="btn-submit" name="btnThem">
                    <span>✅</span> Lưu lại
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * MỞ MODAL THÊM/SỬA NHÂN VIÊN
 */
function openEmployeeModal(action, employeeData = null) {
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
        document.getElementById('formAction').value = 'create';
        form.action = '?section=employees'; // SUBMIT VỀ ADMIN CONTROLLER
        passwordGroup.style.display = 'block';
        usernameField.readOnly = false;
        document.getElementById('password').required = true;

        submitBtn.name = 'btnThem';
        submitBtn.innerHTML = '<span>✅</span> Lưu lại';
    } else {
        // Chế độ sửa
        title.textContent = '✏️ Sửa thông tin nhân viên';
        document.getElementById('formAction').value = 'update';
        form.action = '?section=employees'; // SUBMIT VỀ ADMIN CONTROLLER

        // Điền dữ liệu vào form
        document.getElementById('employeeId').value = employeeData.id;
        document.getElementById('username').value = employeeData.username;
        document.getElementById('fullname').value = employeeData.fullname;
        document.getElementById('email').value = employeeData.email || '';
        document.getElementById('phonenumber').value = employeeData.phonenumber;
        document.getElementById('address').value = employeeData.address || '';
        document.getElementById('roleId').value = employeeData.roleId;
        document.getElementById('luong').value = employeeData.luong;

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
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('employeeModal');
    
    if (modal) {
        // Click outside để đóng
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEmployeeModal();
            }
        });
    }
    
    // Nhấn ESC để đóng
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('employeeModal');
            if (modal && modal.classList.contains('active')) {
                closeEmployeeModal();
            }
        }
    });
    
    // Validation form
    const form = document.getElementById('employeeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const action = document.getElementById('formAction').value;

            if (action === 'create' && password.value.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                password.focus();
                return false;
            }

            const phone = document.getElementById('phonenumber');
            if (!/^[0-9]{10}$/.test(phone.value)) {
                e.preventDefault();
                alert('Số điện thoại phải có đúng 10 chữ số!');
                phone.focus();
                return false;
            }
        });
    }
    
    // Hiển thị thông báo qua alert
    <?php if (isset($successMessage)): ?>
        alert('<?= addslashes($successMessage) ?>');
    <?php endif; ?>
    
    <?php if (isset($errorMessage)): ?>
        alert('<?= addslashes($errorMessage) ?>');
    <?php endif; ?>
});
</script>

