document.addEventListener("DOMContentLoaded", function () {
    window.openAddModal = function() {
        document.getElementById('modalTitle').textContent = 'Thêm Voucher';
        document.getElementById('voucherForm').action = '/COFFEE_PHP/VoucherController/ins';
        document.getElementById('voucherForm').reset();
        document.getElementById('txtId').value = '';
        document.getElementById('txtUsedCount').value = '0';
        window.updateDiscountValueLabel();
        document.getElementById('voucherModal').style.display = 'block';
    };

    window.openEditModal = function(voucher) {
        document.getElementById('modalTitle').textContent = 'Sửa Voucher';
        document.getElementById('voucherForm').action = '/COFFEE_PHP/VoucherController/upd';
        
        document.getElementById('txtId').value = voucher.id;
        document.getElementById('txtName').value = voucher.name;
        document.getElementById('txtPointCost').value = voucher.point_cost;
        document.getElementById('ddlDiscountType').value = voucher.discount_type;
        document.getElementById('txtDiscountValue').value = voucher.discount_value;
        document.getElementById('txtMaxDiscount').value = voucher.max_discount_value || '';
        document.getElementById('txtMinBill').value = voucher.min_bill_total;
        
        var startDate = voucher.start_date ? voucher.start_date.substring(0, 10) : '';
        var endDate = voucher.end_date ? voucher.end_date.substring(0, 10) : '';
        document.getElementById('txtStartDate').value = startDate;
        document.getElementById('txtEndDate').value = endDate;
        
        document.getElementById('txtQuantity').value = voucher.quantity || '';
        document.getElementById('txtUsedCount').value = voucher.used_count;
        document.getElementById('ddlStatus').value = voucher.is_active ? '1' : '0';
        
        window.updateDiscountValueLabel();
        document.getElementById('voucherModal').style.display = 'block';
    };

    window.closeModal = function() {
        document.getElementById('voucherModal').style.display = 'none';
    };

    window.confirmDelete = function(id, name) {
        if (confirm('Bạn có chắc chắn muốn xóa voucher "' + name + '"?')) {
            document.getElementById('idDel').value = id;
            document.getElementById('deleteForm').submit();
        }
    };

    window.updateDiscountValueLabel = function() {
        const discountType = document.getElementById('ddlDiscountType').value;
        const label = document.getElementById('lblDiscountValue');
        const maxGroup = document.getElementById('maxDiscountGroup');
        const maxInput = document.getElementById('txtMaxDiscount');
        
        if (discountType === 'FIXED') {
            label.innerHTML = 'Giá Trị Giảm (VNĐ) <span style="color: red;">*</span>';
            if (maxGroup) maxGroup.style.display = 'none';
            if (maxInput) maxInput.value = ''; 
        } else {
            label.innerHTML = 'Giá Trị Giảm (%) <span style="color: red;">*</span>';
            if (maxGroup) maxGroup.style.display = 'block';
        }
    };

    window.onclick = function(event) {
        const modal = document.getElementById('voucherModal');
        if (event.target === modal) {
            window.closeModal();
        }
    };
});
