/**
 * filepath: /Public/Js/inventory-check-page.js
 * Inventory Check Page Logic - CLEAN VERSION
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('=== InventoryCheck JS loaded ===');

    // ========== DOM Elements ==========
    const ingredientSelect = document.getElementById('ingredientSelect');
    const actualQuantityInput = document.getElementById('actualQuantity');
    const calculateBtn = document.getElementById('calculateBtn');
    const refreshInventoryBtn = document.getElementById('refreshInventoryBtn');
    const form = document.getElementById('inventoryCheckForm');
    const inventoryTableBody = document.getElementById('inventoryTableBody');

    console.log('Elements check:', {
        ingredientSelect: !!ingredientSelect,
        actualQuantityInput: !!actualQuantityInput,
        calculateBtn: !!calculateBtn,
        form: !!form,
        inventoryTableBody: !!inventoryTableBody
    });

    // ========== Load dữ liệu từ table vào Map ==========
    const ingredientMap = new Map();

    if (inventoryTableBody) {
        const rows = inventoryTableBody.querySelectorAll('tr');
        console.log('Found rows:', rows.length);

        rows.forEach((row, idx) => {
            const cells = row.querySelectorAll('td');

            if (cells.length >= 3) {
                const name = cells[0].textContent.trim();
                const unit = cells[1].textContent.trim();
                const theoryText = cells[2].textContent.trim();
                const theory = parseFloat(theoryText.replace(/,/g, ''));

                ingredientMap.set(name, {
                    name: name,
                    unit: unit,
                    theory: theory
                });

                console.log('✅ Loaded:', name, '→', theory);
            }
        });
    }

    console.log('Total ingredients in map:', ingredientMap.size);
    console.log('Map keys:', Array.from(ingredientMap.keys()));

    // ========== Nút TÍNH TOÁN ==========
    if (calculateBtn) {
        console.log('✅ calculateBtn found');

        calculateBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔵 CALCULATE CLICKED!');

            const ingredientName = ingredientSelect.value;
            const actualQty = parseFloat(actualQuantityInput.value);

            console.log('Input:', {ingredientName, actualQty});

            // Validation
            if (!ingredientName) {
                alert('❌ Vui lòng chọn nguyên liệu!');
                return;
            }

            if (isNaN(actualQty) || actualQty < 0) {
                alert('❌ Vui lòng nhập số lượng hợp lệ!');
                return;
            }

            // Lấy từ map
            const info = ingredientMap.get(ingredientName);
            console.log('Info from map:', info);

            if (!info) {
                alert('❌ Không tìm thấy: ' + ingredientName);
                console.error('Available:', Array.from(ingredientMap.keys()));
                return;
            }

            const theoryQty = info.theory;
            const difference = actualQty - theoryQty;
            const percentDiff = theoryQty !== 0 ? Math.abs((difference / theoryQty) * 100) : 0;

            // Xác định trạng thái và badge class
            let statusText = 'THỪA HÀNG';
            let badgeClass = 'badge-success';

            if (percentDiff >= 1 && percentDiff <= 2) {
                statusText = 'CHÍNH XÁC';
                badgeClass = 'badge-success';
            } else if (percentDiff > 2 && percentDiff <= 5) {
                statusText = 'CẢNH BÁO';
                badgeClass = 'badge-warning';
            } else if (percentDiff > 5) {
                statusText = 'NGHIÊM TRỌNG';
                badgeClass = 'badge-danger';
            }

            console.log('✅ Calculated:', {
                ingredient: ingredientName,
                theory: theoryQty,
                actual: actualQty,
                difference: difference,
                percent: percentDiff,
                status: statusText
            });

            // TÌM DÒNG TRONG BẢNG VÀ CẬP NHẬT
            const rows = inventoryTableBody.querySelectorAll('tr');
            let rowFound = false;

            rows.forEach(row => {
                const nameCell = row.querySelector('td:first-child');
                if (nameCell && nameCell.textContent.trim() === ingredientName) {
                    rowFound = true;

                    // Cập nhật các ô
                    const cells = row.querySelectorAll('td');

                    // Cột 4: Thực tế (index 3)
                    cells[3].textContent = actualQty.toFixed(2);
                    cells[3].classList.add('text-right');

                    // Cột 5: Chênh lệch (index 4)
                    cells[4].textContent = difference.toFixed(2);
                    cells[4].classList.add('text-right');

                    // Cột 6: Trạng thái (index 5)
                    cells[5].innerHTML = `<span class="badge ${badgeClass}">${statusText}</span>`;

                    // Cột 7: Thời gian (index 6)
                    const now = new Date();
                    const timeStr = now.toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    cells[6].textContent = timeStr;

                    // Highlight row tạm thời
                    row.style.backgroundColor = '#fff9c4';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 1500);

                    console.log('✅ Updated row for:', ingredientName);
                }
            });

            if (!rowFound) {
                alert('❌ Không tìm thấy dòng trong bảng!');
            }
        });
    } else {
        console.error('❌ calculateBtn NOT FOUND!');
    }

    // ========== Form Submit Handler ==========
    if (form) {
        // Thêm event listener cho từng nút submit
        const saveBtn = document.querySelector('button[name="btnSave"]');
        const updateBtn = document.querySelector('button[name="btnUpdate"]');

        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🔵 SAVE button clicked');

                const ingredientName = ingredientSelect.value;
                const actualQty = actualQuantityInput.value;

                // Validation
                if (!ingredientName) {
                    alert('❌ Vui lòng chọn nguyên liệu!');
                    return false;
                }

                if (!actualQty || parseFloat(actualQty) < 0) {
                    alert('❌ Vui lòng nhập số lượng thực tế hợp lệ!');
                    return false;
                }

                // Set action và submit (dùng URL format cho Router)
                form.action = 'index.php?url=InventoryCheck/save';
                console.log('Form action set to:', form.action);
                console.log('Form data:', {
                    txtIngredient: ingredientName,
                    txtActualQuantity: actualQty
                });

                form.submit();
            });
        }

        if (updateBtn) {
            updateBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🔵 UPDATE button clicked');

                const ingredientName = ingredientSelect.value;
                const actualQty = actualQuantityInput.value;

                // Validation
                if (!ingredientName) {
                    alert('❌ Vui lòng chọn nguyên liệu!');
                    return false;
                }

                if (!actualQty || parseFloat(actualQty) < 0) {
                    alert('❌ Vui lòng nhập số lượng thực tế hợp lệ!');
                    return false;
                }

                // Set action và submit (dùng URL format cho Router)
                form.action = 'index.php?url=InventoryCheck/update';
                console.log('Form action set to:', form.action);

                form.submit();
            });
        }
    }

    // ========== Refresh Button ==========
    if (refreshInventoryBtn) {
        refreshInventoryBtn.addEventListener('click', function() {
            console.log('🔄 Refreshing...');
            window.location.reload();
        });
    }

    // ========== Click row to select ==========
    if (inventoryTableBody) {
        inventoryTableBody.addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (!row) return;

            const nameCell = row.querySelector('td:first-child');
            if (!nameCell) return;

            const ingredientName = nameCell.textContent.trim();
            console.log('📌 Row clicked, selecting:', ingredientName);

            if (ingredientSelect) {
                ingredientSelect.value = ingredientName;
            }

            // Highlight row
            inventoryTableBody.querySelectorAll('tr').forEach(r => {
                r.style.backgroundColor = '';
            });
            row.style.backgroundColor = '#e8f5e9';
        });
    }

    console.log('✅ Script initialization complete');
});

