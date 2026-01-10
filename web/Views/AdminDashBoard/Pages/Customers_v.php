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

<!-- Import Customer Page CSS -->
<link rel="stylesheet" href="/COFFEE_PHP/Public/Css/customer-page.css">

<section id="customers" class="content-section">
    <div class="customer-management">
        <!-- Section Header (match Employees UI) -->
        <div class="section-header">
            <div class="header-title">
                <h2><i class="fas fa-users"></i> Quản Lý Khách Hàng</h2>
                <p class="subtitle">Tổng số: <strong><?php echo $totalCustomers; ?></strong> khách hàng</p>
            </div>
            <div class="header-actions">
                <!-- reserved for actions (filters / add) -->
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="search-box">
                <form action="/COFFEE_PHP/CustomerController/timkiem" method="POST">
                    <input type="text" name="txtTimKiem" placeholder="Tìm kiếm theo tên, số điện thoại, email..." 
                           value="<?php echo isset($keyword) ? htmlspecialchars($keyword) : ''; ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Button Xuất Excel -->
            <form method="POST" action="/COFFEE_PHP/CustomerController/xuatexcel" style="display: inline-block; margin: 0 10px;">
                <input type="hidden" name="txtSearch" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="btnXuatexcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
            </form>

            <?php if (isset($keyword) && !empty($keyword)): ?>
                <a href="/COFFEE_PHP/CustomerController/GetData" class="btn btn-warning">
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
    <form id="deleteForm" action="/COFFEE_PHP/CustomerController/del" method="POST" style="display: none;">
        <input type="hidden" name="idDel" id="idDel">
    </form>

    <!-- Import Customer Page JS -->
    <script src="/COFFEE_PHP/Public/Js/customer-page.js"></script>
    </div>
</section>

