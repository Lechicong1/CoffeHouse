<?php
/**
 * VIEW CON - Quản lý Khách hàng
 * Chỉ hiển thị dữ liệu, không xử lý logic
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$customers = $data['customers'] ?? [];
$totalCustomers = $data['totalCustomers'] ?? 0;
$keyword = $data['keyword'] ?? '';
?>

<!-- Import Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
        .customer-management {
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            color: #333;
            font-size: 28px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 10px;
            color: white;
        }

        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .search-box {
            flex: 1;
            max-width: 500px;
        }

        .search-box form {
            display: flex;
            gap: 10px;
        }

        .search-box input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-warning {
            background: #f6ad55;
            color: white;
        }

        .btn-warning:hover {
            background: #ed8936;
        }

        .btn-danger {
            background: #f56565;
            color: white;
        }

        .btn-danger:hover {
            background: #e53e3e;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-active {
            background: #c6f6d5;
            color: #22543d;
        }

        .badge-inactive {
            background: #fed7d7;
            color: #742a2a;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #333;
            font-size: 24px;
        }

        .close {
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
</style>

<section id="customers" class="content-section">
    <div class="customer-management">
        <!-- Page Header -->
        <div class="page-header">
            <h2><i class="fas fa-users"></i> Quản Lý Khách Hàng</h2>
            <button class="btn btn-success" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Thêm Khách Hàng
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <h3>Tổng Khách Hàng</h3>
                <div class="number"><?php echo $totalCustomers; ?></div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="search-box">
                <form action="/COFFEHOUSE/CustomerController/timkiem" method="POST">
                    <input type="text" name="txtTimKiem" placeholder="Tìm kiếm theo tên, số điện thoại, email..." 
                           value="<?php echo isset($keyword) ? htmlspecialchars($keyword) : ''; ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>
            <?php if (isset($keyword) && !empty($keyword)): ?>
                <a href="/COFFEHOUSE/CustomerController/GetData" class="btn btn-warning">
                    <i class="fas fa-redo"></i> Xem tất cả
                </a>
            <?php endif; ?>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ Tên</th>
                        <th>Số Điện Thoại</th>
                        <th>Email</th>
                        <th>Điểm Tích Lũy</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($customers) && !empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo $customer->id; ?></td>
                                <td><?php echo htmlspecialchars($customer->full_name); ?></td>
                                <td><?php echo htmlspecialchars($customer->phone); ?></td>
                                <td><?php echo htmlspecialchars($customer->email ?? '-'); ?></td>
                                <td><strong><?php echo number_format($customer->points); ?></strong> điểm</td>
                                <td>
                                    <?php if ($customer->status): ?>
                                        <span class="badge badge-active">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick='openEditModal(<?php echo json_encode($customer->toArray()); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete(<?php echo $customer->id; ?>, '<?php echo htmlspecialchars($customer->full_name); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="no-data">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>Không có dữ liệu khách hàng</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Khách Hàng -->
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm Khách Hàng</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="customerForm" method="POST">
                <input type="hidden" name="txtId" id="txtId">
                
                <div class="form-group">
                    <label>Họ Tên <span style="color: red;">*</span></label>
                    <input type="text" name="txtFullName" id="txtFullName" required>
                </div>

                <div class="form-group">
                    <label>Số Điện Thoại <span style="color: red;">*</span></label>
                    <input type="text" name="txtPhone" id="txtPhone" required 
                           pattern="[0-9]{10,11}" 
                           title="Số điện thoại phải có 10-11 chữ số">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="txtEmail" id="txtEmail">
                </div>

                <div class="form-group">
                    <label>Điểm Tích Lũy</label>
                    <input type="number" name="txtPoints" id="txtPoints" value="0" min="0">
                </div>

                <div class="form-group">
                    <label>Trạng Thái</label>
                    <select name="ddlStatus" id="ddlStatus">
                        <option value="1">Hoạt động</option>
                        <option value="0">Không hoạt động</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-warning" onclick="closeModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Xóa (ẩn) -->
    <form id="deleteForm" action="/COFFEHOUSE/CustomerController/del" method="POST" style="display: none;">
        <input type="hidden" name="idDel" id="idDel">
    </form>

    <script>
        // Mở modal thêm khách hàng
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Khách Hàng';
            document.getElementById('customerForm').action = '/COFFEHOUSE/CustomerController/ins';
            document.getElementById('customerForm').reset();
            document.getElementById('txtId').value = '';
            document.getElementById('customerModal').style.display = 'block';
        }

        // Mở modal sửa khách hàng
        function openEditModal(customer) {
            document.getElementById('modalTitle').textContent = 'Sửa Khách Hàng';
            document.getElementById('customerForm').action = '/COFFEHOUSE/CustomerController/upd';
            
            document.getElementById('txtId').value = customer.id;
            document.getElementById('txtFullName').value = customer.full_name;
            document.getElementById('txtPhone').value = customer.phone;
            document.getElementById('txtEmail').value = customer.email || '';
            document.getElementById('txtPoints').value = customer.points;
            document.getElementById('ddlStatus').value = customer.status ? '1' : '0';
            
            document.getElementById('customerModal').style.display = 'block';
        }

        // Đóng modal
        function closeModal() {
            document.getElementById('customerModal').style.display = 'none';
        }

        // Xác nhận xóa
        function confirmDelete(id, name) {
            if (confirm('Bạn có chắc chắn muốn xóa khách hàng "' + name + '"?')) {
                document.getElementById('idDel').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('customerModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
    </div>
</section>
