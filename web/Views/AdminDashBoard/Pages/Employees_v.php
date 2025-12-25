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

<!-- Import CSS riêng cho trang Employees -->
<link rel="stylesheet" href="web/Views/AdminDashBoard/Pages/employees-page.css">

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
