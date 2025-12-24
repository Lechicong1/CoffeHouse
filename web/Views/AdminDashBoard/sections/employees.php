<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/sections/employees.php
 * Employees Management Section - Quản lý nhân viên
 */

// Mock data
$employees = [
    [
        'id' => 'NV001',
        'name' => 'Trần Thị B',
        'position' => 'Pha chế',
        'phone' => '0909876543',
        'email' => 'tranthib@coffeehouse.vn',
        'shift' => 'Sáng (6:00 - 14:00)',
        'salary' => '8,000,000₫',
        'hire_date' => '01/01/2024',
        'status' => 'active'
    ],
    [
        'id' => 'NV002',
        'name' => 'Lê Văn C',
        'position' => 'Thu ngân',
        'phone' => '0908765432',
        'email' => 'levanc@coffeehouse.vn',
        'shift' => 'Chiều (14:00 - 22:00)',
        'salary' => '7,500,000₫',
        'hire_date' => '15/02/2024',
        'status' => 'active'
    ],
];

$positionLabels = [
    'barista' => 'Pha chế',
    'cashier' => 'Thu ngân',
    'manager' => 'Quản lý',
    'cleaner' => 'Vệ sinh'
];
?>
<section id="employees" class="content-section">
    <div class="section-header">
        <h2>Quản lý Nhân viên</h2>
        <div class="header-actions">
            <input type="text" class="search-input" placeholder="🔍 Tìm nhân viên...">
            <select class="filter-select">
                <option value="all">Tất cả chức vụ</option>
                <option value="barista">Pha chế</option>
                <option value="cashier">Thu ngân</option>
                <option value="manager">Quản lý</option>
                <option value="cleaner">Vệ sinh</option>
            </select>
            <button class="btn-primary" onclick="openModal('employee', 'create')">
                + Thêm nhân viên
            </button>
        </div>
    </div>

    <!-- Employee Statistics -->
    <div class="mini-stats">
        <div class="mini-stat">
            <span class="mini-stat-label">Tổng nhân viên</span>
            <span class="mini-stat-value">24</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Đang làm việc</span>
            <span class="mini-stat-value success">22</span>
        </div>
        <div class="mini-stat">
            <span class="mini-stat-label">Nghỉ phép</span>
            <span class="mini-stat-value warning">2</span>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="table-responsive">
        <table class="data-table" id="employeesTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="select-all" aria-label="Chọn tất cả">
                    </th>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Chức vụ</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Ca làm việc</th>
                    <th>Lương</th>
                    <th>Ngày vào</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                    <tr data-employee-id="<?= $employee['id'] ?>">
                        <td>
                            <input type="checkbox" class="select-item">
                        </td>
                        <td><strong><?= htmlspecialchars($employee['id']) ?></strong></td>
                        <td><?= htmlspecialchars($employee['name']) ?></td>
                        <td>
                            <span class="position-badge">
                                <?= htmlspecialchars($employee['position']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($employee['phone']) ?></td>
                        <td><?= htmlspecialchars($employee['email']) ?></td>
                        <td><?= htmlspecialchars($employee['shift']) ?></td>
                        <td><strong><?= $employee['salary'] ?></strong></td>
                        <td><?= htmlspecialchars($employee['hire_date']) ?></td>
                        <td>
                            <span class="status <?= $employee['status'] ?>">
                                <?= $employee['status'] === 'active' ? 'Đang làm' : 'Nghỉ' ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-icon edit" 
                                        onclick="viewEmployee('<?= $employee['id'] ?>')" 
                                        title="Xem chi tiết">
                                    👁️
                                </button>
                                <button class="btn-icon edit" 
                                        onclick="editEmployee('<?= $employee['id'] ?>')" 
                                        title="Chỉnh sửa">
                                    ✏️
                                </button>
                                <button class="btn-icon delete" 
                                        onclick="deleteEmployee('<?= $employee['id'] ?>')" 
                                        title="Xóa">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <button class="btn-secondary" disabled>← Trước</button>
        <span class="page-info">Trang 1 / 2</span>
        <button class="btn-secondary">Sau →</button>
    </div>
</section>
