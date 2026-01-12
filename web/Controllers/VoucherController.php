<?php
require_once __DIR__ . '/../../Config/ExcelHelper.php';

class VoucherController extends Controller {

    public function GetData() {
        $service = $this->service('VoucherService');
        $service->syncExpiryStatuses();
        
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'vouchers',
            'page' => 'Vouchers_v',
            'vouchers' => $service->getAllVouchers()
        ]);
    }

    public function timkiem() {
        $service = $this->service('VoucherService');
        $service->syncExpiryStatuses();
        $keyword = trim($_POST['txtTimKiem'] ?? '');
        
        $vouchers = empty($keyword) 
            ? $service->getAllVouchers() 
            : $service->searchVouchers($keyword);
        
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'vouchers',
            'page' => 'Vouchers_v',
            'vouchers' => $vouchers,
            'keyword' => $keyword
        ]);
    }

    public function ins() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/VoucherController/GetData');
            return;
        }

        $data = [
            'name' => trim($_POST['txtName'] ?? ''),
            'point_cost' => (int)($_POST['txtPointCost'] ?? 0),
            'discount_type' => $_POST['ddlDiscountType'] ?? 'FIXED',
            'discount_value' => (float)($_POST['txtDiscountValue'] ?? 0),
            'max_discount_value' => !empty($_POST['txtMaxDiscount']) ? (float)$_POST['txtMaxDiscount'] : null,
            'min_bill_total' => (float)($_POST['txtMinBill'] ?? 0),
            'start_date' => $_POST['txtStartDate'] ?? null,
            'end_date' => $_POST['txtEndDate'] ?? null,
            'quantity' => !empty($_POST['txtQuantity']) ? (int)$_POST['txtQuantity'] : null,
            'is_active' => (int)($_POST['ddlStatus'] ?? 1)
        ];
        
        $result = $this->service('VoucherService')->createVoucher($data);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/VoucherController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function upd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/VoucherController/GetData');
            return;
        }

        $id = (int)($_POST['txtId'] ?? 0);
        if ($id <= 0) {
            echo "<script>alert('ID voucher không hợp lệ!'); window.history.back();</script>";
            return;
        }

        $data = [
            'name' => trim($_POST['txtName'] ?? ''),
            'point_cost' => (int)($_POST['txtPointCost'] ?? 0),
            'discount_type' => $_POST['ddlDiscountType'] ?? 'FIXED',
            'discount_value' => (float)($_POST['txtDiscountValue'] ?? 0),
            'max_discount_value' => !empty($_POST['txtMaxDiscount']) ? (float)$_POST['txtMaxDiscount'] : null,
            'min_bill_total' => (float)($_POST['txtMinBill'] ?? 0),
            'start_date' => $_POST['txtStartDate'] ?? null,
            'end_date' => $_POST['txtEndDate'] ?? null,
            'quantity' => !empty($_POST['txtQuantity']) ? (int)$_POST['txtQuantity'] : null,
            'used_count' => (int)($_POST['txtUsedCount'] ?? 0),
            'is_active' => (int)($_POST['ddlStatus'] ?? 1)
        ];
        
        $result = $this->service('VoucherService')->updateVoucher($id, $data);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/VoucherController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function del() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/VoucherController/GetData');
            return;
        }

        $id = (int)($_POST['idDel'] ?? 0);
        if ($id <= 0) {
            echo "<script>alert('ID voucher không hợp lệ!'); window.history.back();</script>";
            return;
        }
        
        $result = $this->service('VoucherService')->deleteVoucher($id);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/VoucherController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function getEligibleVouchers() {
        header('Content-Type: text/html; charset=UTF-8');
        $customerId = (int)($_POST['customer_id'] ?? 0) ?: null;
        $billTotal = (float)($_POST['bill_total'] ?? 0);

        $this->view('EmployeeDashBoard/Pages/voucher_list', [
            'vouchers' => $this->service('VoucherService')->getEligibleVouchers($customerId, $billTotal)
        ]);
        exit;
    }

    public function previewVoucher() {
        header('Content-Type: text/html; charset=UTF-8');
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $voucherId = (int)($_POST['voucher_id'] ?? 0);
        $total = (float)($_POST['total_amount'] ?? 0);

        $res = $this->service('VoucherService')->previewApplyVoucher($customerId, $voucherId, $total);

        if (!$res['success']) {
            echo '<span id="pv" data-ok="0" data-msg="'.htmlspecialchars($res['message'], ENT_QUOTES).'"></span>';
            exit;
        }
        echo '<span id="pv" data-ok="1" data-discount="'.$res['discount_amount'].'" data-total-after="'.$res['total_after'].'"></span>';
        exit;
    }

    public function xuatexcel() {
        if (!isset($_POST['btnXuatexcel'])) return;

        $service = $this->service('VoucherService');
        $keyword = trim($_POST['txtSearch'] ?? '');

        $vouchers = !empty($keyword) 
            ? $service->searchVouchers($keyword) 
            : $service->getAllVouchers();

        $data = array_map(fn($v) => [
            'id' => $v->id,
            'name' => $v->name,
            'point_cost' => $v->point_cost,
            'discount_type' => $v->discount_type == 'FIXED' ? 'Giảm cố định' : 'Giảm %',
            'discount_value' => $v->discount_value,
            'min_bill_total' => $v->min_bill_total,
            'quantity' => $v->quantity ?? 'Không giới hạn',
            'used_count' => $v->used_count,
            'is_active' => $v->is_active ? 'Hoạt động' : 'Vô hiệu hóa'
        ], $vouchers);

        $headers = [
            'id' => 'ID',
            'name' => 'Tên Voucher',
            'point_cost' => 'Điểm đổi',
            'discount_type' => 'Loại giảm giá',
            'discount_value' => 'Giá trị giảm',
            'min_bill_total' => 'Đơn tối thiểu',
            'quantity' => 'Số lượng',
            'used_count' => 'Đã sử dụng',
            'is_active' => 'Trạng thái'
        ];

        ExcelHelper::exportToExcel($data, $headers, 'DanhSachVoucher');
    }
}

