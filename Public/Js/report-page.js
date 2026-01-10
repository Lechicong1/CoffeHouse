/**
 * Report Page JavaScript
 * Xử lý logic hiển thị modal và gọi API lấy chi tiết
 */

// ========== REPORT PAGE - MINIMAL JS ==========
// Chỉ validation cơ bản - KHÔNG có AJAX, modal, animation

document.addEventListener('DOMContentLoaded', function() {
    // Validation form lọc ngày
    const dateForm = document.querySelector('.date-filter-form');

    if (dateForm) {
        dateForm.addEventListener('submit', function(e) {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            // Kiểm tra ngày hợp lệ
            if (!fromDate || !toDate) {
                alert('Vui lòng chọn đầy đủ khoảng thời gian!');
                e.preventDefault();
                return false;
            }

            // Kiểm tra from_date <= to_date
            if (new Date(fromDate) > new Date(toDate)) {
                alert('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc!');
                e.preventDefault();
                return false;
            }

            // OK - cho phép submit
            return true;
        });
    }
});

// Hàm format số tiền (nếu cần dùng trong tương lai)
function formatCurrency(number) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(number);
}

console.log('✅ Report page script loaded successfully');
