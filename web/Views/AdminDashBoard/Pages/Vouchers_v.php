<?php
/**
 * VIEW CON - Quản lý Voucher
 * Chỉ hiển thị dữ liệu, không xử lý logic
 */

// Lấy dữ liệu từ Controller (đã truyền qua $data)
$vouchers = $data['vouchers'] ?? [];
$keyword = $data['keyword'] ?? '';

?>

<!-- Import Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Import Voucher Page CSS -->
<link rel="stylesheet" href="/COFFEE_PHP/Public/Css/voucher-page.css">

<section id="vouchers" class="content-section">
    <div class="voucher-management">
        <!-- Page Header -->
        <div class="page-header">
            <h2><i class="fas fa-ticket-alt"></i> Quản Lý Voucher</h2>
            <button class="btn btn-success" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Thêm Voucher
            </button>
        </div>

        <!-- Stats Cards removed per request -->

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="search-box">
                <form action="/COFFEE_PHP/VoucherController/timkiem" method="POST">
                    <input type="text" name="txtTimKiem" placeholder="Tìm kiếm theo tên voucher, loại giảm giá..." 
                           value="<?php echo isset($keyword) ? htmlspecialchars($keyword) : ''; ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>

            <!-- Button Xuất Excel -->
            <form method="POST" action="/COFFEE_PHP/VoucherController/xuatexcel" style="display: inline-block; margin: 0 10px;">
                <input type="hidden" name="txtSearch" value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" name="btnXuatexcel" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </button>
            </form>

            <?php if (isset($keyword) && !empty($keyword)): ?>
                <a href="/COFFEE_PHP/VoucherController/GetData" class="btn btn-warning">
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
                        <th>Tên Voucher</th>
                        <th>Điểm Đổi</th>
                        <th>Loại Giảm</th>
                        <th>Giá Trị</th>
                        <th>Đơn Tối Thiểu</th>
                        <th>Số Lượng</th>
                        <th>Đã Dùng</th>
                        <th>Hạn Dùng</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($vouchers) && !empty($vouchers)): ?>
                        <?php foreach ($vouchers as $voucher): ?>
                            <tr>
                                <td><?php echo $voucher->id; ?></td>
                                <td><strong><?php echo htmlspecialchars($voucher->name); ?></strong></td>
                                <td><?php echo number_format($voucher->point_cost); ?> điểm</td>
                                <td>
                                    <?php if ($voucher->discount_type === 'FIXED'): ?>
                                        <span class="badge badge-info">Cố định</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Phần trăm</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($voucher->discount_type === 'FIXED') {
                                        echo number_format($voucher->discount_value) . ' đ';
                                    } else {
                                        echo $voucher->discount_value . '%';
                                    }
                                    ?>
                                </td>
                                <td><?php echo number_format($voucher->min_bill_total); ?> đ</td>
                                <td>
                                    <?php echo $voucher->quantity ? number_format($voucher->quantity) : '<span class="text-muted">Không giới hạn</span>'; ?>
                                </td>
                                <td><?php echo number_format($voucher->used_count); ?></td>
                                <td>
                                    <?php if ($voucher->end_date): ?>
                                        <?php 
                                        $endDate = date('d/m/Y', strtotime($voucher->end_date));
                                        $isExpired = strtotime($voucher->end_date) < time();
                                        ?>
                                        <span class="<?php echo $isExpired ? 'text-danger' : ''; ?>">
                                            <?php echo $endDate; ?>
                                            <?php if ($isExpired): ?>
                                                <i class="fas fa-exclamation-circle"></i>
                                            <?php endif; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Không giới hạn</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($voucher->is_active): ?>
                                        <span class="badge badge-active">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactive">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick='openEditModal(<?php echo json_encode($voucher); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="confirmDelete(<?php echo $voucher->id; ?>, '<?php echo htmlspecialchars($voucher->name); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="no-data">
                                <i class="fas fa-inbox fa-3x"></i>
                                <p>Không có dữ liệu voucher</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Voucher -->
    <div id="voucherModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm Voucher</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="voucherForm" method="POST">
                <input type="hidden" name="txtId" id="txtId">
                <input type="hidden" name="txtUsedCount" id="txtUsedCount" value="0">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên Voucher <span style="color: red;">*</span></label>
                        <input type="text" name="txtName" id="txtName" required>
                    </div>

                    <div class="form-group">
                        <label>Điểm Đổi <span style="color: red;">*</span></label>
                        <input type="number" name="txtPointCost" id="txtPointCost" required min="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Loại Giảm Giá <span style="color: red;">*</span></label>
                        <select name="ddlDiscountType" id="ddlDiscountType" onchange="updateDiscountValueLabel()">
                            <option value="FIXED">Cố định (VNĐ)</option>
                            <option value="PERCENT">Phần trăm (%)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label id="lblDiscountValue">Giá Trị Giảm <span style="color: red;">*</span></label>
                        <input type="number" name="txtDiscountValue" id="txtDiscountValue" required min="0" step="0.01">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" id="maxDiscountGroup" style="display: none;">
                            <label>Giảm Tối Đa (VNĐ)</label>
                            <input type="number" name="txtMaxDiscount" id="txtMaxDiscount" min="0">
                            <small class="form-help">Chỉ áp dụng cho loại phần trăm</small>
                        </div>

                    <div class="form-group">
                        <label>Đơn Hàng Tối Thiểu (VNĐ)</label>
                        <input type="number" name="txtMinBill" id="txtMinBill" min="0" value="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày Bắt Đầu</label>
                        <input type="date" name="txtStartDate" id="txtStartDate">
                    </div>

                    <div class="form-group">
                        <label>Ngày Kết Thúc</label>
                        <input type="date" name="txtEndDate" id="txtEndDate">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số Lượng</label>
                        <input type="number" name="txtQuantity" id="txtQuantity" min="0">
                        <small class="form-help">Để trống nếu không giới hạn</small>
                    </div>

                    <div class="form-group">
                        <label>Trạng Thái</label>
                        <select name="ddlStatus" id="ddlStatus">
                            <option value="1">Hoạt động</option>
                            <option value="0">Không hoạt động</option>
                        </select>
                    </div>
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
    <form id="deleteForm" action="/COFFEE_PHP/VoucherController/del" method="POST" style="display: none;">
        <input type="hidden" name="idDel" id="idDel">
    </form>

    <script>
        // Mở modal thêm voucher
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Thêm Voucher';
            document.getElementById('voucherForm').action = '/COFFEE_PHP/VoucherController/ins';
            document.getElementById('voucherForm').reset();
            document.getElementById('txtId').value = '';
            document.getElementById('txtUsedCount').value = '0';
            updateDiscountValueLabel();
            document.getElementById('voucherModal').style.display = 'block';
        }

        // Mở modal sửa voucher
        function openEditModal(voucher) {
            document.getElementById('modalTitle').textContent = 'Sửa Voucher';
            document.getElementById('voucherForm').action = '/COFFEE_PHP/VoucherController/upd';
            
            document.getElementById('txtId').value = voucher.id;
            document.getElementById('txtName').value = voucher.name;
            document.getElementById('txtPointCost').value = voucher.point_cost;
            document.getElementById('ddlDiscountType').value = voucher.discount_type;
            document.getElementById('txtDiscountValue').value = voucher.discount_value;
            document.getElementById('txtMaxDiscount').value = voucher.max_discount_value || '';
            document.getElementById('txtMinBill').value = voucher.min_bill_total;
            document.getElementById('txtStartDate').value = voucher.start_date || '';
            document.getElementById('txtEndDate').value = voucher.end_date || '';
            document.getElementById('txtQuantity').value = voucher.quantity || '';
            document.getElementById('txtUsedCount').value = voucher.used_count;
            document.getElementById('ddlStatus').value = voucher.is_active ? '1' : '0';
            
            updateDiscountValueLabel();
            document.getElementById('voucherModal').style.display = 'block';
        }

        // Đóng modal
        function closeModal() {
            document.getElementById('voucherModal').style.display = 'none';
        }

        // Xác nhận xóa
        function confirmDelete(id, name) {
            if (confirm('Bạn có chắc chắn muốn xóa voucher "' + name + '"?')) {
                document.getElementById('idDel').value = id;
                document.getElementById('deleteForm').submit();
            }
        }

        // Cập nhật label cho discount value
        function updateDiscountValueLabel() {
            const discountType = document.getElementById('ddlDiscountType').value;
            const label = document.getElementById('lblDiscountValue');
            const maxGroup = document.getElementById('maxDiscountGroup');
            
            if (discountType === 'FIXED') {
                label.innerHTML = 'Giá Trị Giảm (VNĐ) <span style="color: red;">*</span>';
                if (maxGroup) maxGroup.style.display = 'none';
            } else {
                label.innerHTML = 'Giá Trị Giảm (%) <span style="color: red;">*</span>';
                if (maxGroup) maxGroup.style.display = 'block';
            }
        }

        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('voucherModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</section>
