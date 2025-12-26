const modal = document.getElementById('importModal');
const form = document.getElementById('importForm');
const modalTitle = document.getElementById('modalTitle');

function openImportModal(mode, importData = null) {
    modal.classList.add('active');
    
    if (mode === 'add') {
        modalTitle.textContent = 'Tạo phiếu nhập mới';
        form.action = '?url=InventoryImportController/store';
        form.reset();
        document.getElementById('importId').value = '';
        // Set default date to today
        document.getElementById('importDate').valueAsDate = new Date();
    } else {
        modalTitle.textContent = 'Cập nhật phiếu nhập';
        form.action = '?url=InventoryImportController/update';
        
        document.getElementById('importId').value = importData.id;
        document.getElementById('ingredientId').value = importData.ingredient_id;
        document.getElementById('importQuantity').value = importData.import_quantity;
        document.getElementById('totalCost').value = importData.total_cost;
        document.getElementById('importDate').value = importData.import_date; // Format YYYY-MM-DD matches input type date
        document.getElementById('note').value = importData.note;
    }
}

function closeImportModal() {
    modal.classList.remove('active');
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == modal) {
        closeImportModal();
    }
}
