<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/Pages/Employees_v.php
 * VIEW CON - Quản lý Nhân viên
 * Chỉ hiển thị dữ liệu, không xử lý logic
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$employees = $data['employees'] ?? [];
$stats = $data['stats'] ?? ['total' => 0, 'manager' => 0, 'barista' => 0, 'cashier' => 0, 'waiter' => 0, 'cleaner' => 0];
$keyword = $data['keyword'] ?? '';
$roleFilter = $data['roleFilter'] ?? 'all';
$successMessage = $data['successMessage'] ?? null;
$errorMessage = $data['errorMessage'] ?? null;

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
/* ========== EMPLOYEES PAGE STYLES ========== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 3px solid #e2e8f0;
}

.header-title h2 {
    font-size: 32px;
    font-weight: 900;
    color: #2d3748;
    margin: 0 0 8px 0;
}

.subtitle {
    color: #718096;
    font-size: 15px;
}

.header-actions {
    display: flex;
    gap: 16px;
    align-items: center;
}

.filter-select {
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: #2d3748;
    background: white;
    cursor: pointer;
    outline: none;
    min-width: 200px;
    transition: all 0.3s;
}

.filter-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-primary {
    padding: 12px 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Search Form */
.search-form {
    display: flex;
    gap: 12px;
    max-width: 600px;
    margin-bottom: 24px;
}

.search-input {
    flex: 1;
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    outline: none;
    transition: all 0.3s;
}

.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px;
    border-radius: 16px;
    text-align: center;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-4px);
}

.stat-value {
    font-size: 36px;
    font-weight: 900;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    font-weight: 600;
}

/* Table Styles */
.table-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.data-table th {
    padding: 16px;
    text-align: left;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.data-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s;
}

.data-table tbody tr:hover {
    background: #f7fafc;
}

.data-table td {
    padding: 16px;
    font-size: 14px;
    color: #2d3748;
}

/* Badge Styles */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-role-1 { background: #667eea; color: white; }
.badge-role-2 { background: #f6ad55; color: white; }
.badge-role-3 { background: #48bb78; color: white; }
.badge-role-4 { background: #4299e1; color: white; }
.badge-role-5 { background: #ed8936; color: white; }

/* Action Buttons */
.btn-edit, .btn-delete {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
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
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(10px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}

.modal.active {
    display: flex !important;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: white;
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

.form-group input, .form-group select {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 15px;
    background: #ffffff;
    transition: all 0.3s;
    outline: none;
}

.form-group input:focus, .form-group select:focus {
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

.form-actions {
    display: flex;
    gap: 20px;
    justify-content: flex-end;
    padding-top: 40px;
    margin-top: 40px;
    border-top: 3px solid #e2e8f0;
}

.btn-cancel, .btn-submit {
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
            <form method="GET" style="margin: 0;">
                <input type="hidden" name="url" value="Employee">
                <select class="filter-select" name="role" onchange="this.form.submit()">
                    <option value="all" <?= $roleFilter === 'all' ? 'selected' : '' ?>>Tất cả vai trò</option>
                    <?php foreach ($roles as $id => $name): ?>
                        <option value="<?= $id ?>" <?= $roleFilter == $id ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <!-- Button Thêm mới -->
            <button class="btn-primary" onclick="openEmployeeModal('add')">
                ➕ Thêm nhân viên mới
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div style="margin-bottom: 24px;">
        <form method="GET" action="" class="search-form">
            <input type="hidden" name="url" value="Employee">
            <input type="text" name="search" class="search-input" placeholder="🔍 Tìm kiếm theo tên, email, số điện thoại..." value="<?= htmlspecialchars($keyword) ?>">
            <button type="submit" class="btn-primary">🔍 Tìm kiếm</button>
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

    <!-- Employees Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>STT</th>
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
                    <?php
                    $i = 1;
                    foreach ($employees as $employee):
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
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
                                <button class="btn-edit" onclick='openEmployeeModal("edit", <?= htmlspecialchars(json_encode($employee->toArray())) ?>)' title="Sửa">
                                    ✏️ Sửa
                                </button>
                                <form method="POST" action="?url=Employee/del" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')">
                                    <input type="hidden" name="txtId" value="<?= $employee->id ?>">
                                    <button type="submit" name="btnXoa" class="btn-delete">🗑️ Xóa</button>
                                </form>
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

        <form id="employeeForm" method="POST" action="">
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
        form.action = '?url=Employee/ins';
        passwordGroup.style.display = 'block';
        usernameField.readOnly = false;
        document.getElementById('password').required = true;

        submitBtn.name = 'btnThem';
        submitBtn.innerHTML = '<span>✅</span> Lưu lại';
    } else {
        // Chế độ sửa
        title.textContent = '✏️ Sửa thông tin nhân viên';
        form.action = '?url=Employee/upd';

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

    // Hiển thị thông báo nếu có
    <?php if ($successMessage): ?>
        alert('<?= addslashes($successMessage) ?>');
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        alert('<?= addslashes($errorMessage) ?>');
    <?php endif; ?>
});
</script>
