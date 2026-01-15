<?php
require_once __DIR__ . '/../../Config/ExcelHelper.php';

class CustomerController extends Controller {

    public function GetData() {
        $service = $this->service('CustomerService');
        $customers = $service->getAllCustomers();
        
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'customers',
            'page' => 'Customers_v',
            'customers' => $customers,
            'totalCustomers' => $service->countCustomers()
        ]);
    }

    public function timkiem() {
        $service = $this->service('CustomerService');
        $keyword = trim($_POST['txtTimKiem'] ?? '');
        
        $customers = empty($keyword) 
            ? $service->getAllCustomers() 
            : $service->searchCustomers($keyword);
        
        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'customers',
            'page' => 'Customers_v',
            'customers' => $customers,
            'keyword' => $keyword,
            'totalCustomers' => count($customers)
        ]);
    }

    public function ins() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/CustomerController/GetData');
            return;
        }

        $service = $this->service('CustomerService');
        $data = [
            'full_name' => trim($_POST['txtFullName'] ?? ''),
            'phone' => trim($_POST['txtPhone'] ?? ''),
            'email' => trim($_POST['txtEmail'] ?? ''),
            'points' => (int)($_POST['txtPoints'] ?? 0),
            'status' => (int)($_POST['ddlStatus'] ?? 1)
        ];
        
        $result = $service->createCustomer($data);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/CustomerController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function upd() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/CustomerController/GetData');
            return;
        }

        $id = (int)($_POST['txtId'] ?? 0);
        if ($id <= 0) {
            echo "<script>alert('ID khách hàng không hợp lệ!'); window.history.back();</script>";
            return;
        }

        $service = $this->service('CustomerService');
        $data = [
            'full_name' => trim($_POST['txtFullName'] ?? ''),
            'phone' => trim($_POST['txtPhone'] ?? ''),
            'email' => trim($_POST['txtEmail'] ?? ''),
            'points' => (int)($_POST['txtPoints'] ?? 0),
            'status' => (int)($_POST['ddlStatus'] ?? 1)
        ];
        
        $result = $service->updateCustomer($id, $data);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/CustomerController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function del() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/CustomerController/GetData');
            return;
        }

        $id = (int)($_POST['idDel'] ?? 0);
        if ($id <= 0) {
            echo "<script>alert('ID khách hàng không hợp lệ!'); window.history.back();</script>";
            return;
        }
        
        $service = $this->service('CustomerService');
        $result = $service->deleteCustomer($id);
        
        if ($result['success']) {
            echo "<script>alert('{$result['message']}'); window.location.href = '/COFFEE_PHP/CustomerController/GetData';</script>";
        } else {
            echo "<script>alert('{$result['message']}'); window.history.back();</script>";
        }
    }

    public function updatePoints() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = (int)($_POST['customerId'] ?? 0);
        $points = (int)($_POST['points'] ?? 0);
        
        if ($id <= 0) {
            echo 'Lỗi: ID không hợp lệ';
            return;
        }
        
        $result = $this->service('CustomerService')->updateCustomerPoints($id, $points);
        echo $result['success'] ? 'Thành công: ' . $result['message'] : 'Lỗi: ' . $result['message'];
    }

    public function xuatexcel() {
        if (!isset($_POST['btnXuatexcel'])) return;

        $service = $this->service('CustomerService');
        $keyword = trim($_POST['txtSearch'] ?? '');

        $customers = !empty($keyword) 
            ? $service->searchCustomers($keyword) 
            : $service->getAllCustomers();

        $data = array_map(fn($c) => [
            'id' => $c->id,
            'full_name' => $c->full_name,
            'phone' => $c->phone,
            'email' => $c->email ?? '-',
            'points' => $c->points ?? 0,
            'status' => $c->status ? 'Hoạt động' : 'Vô hiệu hóa'
        ], $customers);

        $headers = [
            'id' => 'ID',
            'full_name' => 'Họ và Tên',
            'phone' => 'Số Điện Thoại',
            'email' => 'Email',
            'points' => 'Điểm Tích Lũy',
            'status' => 'Trạng Thái'
        ];

        ExcelHelper::exportToExcel($data, $headers, 'DanhSachKhachHang');
    }
}
