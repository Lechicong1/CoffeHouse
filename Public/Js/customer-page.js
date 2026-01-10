document.addEventListener("DOMContentLoaded", function () {
    // Mở modal thêm khách hàng
    window.openAddModal = function() {
        document.getElementById('modalTitle').textContent = 'Thêm Khách Hàng';
        document.getElementById('customerForm').action = '/COFFEE_PHP/CustomerController/ins';
        document.getElementById('customerForm').reset();
        document.getElementById('txtId').value = '';
        document.getElementById('customerModal').style.display = 'block';
    };

    // Mở modal sửa khách hàng
    window.openEditModal = function(customer) {
        document.getElementById('modalTitle').textContent = 'Sửa Khách Hàng';
        document.getElementById('customerForm').action = '/COFFEE_PHP/CustomerController/upd';
        
        document.getElementById('txtId').value = customer.id;
        document.getElementById('txtFullName').value = customer.full_name;
        document.getElementById('txtPhone').value = customer.phone;
        document.getElementById('txtEmail').value = customer.email || '';
        document.getElementById('txtPoints').value = customer.points;
        document.getElementById('ddlStatus').value = customer.status ? '1' : '0';
        
        document.getElementById('customerModal').style.display = 'block';
    };

    // Đóng modal
    window.closeModal = function() {
        document.getElementById('customerModal').style.display = 'none';
    };

    // Xác nhận xóa
    window.confirmDelete = function(id, name) {
        if (confirm('Bạn có chắc chắn muốn xóa khách hàng "' + name + '"?')) {
            document.getElementById('idDel').value = id;
            document.getElementById('deleteForm').submit();
        }
    };

    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('customerModal');
        if (event.target === modal) {
            window.closeModal();
        }
    };
});
