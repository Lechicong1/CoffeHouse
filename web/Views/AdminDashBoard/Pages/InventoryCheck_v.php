<!-- 
    filepath: /web/Views/AdminDashBoard/Pages/InventoryCheck_v.php
    Trang Kiểm Kho Hàng Ngày - Admin Dashboard
    Minimalist White Design
-->

<?php
// DEBUG: Kiểm tra dữ liệu
echo "<!-- DEBUG: inventoryData count = " . count($inventoryData ?? []) . " -->";
if (!empty($inventoryData)) {
    echo "<!-- DEBUG: First item: " . json_encode($inventoryData[0]) . " -->";
}
?>

<!-- Include CSS cho trang này -->
<link rel="stylesheet" href="Public/Css/inventory-check-page.css">

<div class="page-container">
    <!-- Header Section -->
    <div class="page-header">
        <div class="header-left">
            <h2 class="section-title">📋 KIỂM KHO HÀNG NGÀY</h2>
            <p class="section-subtitle">Theo dõi và kiểm tra tồn kho nguyên liệu</p>
        </div>
        <div class="header-right">
            <button class="btn btn-primary" id="refreshInventoryBtn">
                <span class="btn-icon">🔄</span>
                <span class="btn-text">LÀM MỚI</span>
            </button>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="content-section">
        <!-- Inventory Check Table -->
        <div class="data-card">
            <div class="table-container">
                <table class="data-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>NGUYÊN LIỆU</th>
                            <th>ĐƠN VỊ</th>
                            <th>LÝ THUYẾT</th>
                            <th>THỰC TẾ</th>
                            <th>CHÊNH LỆCH</th>
                            <th>TRẠNG THÁI</th>
                            <th>THỜI GIAN</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php if (!empty($inventoryData)): ?>
                            <?php foreach ($inventoryData as $item): ?>
                                <?php 
                                    $ingredient = $item['ingredient'];
                                    $check = $item['check'];
                                    // Kiểm tra check có dữ liệu không
                                    $hasCheck = !empty($check) && isset($check['actualQuantity']);
                                ?>
                                <tr>
                                    <td class="text-bold"><?php echo htmlspecialchars($ingredient['name']); ?></td>
                                    <td><?php echo htmlspecialchars($ingredient['unit']); ?></td>
                                    <td class="text-right"><?php echo number_format($ingredient['stock_quantity'], 2); ?></td>
                                    
                                    <?php if ($hasCheck): ?>
                                        <!-- Đã kiểm kho -->
                                        <td class="text-right"><?php echo number_format($check['actualQuantity'], 2); ?></td>
                                        <td class="text-right"><?php echo number_format($check['difference'], 2); ?></td>
                                        <td>
                                            <?php
                                            // Tính trạng thái
                                            $theory = $ingredient['stock_quantity'];
                                            $actual = $check['actualQuantity'];
                                            $percentDiff = $theory != 0 ? abs(($actual - $theory) / $theory * 100) : 0;

                                            $badgeClass = 'badge-success';
                                            $statusText = 'CHÍNH XÁC';

                                            if ($percentDiff >= 1 && $percentDiff <= 2) {
                                                $badgeClass = 'badge-success';
                                                $statusText = 'CHÍNH XÁC';
                                            } else if ($percentDiff > 2 && $percentDiff <= 5) {
                                                $badgeClass = 'badge-warning';
                                                $statusText = 'CẢNH BÁO';
                                            } else if ($percentDiff > 5) {
                                                $badgeClass = 'badge-danger';
                                                $statusText = 'NGHIÊM TRỌNG';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($check['checked_at'])); ?></td>
                                    <?php else: ?>
                                        <!-- Chưa kiểm kho -->
                                        <td class="text-right">-</td>
                                        <td class="text-right">-</td>
                                        <td><span class="badge badge-warning">CHƯA KIỂM TRA</span></td>
                                        <td>-</td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có dữ liệu</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Check Form -->
        <div class="form-card">
            <div class="form-header">
                <h3 class="form-title">📋 NHẬP THÔNG TIN KIỂM TRA KHO</h3>
            </div>
            <div class="form-body">
                <form id="inventoryCheckForm" method="POST" action="InventoryCheck/save">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ingredientSelect" class="form-label">Nguyên liệu:</label>
                            <select class="form-control" id="ingredientSelect" name="txtIngredient" required>
                                <option value="">-- Chọn nguyên liệu --</option>
                                <?php if (!empty($inventoryData)): ?>
                                    <?php foreach ($inventoryData as $item): ?>
                                        <option value="<?php echo htmlspecialchars($item['ingredient']['name']); ?>">
                                            <?php echo htmlspecialchars($item['ingredient']['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="actualQuantity" class="form-label">Số lượng thực tế:</label>
                            <input 
                                type="number" 
                                class="form-control" 
                                id="actualQuantity"
                                name="txtActualQuantity" 
                                step="0.01" 
                                placeholder="Nhập số lượng..." 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success" name="btnSave">
                            <span class="btn-icon">✅</span>
                            <span class="btn-text">LƯU MỚI</span>
                        </button>
                        <button type="submit" class="btn btn-warning" name="btnUpdate">
                            <span class="btn-icon">✏️</span>
                            <span class="btn-text">SỬA</span>
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <span class="btn-icon">🔄</span>
                            <span class="btn-text">LÀM MỚI</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript for this page -->
<script src="Public/Js/inventory-check-page.js"></script>
