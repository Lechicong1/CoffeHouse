<?php
/**
 * Employee Controller - Quản lý nhân viên
 * Theo mô hình MVC chuẩn
 */

require_once __DIR__ . '/../../Config/Controller.php';
require_once __DIR__ . '/../Services/EmployeeService.php';

class EmployeeController extends Controller {
    private $employeeService;
    
    function __construct() {
        $this->employeeService = new EmployeeService();

        // Tự động gọi view khi khởi tạo controller
        $this->index();
    }
    
    /**
     * Hiển thị danh sách nhân viên (Method mặc định)
     */
    function index() {
        $keyword = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? 'all';

        try {
            // Lấy danh sách nhân viên
            if (!empty($keyword)) {
                $employees = $this->employeeService->searchEmployees($keyword);
            } elseif ($roleFilter !== 'all') {
                $employees = $this->employeeService->getEmployeesByRole((int)$roleFilter);
            } else {
                $employees = $this->employeeService->getAllEmployees();
            }
            
            // Lấy thống kê
            $stats = $this->employeeService->getStatistics();
            
        } catch (Exception $e) {
            $employees = [];
            $stats = ['total' => 0, 'manager' => 0, 'barista' => 0, 'cashier' => 0, 'waiter' => 0, 'cleaner' => 0];
            $errorMessage = $e->getMessage();
        }
        
        // Gọi MasterLayout (view cha) và truyền page (view con) vào
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Employees_v',
            'section' => 'employees',
            'employees' => $employees,
            'stats' => $stats,
            'keyword' => $keyword,
            'roleFilter' => $roleFilter,
            'successMessage' => $_GET['msg'] ?? null,
            'errorMessage' => $errorMessage ?? null
        ]);
    }
    
    /**
     * Tìm kiếm nhân viên (POST)
     */
    function timkiem() {
        if (isset($_POST['btnTimkiem'])) {
            $keyword = $_POST['txtSearch'] ?? '';
            $roleFilter = $_POST['ddlRole'] ?? 'all';

            header("Location: ?url=Employee&search=" . urlencode($keyword) . "&role=" . $roleFilter);
            exit;
        }
    }

    /**
     * Thêm nhân viên mới (POST)
     */
    function ins() {
        if (isset($_POST['btnThem'])) {
            try {
                // Kiểm tra username có trùng không
                if ($this->employeeService->checkUsernameExists($_POST['txtUsername'])) {
                    header("Location: ?url=Employee&msg=" . urlencode("❌ Username đã tồn tại!"));
                    exit;
                }
                
                $data = [
                    'username' => $_POST['txtUsername'],
                    'password' => $_POST['txtPassword'],
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (float)$_POST['txtLuong']
                ];
                
                $result = $this->employeeService->createEmployee($data);
                
                if ($result['success']) {
                    header("Location: ?url=Employee&msg=" . urlencode("✅ Thêm nhân viên thành công!"));
                } else {
                    header("Location: ?url=Employee&msg=" . urlencode("❌ " . $result['message']));
                }
                
            } catch (Exception $e) {
                header("Location: ?url=Employee&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }

    /**
     * Cập nhật nhân viên (POST)
     */
    function upd() {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $id = (int)$_POST['txtId'];
                
                $data = [
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (float)$_POST['txtLuong']
                ];
                
                // Nếu có password mới
                if (!empty($_POST['txtPassword'])) {
                    $data['password'] = $_POST['txtPassword'];
                }
                
                $result = $this->employeeService->updateEmployee($id, $data);
                
                if ($result['success']) {
                    header("Location: ?url=Employee&msg=" . urlencode("✅ Cập nhật thành công!"));
                } else {
                    header("Location: ?url=Employee&msg=" . urlencode("❌ " . $result['message']));
                }
                
            } catch (Exception $e) {
                header("Location: ?url=Employee&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
    
    /**
     * Xóa nhân viên (POST)
     */
    function del() {
        if (isset($_POST['btnXoa'])) {
            try {
                $id = (int)$_POST['txtId'];
                $result = $this->employeeService->deleteEmployee($id);

                if ($result['success']) {
                    header("Location: ?url=Employee&msg=" . urlencode("✅ Xóa nhân viên thành công!"));
                } else {
                    header("Location: ?url=Employee&msg=" . urlencode("❌ " . $result['message']));
                }

            } catch (Exception $e) {
                header("Location: ?url=Employee&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
}
?>
