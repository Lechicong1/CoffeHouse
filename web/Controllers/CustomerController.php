<?php

class CustomerController extends Controller {

    /**
     * Hiển thị danh sách khách hàng
     */
    public function GetData() {
        $service = $this->service('CustomerService');
        
        // Lấy danh sách khách hàng
        $customers = $service->getAllCustomers();
        
        // Truyền dữ liệu vào view
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Customers_v',
            'customers' => $customers,
            'totalCustomers' => $service->countCustomers()
        ]);
    }

    /**
     * Tìm kiếm khách hàng
     */
    public function timkiem() {
        $service = $this->service('CustomerService');
        
        // Lấy từ khóa tìm kiếm
        $keyword = isset($_POST['txtTimKiem']) ? trim($_POST['txtTimKiem']) : '';
        
        if (empty($keyword)) {
            // Nếu không có từ khóa, hiển thị tất cả
            $customers = $service->getAllCustomers();
        } else {
            // Tìm kiếm theo từ khóa
            $customers = $service->searchCustomers($keyword);
        }
        
        // Truyền dữ liệu vào view
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Customers_v',
            'customers' => $customers,
            'keyword' => $keyword,
            'totalCustomers' => count($customers)
        ]);
    }

    /**
     * Thêm khách hàng mới
     */
    public function ins() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('CustomerService');
            
            // Lấy dữ liệu từ form
            $data = [
                'full_name' => isset($_POST['txtFullName']) ? trim($_POST['txtFullName']) : '',
                'phone' => isset($_POST['txtPhone']) ? trim($_POST['txtPhone']) : '',
                'email' => isset($_POST['txtEmail']) ? trim($_POST['txtEmail']) : '',
                'points' => isset($_POST['txtPoints']) ? (int)$_POST['txtPoints'] : 0,
                'status' => isset($_POST['ddlStatus']) ? (int)$_POST['ddlStatus'] : 1
            ];
            
            // Gọi service để tạo khách hàng
            $result = $service->createCustomer($data);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEHOUSE/CustomerController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEHOUSE/CustomerController/GetData');
        }
    }

    /**
     * Cập nhật khách hàng
     */
    public function upd() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('CustomerService');
            
            // Lấy ID khách hàng
            $id = isset($_POST['txtId']) ? (int)$_POST['txtId'] : 0;
            
            if ($id <= 0) {
                echo "<script>
                    alert('ID khách hàng không hợp lệ!');
                    window.history.back();
                </script>";
                return;
            }
            
            // Lấy dữ liệu từ form
            $data = [
                'full_name' => isset($_POST['txtFullName']) ? trim($_POST['txtFullName']) : '',
                'phone' => isset($_POST['txtPhone']) ? trim($_POST['txtPhone']) : '',
                'email' => isset($_POST['txtEmail']) ? trim($_POST['txtEmail']) : '',
                'points' => isset($_POST['txtPoints']) ? (int)$_POST['txtPoints'] : 0,
                'status' => isset($_POST['ddlStatus']) ? (int)$_POST['ddlStatus'] : 1
            ];
            
            // Gọi service để cập nhật
            $result = $service->updateCustomer($id, $data);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEHOUSE/CustomerController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEHOUSE/CustomerController/GetData');
        }
    }

    /**
     * Xóa khách hàng
     */
    public function del() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('CustomerService');
            
            // Lấy ID khách hàng
            $id = isset($_POST['idDel']) ? (int)$_POST['idDel'] : 0;
            
            if ($id <= 0) {
                echo "<script>
                    alert('ID khách hàng không hợp lệ!');
                    window.history.back();
                </script>";
                return;
            }
            
            // Gọi service để xóa
            $result = $service->deleteCustomer($id);
            
            // Hiển thị thông báo
            if ($result['success']) {
                echo "<script>
                    alert('{$result['message']}');
                    window.location.href = '/COFFEHOUSE/CustomerController/GetData';
                </script>";
            } else {
                echo "<script>
                    alert('{$result['message']}');
                    window.history.back();
                </script>";
            }
        } else {
            // Redirect về trang danh sách nếu không phải POST
            header('Location: /COFFEHOUSE/CustomerController/GetData');
        }
    }

    /**
     * Lấy thông tin khách hàng theo ID (API)
     */
    public function getById() {
        $service = $this->service('CustomerService');
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            return;
        }
        
        $customer = $service->getCustomerById($id);
        
        if ($customer) {
            echo json_encode([
                'success' => true,
                'data' => $customer->toArray()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy khách hàng'
            ]);
        }
    }

    /**
     * Cập nhật điểm tích lũy
     */
    public function updatePoints() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $service = $this->service('CustomerService');
            
            $id = isset($_POST['customerId']) ? (int)$_POST['customerId'] : 0;
            $points = isset($_POST['points']) ? (int)$_POST['points'] : 0;
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
                return;
            }
            
            $result = $service->updateCustomerPoints($id, $points);
            echo json_encode($result);
        }
    }
}
