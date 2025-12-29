<?php

class VoucherController extends Controller {

    /**
     * Hiển thị danh sách voucher
     */
    public function GetData() {
        $service = $this->service('VoucherService');
        
        // Lấy danh sách voucher
        $vouchers = $service->getAllVouchers();
        
        // Truyền dữ liệu vào view
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'vouchers',
            'page' => 'Vouchers_v',
            'vouchers' => $vouchers,
            'totalVouchers' => $service->countVouchers(),
            'activeVouchers' => $service->countActiveVouchers()
        ]);
    }

    /**
     * Tìm kiếm voucher
     */
    public function timkiem() {
        $service = $this->service('VoucherService');
        
        // Lấy từ khóa tìm kiếm
        $keyword = isset($_POST['txtTimKiem']) ? trim($_POST['txtTimKiem']) : '';
        
        if (empty($keyword)) {
            // Nếu không có từ khóa, hiển thị tất cả
            $vouchers = $service->getAllVouchers();
        } else {
            // Tìm kiếm theo từ khóa
            $vouchers = $service->searchVouchers($keyword);
        }
        
        // Truyền dữ liệu vào view
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'vouchers',
            'page' => 'Vouchers_v',
            'vouchers' => $vouchers,
            'keyword' => $keyword,
            'totalVouchers' => count($vouchers),
            'activeVouchers' => $service->countActiveVouchers()
        ]);
    }

    /**
     * Thêm voucher mới
     */
    public function ins() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('VoucherService');
            
            // Lấy dữ liệu từ form
            $data = [
                'name' => isset($_POST['txtName']) ? trim($_POST['txtName']) : '',
                'point_cost' => isset($_POST['txtPointCost']) ? (int)$_POST['txtPointCost'] : 0,
                'discount_type' => isset($_POST['ddlDiscountType']) ? $_POST['ddlDiscountType'] : 'FIXED',
                'discount_value' => isset($_POST['txtDiscountValue']) ? (float)$_POST['txtDiscountValue'] : 0,
                'max_discount_value' => isset($_POST['txtMaxDiscount']) ? (float)$_POST['txtMaxDiscount'] : null,
                'min_bill_total' => isset($_POST['txtMinBill']) ? (float)$_POST['txtMinBill'] : 0,
                'start_date' => isset($_POST['txtStartDate']) ? $_POST['txtStartDate'] : null,
                'end_date' => isset($_POST['txtEndDate']) ? $_POST['txtEndDate'] : null,
                'quantity' => isset($_POST['txtQuantity']) ? (int)$_POST['txtQuantity'] : null,
                'is_active' => isset($_POST['ddlStatus']) ? (int)$_POST['ddlStatus'] : 1
            ];
            
            // Gọi service để tạo voucher
            $result = $service->createVoucher($data);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEE_PHP/VoucherController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEE_PHP/VoucherController/GetData');
        }
    }

    /**
     * Cập nhật voucher
     */
    public function upd() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('VoucherService');
            
            // Lấy ID voucher
            $id = isset($_POST['txtId']) ? (int)$_POST['txtId'] : 0;
            
            if ($id <= 0) {
                echo "<script>
                    alert('ID voucher không hợp lệ!');
                    window.history.back();
                </script>";
                return;
            }
            
            // Lấy dữ liệu từ form
            $data = [
                'name' => isset($_POST['txtName']) ? trim($_POST['txtName']) : '',
                'point_cost' => isset($_POST['txtPointCost']) ? (int)$_POST['txtPointCost'] : 0,
                'discount_type' => isset($_POST['ddlDiscountType']) ? $_POST['ddlDiscountType'] : 'FIXED',
                'discount_value' => isset($_POST['txtDiscountValue']) ? (float)$_POST['txtDiscountValue'] : 0,
                'max_discount_value' => isset($_POST['txtMaxDiscount']) ? (float)$_POST['txtMaxDiscount'] : null,
                'min_bill_total' => isset($_POST['txtMinBill']) ? (float)$_POST['txtMinBill'] : 0,
                'start_date' => isset($_POST['txtStartDate']) ? $_POST['txtStartDate'] : null,
                'end_date' => isset($_POST['txtEndDate']) ? $_POST['txtEndDate'] : null,
                'quantity' => isset($_POST['txtQuantity']) ? (int)$_POST['txtQuantity'] : null,
                'used_count' => isset($_POST['txtUsedCount']) ? (int)$_POST['txtUsedCount'] : 0,
                'is_active' => isset($_POST['ddlStatus']) ? (int)$_POST['ddlStatus'] : 1
            ];
            
            // Gọi service để cập nhật
            $result = $service->updateVoucher($id, $data);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEE_PHP/VoucherController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEE_PHP/VoucherController/GetData');
        }
    }

    /**
     * Xóa voucher
     */
    public function del() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('VoucherService');
            
            // Lấy ID voucher
            $id = isset($_POST['idDel']) ? (int)$_POST['idDel'] : 0;
            
            if ($id <= 0) {
                echo "<script>
                    alert('ID voucher không hợp lệ!');
                    window.history.back();
                </script>";
                return;
            }
            
            // Gọi service để xóa
            $result = $service->deleteVoucher($id);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEE_PHP/VoucherController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEE_PHP/VoucherController/GetData');
        }
    }

    /**
     * Lấy thông tin voucher theo ID
     */
    public function getById() {
        $service = $this->service('VoucherService');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            echo 'Lỗi: ID không hợp lệ';
            return;
        }
        
        $voucher = $service->getVoucherById($id);
        
        if ($voucher) {
            echo 'Thành công: Tìm thấy voucher ' . $voucher->name;
        } else {
            echo 'Lỗi: Không tìm thấy voucher';
        }
    }

    /**
     * Lấy danh sách voucher đang hoạt động
     */
    public function getActiveVouchers() {
        $service = $this->service('VoucherService');
        $vouchers = $service->getActiveVouchers();
        
        if (!empty($vouchers)) {
            echo 'Tìm thấy ' . count($vouchers) . ' voucher đang hoạt động';
        } else {
            echo 'Không có voucher nào đang hoạt động';
        }
    }

    // Lấy voucher đủ điều kiện áp dụng cho khách hàng và tổng hóa đơn
    function getEligibleVouchers() {
        header('Content-Type: text/html; charset=UTF-8');

        $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
        $billTotal  = isset($_POST['bill_total']) ? (float)$_POST['bill_total'] : 0;

        $voucherService = $this->service('VoucherService');
        $vouchers = $voucherService->getEligibleVouchers($customerId, $billTotal);

        // Render view with vouchers
        $this->view('EmployeeDashBoard/Pages/voucher_list', [
            'vouchers' => $vouchers
        ]);
        exit;
}

    // Preview áp dụng voucher cho đơn hàng
    function previewVoucher() {
        header('Content-Type: text/html; charset=UTF-8');
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $voucherId  = (int)($_POST['voucher_id'] ?? 0);
        $total      = (float)($_POST['total_amount'] ?? 0);

        $service = $this->service('VoucherService');
        $res = $service->previewVoucher($customerId, $voucherId, $total);

        if (!$res['success']) {
            // trả 1 marker lỗi để FE biết
            echo '<span id="pv" data-ok="0" data-msg="'.htmlspecialchars($res['message'], ENT_QUOTES).'"></span>';
            exit;
        }

        echo '<span id="pv" data-ok="1" data-discount="'.$res['discount_amount'].'" data-total-after="'.$res['total_after'].'"></span>';
        exit;
    }

}
?>
