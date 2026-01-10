/**
 * filepath: /Public/Js/inventory-check-page.js
 * Inventory Check Page Logic - CLEAN VERSION
 */

document.addEventListener('DOMContentLoaded', function () {
    console.log('=== InventoryCheck JS loaded ===');

    // ========== DOM Elements ==========
    const ingredientSelect = document.getElementById('ingredientSelect');
    const actualQuantityInput = document.getElementById('actualQuantity');
    const refreshInventoryBtn = document.getElementById('refreshInventoryBtn');
    const form = document.getElementById('inventoryCheckForm');
    const inventoryTableBody = document.getElementById('inventoryTableBody');

    console.log('Elements check:', {
        ingredientSelect: !!ingredientSelect,
        actualQuantityInput: !!actualQuantityInput,
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

